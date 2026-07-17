<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')
            ->where('permission_type', 'custom')
            ->orderBy('id')
            ->each(function ($role) {
                $permissions = json_decode((string) $role->permissions, true) ?: [];
                $upgraded = $permissions;

                if (in_array('dashboard', $permissions, true)) {
                    $upgraded[] = 'dashboard.business_details';
                    $upgraded[] = 'dashboard.customer_details';
                }

                foreach ($permissions as $permission) {
                    if (str_starts_with($permission, 'contacts.')) {
                        $suffix = substr($permission, strlen('contacts.'));
                        $upgraded[] = 'customers.'.$suffix;
                        $upgraded[] = 'vendors.'.$suffix;
                    }

                    if ($permission === 'contacts') {
                        $upgraded[] = 'customers';
                        $upgraded[] = 'vendors';
                    }

                    if (str_starts_with($permission, 'employees.persons.')) {
                        $upgraded[] = 'settings.user.users.'.substr($permission, strlen('employees.persons.'));
                    } elseif (in_array($permission, ['employees', 'employees.persons'], true)) {
                        $upgraded[] = 'settings.user.users';
                    }
                }

                DB::table('roles')->where('id', $role->id)->update([
                    'permissions' => json_encode(array_values(array_unique($upgraded))),
                    'updated_at'  => now(),
                ]);
            });
    }

    public function down(): void
    {
        // Existing permission keys are retained, so rolling back needs no data rewrite.
    }
};
