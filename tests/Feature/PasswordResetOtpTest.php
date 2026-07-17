<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Webkul\Admin\Models\CustomerPortalUser;
use Webkul\Admin\Models\PasswordResetOtp;
use Webkul\Admin\Notifications\PasswordResetOtpNotification;
use Webkul\Admin\Services\Auth\PasswordResetOtpService;
use Webkul\User\Models\User;

uses(DatabaseTransactions::class);

function passwordOtpUrl(string $route): string
{
    return 'http://localhost'.route($route, [], false);
}

it('resets an employee password only after the emailed otp is verified', function () {
    Notification::fake();
    $user = User::query()->firstOrFail();

    $response = $this->post(passwordOtpUrl('admin.forgot_password.store'), ['email' => $user->email]);
    $response->assertSessionHas('password_reset.challenge_id');
    $verifyUrl = $response->headers->get('Location');

    expect($verifyUrl)->toContain(route('admin.forgot_password.verify'))
        ->and($verifyUrl)->toContain('signature=');

    $otp = null;
    Notification::assertSentTo($user, PasswordResetOtpNotification::class, function ($notification) use (&$otp) {
        $otp = $notification->otp;

        return true;
    });

    $challenge = PasswordResetOtp::where('email', $user->email)->firstOrFail();
    expect($challenge->otp)->toBe($otp)
        ->and(Hash::check($otp, $challenge->otp_hash))->toBeTrue();

    $this->get(passwordOtpUrl('admin.reset_password.create'))
        ->assertRedirect(route('admin.forgot_password.create'));

    $this->withSession(['password_reset' => []])
        ->get($verifyUrl)
        ->assertOk()
        ->assertSee('Verify your email');

    $verifyResponse = $this->post($verifyUrl, ['otp' => $otp]);
    $resetUrl = $verifyResponse->headers->get('Location');

    expect($resetUrl)->toContain(route('admin.reset_password.create'))
        ->and($resetUrl)->toContain('signature=');

    $this->withSession(['password_reset' => []])
        ->get($resetUrl)
        ->assertOk()
        ->assertSee('Set a new password');

    $this->post($resetUrl, [
        'password' => 'NewSecurePass123!', 'password_confirmation' => 'NewSecurePass123!',
    ])->assertRedirect(route('admin.session.create'));

    expect(Hash::check('NewSecurePass123!', $user->fresh()->password))->toBeTrue()
        ->and(PasswordResetOtp::where('email', $user->email)->exists())->toBeFalse();
});

it('persists incorrect otp attempts', function () {
    Notification::fake();
    $user = User::query()->firstOrFail();
    $service = app(PasswordResetOtpService::class);
    $challenge = $service->createChallenge($user->email);

    expect(fn () => $service->verify($challenge, '000000'))->toThrow(ValidationException::class)
        ->and($challenge->fresh()->attempts)->toBe(1);
});

it('uses the shared otp flow for customer portal accounts', function () {
    Notification::fake();
    $organizationId = DB::table('organizations')->insertGetId([
        'name' => 'OTP Customer '.str()->uuid(), 'type' => 'customer', 'created_at' => now(), 'updated_at' => now(),
    ]);
    $customer = CustomerPortalUser::create([
        'organization_id'      => $organizationId,
        'name'                 => 'Portal OTP User',
        'email'                => str()->uuid().'@example.test',
        'password'             => Hash::make('OldPassword123!'),
        'status'               => 'active',
        'role'                 => 'organization_admin',
        'must_change_password' => true,
    ]);

    $service = app(PasswordResetOtpService::class);
    $challenge = $service->createChallenge($customer->email);
    $otp = null;
    Notification::assertSentTo($customer, PasswordResetOtpNotification::class, function ($notification) use (&$otp) {
        $otp = $notification->otp;

        return true;
    });

    $challenge = $service->verify($challenge, $otp);
    expect($service->resetPassword($challenge, 'CustomerNewPass123!'))->toBe('customer');

    $customer = $customer->fresh();
    expect(Hash::check('CustomerNewPass123!', $customer->password))->toBeTrue()
        ->and($customer->must_change_password)->toBeFalse()
        ->and($customer->email_verified_at)->not->toBeNull();
});

it('renders branded otp and portal invitation emails', function () {
    $user = (object) ['name' => 'Email Preview', 'email' => 'preview@example.test'];
    $otpHtml = view('admin::emails.auth.password-reset-otp', [
        'name' => $user->name, 'otp' => '123456', 'expiresAt' => now()->addMinutes(10),
    ])->render();
    $invitationHtml = view('admin::emails.customer-portal.invitation', [
        'user'                    => $user,
        'url'                     => 'https://example.test/setup',
        'expiresAt'               => now()->addHours(2),
        'temporaryPasswordWasSet' => false,
    ])->render();

    expect($otpHtml)->toContain('123456')->toContain('Verify your password reset')
        ->and($invitationHtml)->toContain('Set up your portal access')->toContain('preview@example.test');
});
