@php
    $portalManagerId = 'portal-manager-'.$organization->id.'-'.($portalManagerContext ?? 'default');
    $hasPortalUsers = $portalUsers->isNotEmpty();
    $compactPortalManager = ($portalManagerContext ?? 'default') === 'view';
    $compactPortalUsers = $compactPortalManager ? $portalUsers->take(3) : collect();
    $portalUsersModalId = $portalManagerId.'-all-users-modal';
@endphp

@if($compactPortalManager)
<section class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900" id="{{ $portalManagerId }}">
    <div class="flex items-center justify-between gap-3 border-b border-gray-200 px-4 py-3 dark:border-gray-800">
        <div class="flex min-w-0 items-center gap-2.5">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-red-50 text-brandColor dark:bg-red-950"><i class="icon-user text-lg"></i></span>
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-1.5">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Portal Access</h2>
                    <span class="rounded-full px-1.5 py-0.5 text-[11px] font-medium {{ $hasPortalUsers ? 'bg-green-50 text-green-700 dark:bg-green-950 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' }}">
                        {{ $portalUsers->count() }} {{ \Illuminate\Support\Str::plural('user', $portalUsers->count()) }}
                    </span>
                </div>
                <p class="mt-0.5 text-[11px] text-gray-500 dark:text-gray-400">Customer portal logins</p>
            </div>
        </div>

        <button type="button" class="primary-button inline-flex shrink-0 items-center gap-1 !px-2.5 !py-1.5 !text-xs" onclick="window.openPortalUserModal(@js($portalManagerId.'-modal'))">
            <span class="icon-add text-base"></span>
            Add
        </button>
    </div>

    @if(session('portal_invitation_url'))
        <div class="border-b border-green-200 bg-green-50 px-4 py-2.5 dark:border-green-900 dark:bg-green-950">
            <div class="flex items-center gap-2">
                <input readonly aria-label="Fresh invitation link" value="{{ session('portal_invitation_url') }}" onclick="this.select()" class="min-w-0 flex-1 rounded-md border border-green-300 bg-white px-2 py-1.5 text-xs text-gray-800 dark:border-green-800 dark:bg-gray-900 dark:text-white">
                <button type="button" class="secondary-button !px-2 !py-1.5 !text-xs" onclick="navigator.clipboard.writeText(this.previousElementSibling.value)">Copy</button>
            </div>
        </div>
    @endif

    <div class="divide-y divide-gray-200 dark:divide-gray-800">
        @forelse($compactPortalUsers as $portalUser)
            @php
                $isActive = $portalUser->status === 'active';
                $setupState = $portalUser->email_verified_at ? 'Configured' : 'Setup pending';
            @endphp
            <article class="flex items-start gap-2.5 px-4 py-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brandColor text-xs font-semibold text-white">{{ strtoupper(substr($portalUser->name, 0, 1)) }}</span>

                <div class="min-w-0 flex-1">
                    <div class="flex min-w-0 items-center gap-1.5">
                        <h3 class="truncate text-sm font-semibold text-gray-900 dark:text-white" title="{{ $portalUser->name }}">{{ $portalUser->name }}</h3>
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-amber-500' }}" title="{{ ucfirst($portalUser->status) }}"></span>
                    </div>
                    <p class="mt-0.5 truncate text-xs text-gray-600 dark:text-gray-300" title="{{ $portalUser->email }}">{{ $portalUser->email }}</p>
                    <p class="mt-1 truncate text-[11px] text-gray-500 dark:text-gray-400" title="{{ $portalUser->person ? 'Linked to '.$portalUser->person->name : 'Independent login' }}">
                        {{ $setupState }} &middot; {{ $portalUser->last_login_at?->format('M d, Y') ?: 'Never logged in' }}
                    </p>
                </div>

                <details class="relative shrink-0">
                    <summary class="icon-more flex h-8 w-8 cursor-pointer list-none items-center justify-center rounded-md text-xl text-gray-500 hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white" aria-label="Actions for {{ $portalUser->name }}" title="Portal user actions"></summary>
                    <div class="absolute right-0 top-9 z-20 w-40 rounded-md border border-gray-200 bg-white p-1 shadow-lg dark:border-gray-700 dark:bg-gray-900">
                        <button form="portal-user-resend-{{ $portalUser->id }}" class="flex w-full items-center gap-2 rounded px-2.5 py-2 text-left text-xs text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800"><span class="icon-sent text-base"></span>Resend setup</button>
                        <button form="portal-user-status-{{ $portalUser->id }}" class="flex w-full items-center gap-2 rounded px-2.5 py-2 text-left text-xs text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800"><span class="{{ $isActive ? 'icon-eye-hide' : 'icon-tick' }} text-base"></span>{{ $isActive ? 'Suspend access' : 'Reactivate access' }}</button>
                        <button form="portal-user-revoke-{{ $portalUser->id }}" class="flex w-full items-center gap-2 rounded px-2.5 py-2 text-left text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-950" onclick="return confirm('Revoke portal access for {{ addslashes($portalUser->name) }}?')"><span class="icon-delete text-base"></span>Revoke access</button>
                    </div>
                </details>
            </article>
        @empty
            <div class="px-4 py-6 text-center"><p class="text-sm font-medium text-gray-800 dark:text-gray-200">No portal users</p><p class="mt-1 text-xs text-gray-500">Add a login to enable access.</p></div>
        @endforelse
    </div>

    @if($hasPortalUsers)
        <button type="button" class="flex w-full items-center justify-center gap-1 border-t border-gray-200 px-4 py-2.5 text-xs font-semibold text-brandColor hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800" onclick="window.openPortalUsersListModal(@js($portalUsersModalId))">
            View all users
            <span class="icon-right-arrow text-base"></span>
        </button>
    @endif
</section>

<div id="{{ $portalUsersModalId }}" class="fixed inset-0 z-[100000] hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="{{ $portalUsersModalId }}-title" onclick="if (event.target === this) window.closePortalUsersListModal(@js($portalUsersModalId))">
    <div class="flex max-h-[calc(100vh-32px)] w-full max-w-xl flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between gap-3 border-b border-gray-200 px-5 py-4 dark:border-gray-800">
            <div>
                <div class="flex items-center gap-2">
                    <h2 id="{{ $portalUsersModalId }}-title" class="text-base font-bold text-gray-900 dark:text-white">Portal users</h2>
                    <span class="rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-950 dark:text-green-300">{{ $portalUsers->count() }}</span>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">All customer portal logins for {{ $organization->name }}.</p>
            </div>
            <button type="button" class="icon-cross-large rounded-md p-1.5 text-2xl text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Close" onclick="window.closePortalUsersListModal(@js($portalUsersModalId))"></button>
        </div>

        <div class="min-h-0 flex-1 divide-y divide-gray-200 overflow-y-auto dark:divide-gray-800">
            @foreach($portalUsers as $portalUser)
                @php
                    $isActive = $portalUser->status === 'active';
                    $setupState = $portalUser->email_verified_at ? 'Configured' : 'Setup pending';
                @endphp
                <article class="flex items-center gap-3 px-5 py-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brandColor text-xs font-semibold text-white">{{ strtoupper(substr($portalUser->name, 0, 1)) }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="flex min-w-0 items-center gap-1.5">
                            <h3 class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $portalUser->name }}</h3>
                            <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-amber-500' }}" title="{{ ucfirst($portalUser->status) }}"></span>
                        </div>
                        <p class="mt-0.5 truncate text-xs text-gray-600 dark:text-gray-300" title="{{ $portalUser->email }}">{{ $portalUser->email }}</p>
                        <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">{{ $setupState }} &middot; {{ $portalUser->last_login_at?->format('M d, Y') ?: 'Never logged in' }}</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-1">
                        <button form="portal-user-resend-{{ $portalUser->id }}" class="icon-sent flex h-8 w-8 items-center justify-center rounded-md text-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white" aria-label="Resend setup for {{ $portalUser->name }}" title="Resend setup"></button>
                        <button form="portal-user-status-{{ $portalUser->id }}" class="{{ $isActive ? 'icon-eye-hide' : 'icon-tick' }} flex h-8 w-8 items-center justify-center rounded-md text-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white" aria-label="{{ $isActive ? 'Suspend' : 'Reactivate' }} {{ $portalUser->name }}" title="{{ $isActive ? 'Suspend access' : 'Reactivate access' }}"></button>
                        <button form="portal-user-revoke-{{ $portalUser->id }}" class="icon-delete flex h-8 w-8 items-center justify-center rounded-md text-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-950" aria-label="Revoke access for {{ $portalUser->name }}" title="Revoke access" onclick="return confirm('Revoke portal access for {{ addslashes($portalUser->name) }}?')"></button>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="flex justify-end border-t border-gray-200 bg-slate-50 px-5 py-3 dark:border-gray-800 dark:bg-gray-950">
            <button type="button" class="secondary-button" onclick="window.closePortalUsersListModal(@js($portalUsersModalId))">Close</button>
        </div>
    </div>
</div>

<script>
    window.openPortalUsersListModal = function (modalId) {
        var modal = document.getElementById(modalId);
        if (! modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    };

    window.closePortalUsersListModal = function (modalId) {
        var modal = document.getElementById(modalId);
        if (! modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    };
</script>
@else
<section class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900" id="{{ $portalManagerId }}">
    <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-200 p-5 dark:border-gray-800">
        <div class="flex min-w-0 items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-red-50 text-brandColor dark:bg-red-950"><i class="icon-user text-xl"></i></span>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Customer Portal Access</h2>
                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $hasPortalUsers ? 'bg-green-50 text-green-700 dark:bg-green-950 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' }}">{{ $portalUsers->count() }} {{ \Illuminate\Support\Str::plural('user', $portalUsers->count()) }}</span>
                </div>
                <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">Manage the people who can sign in to this customer's portal.</p>
            </div>
        </div>

        <button type="button" class="primary-button inline-flex items-center gap-2" onclick="window.openPortalUserModal(@js($portalManagerId.'-modal'))">
            <span class="icon-add text-lg"></span>
            Add portal user
        </button>
    </div>

    <div class="flex items-center justify-between gap-3 border-b border-gray-200 bg-slate-50 px-5 py-3 dark:border-gray-800 dark:bg-gray-950">
        <label class="flex items-center gap-2 text-sm font-medium text-gray-800 dark:text-gray-200">
            <input type="checkbox" @checked($hasPortalUsers) disabled class="h-4 w-4 accent-brandColor">
            Portal access {{ $hasPortalUsers ? 'enabled' : 'not configured' }}
        </label>
        @if($hasPortalUsers)<span class="text-xs text-gray-500">Multiple users supported</span>@endif
    </div>

    @if(session('portal_invitation_url'))
        <div class="border-b border-green-200 bg-green-50 p-4 dark:border-green-900 dark:bg-green-950">
            <label class="grid gap-1.5 text-xs font-medium text-green-800 dark:text-green-300">Fresh invitation link
                <div class="flex gap-2"><input readonly value="{{ session('portal_invitation_url') }}" onclick="this.select()" class="min-w-0 flex-1 rounded-md border border-green-300 bg-white px-3 py-2 text-sm font-normal text-gray-800 dark:border-green-800 dark:bg-gray-900 dark:text-white"><button type="button" class="secondary-button" onclick="navigator.clipboard.writeText(this.previousElementSibling.value)">Copy</button></div>
            </label>
        </div>
    @endif

    <div class="divide-y divide-gray-200 dark:divide-gray-800">
        @forelse($portalUsers as $portalUser)
            @php
                $statusClass = $portalUser->status === 'active'
                    ? 'bg-green-50 text-green-700 dark:bg-green-950 dark:text-green-300'
                    : 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300';
                $setupState = $portalUser->email_verified_at ? 'Password configured' : 'Setup pending';
            @endphp
            <article class="p-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex min-w-0 items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brandColor font-semibold text-white">{{ strtoupper(substr($portalUser->name, 0, 1)) }}</span>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2"><h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $portalUser->name }}</h3><span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClass }}">{{ ucfirst($portalUser->status) }}</span></div>
                            <p class="mt-1 break-all text-sm text-gray-700 dark:text-gray-300"><span class="icon-mail mr-1 text-base text-gray-400"></span>{{ $portalUser->email }}</p>
                            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $setupState }}</span>
                                <span>Last login: {{ $portalUser->last_login_at?->format('M d, Y H:i') ?: 'Never' }}</span>
                                @if($portalUser->person)<span>Linked to {{ $portalUser->person->name }}</span>@else<span>Independent login</span>@endif
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button form="portal-user-resend-{{ $portalUser->id }}" class="secondary-button inline-flex items-center gap-1.5" title="Send a fresh password setup link"><span class="icon-sent"></span> Resend setup</button>
                        <button form="portal-user-status-{{ $portalUser->id }}" class="secondary-button">{{ $portalUser->status === 'active' ? 'Suspend' : 'Reactivate' }}</button>
                        <button form="portal-user-revoke-{{ $portalUser->id }}" class="secondary-button !border-red-300 !text-red-600 hover:!bg-red-50 dark:!border-red-800 dark:hover:!bg-red-950" onclick="return confirm('Revoke portal access for {{ addslashes($portalUser->name) }}?')">Revoke</button>
                    </div>
                </div>
            </article>
        @empty
            <div class="px-5 py-10 text-center"><span class="icon-user text-4xl text-gray-300 dark:text-gray-700"></span><p class="mt-3 text-sm font-semibold text-gray-800 dark:text-gray-200">No portal users yet</p><p class="mt-1 text-xs text-gray-500">Add a contact or create an independent login to enable access.</p></div>
        @endforelse
    </div>

    {{-- <div class="border-t border-gray-200 px-5 py-3 text-xs text-gray-500 dark:border-gray-800 dark:text-gray-400">Passwords are securely hashed and cannot be displayed. Login email, setup state, and access status are shown above.</div> --}}
</section>
@endif
