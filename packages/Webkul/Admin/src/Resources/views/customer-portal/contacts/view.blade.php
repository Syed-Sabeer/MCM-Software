@php
    $contactName = trim(($contact->first_name ?? '').' '.($contact->last_name ?? '')) ?: ($contact->name ?: 'Contact #'.$contact->id);
@endphp

@extends('admin::customer-portal.layouts.app', ['title' => $contactName, 'subtitle' => $contact->job_title ?: 'Company contact details.'])

@section('content')
    @php
        $primaryEmail = $contact->email ?: data_get($contact->emails, '0.value');
        $primaryPhone = $contact->phone ?: $contact->cell_phone ?: $contact->direct_phone ?: data_get($contact->contact_numbers, '0.value');
        $mailingAddress = collect([$contact->mailing_street, $contact->mailing_city, $contact->mailing_state, $contact->mailing_postcode, $contact->mailing_country])->filter();
    @endphp

    <div class="mb-4 flex flex-wrap justify-end gap-2">
        @if($primaryEmail)<a href="mailto:{{ $primaryEmail }}" class="primary-button inline-flex items-center gap-2"><span class="icon-mail"></span> Email</a>@endif
        @if($primaryPhone)<a href="tel:{{ $primaryPhone }}" class="secondary-button inline-flex items-center gap-2"><span class="icon-call"></span> Call</a>@endif
        <a href="{{ route('customer_portal.contacts') }}" class="secondary-button inline-flex items-center gap-2"><span class="icon-left-arrow"></span> Back to contacts</a>
    </div>

    <div class="grid gap-4 xl:grid-cols-[380px_minmax(0,1fr)]">
        <aside class="portal-card h-fit overflow-hidden">
            <div class="flex flex-col items-center border-b border-gray-200 p-6 text-center dark:border-gray-800">
                <span class="flex h-20 w-20 items-center justify-center rounded-full bg-brandColor text-2xl font-semibold text-white">{{ strtoupper(substr($contactName, 0, 1)) }}</span>
                <h2 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">{{ $contactName }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $contact->job_title ?: 'Company contact' }}</p>
                @if($contact->type)<span class="mt-3 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium capitalize text-blue-700 dark:bg-blue-950 dark:text-blue-300">{{ $contact->type }}</span>@endif
            </div>

            <div class="grid gap-4 p-5 text-sm">
                <div><p class="text-xs text-gray-500">Organization</p><p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $organization->name }}</p></div>
                <div><p class="text-xs text-gray-500">Primary email</p>@if($primaryEmail)<a href="mailto:{{ $primaryEmail }}" class="mt-1 block break-all font-medium text-brandColor hover:underline">{{ $primaryEmail }}</a>@else<p class="mt-1 text-gray-700 dark:text-gray-300">-</p>@endif</div>
                <div><p class="text-xs text-gray-500">Primary phone</p>@if($primaryPhone)<a href="tel:{{ $primaryPhone }}" class="mt-1 block font-medium text-gray-900 hover:text-brandColor dark:text-white">{{ $primaryPhone }}</a>@else<p class="mt-1 text-gray-700 dark:text-gray-300">-</p>@endif</div>
            </div>
        </aside>

        <div class="grid gap-4">
            <section class="portal-card p-5">
                <p class="portal-kicker">Contact Information</p>
                <h2 class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Personal details</h2>
                <dl class="mt-5 grid gap-x-8 gap-y-5 sm:grid-cols-2">
                    @foreach([
                        'Full name' => $contactName,
                        'Salutation' => $contact->salutation,
                        'Job title' => $contact->job_title ?: $contact->title,
                        'Primary email' => $primaryEmail,
                        'Secondary email' => $contact->email_secondary,
                        'Office phone' => $contact->phone ?: $contact->direct_phone,
                        'Cell phone' => $contact->cell_phone,
                        'Birth date' => $contact->birth_date?->format('Y-m-d'),
                    ] as $label => $value)
                        <div><dt class="text-xs font-medium text-gray-500">{{ $label }}</dt><dd class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-200">{{ $value ?: '-' }}</dd></div>
                    @endforeach
                </dl>

                @if($contact->description)<div class="mt-6 border-t border-gray-200 pt-5 dark:border-gray-800"><p class="text-xs font-medium text-gray-500">Description</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700 dark:text-gray-300">{{ $contact->description }}</p></div>@endif
            </section>

            <section class="portal-card p-5">
                <div class="flex items-start gap-3"><span class="portal-icon-box shrink-0"><i class="icon-location text-xl"></i></span><div><p class="portal-kicker">Mailing Address</p><h2 class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $contactName }}</h2><div class="mt-3 space-y-1 text-sm text-gray-700 dark:text-gray-300">@forelse($mailingAddress as $line)<p>{{ $line }}</p>@empty<p class="text-gray-500">No mailing address has been registered.</p>@endforelse</div></div></div>
            </section>
        </div>
    </div>
@endsection
