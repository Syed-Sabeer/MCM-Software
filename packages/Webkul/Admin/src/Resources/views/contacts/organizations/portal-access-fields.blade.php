@php
    $portalAccessId = 'portal-access-'.($organization->id ?? 'new');
    $portalAccessEnabled = (bool) old('create_portal_access', false);
    $portalCredentialMethod = old('portal_credential_method', 'invitation');
@endphp

<section class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900" id="{{ $portalAccessId }}">
    <div>
        <h3 class="text-base font-semibold text-gray-800 dark:text-white">Customer Portal</h3>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Create a separate customer login for this organization.</p>
    </div>

    <label class="mt-4 flex cursor-pointer items-center gap-2 text-sm font-medium text-gray-800 dark:text-white">
        <input type="hidden" name="create_portal_access" value="0">
        <input
            type="checkbox"
            id="{{ $portalAccessId }}-enabled"
            name="create_portal_access"
            value="1"
            @checked($portalAccessEnabled)
            onchange="window.syncCustomerPortalAccess(@js($portalAccessId))"
        >
        Create portal access
    </label>

    <div id="{{ $portalAccessId }}-fields" class="mt-4 grid gap-4 border-t border-gray-200 pt-4 dark:border-gray-700 md:grid-cols-2" style="display: {{ $portalAccessEnabled ? 'grid' : 'none' }};">
        <label class="grid gap-1 text-sm dark:text-white">
            Portal user/contact name
            <input name="portal_name" value="{{ old('portal_name') }}" class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-900">
        </label>

        <label class="grid gap-1 text-sm dark:text-white">
            Portal login email
            <input type="email" name="portal_email" value="{{ old('portal_email') }}" class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-900">
        </label>

        @isset($organization)
            <label class="grid gap-1 text-sm dark:text-white">
                Link contact
                <select name="portal_person_id" class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-900">
                    <option value="">No linked contact</option>
                    @foreach($organization->persons()->orderBy('name')->get(['id', 'name', 'email']) as $contact)
                        <option value="{{ $contact->id }}" @selected(old('portal_person_id') == $contact->id)>
                            {{ $contact->name }}{{ $contact->email ? ' ('.$contact->email.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </label>
        @endisset

        <label class="grid gap-1 text-sm dark:text-white">
            Role
            <select name="portal_role" class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-900">
                <option value="organization_admin" @selected(old('portal_role', 'organization_admin') === 'organization_admin')>Organization admin</option>
                <option value="member" @selected(old('portal_role') === 'member')>Member</option>
            </select>
        </label>

        <fieldset class="grid gap-3 md:col-span-2">
            <legend class="text-sm font-semibold text-gray-900 dark:text-white">Credential method</legend>

            <label class="flex cursor-pointer items-start gap-2 text-sm text-gray-800 dark:text-white">
                <input type="radio" name="portal_credential_method" value="invitation" @checked($portalCredentialMethod === 'invitation') onchange="window.syncCustomerPortalAccess(@js($portalAccessId))">
                <span><span class="font-medium">Send secure invitation to set password</span><span class="mt-0.5 block text-xs text-gray-500">The customer receives a secure, expiring password setup link.</span></span>
            </label>

            <label class="flex cursor-pointer items-start gap-2 text-sm text-gray-800 dark:text-white">
                <input type="radio" name="portal_credential_method" value="temporary_password" @checked($portalCredentialMethod === 'temporary_password') onchange="window.syncCustomerPortalAccess(@js($portalAccessId))">
                <span><span class="font-medium">Set temporary password manually</span><span class="mt-0.5 block text-xs text-gray-500">The customer must replace this password after signing in.</span></span>
            </label>
        </fieldset>

        <div id="{{ $portalAccessId }}-temporary-password" class="grid gap-4 md:col-span-2 md:grid-cols-2" style="display: {{ $portalCredentialMethod === 'temporary_password' ? 'grid' : 'none' }};">
            <label class="grid gap-1 text-sm dark:text-white">
                Temporary password
                <input type="password" name="portal_password" minlength="8" autocomplete="new-password" class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-900">
            </label>

            <label class="grid gap-1 text-sm dark:text-white">
                Confirm temporary password
                <input type="password" name="portal_password_confirmation" minlength="8" autocomplete="new-password" class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-900">
            </label>
        </div>

        <label class="flex cursor-pointer items-start gap-2 text-sm text-gray-800 dark:text-white md:col-span-2">
            <input type="hidden" name="portal_send_email" value="0">
            <input type="checkbox" name="portal_send_email" value="1" @checked(old('portal_send_email', true))>
            <span><span class="font-medium">Send invitation/login email</span><span class="mt-0.5 block text-xs text-gray-500">Email is sent for either credential method. Temporary passwords are never included in email.</span></span>
        </label>
    </div>

    @foreach(['create_portal_access', 'portal_name', 'portal_email', 'portal_person_id', 'portal_role', 'portal_credential_method', 'portal_password'] as $field)
        <x-admin::form.control-group.error :control-name="$field" />
    @endforeach
</section>

<script>
    window.syncCustomerPortalAccess = function (sectionId) {
        var enabled = document.getElementById(sectionId + '-enabled');
        var fields = document.getElementById(sectionId + '-fields');
        var temporaryFields = document.getElementById(sectionId + '-temporary-password');

        if (! enabled || ! fields || ! temporaryFields) {
            return;
        }

        var selectedMethod = document.querySelector('#' + sectionId + ' input[name="portal_credential_method"]:checked');
        var usesTemporaryPassword = enabled.checked && selectedMethod && selectedMethod.value === 'temporary_password';

        fields.style.display = enabled.checked ? 'grid' : 'none';
        temporaryFields.style.display = usesTemporaryPassword ? 'grid' : 'none';

        fields.querySelectorAll('input[name="portal_name"], input[name="portal_email"]').forEach(function (input) {
            input.required = enabled.checked;
        });

        temporaryFields.querySelectorAll('input').forEach(function (input) {
            input.required = Boolean(usesTemporaryPassword);
        });
    };

    window.syncCustomerPortalAccess(@js($portalAccessId));
</script>
