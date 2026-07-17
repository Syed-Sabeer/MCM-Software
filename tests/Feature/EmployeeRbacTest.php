<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

uses(DatabaseTransactions::class);

function rbacEmployee(array $permissions): User
{
    $role = Role::create([
        'name'            => 'RBAC Test '.str()->uuid(),
        'description'     => 'Automated employee access test',
        'permission_type' => 'custom',
        'permissions'     => $permissions,
    ]);

    return User::create([
        'name'            => 'RBAC Employee',
        'email'           => str()->uuid().'@example.test',
        'password'        => Hash::make('EmployeePass123!'),
        'role_id'         => $role->id,
        'status'          => 1,
        'view_permission' => 'individual',
    ]);
}

it('filters dashboard business and customer data independently', function () {
    $employee = rbacEmployee(['dashboard', 'dashboard.customer_details']);

    $this->actingAs($employee, 'user')
        ->get(route('admin.dashboard.index'))
        ->assertOk()
        ->assertSee('canViewBusinessDetails: false', false)
        ->assertSee('canViewCustomerDetails: true', false);

    $statistics = $this->actingAs($employee, 'user')
        ->getJson(route('admin.dashboard.stats', ['type' => 'erp-overview']))
        ->assertOk()
        ->json('statistics');

    expect($statistics)
        ->toHaveKey('top_customers')
        ->not->toHaveKeys(['quote_status', 'cases_by_stage', 'sales_purchasing', 'best_products']);
});

it('enforces customer and vendor permissions independently', function () {
    $employee = rbacEmployee(['customers', 'customers.organizations']);

    $this->actingAs($employee, 'user')
        ->get(route('admin.customers.organizations.index'))
        ->assertOk();

    $this->actingAs($employee, 'user')
        ->get(route('admin.vendors.organizations.index'))
        ->assertForbidden();
});

it('protects CRUD routes independently from module list access', function () {
    $employee = rbacEmployee(['products', 'products.view']);

    $this->actingAs($employee, 'user')
        ->get(route('admin.products.index'))
        ->assertOk();

    $this->actingAs($employee, 'user')
        ->get(route('admin.products.create'))
        ->assertForbidden();
});
