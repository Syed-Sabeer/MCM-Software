@extends('admin::customer-portal.layouts.app', ['title' => 'My Company', 'subtitle' => 'Your organization profile and registered business addresses.'])

@section('content')
    @php
        $billingAddress = collect([$organization->billing_street, $organization->billing_city, $organization->billing_state, $organization->billing_postcode, $organization->billing_country])->filter();
        $shippingAddress = collect([$organization->shipping_street, $organization->shipping_city, $organization->shipping_state, $organization->shipping_postcode, $organization->shipping_country])->filter();
    @endphp

    <div class="grid gap-4">
        <section class="portal-card p-5">
            <div class="flex flex-wrap items-start justify-between gap-5">
                <div class="flex min-w-0 items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-md bg-brandColor text-xl font-bold text-white">{{ strtoupper(substr($organization->name, 0, 2)) }}</div>
                    <div class="min-w-0"><p class="portal-kicker">Customer organization</p><h2 class="mt-1 truncate text-xl font-semibold text-gray-900 dark:text-white">{{ $organization->name }}</h2><p class="mt-1 text-sm text-gray-500">{{ $organization->industry ?: 'Industry not specified' }}</p></div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($organization->phone)<a href="tel:{{ $organization->phone }}" class="secondary-button inline-flex items-center gap-2"><span class="icon-call"></span> Call</a>@endif
                    @if($organization->website)<a href="{{ $organization->website }}" target="_blank" rel="noopener" class="secondary-button inline-flex items-center gap-2"><span class="icon-forward"></span> Website</a>@endif
                </div>
            </div>
        </section>

        <div class="grid gap-4 xl:grid-cols-[1.1fr_1fr]">
            <section class="portal-card p-5">
                <p class="portal-kicker">Company information</p><h2 class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Business details</h2>
                <dl class="mt-5 grid gap-x-6 gap-y-5 sm:grid-cols-2">
                    @foreach(['Company name' => $organization->name, 'Industry' => $organization->industry, 'Phone' => $organization->phone, 'Website' => $organization->website, 'Employees' => $organization->employees] as $label => $value)
                        <div><dt class="text-xs font-medium text-gray-500">{{ $label }}</dt><dd class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-200">{{ $value ?: '-' }}</dd></div>
                    @endforeach
                </dl>
                <div class="mt-6 border-t border-gray-200 pt-5 dark:border-gray-800"><p class="text-xs font-medium text-gray-500">Description</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700 dark:text-gray-300">{{ $organization->description ?: 'No company description is available.' }}</p></div>
            </section>

            <div class="grid gap-4">
                @foreach([['label' => 'Billing address', 'icon' => 'icon-dollar', 'address' => $billingAddress], ['label' => 'Shipping address', 'icon' => 'icon-location', 'address' => $shippingAddress]] as $addressBlock)
                    <section class="portal-card p-5"><div class="flex items-start gap-3"><span class="portal-icon-box shrink-0"><i class="{{ $addressBlock['icon'] }} text-xl"></i></span><div><p class="portal-kicker">{{ $addressBlock['label'] }}</p>@if($addressBlock['address']->isNotEmpty())<address class="mt-2 text-sm not-italic leading-6 text-gray-700 dark:text-gray-300">{{ $addressBlock['address']->implode(', ') }}</address>@else<p class="mt-2 text-sm text-gray-500">No address has been registered.</p>@endif</div></div></section>
                @endforeach
            </div>
        </div>
    </div>
@endsection
