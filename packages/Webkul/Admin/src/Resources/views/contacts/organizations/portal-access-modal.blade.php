@php
    $portalManagerId = 'portal-manager-'.$organization->id.'-'.($portalManagerContext ?? 'default');
    $portalModalId = $portalManagerId.'-modal';
    $portalHasErrors = $errors->hasAny(['portal_name', 'portal_email', 'portal_person_id', 'portal_credential_method', 'portal_password']);
    $selectedPortalContact = (string) old('portal_person_id', '');
    $selectedCredentialMethod = old('portal_credential_method', 'invitation');
@endphp

<div id="{{ $portalModalId }}" class="fixed inset-0 z-[100000] {{ $portalHasErrors ? 'flex' : 'hidden' }} items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="{{ $portalModalId }}-title" onclick="if (event.target === this) window.closePortalUserModal(@js($portalModalId))">
    <div class="max-h-[calc(100vh-32px)] w-full max-w-2xl overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-start justify-between gap-4 border-b border-gray-200 p-5 dark:border-gray-800">
            <div class="flex items-start gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-red-50 text-brandColor dark:bg-red-950"><i class="icon-user text-xl"></i></span><div><h2 id="{{ $portalModalId }}-title" class="text-lg font-bold text-gray-900 dark:text-white">Add portal user</h2><p class="mt-1 text-xs text-gray-500">Create another login for {{ $organization->name }}.</p></div></div>
            <button type="button" class="icon-cross-large rounded-md p-1.5 text-2xl text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Close" onclick="window.closePortalUserModal(@js($portalModalId))"></button>
        </div>

        <form id="{{ $portalModalId }}-form" method="POST" action="{{ route('admin.customers.organizations.portal_users.store', $organization) }}">
            @csrf
            <div class="grid gap-5 p-5">
                <div>
                    <label for="{{ $portalModalId }}-contact" class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Link a contact</label>
                    <select id="{{ $portalModalId }}-contact" name="portal_person_id" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-brandColor dark:border-gray-700 dark:bg-gray-950 dark:text-white" onchange="window.syncPortalUserModal(@js($portalModalId))">
                        <option value="">No linked contact - create an independent login</option>
                        @foreach($portalContacts as $contact)
                            @php
                                $contactEmail = $contact->email ?: collect($contact->emails ?? [])->map(fn ($email) => is_array($email) ? ($email['value'] ?? null) : $email)->filter()->first();
                            @endphp
                            <option value="{{ $contact->id }}" data-name="{{ $contact->name }}" data-email="{{ $contactEmail }}" @selected($selectedPortalContact === (string) $contact->id)>{{ $contact->name }}{{ $contactEmail ? ' - '.$contactEmail : ' - email required' }}</option>
                        @endforeach
                    </select>
                    @error('portal_person_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div id="{{ $portalModalId }}-linked-summary" class="hidden rounded-md border border-green-200 bg-green-50 p-4 dark:border-green-900 dark:bg-green-950">
                    <div class="flex items-start gap-3"><span class="icon-tick mt-0.5 text-xl text-green-600"></span><div><p class="text-sm font-semibold text-green-800 dark:text-green-300" data-contact-name></p><p class="mt-1 text-xs text-green-700 dark:text-green-400">Login email: <strong data-contact-email></strong></p><p class="mt-1 hidden text-xs font-medium text-red-600" data-contact-email-error>This contact has no valid email. Add an email to the contact before creating access.</p></div></div>
                </div>

                <div id="{{ $portalModalId }}-independent-fields" class="grid gap-4 sm:grid-cols-2">
                    <label class="grid gap-1.5 text-sm font-medium text-gray-800 dark:text-white">User name<input name="portal_name" value="{{ old('portal_name') }}" class="rounded-md border border-gray-300 px-3 py-2.5 font-normal outline-none focus:border-brandColor dark:border-gray-700 dark:bg-gray-950" placeholder="Customer contact name">@error('portal_name')<span class="text-xs text-red-600">{{ $message }}</span>@enderror</label>
                    <label class="grid gap-1.5 text-sm font-medium text-gray-800 dark:text-white">Login email<input type="email" name="portal_email" value="{{ old('portal_email') }}" class="rounded-md border border-gray-300 px-3 py-2.5 font-normal outline-none focus:border-brandColor dark:border-gray-700 dark:bg-gray-950" placeholder="name@company.com">@error('portal_email')<span class="text-xs text-red-600">{{ $message }}</span>@enderror</label>
                </div>

                <fieldset>
                    <legend class="text-sm font-semibold text-gray-900 dark:text-white">Credential method</legend>
                    <div class="mt-2 grid gap-3 sm:grid-cols-2">
                        <label class="cursor-pointer rounded-md border border-gray-200 p-4 has-[:checked]:border-brandColor has-[:checked]:bg-red-50 dark:border-gray-700 dark:has-[:checked]:bg-red-950"><span class="flex items-start gap-2"><input type="radio" name="portal_credential_method" value="invitation" @checked($selectedCredentialMethod === 'invitation') onchange="window.syncPortalUserModal(@js($portalModalId))"><span><span class="block text-sm font-semibold text-gray-900 dark:text-white">Secure invitation</span><span class="mt-1 block text-xs leading-5 text-gray-500">Email a protected link so the customer chooses a password.</span></span></span></label>
                        <label class="cursor-pointer rounded-md border border-gray-200 p-4 has-[:checked]:border-brandColor has-[:checked]:bg-red-50 dark:border-gray-700 dark:has-[:checked]:bg-red-950"><span class="flex items-start gap-2"><input type="radio" name="portal_credential_method" value="temporary_password" @checked($selectedCredentialMethod === 'temporary_password') onchange="window.syncPortalUserModal(@js($portalModalId))"><span><span class="block text-sm font-semibold text-gray-900 dark:text-white">Temporary password</span><span class="mt-1 block text-xs leading-5 text-gray-500">Set the first password manually; it must be changed after login.</span></span></span></label>
                    </div>
                    @error('portal_credential_method')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </fieldset>

                <div id="{{ $portalModalId }}-password-fields" class="hidden grid gap-4 rounded-md border border-gray-200 bg-slate-50 p-4 sm:grid-cols-2 dark:border-gray-700 dark:bg-gray-950">
                    <label class="grid gap-1.5 text-sm font-medium text-gray-800 dark:text-white">Temporary password<input type="password" name="portal_password" minlength="8" autocomplete="new-password" class="rounded-md border border-gray-300 bg-white px-3 py-2.5 font-normal outline-none focus:border-brandColor dark:border-gray-700 dark:bg-gray-900">@error('portal_password')<span class="text-xs text-red-600">{{ $message }}</span>@enderror</label>
                    <label class="grid gap-1.5 text-sm font-medium text-gray-800 dark:text-white">Confirm password<input type="password" name="portal_password_confirmation" minlength="8" autocomplete="new-password" class="rounded-md border border-gray-300 bg-white px-3 py-2.5 font-normal outline-none focus:border-brandColor dark:border-gray-700 dark:bg-gray-900"></label>
                </div>

                <label class="flex cursor-pointer items-start gap-2 rounded-md border border-gray-200 p-3 text-sm dark:border-gray-700 dark:text-white"><input type="hidden" name="portal_send_email" value="0"><input type="checkbox" name="portal_send_email" value="1" @checked(old('portal_send_email', true)) class="mt-0.5"><span><span class="font-medium">Send invitation/login email</span><span class="mt-0.5 block text-xs text-gray-500">An email is sent for either credential method. Manually entered passwords are not included.</span></span></label>
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-200 bg-slate-50 px-5 py-4 dark:border-gray-800 dark:bg-gray-950"><button type="button" class="secondary-button" onclick="window.closePortalUserModal(@js($portalModalId))">Cancel</button><button id="{{ $portalModalId }}-submit" type="submit" class="primary-button inline-flex min-w-[142px] items-center justify-center gap-2"><span class="icon-add"></span> Create access</button></div>
        </form>
    </div>
</div>

<script>
    window.openPortalUserModal = function (modalId) {
        var modal = document.getElementById(modalId);
        if (! modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        window.syncPortalUserModal(modalId);
    };

    window.closePortalUserModal = function (modalId) {
        var modal = document.getElementById(modalId);
        if (! modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    };

    window.syncPortalUserModal = function (modalId) {
        var modal = document.getElementById(modalId);
        if (! modal) return;
        var contactSelect = modal.querySelector('select[name="portal_person_id"]');
        var selectedContact = contactSelect && contactSelect.value ? contactSelect.options[contactSelect.selectedIndex] : null;
        var independentFields = document.getElementById(modalId + '-independent-fields');
        var linkedSummary = document.getElementById(modalId + '-linked-summary');
        var method = modal.querySelector('input[name="portal_credential_method"]:checked');
        var passwordFields = document.getElementById(modalId + '-password-fields');
        var linkedEmail = selectedContact ? selectedContact.dataset.email : '';

        contactSelect.setCustomValidity(selectedContact && ! linkedEmail
            ? 'The linked contact must have a valid email before portal access can be created.'
            : '');

        independentFields.classList.toggle('hidden', Boolean(selectedContact));
        linkedSummary.classList.toggle('hidden', ! selectedContact);
        independentFields.querySelectorAll('input').forEach(function (input) {
            input.disabled = Boolean(selectedContact);
            input.required = ! selectedContact;
        });

        if (selectedContact) {
            linkedSummary.querySelector('[data-contact-name]').textContent = selectedContact.dataset.name || selectedContact.textContent;
            linkedSummary.querySelector('[data-contact-email]').textContent = linkedEmail || 'Not available';
            linkedSummary.querySelector('[data-contact-email-error]').classList.toggle('hidden', Boolean(linkedEmail));
        }

        var usesPassword = method && method.value === 'temporary_password';
        passwordFields.classList.toggle('hidden', ! usesPassword);
        passwordFields.querySelectorAll('input').forEach(function (input) {
            input.disabled = ! usesPassword;
            input.required = Boolean(usesPassword);
        });
    };

    document.getElementById(@js($portalModalId) + '-form')?.addEventListener('submit', function (event) {
        var contact = document.getElementById(@js($portalModalId) + '-contact');
        var selected = contact && contact.value ? contact.options[contact.selectedIndex] : null;
        if (selected && ! selected.dataset.email) {
            event.preventDefault();
            window.syncPortalUserModal(@js($portalModalId));

            return;
        }

        var submitButton = document.getElementById(@js($portalModalId) + '-submit');
        if (submitButton) {
            submitButton.innerHTML = '<span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span><span>Creating access...</span>';
        }
    });

    window.syncPortalUserModal(@js($portalModalId));
    @if($portalHasErrors) document.body.style.overflow = 'hidden'; @endif
</script>
