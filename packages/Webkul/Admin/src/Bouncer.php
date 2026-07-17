<?php

namespace Webkul\Admin;

use Webkul\User\Repositories\UserRepository;

class Bouncer
{
    /**
     * Checks if user allowed or not for certain action
     *
     * @param  string  $permission
     * @return void
     */
    public function hasPermission($permission)
    {
        if (! auth()->guard('user')->check()) {
            return false;
        }

        if (is_array($permission)) {
            return collect($permission)->contains(fn ($item) => $this->hasPermission($item));
        }

        $user = auth()->guard('user')->user();

        return collect($this->permissionCandidates((string) $permission))
            ->contains(fn ($candidate) => $user->hasPermission($candidate));
    }

    /**
     * Checks if user allowed or not for certain action
     *
     * @param  string  $permission
     * @return void
     */
    public static function allow($permission)
    {
        if (! app('bouncer')->hasPermission($permission)) {
            abort(403, 'This action is unauthorized');
        }
    }

    /**
     * Keep legacy contact permissions working while customer and vendor access
     * is managed independently for new roles.
     */
    protected function permissionCandidates(string $permission): array
    {
        $candidates = [$permission];

        if (str_starts_with($permission, 'customers.')) {
            $candidates[] = 'contacts.'.substr($permission, strlen('customers.'));
        }

        if (str_starts_with($permission, 'vendors.')) {
            $candidates[] = 'contacts.'.substr($permission, strlen('vendors.'));
        }

        if (str_starts_with($permission, 'contacts.')) {
            $suffix = substr($permission, strlen('contacts.'));
            $routeName = (string) request()->route()?->getName();

            if (str_starts_with($routeName, 'admin.customers.')) {
                array_unshift($candidates, 'customers.'.$suffix);
            } elseif (str_starts_with($routeName, 'admin.vendors.')) {
                array_unshift($candidates, 'vendors.'.$suffix);
            }
        }

        if ($permission === 'configuration') {
            $candidates[] = 'general-settings';
        }

        return array_values(array_unique($candidates));
    }

    /**
     * This function will return user ids of current user's groups
     *
     * @return array|null
     */
    public function getAuthorizedUserIds()
    {
        $user = auth()->guard('user')->user();

        if ($user->view_permission == 'global') {
            return null;
        }

        if ($user->view_permission == 'group') {
            return app(UserRepository::class)->getCurrentUserGroupsUserIds();
        } else {
            return [$user->id];
        }
    }
}
