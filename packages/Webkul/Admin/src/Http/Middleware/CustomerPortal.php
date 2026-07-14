<?php

namespace Webkul\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Webkul\Contact\Models\Organization;

class CustomerPortal
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = auth()->guard('user')->user();

        if (! $user) {
            return redirect()->route('admin.session.create');
        }

        if (! (bool) $user->status) {
            auth()->guard('user')->logout();

            session()->flash('error', __('admin::app.errors.401'));

            return redirect()->route('admin.session.create');
        }

        if (! $this->isCustomerPortalUser($user)) {
            abort(403, 'This portal is only available to customer users.');
        }

        $organization = Organization::query()
            ->where('user_id', $user->id)
            ->whereIn('type', ['customer', 'Customer'])
            ->first();

        view()->share('portalOrganization', $organization);

        return $next($request);
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
