<?php

namespace Webkul\Admin\Http\Controllers\CustomerPortal;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Models\CustomerPortalUser;
use Webkul\Admin\Services\CustomerPortal\InvitationService;

class AuthController extends Controller
{
    public function loginForm(): View
    {
        return view('admin::customer-portal.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        $credentials['email'] = CustomerPortalUser::normalizeEmail($credentials['email']);

        if (! Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'The supplied credentials are not valid.']);
        }

        $user = Auth::guard('customer')->user();

        if (! $user->isActive() || strtolower((string) $user->organization?->type) !== 'customer') {
            Auth::guard('customer')->logout();
            throw ValidationException::withMessages(['email' => 'This portal account is not available.']);
        }

        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route($user->must_change_password ? 'customer_portal.security' : 'customer_portal.dashboard'));
    }

    public function forgotForm(): View
    {
        return view('admin::customer-portal.auth.forgot-password');
    }

    public function forgot(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);
        $email = CustomerPortalUser::normalizeEmail($request->string('email'));
        $user = CustomerPortalUser::where('email', $email)->where('status', 'active')->first();

        if ($user) {
            Password::broker('customer_portal_users')->sendResetLink(['email' => $email]);
        }

        return back()->with('success', 'If an active account matches that email, a reset link has been sent.');
    }

    public function resetForm(Request $request, string $token): View
    {
        return view('admin::customer-portal.auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $data['email'] = CustomerPortalUser::normalizeEmail($data['email']);

        $status = Password::broker('customer_portal_users')->reset($data, function (CustomerPortalUser $user, string $password) {
            $user->forceFill([
                'password'             => Hash::make($password),
                'remember_token'       => Str::random(60),
                'email_verified_at'    => $user->email_verified_at ?: now(),
                'must_change_password' => false,
            ])->save();
            event(new PasswordReset($user));
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.session.create')->with('success', __($status));
    }

    public function invitationForm(InvitationService $invitations, string $token): View
    {
        $invitation = $invitations->resolve($token);

        return view('admin::customer-portal.auth.invitation', compact('invitation', 'token'));
    }

    public function acceptInvitation(Request $request, InvitationService $invitations, string $token): RedirectResponse
    {
        $data = $request->validate(['password' => ['required', 'string', 'min:8', 'confirmed']]);
        $invitations->accept($token, $data['password']);

        return redirect()->route('admin.session.create')->with('success', 'Your password is set. You can now sign in.');
    }
}
