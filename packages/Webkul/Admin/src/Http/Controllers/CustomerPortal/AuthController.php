<?php

namespace Webkul\Admin\Http\Controllers\CustomerPortal;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
