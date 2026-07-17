<?php

namespace Webkul\Admin\Services\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Webkul\Admin\Models\CustomerPortalUser;
use Webkul\Admin\Models\PasswordResetOtp;
use Webkul\Admin\Notifications\PasswordResetOtpNotification;
use Webkul\User\Models\User;

class PasswordResetOtpService
{
    public const EXPIRY_MINUTES = 10;

    public const MAX_ATTEMPTS = 5;

    public function createChallenge(string $email): PasswordResetOtp
    {
        $email = Str::lower(trim($email));
        [$accountType, $account] = $this->resolveAccount($email);
        $otp = (string) random_int(100000, 999999);

        $challenge = DB::transaction(function () use ($email, $accountType, $otp) {
            PasswordResetOtp::where('email', $email)->delete();

            return PasswordResetOtp::create([
                'email'        => $email,
                'account_type' => $accountType,
                'otp_hash'     => Hash::make($otp),
                'attempts'     => 0,
                'expires_at'   => now()->addMinutes(self::EXPIRY_MINUTES),
            ]);
        });

        if ($account) {
            $account->notify(new PasswordResetOtpNotification($otp, $challenge->expires_at));
        }

        return $challenge;
    }

    public function resend(PasswordResetOtp $challenge): PasswordResetOtp
    {
        return $this->createChallenge($challenge->email);
    }

    public function verify(PasswordResetOtp $challenge, string $otp): PasswordResetOtp
    {
        $result = DB::transaction(function () use ($challenge, $otp) {
            $challenge = PasswordResetOtp::query()->lockForUpdate()->findOrFail($challenge->id);

            if ($challenge->isExpired()) {
                return 'expired';
            }

            if ($challenge->attempts >= self::MAX_ATTEMPTS) {
                return 'locked';
            }

            if (! Hash::check($otp, $challenge->otp_hash)) {
                $challenge->increment('attempts');

                return 'incorrect';
            }

            $challenge->forceFill(['verified_at' => now()])->save();

            return $challenge->fresh();
        });

        if ($result === 'expired') {
            throw ValidationException::withMessages(['otp' => 'This verification code has expired. Request a new code.']);
        }

        if ($result === 'locked') {
            throw ValidationException::withMessages(['otp' => 'Too many incorrect attempts. Request a new code.']);
        }

        if ($result === 'incorrect') {
            throw ValidationException::withMessages(['otp' => 'The verification code is incorrect.']);
        }

        return $result;
    }

    public function resetPassword(PasswordResetOtp $challenge, string $password): string
    {
        return DB::transaction(function () use ($challenge, $password) {
            $challenge = PasswordResetOtp::query()->lockForUpdate()->findOrFail($challenge->id);

            if (! $challenge->isVerified()) {
                throw ValidationException::withMessages(['password' => 'Verify the emailed code before setting a new password.']);
            }

            [, $account] = $this->resolveAccount($challenge->email, $challenge->account_type);

            if (! $account) {
                throw ValidationException::withMessages(['password' => 'This password reset request is no longer valid.']);
            }

            $account->forceFill([
                'password'       => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

            if ($challenge->account_type === 'customer') {
                $account->forceFill([
                    'email_verified_at'    => $account->email_verified_at ?: now(),
                    'must_change_password' => false,
                ])->save();
            }

            DB::table($challenge->account_type === 'customer' ? 'customer_portal_password_resets' : 'user_password_resets')
                ->where('email', $challenge->email)
                ->delete();

            event(new PasswordReset($account));

            $accountType = $challenge->account_type;
            $challenge->delete();

            return $accountType;
        });
    }

    protected function resolveAccount(string $email, ?string $onlyType = null): array
    {
        if (! $onlyType || $onlyType === 'user') {
            if ($user = User::where('email', $email)->first()) {
                return ['user', $user];
            }
        }

        if (! $onlyType || $onlyType === 'customer') {
            if ($customer = CustomerPortalUser::where('email', $email)->where('status', 'active')->first()) {
                return ['customer', $customer];
            }
        }

        return [$onlyType ?: 'unknown', null];
    }
}
