@extends('admin::customer-portal.layouts.app', ['title' => 'Company Contacts', 'subtitle' => 'Contacts registered under your organization.'])

@section('content')
    @include('admin::customer-portal.partials.list-toolbar', ['route' => 'customer_portal.contacts', 'placeholder' => 'Search name, title, email, or phone'])

    <section class="portal-card overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-800"><div><p class="portal-kicker">Organization directory</p><p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $records->total() }} contact{{ $records->total() === 1 ? '' : 's' }}</p></div><span class="portal-icon-box"><i class="icon-contact text-xl"></i></span></div>

        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-left">
                <thead class="border-b border-gray-200 bg-slate-50 text-xs font-semibold uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300"><tr><th class="px-4 py-3">Contact</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Phone</th><th class="w-12 px-4 py-3"></th></tr></thead>
                <tbody class="divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    @forelse($records as $contact)
                        @php($contactName = trim(($contact->first_name ?? '').' '.($contact->last_name ?? '')) ?: ($contact->name ?: 'Contact #'.$contact->id))
                        <tr class="portal-row"><td class="px-4 py-3"><a href="{{ route('customer_portal.contacts.view', $contact->id) }}" class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brandColor font-semibold text-white">{{ strtoupper(substr($contactName, 0, 1)) }}</span><span class="font-semibold text-gray-900 hover:text-brandColor dark:text-white">{{ $contactName }}</span></a></td><td class="px-4 py-3">@if($contact->email)<a href="mailto:{{ $contact->email }}" class="text-brandColor hover:underline">{{ $contact->email }}</a>@else<span class="text-gray-500">-</span>@endif</td><td class="px-4 py-3">@if($contact->phone || $contact->cell_phone)<a href="tel:{{ $contact->phone ?: $contact->cell_phone }}" class="text-gray-700 hover:text-brandColor dark:text-gray-300">{{ $contact->phone ?: $contact->cell_phone }}</a>@else<span class="text-gray-500">-</span>@endif</td><td class="px-4 py-3"><a href="{{ route('customer_portal.contacts.view', $contact->id) }}" class="inline-flex rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-brandColor dark:hover:bg-gray-800" aria-label="View {{ $contactName }}"><span class="icon-right-arrow text-xl"></span></a></td></tr>
                    @empty
                        @include('admin::customer-portal.partials.table-empty', ['colspan' => 4, 'message' => 'No contacts match your search.'])
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-gray-200 md:hidden dark:divide-gray-800">
            @forelse($records as $contact)
                @php($contactName = trim(($contact->first_name ?? '').' '.($contact->last_name ?? '')) ?: ($contact->name ?: 'Contact #'.$contact->id))
                <article class="portal-row p-4"><div class="flex items-start gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brandColor font-semibold text-white">{{ strtoupper(substr($contactName, 0, 1)) }}</span><div class="min-w-0 flex-1"><a href="{{ route('customer_portal.contacts.view', $contact->id) }}" class="font-semibold text-gray-900 hover:text-brandColor dark:text-white">{{ $contactName }}</a>@if($contact->email)<a href="mailto:{{ $contact->email }}" class="mt-2 block truncate text-sm text-brandColor">{{ $contact->email }}</a>@endif @if($contact->phone || $contact->cell_phone)<a href="tel:{{ $contact->phone ?: $contact->cell_phone }}" class="mt-1 block text-sm text-gray-700 dark:text-gray-300">{{ $contact->phone ?: $contact->cell_phone }}</a>@endif</div><a href="{{ route('customer_portal.contacts.view', $contact->id) }}" aria-label="View {{ $contactName }}"><span class="icon-right-arrow text-xl text-gray-400"></span></a></div></article>
            @empty
                <div class="p-8 text-center text-sm text-gray-500">No contacts match your search.</div>
            @endforelse
        </div>
    </section>
    <div class="mt-4">{{ $records->links() }}</div>
@endsection
