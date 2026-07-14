<?php

namespace Webkul\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomerPortal
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = auth()->guard('customer')->user();

        if (! $user) {
            return redirect()->guest(route('admin.session.create'));
        }

        if (! $user->isActive()) {
            auth()->guard('customer')->logout();

            session()->flash('error', __('admin::app.errors.401'));

            $request->session()->invalidate();

            return redirect()->route('admin.session.create');
        }

        $organization = $user->organization;

        abort_unless($organization && strtolower((string) $organization->type) === 'customer', 403);

        view()->share('portalOrganization', $organization);

        if ($user->must_change_password && ! $request->routeIs('customer_portal.security*') && ! $request->routeIs('customer_portal.logout')) {
            return redirect()->route('customer_portal.security');
        }

        return $next($request);
    }
}
