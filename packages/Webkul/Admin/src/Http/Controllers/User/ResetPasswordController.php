<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Models\PasswordResetOtp;
use Webkul\Admin\Services\Auth\PasswordResetOtpService;

class ResetPasswordController extends Controller
{
    public function __construct(protected PasswordResetOtpService $otpService) {}

    public function create(Request $request): View|RedirectResponse
    {
        if (! $this->verifiedChallenge($request)) {
            return redirect()->route('admin.forgot_password.create')
                ->withErrors(['email' => 'Verify your password reset code first.']);
        }

        return view('admin::sessions.reset-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $challenge = $this->verifiedChallenge($request);

        if (! $challenge) {
            return redirect()->route('admin.forgot_password.create')
                ->withErrors(['email' => 'Your verified password reset session has expired.']);
        }

        $this->otpService->resetPassword($challenge, $data['password']);
        $request->session()->forget('password_reset');

        return redirect()->route('admin.session.create')
            ->with('success', 'Your password has been updated. You can now sign in.');
    }

    protected function verifiedChallenge(Request $request): ?PasswordResetOtp
    {
        $id = $request->session()->get('password_reset.verified_id');
        $challenge = $id ? PasswordResetOtp::find($id) : null;

        return $challenge?->isVerified() ? $challenge : null;
    }
}
