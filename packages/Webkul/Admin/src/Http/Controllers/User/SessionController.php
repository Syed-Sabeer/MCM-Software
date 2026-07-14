<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Core\Menu\MenuItem;

class SessionController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse|View
    {
        if (auth()->guard('user')->check()) {
            if ($this->isCustomerPortalUser(auth()->guard('user')->user())) {
                return redirect()->route('customer_portal.dashboard');
            }

            return redirect()->route('admin.dashboard.index');
        }

        $previousUrl = url()->previous();

        $intendedUrl = str_contains($previousUrl, 'customer-portal')
            ? $previousUrl
            : (str_contains($previousUrl, 'admin')
            ? $previousUrl
            : route('admin.dashboard.index'));

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

        if (! auth()->guard('user')->attempt(request(['email', 'password']), request('remember'))) {
            session()->flash('error', trans('admin::app.users.login-error'));

            return redirect()->back();
        }

        if (auth()->guard('user')->user()->status == 0) {
            session()->flash('warning', trans('admin::app.users.activate-warning'));

            auth()->guard('user')->logout();

            return redirect()->route('admin.session.create');
        }

        if ($this->isCustomerPortalUser(auth()->guard('user')->user())) {
            $intendedUrl = session('url.intended');

            if ($intendedUrl && str_contains($intendedUrl, 'customer-portal')) {
                return redirect()->intended(route('customer_portal.dashboard'));
            }

            session()->forget('url.intended');

            return redirect()->route('customer_portal.dashboard');
        }

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

    protected function isCustomerPortalUser($user): bool
    {
        $roleName = strtolower((string) ($user->role?->name ?? ''));

        if (str_contains($roleName, 'customer') || str_contains($roleName, 'portal') || str_contains($roleName, 'client')) {
            return true;
        }

        return in_array('customer_portal.access', $user->role?->permissions ?? [], true);
    }
}
