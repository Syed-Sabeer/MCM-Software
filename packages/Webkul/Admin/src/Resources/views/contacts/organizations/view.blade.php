<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.contacts.organizations.view.title', ['name' => $organization->name])
    </x-slot>

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

                <div class="flex flex-col gap-0.5 text-sm dark:text-white">
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

                <div class="flex flex-col gap-0.5 text-sm dark:text-white">
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
            <!-- Activity toolbar similar to Salesforce -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <!-- Activity action buttons -->
                    <div class="flex flex-wrap gap-2">
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

                    <!-- Simple filter / toggle -->
                    <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                        <span>Only show activities with insights</span>

                        <button
                            type="button"
                            class="relative inline-flex h-5 w-9 items-center rounded-full bg-gray-300 transition dark:bg-gray-700"
                        >
                            <span class="inline-block h-4 w-4 translate-x-1 transform rounded-full bg-white shadow"></span>
                        </button>
                    </div>
                </div>

                <!-- Upcoming & Overdue placeholder -->
                <div class="rounded-md border border-dashed border-gray-200 bg-gray-50 p-4 text-xs text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    <p class="mb-1 font-semibold text-gray-700 dark:text-gray-100">
                        Upcoming &amp; Overdue
                    </p>

                    <p>No activities to show.</p>
                    <p>Get started by sending an email, scheduling a task, and more.</p>
                </div>

                <div class="mt-4 flex justify-center">
                    <a
                        href="{{ route('admin.activities.index', ['entity_type' => 'organizations', 'entity_id' => $organization->id]) }}"
                        class="primary-button"
                    >
                        Show All Activities
                    </a>
                </div>
            </div>
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
            <div class="flex flex-col gap-2 rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex items-center justify-between">
                    <p class="text-base font-semibold dark:text-white">
                        Contacts
                    </p>

                    <a
                        href="{{ route('admin.contacts.persons.create', ['organization_id' => $organization->id]) }}"
                        class="text-xs font-semibold text-brandColor hover:underline"
                    >
                        + New Contact
                    </a>
                </div>

                @php
                    $recentContacts = $organization->persons()->latest()->limit(3)->get();
                @endphp

                @if ($recentContacts->count() > 0)
                    <div class="flex flex-col gap-2">
                        @foreach ($recentContacts as $contact)
                            <a
                                href="{{ route('admin.contacts.persons.view', $contact->id) }}"
                                class="flex items-center gap-2 rounded px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-800"
                            >
                                <x-admin::avatar :name="$contact->first_name . ' ' . $contact->last_name" class="h-6 w-6" />
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium truncate dark:text-white">
                                        {{ $contact->first_name }} {{ $contact->last_name }}
                                    </p>
                                    @if ($contact->email)
                                        <p class="text-xs text-gray-500 truncate dark:text-gray-400">
                                            {{ $contact->email }}
                                        </p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <a
                        href="{{ route('admin.contacts.persons.index', ['organization_id' => $organization->id]) }}"
                        class="text-xs font-semibold text-brandColor hover:underline"
                    >
                        View All
                    </a>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Contacts list for this organization can be added and managed here.
                    </p>
                @endif
            </div>

            <!-- Opportunities card (placeholder) -->
            <div class="flex flex-col gap-2 rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex items-center justify-between">
                    <p class="text-base font-semibold dark:text-white">
                        Opportunities
                    </p>

                    <button type="button" class="text-xs font-semibold text-brandColor hover:underline">
                        + New Opportunity
                    </button>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    No opportunities yet. Create one to track potential deals for this company.
                </p>
            </div>

            <!-- Cases card (placeholder) -->
            @php
                $casesQuery = \Webkul\Lead\Models\Lead::query()
                    ->whereHas('person', function ($query) use ($organization) {
                        $query->where('organization_id', $organization->id);
                    })
                    ->latest();

                $casesCount = $casesQuery->count();
                $recentCases = $casesQuery->take(3)->get();
            @endphp

            <div class="flex flex-col gap-2 rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex items-center justify-between">
                    <p class="text-base font-semibold dark:text-white">
                        Cases @if($casesCount) ({{ $casesCount }}) @endif
                    </p>

                    <a
                        href="{{ route('admin.leads.create', ['organization_id' => $organization->id]) }}"
                        class="text-xs font-semibold text-brandColor hover:underline"
                    >
                        + New Case
                    </a>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Track support cases and issues related to this company here.
                </p>

                @if ($casesCount)
                    <div class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                        <p class="mb-1 font-semibold">
                            Recently added cases
                        </p>

                        <ul class="flex flex-col gap-1">
                            @foreach ($recentCases as $case)
                                <li class="flex items-center justify-between gap-2">
                                    <div class="flex flex-col">
                                        <a
                                            href="{{ route('admin.leads.view', $case->id) }}"
                                            class="text-brandColor hover:underline"
                                        >
                                            {{ $case->title }}
                                        </a>

                                        @if ($case->person)
                                            <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                                {{ $case->person->name ?? trim(($case->person->first_name ?? '') . ' ' . ($case->person->last_name ?? '')) }}
                                            </span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-2 text-center">
                        <a
                            href="{{ route('admin.leads.index', ['organization_id' => $organization->id]) }}"
                            class="text-xs font-semibold text-brandColor hover:underline"
                        >
                            View All
                        </a>
                    </div>
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
                    ->take(3)
                    ->get()
                    ->flatMap(fn ($activity) => $activity->files)
                    ->take(3);
            @endphp

            <div class="flex flex-col gap-2 rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex items-center justify-between">
                    <p class="text-base font-semibold dark:text-white">
                        Files @if($filesCount) ({{ $filesCount }}) @endif
                    </p>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Attach proposals, contracts, and other documents related to this company.
                </p>

                <!-- Upload modal (plain HTML so it always works) -->
                <div
                    id="organization-files-modal"
                    class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/50"
                    role="dialog"
                    aria-modal="true"
                >
                    <div class="max-h-[90vh] w-full max-w-md overflow-auto rounded-lg border border-gray-200 bg-white p-4 shadow-xl dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-base font-semibold dark:text-white">Upload File</h3>
                            <button
                                type="button"
                                class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                                onclick="document.getElementById('organization-files-modal').classList.add('hidden')"
                            >
                                &times;
                            </button>
                        </div>

                        <form
                            action="{{ route('admin.contacts.organizations.files.store', $organization->id) }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="flex flex-col gap-4"
                        >
                            @csrf

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Title</x-admin::form.control-group.label>
                                <input
                                    type="text"
                                    name="title"
                                    value="{{ old('title') }}"
                                    class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Description</x-admin::form.control-group.label>
                                <textarea
                                    name="description"
                                    class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                    rows="3"
                                >{{ old('description') }}</textarea>
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">File</x-admin::form.control-group.label>
                                <input
                                    type="file"
                                    name="files[]"
                                    multiple
                                    required
                                    class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                />
                                <x-admin::form.control-group.error control-name="files.0" />
                            </x-admin::form.control-group>

                            <div class="flex gap-2">
                                <button type="submit" class="primary-button">Upload</button>
                                <button
                                    type="button"
                                    class="secondary-button"
                                    onclick="document.getElementById('organization-files-modal').classList.add('hidden')"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                @if ($filesCount)
                    <div class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                        <p class="mb-1 font-semibold">
                            Recently added files
                        </p>

                        <ul class="flex flex-col gap-1">
                            @foreach ($recentFiles as $file)
                                <li class="flex items-center justify-between gap-2">
                                    <div class="flex flex-col">
                                        <a
                                            href="{{ $file->url ?? asset('storage/' . $file->path) }}"
                                            target="_blank"
                                            class="text-brandColor hover:underline"
                                        >
                                            {{ $file->name ?? $file->original_name }}
                                        </a>

                                        <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                            {{ core()->formatDate($file->created_at) }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-2 text-center">
                        <a
                            href="{{ route('admin.activities.index', ['entity_type' => 'organizations', 'entity_id' => $organization->id]) }}"
                            class="text-xs font-semibold text-brandColor hover:underline"
                        >
                            View All
                        </a>
                    </div>
                @else
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        No files uploaded yet.
                    </p>
                @endif
            </div>
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
