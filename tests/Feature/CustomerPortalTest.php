<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Webkul\Admin\Models\CustomerPortalUser;
use Webkul\Admin\Services\CustomerPortal\InvitationService;

uses(DatabaseTransactions::class);

function portalOrganization(string $type = 'customer'): int
{
    return DB::table('organizations')->insertGetId([
        'name'       => 'Portal Test '.str()->uuid(),
        'type'       => $type,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function portalAccount(int $organizationId, array $overrides = []): CustomerPortalUser
{
    return CustomerPortalUser::create(array_merge([
        'organization_id' => $organizationId,
        'name'            => 'Portal Tester',
        'email'           => str()->uuid().'@example.test',
        'password'        => Hash::make('PortalPass123!'),
        'status'          => 'active',
        'role'            => 'organization_admin',
    ], $overrides));
}

function portalTestUrl(string $route, mixed $parameters = []): string
{
    return 'http://localhost'.route($route, $parameters, false);
}

it('uses a dedicated customer login and regenerates into the customer guard', function () {
    $account = portalAccount(portalOrganization());

    $response = $this->post(portalTestUrl('admin.session.store'), [
        'email'    => strtoupper($account->email),
        'password' => 'PortalPass123!',
    ]);
    if ($response->exception) {
        throw $response->exception;
    }
    $response->assertRedirect(route('customer_portal.dashboard'));

    expect(auth('customer')->id())->toBe($account->id)
        ->and(auth('user')->check())->toBeFalse();
});

it('denies suspended portal accounts', function () {
    $account = portalAccount(portalOrganization(), ['status' => 'suspended']);

    $this->post(portalTestUrl('admin.session.store'), [
        'email'    => $account->email,
        'password' => 'PortalPass123!',
    ])->assertSessionHas('error');

    expect(auth('customer')->check())->toBeFalse();
});

it('rejects portal access for vendor organizations and supports multiple customer users', function () {
    $service = app(InvitationService::class);
    $vendor = \Webkul\Contact\Models\Organization::find(portalOrganization('vendor'));

    expect(fn () => $service->createAccount($vendor, [
        'name' => 'Vendor Login', 'email' => 'vendor-'.str()->uuid().'@example.test',
    ], false))->toThrow(ValidationException::class);

    $customerId = portalOrganization();
    portalAccount($customerId);
    portalAccount($customerId);
    expect(CustomerPortalUser::where('organization_id', $customerId)->count())->toBe(2);
});

it('stores an unselected optional portal contact as null', function () {
    $organization = \Webkul\Contact\Models\Organization::find(portalOrganization());

    $result = app(InvitationService::class)->createAccount($organization, [
        'name'              => 'No Linked Contact',
        'email'             => 'no-contact-'.str()->uuid().'@example.test',
        'person_id'         => '',
        'role'              => 'organization_admin',
        'credential_method' => 'temporary_password',
        'password'          => 'TemporaryPass123!',
    ], false);

    expect($result['user']->person_id)->toBeNull()
        ->and($result['user']->fresh()->person_id)->toBeNull();
});

it('makes invitation tokens expiring and one time', function () {
    $account = portalAccount(portalOrganization());
    $service = app(InvitationService::class);
    [, $token] = $service->freshInvitation($account);

    expect($service->resolve($token)->user->is($account))->toBeTrue();
    $service->accept($token, 'ChangedPass123!');
    expect(fn () => $service->resolve($token))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);

    [$expired, $expiredToken] = $service->freshInvitation($account);
    $expired->update(['expires_at' => now()->subMinute()]);
    expect(fn () => $service->resolve($expiredToken))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('enforces granular member permissions', function () {
    $account = portalAccount(portalOrganization(), ['role' => 'member', 'permissions' => ['view_products']]);

    $this->actingAs($account, 'customer')
        ->get(portalTestUrl('customer_portal.contacts'))
        ->assertForbidden();
    $this->actingAs($account, 'customer')
        ->get(portalTestUrl('customer_portal.products.index'))
        ->assertOk();
});

it('shows only contacts belonging to the signed-in customer organization', function () {
    $organizationId = portalOrganization();
    $otherOrganizationId = portalOrganization();
    $account = portalAccount($organizationId);
    $contactId = DB::table('persons')->insertGetId([
        'name' => 'Customer Contact', 'organization_id' => $organizationId, 'created_at' => now(), 'updated_at' => now(),
    ]);
    $otherContactId = DB::table('persons')->insertGetId([
        'name' => 'Other Customer Contact', 'organization_id' => $otherOrganizationId, 'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($account, 'customer')
        ->get(portalTestUrl('customer_portal.contacts.view', $contactId))
        ->assertOk()
        ->assertSee('Customer Contact');

    $this->actingAs($account, 'customer')
        ->get(portalTestUrl('customer_portal.contacts.view', $otherContactId))
        ->assertNotFound();
});

it('hides unpublished and cross organization quotes', function () {
    $organizationId = portalOrganization();
    $otherOrganizationId = portalOrganization();
    $account = portalAccount($organizationId);
    $personId = DB::table('persons')->insertGetId([
        'name' => 'Portal Person', 'organization_id' => $organizationId, 'created_at' => now(), 'updated_at' => now(),
    ]);
    $otherPersonId = DB::table('persons')->insertGetId([
        'name' => 'Other Person', 'organization_id' => $otherOrganizationId, 'created_at' => now(), 'updated_at' => now(),
    ]);
    $visibleId = DB::table('quotes')->insertGetId([
        'quote_number' => 'PT-'.str()->random(10), 'subject' => 'Visible', 'organization_id' => $organizationId,
        'person_id'    => $personId, 'user_id' => 1, 'status' => 'open', 'customer_visible_at' => now(), 'created_at' => now(), 'updated_at' => now(),
    ]);
    $hiddenId = DB::table('quotes')->insertGetId([
        'quote_number' => 'PT-'.str()->random(10), 'subject' => 'Hidden', 'organization_id' => $organizationId,
        'person_id'    => $personId, 'user_id' => 1, 'status' => 'open', 'created_at' => now(), 'updated_at' => now(),
    ]);
    $otherId = DB::table('quotes')->insertGetId([
        'quote_number' => 'PT-'.str()->random(10), 'subject' => 'Other', 'organization_id' => $otherOrganizationId,
        'person_id'    => $otherPersonId, 'user_id' => 1, 'status' => 'open', 'customer_visible_at' => now(), 'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($account, 'customer')->get(portalTestUrl('customer_portal.quotes.view', $visibleId))->assertOk();
    $this->actingAs($account, 'customer')->get(portalTestUrl('customer_portal.quotes.view', $hiddenId))->assertNotFound();
    $this->actingAs($account, 'customer')->get(portalTestUrl('customer_portal.quotes.view', $otherId))->assertNotFound();
});
