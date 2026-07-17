<?php

namespace Webkul\Admin\Http\Controllers\User;

use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Models\PasswordResetOtp;
use Webkul\Admin\Services\Auth\PasswordResetOtpService;

class ForgotPasswordController extends Controller
{
    public function __construct(protected PasswordResetOtpService $otpService) {}

    public function create(): View|RedirectResponse
    {
        if (auth()->guard('user')->check()) {
            return redirect()->route('admin.dashboard.index');
        }

        return view('admin::sessions.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);

        try {
            $challenge = $this->otpService->createChallenge($data['email']);
        } catch (\Throwable $exception) {
            Log::error('Password reset OTP delivery failed', ['email' => $data['email'], 'exception' => $exception::class]);

            return back()->withInput()->withErrors(['email' => 'The verification email could not be sent. Please try again shortly.']);
        }

        $request->session()->put('password_reset.challenge_id', $challenge->id);
        $request->session()->forget('password_reset.verified_id');

        return redirect()->to($this->signedChallengeUrl('admin.forgot_password.verify', $challenge))
            ->with('success', 'If an account matches that email, a verification code has been sent.');
    }

    public function verifyForm(Request $request): View|RedirectResponse
    {
        $challenge = $this->challenge($request);

        if (! $challenge) {
            return redirect()->route('admin.forgot_password.create');
        }

        return view('admin::sessions.verify-password-otp', [
            'maskedEmail'  => $this->maskEmail($challenge->email),
            'verifyAction' => $this->signedChallengeUrl('admin.forgot_password.verify.store', $challenge),
            'resendAction' => $this->signedChallengeUrl('admin.forgot_password.resend', $challenge),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $data = $request->validate(['otp' => ['required', 'digits:6']]);
        $challenge = $this->challenge($request);

        if (! $challenge) {
            return redirect()->route('admin.forgot_password.create')
                ->withErrors(['email' => 'Start a new password reset request.']);
        }

        $challenge = $this->otpService->verify($challenge, $data['otp']);
        $request->session()->put('password_reset.verified_id', $challenge->id);

        return redirect()->to($this->signedChallengeUrl('admin.reset_password.create', $challenge));
    }

    public function resend(Request $request): RedirectResponse
    {
        $challenge = $this->challenge($request);

        if (! $challenge) {
            return redirect()->route('admin.forgot_password.create');
        }

        try {
            $challenge = $this->otpService->resend($challenge);
        } catch (\Throwable $exception) {
            Log::error('Password reset OTP resend failed', ['challenge_id' => $challenge->id, 'exception' => $exception::class]);

            return back()->withErrors(['otp' => 'A new code could not be sent. Please try again shortly.']);
        }

        $request->session()->put('password_reset.challenge_id', $challenge->id);
        $request->session()->forget('password_reset.verified_id');

        return redirect()->to($this->signedChallengeUrl('admin.forgot_password.verify', $challenge))
            ->with('success', 'A new verification code has been sent.');
    }

    protected function challenge(Request $request): ?PasswordResetOtp
    {
        $signedChallengeId = $request->hasValidSignature()
            ? $request->integer('challenge')
            : null;
        $id = $signedChallengeId ?: $request->session()->get('password_reset.challenge_id');

        return $id ? PasswordResetOtp::find($id) : null;
    }

    protected function signedChallengeUrl(string $route, PasswordResetOtp $challenge): string
    {
        return URL::temporarySignedRoute(
            $route,
            $challenge->expires_at_epoch
                ? CarbonImmutable::createFromTimestampUTC($challenge->expires_at_epoch)
                : $challenge->expires_at,
            ['challenge' => $challenge->id],
        );
    }

    protected function maskEmail(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visible = mb_substr($name, 0, min(2, mb_strlen($name)));

        return $visible.str_repeat('*', max(mb_strlen($name) - mb_strlen($visible), 3)).'@'.$domain;
    }
}
