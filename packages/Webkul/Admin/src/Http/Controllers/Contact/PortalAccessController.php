<?php

namespace Webkul\Admin\Http\Controllers\Contact;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Models\CustomerPortalUser;
use Webkul\Admin\Services\CustomerPortal\InvitationService;
use Webkul\Contact\Models\Organization;

class PortalAccessController extends Controller
{
    public function store(Request $request, Organization $organization, InvitationService $invitations): RedirectResponse
    {
        $this->authorizeManagement();
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255', 'unique:customer_portal_users,email'],
            'person_id'         => ['nullable', 'integer'],
            'role'              => ['required', Rule::in(['organization_admin', 'member'])],
            'credential_method' => ['required', Rule::in(['invitation', 'temporary_password'])],
            'password'          => ['nullable', 'required_if:credential_method,temporary_password', 'min:8', 'confirmed'],
            'send_email'        => ['nullable', 'boolean'],
        ]);
        $result = $invitations->createAccount($organization, $data, $request->boolean('send_email'));
        Log::info('Customer portal account created', ['organization_id' => $organization->id, 'portal_user_id' => $result['user']->id, 'actor_id' => auth()->id()]);

        return back()->with('success', 'Portal account created.')->with('portal_invitation_url', $result['url']);
    }

    public function update(Request $request, Organization $organization, CustomerPortalUser $portalUser): RedirectResponse
    {
        $this->authorizeManagement();
        $this->assertOwned($organization, $portalUser);
        $data = $request->validate([
            'role'          => ['required', Rule::in(['organization_admin', 'member'])],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => [Rule::in(['view_documents', 'view_products', 'view_contacts'])],
        ]);
        $portalUser->update(['role' => $data['role'], 'permissions' => $data['permissions'] ?? []]);
        Log::info('Customer portal account permissions updated', ['organization_id' => $organization->id, 'portal_user_id' => $portalUser->id, 'actor_id' => auth()->id()]);

        return back()->with('success', 'Portal access updated.');
    }

    public function status(Request $request, Organization $organization, CustomerPortalUser $portalUser): RedirectResponse
    {
        $this->authorizeManagement();
        $this->assertOwned($organization, $portalUser);
        $data = $request->validate(['status' => ['required', Rule::in(['active', 'suspended'])]]);
        $portalUser->update($data);
        Log::info('Customer portal account status changed', ['organization_id' => $organization->id, 'portal_user_id' => $portalUser->id, 'status' => $data['status'], 'actor_id' => auth()->id()]);

        return back()->with('success', 'Portal account status updated.');
    }

    public function resend(Organization $organization, CustomerPortalUser $portalUser, InvitationService $invitations): RedirectResponse
    {
        $this->authorizeManagement();
        $this->assertOwned($organization, $portalUser);
        [, , $url] = $invitations->freshInvitation($portalUser, true);
        Log::info('Customer portal invitation resent', ['organization_id' => $organization->id, 'portal_user_id' => $portalUser->id, 'actor_id' => auth()->id()]);

        return back()->with('success', 'A fresh invitation was sent.')->with('portal_invitation_url', $url);
    }

    public function destroy(Organization $organization, CustomerPortalUser $portalUser): RedirectResponse
    {
        $this->authorizeManagement();
        $this->assertOwned($organization, $portalUser);
        $id = $portalUser->id;
        $portalUser->delete();
        Log::info('Customer portal account revoked', ['organization_id' => $organization->id, 'portal_user_id' => $id, 'actor_id' => auth()->id()]);

        return back()->with('success', 'Portal access revoked.');
    }

    protected function authorizeManagement(): void
    {
        abort_unless(bouncer()->hasPermission('contacts.organizations.edit'), 403);
    }

    protected function assertOwned(Organization $organization, CustomerPortalUser $portalUser): void
    {
        abort_unless($portalUser->organization_id === $organization->id && strtolower((string) $organization->type) === 'customer', 404);
    }
}
