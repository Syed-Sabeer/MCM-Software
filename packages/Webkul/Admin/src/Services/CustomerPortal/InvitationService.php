<?php

namespace Webkul\Admin\Services\CustomerPortal;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Webkul\Admin\Models\CustomerPortalInvitation;
use Webkul\Admin\Models\CustomerPortalUser;
use Webkul\Admin\Notifications\CustomerPortalInvitationNotification;
use Webkul\Contact\Models\Organization;

class InvitationService
{
    public function createAccount(Organization $organization, array $data, bool $send = true): array
    {
        if (strtolower((string) $organization->type) !== 'customer') {
            throw ValidationException::withMessages(['portal_access' => 'Portal access is available only to customer organizations.']);
        }

        $personId = filled($data['person_id'] ?? null) ? (int) $data['person_id'] : null;

        if ($personId && ! $organization->persons()->whereKey($personId)->exists()) {
            throw ValidationException::withMessages(['portal_person_id' => 'The selected contact does not belong to this customer.']);
        }

        $email = CustomerPortalUser::normalizeEmail($data['email']);

        if (CustomerPortalUser::where('email', $email)->exists()) {
            throw ValidationException::withMessages(['portal_email' => 'This portal login email is already in use.']);
        }

        return DB::transaction(function () use ($organization, $data, $email, $personId, $send) {
            $manualPassword = ($data['credential_method'] ?? 'invitation') === 'temporary_password';
            $user = CustomerPortalUser::create([
                'organization_id'      => $organization->id,
                'person_id'            => $personId,
                'name'                 => $data['name'],
                'email'                => $email,
                'password'             => Hash::make($manualPassword ? $data['password'] : Str::random(64)),
                'status'               => 'active',
                'role'                 => $data['role'] ?? 'organization_admin',
                'permissions'          => $data['permissions'] ?? [],
                'invited_at'           => now(),
                'must_change_password' => $manualPassword,
            ]);

            [$invitation, $token, $url] = $this->freshInvitation($user);

            if ($send) {
                $this->deliverAfterCommit($user, $url, $invitation);
            }

            return compact('user', 'token', 'url', 'invitation');
        });
    }

    public function freshInvitation(CustomerPortalUser $user, bool $send = false): array
    {
        $user->invitations()->whereNull('used_at')->update(['used_at' => now()]);
        $token = Str::random(64);
        $invitation = $user->invitations()->create([
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addHours(config('customer_portal.invitation_expiry_hours', 72)),
        ]);
        $url = route('customer_portal.invitation.show', ['token' => $token]);

        if ($send) {
            $this->deliverAfterCommit($user, $url, $invitation);
        }

        return [$invitation, $token, $url];
    }

    public function resolve(string $token): CustomerPortalInvitation
    {
        $invitation = CustomerPortalInvitation::with('user.organization')
            ->where('token_hash', hash('sha256', $token))->firstOrFail();

        abort_unless($invitation->isUsable() && $invitation->user->isActive(), 410, 'This invitation is invalid or has expired.');

        return $invitation;
    }

    public function accept(string $token, string $password): CustomerPortalUser
    {
        return DB::transaction(function () use ($token, $password) {
            $invitation = $this->resolve($token);
            $invitation->user->forceFill([
                'password'             => Hash::make($password),
                'email_verified_at'    => $invitation->user->email_verified_at ?: now(),
                'must_change_password' => false,
            ])->save();
            $invitation->forceFill(['used_at' => now()])->save();
            $invitation->user->invitations()->whereNull('used_at')->update(['used_at' => now()]);

            return $invitation->user;
        });
    }

    protected function deliverAfterCommit(CustomerPortalUser $user, string $url, CustomerPortalInvitation $invitation): void
    {
        DB::afterCommit(function () use ($user, $url, $invitation) {
            try {
                $user->notify(new CustomerPortalInvitationNotification($url, $invitation->expires_at, $user->must_change_password));
            } catch (\Throwable $exception) {
                Log::error('Customer portal invitation delivery failed', [
                    'portal_user_id' => $user->id,
                    'exception'      => $exception::class,
                ]);
            }
        });
    }
}
