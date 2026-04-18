<x-admin::layouts>
    @php
        $currentRouteName = request()->route()?->getName() ?? 'admin.contacts.organizations.view';
        $routePrefix = str_contains($currentRouteName, 'admin.customers.')
            ? 'customers'
            : (str_contains($currentRouteName, 'admin.vendors.') ? 'vendors' : 'contacts');
        $organizationLabel = $routePrefix === 'vendors' ? 'Vendor' : 'Company';
        $contactLabel = 'Contact';
        $contactPluralLabel = 'Contacts';
    @endphp

    @php
        $activityTypes = [
            ['name' => 'all', 'label' => 'All'],
            ['name' => 'email', 'label' => 'Mail'],
            ['name' => 'call', 'label' => 'Log a Call'],
            ['name' => 'meeting', 'label' => 'Event'],
            ['name' => 'note', 'label' => 'Comment'],
            ['name' => 'file', 'label' => 'File'],
            ['name' => 'task', 'label' => 'Task'],
            ['name' => 'system', 'label' => 'Change Log'],
        ];
    @endphp

    <x-slot:title>
        {{ $organizationLabel }}: {{ $organization->name }}
    </x-slot>

    @php
        $contactsQuery = $organization->persons()->latest();
        $contactsCount = $contactsQuery->count();
        $recentContacts = $contactsQuery->take(3)->get();

        $formatAddressSummary = function ($city, $state, $postcode, $country) {
            return collect([$city, $state, $postcode, $country])
                ->map(fn ($value) => trim((string) $value))
                ->filter()
                ->implode(', ');
        };

        $addressBook = collect($organization->address ?? [])
            ->filter(fn ($address) => is_array($address));

        $extraAddresses = $addressBook->filter(function ($address) {
            $type = strtolower((string) ($address['type'] ?? 'other'));

            return $type === 'other';
        })->values();
    @endphp

    <script>
        window.handleCaseDelete = function(e, btn) {
            e.preventDefault();
            e.stopPropagation();
            var form = btn.closest('.case-delete-form');
            if (!form) return;
            if (typeof window.emitter !== 'undefined') {
                window.emitter.emit('open-confirm-modal', {
                    agree: function () {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Are you sure you want to perform this action?')) form.submit();
            }
        };
    </script>

    <div class="flex gap-4 max-xl:flex-wrap">
        <!-- Left Panel: About / Details -->
        <div class="max-xl:min-w-full max-xl:max-w-full xl:sticky xl:top-[73px] flex min-w-[320px] max-w-[340px] flex-col self-start rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800">
                <!-- Breadcrumbs and title -->
                <div class="flex items-center justify-between">
                    <x-admin::breadcrumbs
                        :name="$routePrefix . '.organizations.view'"
                        :entity="$organization"
                    />
                </div>

                <div class="flex items-center gap-3">
                    <x-admin::avatar :name="$organization->name" />

                    <div class="flex flex-col">
                        <h3 class="text-lg font-bold dark:text-white">
                            {{ $organization->name }}
                        </h3>

                        @if ($organization->website)
                            <a
                                href="{{ $organization->website }}"
                                target="_blank"
                                class="text-xs text-brandColor hover:underline"
                            >
                                {{ $organization->website }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- About section -->
            <div class="flex w-full flex-col gap-2 border-b border-gray-200 p-4 dark:border-gray-800">
                <p class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    About
                </p>

                <div class="flex flex-col gap-2 text-sm dark:text-white">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $organizationLabel }} Name</p>
                        <p>{{ $organization->name }}</p>
                    </div>

                    @if ($organization->phone)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Phone</p>
                            <p>{{ $organization->phone }}</p>
                        </div>
                    @endif

                    @if ($organization->fax)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Fax</p>
                            <p>{{ $organization->fax }}</p>
                        </div>
                    @endif

                    @if ($organization->website)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Website</p>
                            <a
                                href="{{ $organization->website }}"
                                target="_blank"
                                class="text-brandColor hover:underline"
                            >
                                {{ $organization->website }}
                            </a>
                        </div>
                    @endif

                    @if ($organization->annual_revenue)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Annual Revenue</p>
                            <p>{{ $organization->annual_revenue }}</p>
                        </div>
                    @endif

                    @if ($organization->employees)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Employees</p>
                            <p>{{ $organization->employees }}</p>
                        </div>
                    @endif

                    @php
                        $typeLabels = [
                            'customer'  => 'Customer',
                            'vendor'    => 'Vendor',
                        ];

                        $industryLabels = [
                            'home_apparel'   => 'Home Apparel',
                            'other_shipping' => 'Other Shipping',
                        ];
                    @endphp

                    @if ($organization->type)
                        @php
                            $normalizedType = strtolower((string) $organization->type);
                        @endphp
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Type</p>
                            <p>{{ $typeLabels[$normalizedType] ?? ucfirst(str_replace('_', ' ', $organization->type)) }}</p>
                        </div>
                    @endif

                    @if ($organization->industry)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Industry</p>
                            <p>{{ $industryLabels[$organization->industry] ?? ucfirst(str_replace('_', ' ', $organization->industry)) }}</p>
                        </div>
                    @endif

                    @if ($organization->description)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Description</p>
                            <p>{{ $organization->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Billing Address -->
            <div class="flex w-full flex-col gap-2 border-b border-gray-200 p-4 dark:border-gray-800">
                <p class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    Billing Address
                </p>

                <div class="flex flex-col gap-0.5 text-sm dark:text-white" style="max-width: 280px; word-wrap: break-word; word-break: break-word;">
                    @if ($organization->billing_street)
                        <span>{{ $organization->billing_street }}</span>
                    @endif

                    @if ($organization->billing_postcode || $organization->billing_city)
                        <span>
                            {{ $formatAddressSummary($organization->billing_city, $organization->billing_state, $organization->billing_postcode, $organization->billing_country) }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="flex w-full flex-col gap-2 border-b border-gray-200 p-4 dark:border-gray-800">
                <p class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    Shipping Address
                </p>

                <div class="flex flex-col gap-0.5 text-sm dark:text-white" style="max-width: 280px; word-wrap: break-word; word-break: break-word;">
                    @if ($organization->shipping_street)
                        <span>{{ $organization->shipping_street }}</span>
                    @endif

                    @if ($organization->shipping_postcode || $organization->shipping_city)
                        <span>
                            {{ $formatAddressSummary($organization->shipping_city, $organization->shipping_state, $organization->shipping_postcode, $organization->shipping_country) }}
                        </span>
                    @endif
                </div>
            </div>

            @if ($extraAddresses->isNotEmpty())
                <div class="flex w-full flex-col gap-2 border-b border-gray-200 p-4 dark:border-gray-800">
                    <p class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Other Addresses
                    </p>

                    <div class="flex flex-col gap-3 text-sm dark:text-white">
                        @foreach ($extraAddresses as $address)
                            <div class="rounded border border-gray-200 p-2 dark:border-gray-700" style="max-width: 280px; word-wrap: break-word; word-break: break-word;">
                                @if (!empty($address['street']))
                                    <p>{{ $address['street'] }}</p>
                                @endif

                                <p>
                                    {{ $formatAddressSummary($address['city'] ?? '', $address['state'] ?? '', $address['postcode'] ?? '', $address['country'] ?? '') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Center Panel: Activities -->
        <div class="flex min-w-[420px] flex-1 flex-col gap-4">
            {!! view_render_event('admin.contacts.organizations.view.activities.before', ['organization' => $organization]) !!}

            <!-- Activity action buttons toolbar -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-wrap gap-3 mb-4">
                    <x-admin::activities.actions.mail
                        :entity="$organization"
                        entity-control-name="organization_id"
                    />

                    <x-admin::activities.actions.activity
                        :entity="$organization"
                        entity-control-name="organization_id"
                    />

                    <x-admin::activities.actions.event
                        :entity="$organization"
                        entity-control-name="organization_id"
                    />

                    <x-admin::activities.actions.note
                        :entity="$organization"
                        entity-control-name="organization_id"
                    />

                    <x-admin::activities.actions.file
                        :entity="$organization"
                        entity-control-name="organization_id"
                    />

                    <x-admin::activities.actions.task
                        :entity="$organization"
                        entity-control-name="organization_id"
                    />
                </div>

                <!-- Only show activities with insights toggle -->
                {{-- <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <span>Only show activities with insights</span>

                    <button
                        type="button"
                        class="relative inline-flex h-5 w-9 items-center rounded-full bg-gray-300 transition dark:bg-gray-700"
                    >
                        <span class="inline-block h-4 w-4 translate-x-1 transform rounded-full bg-white shadow"></span>
                    </button>
                </div> --}}
            </div>

            <!-- Activities Tabs Component -->
            <x-admin::activities
                :endpoint="route('admin.' . $routePrefix . '.organizations.activities.index', $organization->id)"
                :types="$activityTypes"
            />

            {!! view_render_event('admin.contacts.organizations.view.activities.after', ['organization' => $organization]) !!}
        </div>

        <!-- Right Panel: Actions & Related Lists -->
        <div class="flex min-w-[320px] max-w-[340px] flex-col gap-4">
            <!-- Header actions similar to Salesforce: New Contact, Edit, Delete -->
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ $organizationLabel }}
                    </p>

                    <p class="text-lg font-semibold dark:text-white">
                        {{ $organization->name }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Add Contact button (person) pre-linked to this organization -->
                    {{-- <a
                        href="{{ route('admin.contacts.persons.create', ['organization_id' => $organization->id]) }}"
                        class="secondary-button"
                    >
                        @lang('admin::app.contacts.organizations.view.new-contact-btn')
                    </a> --}}

                    <!-- Edit -->
                    <a
                        href="{{ route('admin.' . $routePrefix . '.organizations.edit', $organization->id) }}"
                        class="secondary-button"
                    >
                        @lang('admin::app.contacts.organizations.view.edit-btn')
                    </a>

                    <!-- Delete -->
                    <form
                        method="POST"
                        action="{{ route('admin.' . $routePrefix . '.organizations.delete', $organization->id) }}"
                        onsubmit="return confirm('@lang('admin::app.contacts.organizations.view.delete-confirm')');"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="danger-button">
                            @lang('admin::app.contacts.organizations.view.delete-btn')
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contacts card -->
            <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-base font-bold text-gray-900 dark:text-white">
                        {{ $contactPluralLabel }}
                        @if ($contactsCount)
                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ $contactsCount }})</span>
                        @endif
                    </p>

                    <a
                        href="{{ route('admin.' . $routePrefix . '.persons.create', ['organization_id' => $organization->id]) }}"
                        class="inline-flex items-center gap-1 rounded bg-brandColor px-2.5 py-1 text-xs font-semibold text-white hover:bg-brandColor/90"
                    >
                        + New
                    </a>
                </div>

                @if ($contactsCount)
                    <div class="flex flex-col gap-2.5 border-t border-gray-200 pt-3 dark:border-gray-700">
                        @foreach ($recentContacts as $contact)
                            @php
                                $contactName = trim(($contact->first_name ?? '') . ' ' . ($contact->last_name ?? '')) ?: $contact->name;
                                $contactNameLimited = \Illuminate\Support\Str::limit($contactName, 35);
                                $contactEmail = $contact->email ?: 'No email';
                            @endphp
                            <a
                                href="{{ route('admin.' . $routePrefix . '.persons.view', $contact->id) }}"
                                class="flex items-center gap-3 rounded-md p-2 transition hover:bg-gray-50 dark:hover:bg-gray-800"
                            >
                                <x-admin::avatar :name="$contactName" class="h-8 w-8 flex-shrink-0" />
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $contactNameLimited }}
                                    </p>
                                    <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                        {{ $contactEmail }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if ($contactsCount > 3)
                        <div class="border-t border-gray-200 pt-3 text-center dark:border-gray-700">
                            <a
                                href="{{ route('admin.' . $routePrefix . '.persons.index') }}?organization_id={{ $organization->id }}"
                                class="text-xs font-semibold text-brandColor hover:underline"
                            >
                                View All {{ $contactPluralLabel }}
                            </a>
                        </div>
                    @endif
                @else
                    <p class="rounded-md bg-gray-50 p-3 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                        No {{ strtolower($contactPluralLabel) }} yet. Add one to manage people from this {{ strtolower($organizationLabel) }}.
                    </p>
                @endif
            </div>

              <!-- Cases card -->
            @php
                $casesQuery = \Webkul\Lead\Models\Lead::query()
                    ->with(['organization', 'person'])
                    ->where(function ($query) use ($organization) {
                        $query->where('organization_id', $organization->id)
                            ->orWhereHas('person', function ($personQuery) use ($organization) {
                                $personQuery->where('organization_id', $organization->id);
                            });
                    })
                    ->latest();

                $casesCount = $casesQuery->count();
                $recentCases = $casesQuery->take(3)->get();
            @endphp

            <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-base font-bold text-gray-900 dark:text-white">
                        Cases
                        @if($casesCount)
                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ $casesCount }})</span>
                        @endif
                    </p>

                    <a
                        href="{{ route('admin.leads.create', ['organization_id' => $organization->id]) }}"
                        class="inline-flex items-center gap-1 rounded bg-brandColor px-2.5 py-1 text-xs font-semibold text-white hover:bg-brandColor/90"
                    >
                        + New
                    </a>
                </div>

                @if ($casesCount)
                    <div class="flex flex-col gap-2.5 border-t border-gray-200 pt-3 dark:border-gray-700">
                        @foreach ($recentCases as $case)
                            @php
                                $casePersonName = $case->person
                                    ? ($case->person->name ?? trim(($case->person->first_name ?? '') . ' ' . ($case->person->last_name ?? '')))
                                    : null;
                            @endphp
                            <div class="flex items-start justify-between gap-3 rounded-md p-2 transition hover:bg-gray-50 dark:hover:bg-gray-800" data-case-row="{{ $case->id }}">
                                <div class="min-w-0 flex-1">
                                    <a
                                        href="{{ route('admin.leads.view', $case->id) }}"
                                        class="block text-sm font-medium text-brandColor hover:underline"
                                    >
                                        {{ \Illuminate\Support\Str::limit($case->title, 35) }}
                                    </a>

                                    @if ($casePersonName)
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $casePersonName }}
                                        </p>
                                    @endif
                                </div>

                                <x-admin::dropdown position="bottom-right">
                                    <x-slot:toggle>
                                        <button class="icon-more flex h-7 w-7 cursor-pointer items-center justify-center rounded-md text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800" type="button"></button>
                                    </x-slot>

                                    <x-slot:menu class="!min-w-40">
                                        <form
                                            method="POST"
                                            action="{{ route('admin.leads.delete', $case->id) }}"
                                            class="block w-full case-delete-form"
                                            data-case-id="{{ $case->id }}"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="button"
                                                class="case-delete-btn flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                                                onclick="window.handleCaseDelete(event, this)"
                                            >
                                                <span class="icon-delete text-lg"></span>
                                                Delete
                                            </button>
                                        </form>
                                    </x-slot>
                                </x-admin::dropdown>
                            </div>
                        @endforeach
                    </div>

                    @if ($casesCount > 3)
                        <div class="border-t border-gray-200 pt-3 text-center dark:border-gray-700">
                            <a
                                href="{{ route('admin.leads.index', ['organization_id' => $organization->id]) }}"
                                class="text-xs font-semibold text-brandColor hover:underline"
                            >
                                View All Cases
                            </a>
                        </div>
                    @endif
                @else
                    <p class="rounded-md bg-gray-50 p-3 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                        Track support cases and issues here. Start by creating your first case.
                    </p>
                @endif
            </div>

            <!-- Files card -->
            @php
                $filesQuery = \Webkul\Activity\Models\Activity::query()
                    ->where('type', 'file')
                    ->where('entity_type', 'organizations')
                    ->where('entity_id', $organization->id)
                    ->with('files')
                    ->latest();

                $filesCount = $filesQuery->count();

                $recentFiles = $filesQuery
                    ->take(7)
                    ->get()
                    ->flatMap(fn ($activity) => $activity->files)
                    ->take(7);
            @endphp

            <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-base font-bold text-gray-900 dark:text-white">
                        Files
                        @if($filesCount)
                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ $filesCount }})</span>
                        @endif
                    </p>
                </div>

                @if ($filesCount)
                    <div class="grid gap-3 border-t border-gray-200 pt-3 dark:border-gray-700 sm:grid-cols-2 lg:grid-cols-1">
                        @foreach ($recentFiles as $file)
                            @php
                                $filePath = $file->url ?? asset('storage/' . $file->path);
                                $fileName = $file->name ?? $file->original_name;
                                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp

                            <div class="flex flex-col gap-2 rounded-md border border-gray-200 p-2 dark:border-gray-700">
                                @if ($isImage)
                                    <a href="{{ $filePath }}" target="_blank" class="relative overflow-hidden rounded-md bg-gray-100 dark:bg-gray-800">
                                        <img
                                            src="{{ $filePath }}"
                                            alt="{{ $fileName }}"
                                            class="h-24 w-full object-cover transition hover:opacity-75"
                                        />
                                    </a>
                                @else
                                    <div class="flex items-center justify-center rounded-md bg-gray-100 py-6 dark:bg-gray-800">
                                        <i class="icon-document text-3xl text-gray-400"></i>
                                    </div>
                                @endif

                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <a
                                            href="{{ $filePath }}"
                                            target="_blank"
                                            class="block truncate text-xs font-medium text-brandColor hover:underline"
                                            title="{{ $fileName }}"
                                        >
                                            {{ $fileName }}
                                        </a>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ core()->formatDate($file->created_at) }}
                                        </p>
                                    </div>

                                    <form
                                        action="{{ route('admin.' . $routePrefix . '.organizations.files.delete', $file->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Delete this file?');"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="flex-shrink-0 text-gray-400 hover:text-red-500 dark:hover:text-red-400"
                                            title="Delete file"
                                        >
                                            <i class="icon-delete text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($filesCount >= 7)
                        <div class="border-t border-gray-200 pt-3 text-center dark:border-gray-700">
                            <a
                                href="{{ route('admin.activities.index', ['entity_type' => 'organizations', 'entity_id' => $organization->id]) }}"
                                class="text-xs font-semibold text-brandColor hover:underline"
                            >
                                View All Files
                            </a>
                        </div>
                    @endif
                @else
                    <p class="rounded-md bg-gray-50 p-3 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                        No files yet. Upload proposals, contracts, and documents here.
                    </p>
                @endif
            </div>

            @foreach ($documentSections as $section)
                <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-base font-bold text-gray-900 dark:text-white">
                            {{ $section['title'] }}
                            @if($section['count'])
                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ $section['count'] }})</span>
                            @endif
                        </p>
                    </div>

                    @if ($section['count'])
                        <div class="flex flex-col gap-2.5 border-t border-gray-200 pt-3 dark:border-gray-700">
                            @foreach ($section['records'] as $record)
                                <a
                                    href="{{ $record['url'] }}"
                                    class="flex items-start gap-3 rounded-md p-2 transition hover:bg-gray-50 dark:hover:bg-gray-800"
                                >
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-brandColor hover:underline">
                                            {{ \Illuminate\Support\Str::limit($record['title'], 35) }}
                                        </p>

                                        @if ($record['meta'])
                                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                                {{ $record['meta'] }}
                                            </p>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-200 pt-3 text-center dark:border-gray-700">
                            <a
                                href="{{ $section['all_url'] }}"
                                class="text-xs font-semibold text-brandColor hover:underline"
                            >
                                View All {{ $section['title'] }}
                            </a>
                        </div>
                    @else
                        <p class="rounded-md bg-gray-50 p-3 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                            {{ $section['empty_message'] }}
                        </p>
                    @endif
                </div>
            @endforeach

          
        </div>
    </div>
</x-admin::layouts>
