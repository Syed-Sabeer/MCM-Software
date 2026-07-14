@foreach($portalUsers as $portalUser)
    <form id="portal-user-status-{{ $portalUser->id }}" method="POST" action="{{ route('admin.customers.organizations.portal_users.status', [$organization, $portalUser]) }}" class="hidden">@csrf @method('PUT')<input type="hidden" name="status" value="{{ $portalUser->status === 'active' ? 'suspended' : 'active' }}"></form>
    <form id="portal-user-resend-{{ $portalUser->id }}" method="POST" action="{{ route('admin.customers.organizations.portal_users.resend', [$organization, $portalUser]) }}" class="hidden">@csrf</form>
    <form id="portal-user-revoke-{{ $portalUser->id }}" method="POST" action="{{ route('admin.customers.organizations.portal_users.destroy', [$organization, $portalUser]) }}" class="hidden">@csrf @method('DELETE')</form>
@endforeach
