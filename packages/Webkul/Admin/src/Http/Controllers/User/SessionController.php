<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Models\CustomerPortalUser;
use Webkul\Core\Menu\MenuItem;

class SessionController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse|View
    {
        if (auth()->guard('user')->check()) {
            return redirect()->route('admin.dashboard.index');
        }

        if (auth()->guard('customer')->check()) {
            return redirect()->route('customer_portal.dashboard');
        }

        $previousUrl = url()->previous();

        $intendedUrl = str_contains($previousUrl, 'admin')
            ? $previousUrl
            : route('admin.dashboard.index');

        session()->put('url.intended', $intendedUrl);

        return view('admin::sessions.login');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse
    {
        $this->validate(request(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $remember = request()->boolean('remember');
        $credentials = request(['email', 'password']);

        if (! auth()->guard('user')->attempt($credentials, $remember)) {
            $customerCredentials = [
                'email'    => CustomerPortalUser::normalizeEmail($credentials['email']),
                'password' => $credentials['password'],
            ];

            if (auth()->guard('customer')->attempt($customerCredentials, $remember)) {
                $customer = auth()->guard('customer')->user();

                if (! $customer->isActive() || strtolower((string) $customer->organization?->type) !== 'customer') {
                    auth()->guard('customer')->logout();
                    session()->flash('error', trans('admin::app.users.login-error'));

                    return redirect()->back();
                }

                request()->session()->regenerate();
                session()->forget('url.intended');
                $customer->forceFill(['last_login_at' => now()])->save();

                return redirect()->route($customer->must_change_password
                    ? 'customer_portal.security'
                    : 'customer_portal.dashboard');
            }

            session()->flash('error', trans('admin::app.users.login-error'));

            return redirect()->back();
        }

        if (auth()->guard('user')->user()->status == 0) {
            session()->flash('warning', trans('admin::app.users.activate-warning'));

            auth()->guard('user')->logout();

            return redirect()->route('admin.session.create');
        }

        request()->session()->regenerate();

        $menus = menu()->getItems('admin');

        $availableNextMenu = $menus?->first();

        if (! bouncer()->hasPermission('dashboard')) {
            if (is_null($availableNextMenu)) {
                session()->flash('error', trans('admin::app.users.not-permission'));

                auth()->guard('user')->logout();

                return redirect()->route('admin.session.create');
            }

            return redirect()->to($availableNextMenu->getUrl());
        }

        $hasAccessToIntendedUrl = $this->canAccessIntendedUrl($menus, redirect()->getIntendedUrl());

        if ($hasAccessToIntendedUrl) {
            return redirect()->intended(route('admin.dashboard.index'));
        }

        return redirect()->to($availableNextMenu->getUrl());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(): RedirectResponse
    {
        auth()->guard('user')->logout();

        return redirect()->route('admin.session.create');
    }

    /**
     * Find menu item by URL.
     */
    protected function canAccessIntendedUrl(Collection $menus, ?string $url): ?MenuItem
    {
        if (is_null($url)) {
            return null;
        }

        foreach ($menus as $menu) {
            if ($menu->getUrl() === $url) {
                return $menu;
            }

            if ($menu->haveChildren()) {
                $found = $this->canAccessIntendedUrl($menu->getChildren(), $url);

                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }
}
