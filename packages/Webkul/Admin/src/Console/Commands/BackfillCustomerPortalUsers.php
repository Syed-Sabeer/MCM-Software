<?php

namespace Webkul\Admin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Webkul\Admin\Models\CustomerPortalUser;
use Webkul\Admin\Services\CustomerPortal\InvitationService;
use Webkul\Contact\Models\Organization;

class BackfillCustomerPortalUsers extends Command
{
    protected $signature = 'customer-portal:backfill-legacy {--apply : Persist the proposed accounts} {--keep-legacy-access : Do not disable unambiguous portal-only internal accounts}';

    protected $description = 'Dry-run or backfill legacy organization-linked portal users into the dedicated customer guard';

    public function handle(InvitationService $invitations): int
    {
        $organizations = Organization::query()->with('user.role')
            ->whereIn('type', ['customer', 'Customer'])->whereNotNull('user_id')->get();
        $created = $skipped = 0;

        foreach ($organizations as $organization) {
            $legacy = $organization->user;
            if (! $legacy || ! $this->qualifies($legacy)) {
                $skipped++;

                continue;
            }

            if (CustomerPortalUser::where('email', CustomerPortalUser::normalizeEmail($legacy->email))->exists()) {
                $this->line("SKIP organization {$organization->id}: {$legacy->email} already migrated");
                $skipped++;

                continue;
            }

            $this->line(($this->option('apply') ? 'CREATE' : 'WOULD CREATE')." organization {$organization->id}: {$legacy->email}");
            if (! $this->option('apply')) {
                $created++;

                continue;
            }

            DB::transaction(function () use ($organization, $legacy, $invitations) {
                $portalUser = CustomerPortalUser::create([
                    'organization_id'      => $organization->id,
                    'name'                 => $legacy->name,
                    'email'                => $legacy->email,
                    'password'             => $legacy->password,
                    'status'               => 'active',
                    'role'                 => 'organization_admin',
                    'permissions'          => [],
                    'invited_at'           => now(),
                    'must_change_password' => true,
                ]);
                $invitations->freshInvitation($portalUser, false);

                if (! $this->option('keep-legacy-access') && $this->isPortalOnly($legacy)) {
                    $legacy->forceFill(['status' => 0])->save();
                }
            });
            $created++;
        }

        $this->info("Candidates: {$created}; skipped: {$skipped}. ".($this->option('apply') ? 'Changes applied.' : 'Dry run only.'));

        return self::SUCCESS;
    }

    protected function qualifies($user): bool
    {
        $name = strtolower((string) $user->role?->name);
        $permissions = $user->role?->permissions ?? [];

        return str_contains($name, 'customer') || str_contains($name, 'portal') || str_contains($name, 'client')
            || in_array(config('customer_portal.legacy_permission'), $permissions, true);
    }

    protected function isPortalOnly($user): bool
    {
        $role = $user->role;
        $permissions = $role?->permissions ?? [];

        return $role && $role->permission_type !== 'all'
            && $permissions === [config('customer_portal.legacy_permission')];
    }
}
