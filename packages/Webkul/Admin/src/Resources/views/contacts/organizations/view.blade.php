<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.contacts.organizations.view.title', ['name' => $organization->name])
    </x-slot>

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
                        name="contacts.organizations.view"
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
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Account Name</p>
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
                            'factory'   => 'Factory',
                            'customer'  => 'Customer',
                            'vendor'    => 'Vendor',
                            'marketing' => 'Marketing',
                            'prospect'  => 'Prospect',
                            'salesrep'  => 'Sales Representative',
                            'other'     => 'Other',
                        ];

                        $industryLabels = [
                            'home_apparel'   => 'Home Apparel',
                            'other_shipping' => 'Other Shipping',
                        ];
                    @endphp

                    @if ($organization->type)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Type</p>
                            <p>{{ $typeLabels[$organization->type] ?? ucfirst(str_replace('_', ' ', $organization->type)) }}</p>
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
                            {{ trim(($organization->billing_postcode ?? '') . ' ' . ($organization->billing_city ?? '')) }}
                        </span>
                    @endif

                    @if ($organization->billing_state)
                        <span>{{ $organization->billing_state }}</span>
                    @endif

                    @if ($organization->billing_country)
                        <span>{{ $organization->billing_country }}</span>
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
                            {{ trim(($organization->shipping_postcode ?? '') . ' ' . ($organization->shipping_city ?? '')) }}
                        </span>
                    @endif

                    @if ($organization->shipping_state)
                        <span>{{ $organization->shipping_state }}</span>
                    @endif

                    @if ($organization->shipping_country)
                        <span>{{ $organization->shipping_country }}</span>
                    @endif
                </div>
            </div>
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
                :endpoint="route('admin.contacts.organizations.activities.index', $organization->id)"
            />

            {!! view_render_event('admin.contacts.organizations.view.activities.after', ['organization' => $organization]) !!}
        </div>

        <!-- Right Panel: Actions & Related Lists -->
        <div class="flex min-w-[320px] max-w-[340px] flex-col gap-4">
            <!-- Header actions similar to Salesforce: New Contact, Edit, Delete -->
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Company
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
                        href="{{ route('admin.contacts.organizations.edit', $organization->id) }}"
                        class="secondary-button"
                    >
                        @lang('admin::app.contacts.organizations.view.edit-btn')
                    </a>

                    <!-- Delete -->
                    <form
                        method="POST"
                        action="{{ route('admin.contacts.organizations.delete', $organization->id) }}"
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
            @php
                $contactsQuery = $organization->persons()->latest();
                $contactsCount = $contactsQuery->count();
                $recentContacts = $contactsQuery->take(3)->get();
            @endphp

            <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-base font-bold text-gray-900 dark:text-white">
                        Contacts
                        @if ($contactsCount)
                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ $contactsCount }})</span>
                        @endif
                    </p>

                    <a
                        href="{{ route('admin.contacts.persons.create', ['organization_id' => $organization->id]) }}"
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
                            @endphp
                            <a
                                href="{{ route('admin.contacts.persons.view', $contact->id) }}"
                                class="flex items-center gap-3 rounded-md p-2 transition hover:bg-gray-50 dark:hover:bg-gray-800"
                            >
                                <x-admin::avatar :name="$contactName" class="h-8 w-8 flex-shrink-0" />
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $contactNameLimited }}
                                    </p>
                                    @if ($contact->email)
                                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                            {{ $contact->email }}
                                        </p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if ($contactsCount > 3)
                        <div class="border-t border-gray-200 pt-3 text-center dark:border-gray-700">
                            <a
                                href="{{ route('admin.contacts.persons.index') }}?organization_id={{ $organization->id }}"
                                class="text-xs font-semibold text-brandColor hover:underline"
                            >
                                View All Contacts
                            </a>
                        </div>
                    @endif
                @else
                    <p class="rounded-md bg-gray-50 p-3 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                        No contacts yet. Add one to manage people from this organization.
                    </p>
                @endif
            </div>

            <!-- Opportunities card -->
            <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-base font-bold text-gray-900 dark:text-white">
                        Opportunities
                    </p>

                    <button type="button" class="inline-flex items-center gap-1 rounded bg-brandColor px-2.5 py-1 text-xs font-semibold text-white hover:bg-brandColor/90">
                        + New
                    </button>
                </div>

                <p class="rounded-md bg-gray-50 p-3 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                    No opportunities yet. Create one to track potential deals.
                </p>
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
                            <div class="flex items-start justify-between gap-3 rounded-md p-2 transition hover:bg-gray-50 dark:hover:bg-gray-800" data-case-row="{{ $case->id }}">
                                <div class="min-w-0 flex-1">
                                    <a
                                        href="{{ route('admin.leads.view', $case->id) }}"
                                        class="block text-sm font-medium text-brandColor hover:underline"
                                    >
                                        {{ \Illuminate\Support\Str::limit($case->title, 35) }}
                                    </a>

                                    @if ($case->person)
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $case->person->name ?? trim(($case->person->first_name ?? '') . ' ' . ($case->person->last_name ?? '')) }}
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
            {{-- @php
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
                                        action="{{ route('admin.contacts.organizations.files.delete', $file->id) }}"
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
            </div> --}}
        </div>
    </div>
</x-admin::layouts>

@pushOnce('scripts')
    <script type="text/x-template" id="v-organization-files-template">
        <div>
            <button
                type="button"
                class="text-xs font-semibold text-brandColor hover:underline"
                @click="emitOpen"
            >
                Upload Files
            </button>

            <Teleport to="body">
                <x-admin::form
                    v-slot="{ handleSubmit }"
                    as="div"
                >
                    <form @submit="handleSubmit($event, save)">
                        <x-admin::modal
                            ref="filesModal"
                            position="bottom-right"
                        >
                            <x-slot:header>
                                <h3 class="text-base font-semibold dark:text-white">
                                    Files
                                </h3>
                            </x-slot>

                            <x-slot:content>
                                <div class="flex flex-col gap-4">
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label>
                                            Title
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="title"
                                            v-model="form.title"
                                        />
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label>
                                            Description
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="textarea"
                                            name="description"
                                            v-model="form.description"
                                        />
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            File
                                        </x-admin::form.control-group.label>

                                        <input
                                            type="file"
                                            name="files[]"
                                            multiple
                                            class="w-full text-sm text-gray-600 dark:text-gray-300"
                                            @change="handleFilesChange"
                                        >
                                    </x-admin::form.control-group>
                                </div>
                            </x-slot>

                            <x-slot:footer>
                                <x-admin::button
                                    class="primary-button"
                                    title="Upload"
                                    ::loading="isUploading"
                                    ::disabled="isUploading"
                                />
                            </x-slot>
                        </x-admin::modal>
                    </form>
                </x-admin::form>
            </Teleport>
        </div>
    </script>

    <script type="module">
        app.component('v-organization-files', {
            template: '#v-organization-files-template',

            props: {
                organizationId: {
                    type: Number,
                    required: true,
                },

                uploadUrl: {
                    type: String,
                    required: true,
                },
            },

            data() {
                return {
                    isUploading: false,
                    form: {
                        title: '',
                        description: '',
                        files: [],
                    },
                };
            },

            methods: {
                emitOpen() {
                    window.dispatchEvent(new Event('open-organization-files'));
                },

                openModal() {
                    if (this.$refs.filesModal && typeof this.$refs.filesModal.open === 'function') {
                        this.$refs.filesModal.open();
                        return;
                    }

                    this.$nextTick(() => {
                        if (this.$refs.filesModal && typeof this.$refs.filesModal.open === 'function') {
                            this.$refs.filesModal.open();
                        }
                    });
                },

                handleFilesChange(event) {
                    this.form.files = Array.from(event.target.files || []);
                },

                async save(params, { setErrors }) {
                    if (! this.form.files.length) {
                        setErrors({ 'files': ['Please select at least one file.'] });

                        return;
                    }

                    this.isUploading = true;

                    const formData = new FormData();

                    if (this.form.title) {
                        formData.append('title', this.form.title);
                    }

                    if (this.form.description) {
                        formData.append('description', this.form.description);
                    }

                    this.form.files.forEach((file) => {
                        formData.append('files[]', file);
                    });

                    try {
                        await this.$axios.post(this.uploadUrl, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            },
                        });

                        this.$emitter.emit('add-flash', {
                            type: 'success',
                            message: '{{ trans('admin::app.contacts.organizations.view.files-uploaded') }}',
                        });

                        window.location.reload();
                    } catch (error) {
                        if (error.response && error.response.status === 422) {
                            setErrors(error.response.data.errors);
                        } else {
                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: error.response?.data?.message || 'Upload failed.',
                            });
                        }
                    } finally {
                        this.isUploading = false;
                        this.$refs.filesModal.close();
                    }
                },
            },
            mounted() {
                this._openFilesListener = () => this.openModal();
                window.addEventListener('open-organization-files', this._openFilesListener);
            },

            beforeUnmount() {
                window.removeEventListener('open-organization-files', this._openFilesListener);
            },
        });
    </script>
@endPushOnce
