<?php

it('can see the admin login page', function () {
    test()->get(route('admin.session.create'))
        ->assertOK()
        ->assertSee('Staff and customer account access');

    test()->get(route('customer_portal.login'))
        ->assertRedirect(route('admin.session.create'));
});

it('can see the dashboard page after login', function () {
    $admin = getDefaultAdmin();

    test()->actingAs($admin)
        ->get(route('admin.dashboard.index'))
        ->assertOK();

    expect(auth()->guard('user')->user()->name)->toBe($admin->name);
});

it('can logout from the admin panel', function () {
    $admin = getDefaultAdmin();

    test()->actingAs($admin)
        ->delete(route('admin.session.destroy'), [
            '_token' => csrf_token(),
        ])
        ->assertStatus(302);

    expect(auth()->guard('user')->user())->toBeNull();
});
