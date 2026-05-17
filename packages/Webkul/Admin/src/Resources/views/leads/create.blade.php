<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.leads.create.title')
    </x-slot>

    {!! view_render_event('admin.leads.create.form.before') !!}

    <!-- Create Lead Form -->
    <x-admin::form :action="route('admin.leads.store')">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs name="leads.create" />

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.leads.create.title')
                    </div>
                </div>

                {!! view_render_event('admin.leads.create.save_button.before') !!}

                <div class="flex items-center gap-x-2.5">
                    <!-- Save button for person -->
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.leads.create.form_buttons.before') !!}

                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.leads.create.save-btn')
                        </button>

                        {!! view_render_event('admin.leads.create.form_buttons.after') !!}
                    </div>
                </div>

                {!! view_render_event('admin.leads.create.save_button.after') !!}
            </div>

            @if (request('stage_id'))
                <input
                    type="hidden"
                    id="lead_pipeline_stage_id"
                    name="lead_pipeline_stage_id"
                    value="{{ request('stage_id') }}"
                />
            @endif

            @if (request('pipeline_id'))
                <input
                    type="hidden"
                    id="lead_pipeline_id"
                    name="lead_pipeline_id"
                    value="{{ request('pipeline_id') }}"
                />
            @endif

            @if (request('organization_id'))
                <input
                    type="hidden"
                    id="lead_organization_id"
                    name="organization_id"
                    value="{{ request('organization_id') }}"
                />
            @endif

            @if (request('person_id'))
                <input
                    type="hidden"
                    id="lead_person_id"
                    name="person_id"
                    value="{{ request('person_id') }}"
                />
            @endif

            @php
                $leadCreateInitialContact = $person
                    ? [
                        'id'                => $person->id,
                        'name'              => trim(($person->first_name ?? '').' '.($person->last_name ?? '')) ?: $person->name,
                        'organization_id'   => $person->organization_id,
                        'organization_name' => $person?->organization?->name,
                    ]
                    : null;
            @endphp

            <!-- Lead Create Component -->
            <v-lead-create
                :prefill-organization-id="{{ request('organization_id') ?? 'null' }}"
                prefill-organization-name="{{ $organization->name ?? '' }}"
                :initial-contact='@json($leadCreateInitialContact)'
            >
                <x-admin::shimmer.leads.datagrid />
            </v-lead-create>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.leads.create.form.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-lead-create-template"
        >
            <div class="box-shadow flex flex-col gap-4 rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                {!! view_render_event('admin.leads.edit.form_controls.before') !!}

                <div class="flex w-full gap-2 border-b border-gray-200 dark:border-gray-800">
                    <!-- Tabs -->
                    <template
                        v-for="tab in tabs"
                        :key="tab.id"
                    >
                        {!! view_render_event('admin.leads.create.tabs.before') !!}

                        <a
                            :href="'#' + tab.id"
                            :class="[
                                'inline-block px-3 py-2.5 border-b-2  text-sm font-medium ',
                                activeTab === tab.id
                                ? 'text-brandColor border-brandColor dark:brandColor dark:brandColor'
                                : 'text-gray-600 dark:text-gray-300  border-transparent hover:text-gray-800 hover:border-gray-400 dark:hover:border-gray-400  dark:hover:text-white'
                            ]"
                            @click="scrollToSection(tab.id)"
                            :text="tab.label"
                        >
                        </a>

                        {!! view_render_event('admin.leads.create.tabs.after') !!}
                    </template>
                </div>

                <div class="flex flex-col gap-4 px-4 py-2">
                    {!! view_render_event('admin.leads.create.case-information.before') !!}

                    <!-- Case Information Section -->
                    <div
                        class="flex flex-col gap-4"
                        id="case-information"
                    >
                        <div class="flex flex-col gap-1 mb-2">
                            <p class="text-base font-semibold dark:text-white">
                                Case Information
                            </p>
                        </div>

                        <div class="w-1/2 max-md:w-full grid gap-4">
                            {!! view_render_event('admin.leads.create.case-information.attributes.before') !!}

                            <!-- Case No -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    Case No
                                </x-admin::form.control-group.label>

                                <input
                                    type="text"
                                    name="case_no"
                                    value="{{ $nextCaseNo ?? '' }}"
                                    placeholder="00001"
                                    class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                />

                                <x-admin::form.control-group.error control-name="case_no" />
                            </x-admin::form.control-group>

                            <!-- Status (Pipeline Stage) -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    Status
                                </x-admin::form.control-group.label>

                                <select
                                    id="lead_pipeline_stage_id"
                                    name="lead_pipeline_stage_id"
                                    class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                >
                                    <option value="">Select Status</option>
                                    @foreach ($pipeline->stages as $stage)
                                        <option value="{{ $stage->id }}" @if(request('stage_id') == $stage->id) selected @endif>
                                            {{ $stage->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <x-admin::form.control-group.error control-name="lead_pipeline_stage_id" />
                            </x-admin::form.control-group>

                            <!-- Case Origin (Source) -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    Case Origin
                                </x-admin::form.control-group.label>

                                <select
                                    name="lead_source_id"
                                    class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                >
                                    <option value="">Select Origin</option>
                                    @foreach (app('Webkul\Lead\Repositories\SourceRepository')->all() as $source)
                                        <option value="{{ $source->id }}">
                                            {{ $source->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </x-admin::form.control-group>

                            <!-- Priority -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    Priority
                                </x-admin::form.control-group.label>

                                <select
                                    name="priority"
                                    class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                >
                                    <option value="">Select Priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </x-admin::form.control-group>

                            <!-- Case Owner (Searchable Dropdown) -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    Case Owner
                                </x-admin::form.control-group.label>

                                <div class="relative" ref="caseOwnerLookup">
                                    <div class="rounded-xl border border-blue-200 bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                        <div v-if="caseOwner.selected" class="mb-2">
                                            <span class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900/50 dark:text-blue-200">
                                                @{{ caseOwner.selected.name }}
                                                <button type="button" class="icon-cross-large text-sm" @click.stop="clearCaseOwner"></button>
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <input
                                                v-model="caseOwner.search"
                                                type="text"
                                                class="w-full border-0 bg-transparent px-1 py-1 text-sm text-gray-900 outline-none placeholder:text-gray-400 dark:text-white"
                                                placeholder="Search employees"
                                                @focus="openLookup('caseOwner')"
                                                @input="searchCaseOwners"
                                            >

                                            <button type="button" class="text-xl text-gray-500" @click="toggleLookup('caseOwner')">
                                                <span :class="caseOwner.open ? 'icon-up-arrow' : 'icon-down-arrow'"></span>
                                            </button>
                                        </div>

                                        <div v-if="caseOwner.open" class="mt-3 max-h-[156px] overflow-y-auto rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-950">
                                            <div v-if="caseOwner.loading" class="px-3 py-2 text-sm text-gray-500">Searching...</div>

                                            <template v-else-if="caseOwner.results.length">
                                                <button
                                                    v-for="user in caseOwner.results"
                                                    :key="`case-owner-option-${user.id}`"
                                                    type="button"
                                                    class="flex w-full items-start justify-between rounded-lg px-3 py-2 text-left hover:bg-blue-50 dark:hover:bg-gray-900"
                                                    @click="selectCaseOwner(user)"
                                                >
                                                    <span class="min-w-0">
                                                        <span class="block text-sm font-medium text-gray-800 dark:text-white">@{{ user.name }}</span>
                                                        <span class="block text-xs text-gray-500">@{{ user.email || 'Employee' }}</span>
                                                    </span>
                                                </button>
                                            </template>

                                            <div v-else class="px-3 py-2 text-sm text-gray-500">
                                                @{{ caseOwner.search.length < 1 ? 'Start typing to search employees.' : 'No employees found.' }}
                                            </div>
                                        </div>
                                    </div>

                                    <input
                                        type="hidden"
                                        id="user_id"
                                        name="user_id"
                                        :value="caseOwner.selected?.id || ''"
                                    >
                                </div>

                                <x-admin::form.control-group.error control-name="user_id" />
                            </x-admin::form.control-group>

                            <!-- Organization (Searchable Dropdown) -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    Organization
                                </x-admin::form.control-group.label>

                                <div class="relative" ref="organizationLookup">
                                    <div class="rounded-xl border border-blue-200 bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                        <div v-if="organization.selected" class="mb-2">
                                            <span class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900/50 dark:text-blue-200">
                                                @{{ organization.selected.name }}
                                                <button type="button" class="icon-cross-large text-sm" @click.stop="clearOrganization"></button>
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <input
                                                v-model="organization.search"
                                                type="text"
                                                class="w-full border-0 bg-transparent px-1 py-1 text-sm text-gray-900 outline-none placeholder:text-gray-400 dark:text-white"
                                                placeholder="Search company"
                                                @focus="openLookup('organization')"
                                                @input="searchOrganizations"
                                            >

                                            <button type="button" class="text-xl text-gray-500" @click="toggleLookup('organization')">
                                                <span :class="organization.open ? 'icon-up-arrow' : 'icon-down-arrow'"></span>
                                            </button>
                                        </div>

                                        <div v-if="organization.open" class="mt-3 max-h-[156px] overflow-y-auto rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-950">
                                            <div v-if="organization.loading" class="px-3 py-2 text-sm text-gray-500">Searching...</div>

                                            <template v-else-if="organization.results.length">
                                                <button
                                                    v-for="company in organization.results"
                                                    :key="`org-option-${company.id}`"
                                                    type="button"
                                                    class="flex w-full items-start justify-between rounded-lg px-3 py-2 text-left hover:bg-blue-50 dark:hover:bg-gray-900"
                                                    @click="selectOrganization(company)"
                                                >
                                                    <span class="block text-sm font-medium text-gray-800 dark:text-white">@{{ company.name }}</span>
                                                </button>
                                            </template>

                                            <div v-else class="px-3 py-2 text-sm text-gray-500">
                                                @{{ organization.search.length < 1 ? 'Start typing to search companies.' : 'No companies found.' }}
                                            </div>
                                        </div>
                                    </div>

                                    <input
                                        type="hidden"
                                        id="organization_id_select"
                                        name="organization_id"
                                        :value="organization.selected?.id || ''"
                                    >
                                </div>

                                <x-admin::form.control-group.error control-name="organization_id" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.leads.create.case-information.attributes.after') !!}
                        </div>
                    </div>

                    {!! view_render_event('admin.leads.create.case-information.after') !!}

                    {!! view_render_event('admin.leads.create.contact-information.before') !!}

                    <!-- Contact Information Section -->
                    <div
                        class="flex flex-col gap-4"
                        id="contact-information"
                    >
                        <div class="flex flex-col gap-1 mb-2">
                            <p class="text-base font-semibold dark:text-white">
                                Contact Information
                            </p>
                        </div>

                        <div class="w-1/2 max-md:w-full">
                            <!-- Contact Person Component -->
                            @include('admin::leads.common.contact')
                        </div>
                    </div>

                    {!! view_render_event('admin.leads.create.contact-information.after') !!}

                    {!! view_render_event('admin.leads.create.description-information.before') !!}

                    <!-- Description Information Section -->
                    <div
                        class="flex flex-col gap-4"
                        id="description-information"
                    >
                        <div class="flex flex-col gap-1 mb-2">
                            <p class="text-base font-semibold dark:text-white">
                                Description Information
                            </p>
                        </div>

                        <div class="w-1/2 max-md:w-full grid gap-4">
                            {!! view_render_event('admin.leads.create.description-information.attributes.before') !!}

                            <!-- Subject (Title) -->
                            <x-admin::attributes
                                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    ['code', 'IN', ['title']],
                                    'entity_type' => 'leads',
                                    'quick_add'   => 1
                                ])"
                            />

                            <!-- Description -->
                            <x-admin::attributes
                                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    ['code', 'IN', ['description']],
                                    'entity_type' => 'leads',
                                    'quick_add'   => 1
                                ])"
                            />

                            <!-- Send Notification Email Checkbox -->
                            <x-admin::form.control-group>
                                <div class="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        id="send_notification_email"
                                        name="send_notification_email"
                                        value="1"
                                        class="h-4 w-4 rounded border-gray-200 text-brandColor dark:border-gray-800"
                                    />

                                    <x-admin::form.control-group.label class="mb-0">
                                        Send notification email to contact
                                    </x-admin::form.control-group.label>
                                </div>
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.leads.create.description-information.attributes.after') !!}
                        </div>
                    </div>

                    {!! view_render_event('admin.leads.create.description-information.after') !!}

                    {{-- <!-- Product Section --> --}}
                    {{-- <div
                        class="flex flex-col gap-4"
                        id="products"
                    >
                        <div class="flex flex-col gap-1">
                            <p class="text-base font-semibold dark:text-white">
                                @lang('admin::app.leads.create.products')
                            </p>

                            <p class="text-gray-600 dark:text-white">
                                @lang('admin::app.leads.create.products-info')
                            </p>
                        </div>

                        <div>
                            <!-- Product Component -->
                            @include('admin::leads.common.products')
                        </div>
                    </div> --}}
                </div>

                {!! view_render_event('admin.leads.form_controls.after') !!}
            </div>
        </script>

        <script type="module">
            app.component('v-lead-create', {
                template: '#v-lead-create-template',

                props: ['prefillOrganizationId', 'prefillOrganizationName', 'initialContact'],

                data() {
                    return {
                        activeTab: 'case-information',

                        tabs: [
                            { id: 'case-information', label: 'Case Information' },
                            { id: 'contact-information', label: 'Contact Information' },
                            { id: 'description-information', label: 'Description Information' },
                        ],

                        selectedOrganizationName: this.prefillOrganizationName || '',
                        selectedOrganizationId: this.prefillOrganizationId || '',
                        initialContacts: this.initialContact ? [this.initialContact] : [],
                        
                        caseOwner: {
                            selected: null,
                            search: '',
                            results: [],
                            loading: false,
                            open: false,
                        },
                        
                        organization: {
                            selected: null,
                            search: '',
                            results: [],
                            loading: false,
                            open: false,
                        },
                        
                        searchRequestTimeout: null,
                    };
                },

                mounted() {
                    // Initialize case owner with current auth user
                    this.$axios.get('{{ route('admin.activities.search_employee_users') }}', {
                        params: { query: '' }
                    }).then(response => {
                        const employees = response.data.data || [];
                        const currentUser = employees.find(emp => Number(emp.id) === Number('{{ auth()->id() }}'));
                        if (currentUser) {
                            this.caseOwner.selected = currentUser;
                        }
                    });

                    // Initialize organization if prefilled
                    if (this.prefillOrganizationId) {
                        this.$axios.get('{{ route('admin.contacts.organizations.fetch', ['id' => '__ID__']) }}'.replace('__ID__', this.prefillOrganizationId))
                            .then(response => {
                                if (response.data && response.data.id) {
                                    this.organization.selected = {
                                        id: response.data.id,
                                        name: response.data.name
                                    };
                                }
                            });
                    }

                    // Add form submission handler to auto-populate status if not selected
                    const form = document.querySelector('form[action*="create"]');
                    if (form) {
                        form.addEventListener('submit', (e) => {
                            const statusSelect = document.getElementById('lead_pipeline_stage_id');
                            if (!statusSelect.value) {
                                const firstOption = statusSelect.querySelector('option:not([value=""])');
                                if (firstOption) {
                                    statusSelect.value = firstOption.value;
                                }
                            }
                        });
                    }
                },

                methods: {
                    /**
                     * Toggle lookup dropdown.
                     */
                    toggleLookup(type) {
                        this[type].open = !this[type].open;
                        if (this[type].open) {
                            this.dispatchSearch(type);
                        }
                    },

                    /**
                     * Open lookup dropdown.
                     */
                    openLookup(type) {
                        this[type].open = true;
                        this.dispatchSearch(type);
                    },

                    /**
                     * Dispatch search based on type.
                     */
                    dispatchSearch(type) {
                        if (type === 'caseOwner') {
                            this.searchCaseOwners();
                        }
                        if (type === 'organization') {
                            this.searchOrganizations();
                        }
                    },

                    /**
                     * Run search with debounce.
                     */
                    runSearch(callback) {
                        clearTimeout(this.searchRequestTimeout);
                        this.searchRequestTimeout = setTimeout(callback, 200);
                    },

                    /**
                     * Search case owners (employees).
                     */
                    searchCaseOwners() {
                        this.runSearch(() => {
                            this.caseOwner.loading = true;

                            this.$axios.get('{{ route('admin.activities.search_employee_users') }}', {
                                params: {
                                    query: this.caseOwner.search,
                                },
                            }).then((response) => {
                                const selectedId = this.caseOwner.selected?.id;
                                this.caseOwner.results = (response.data.data || [])
                                    .filter(item => !selectedId || Number(item.id) !== Number(selectedId));
                                this.caseOwner.loading = false;
                            }).catch(() => {
                                this.caseOwner.results = [];
                                this.caseOwner.loading = false;
                            });
                        });
                    },

                    /**
                     * Search organizations.
                     */
                    searchOrganizations() {
                        this.runSearch(() => {
                            this.organization.loading = true;

                            this.$axios.get('{{ route('admin.activities.search_organizations') }}', {
                                params: {
                                    query: this.organization.search,
                                },
                            }).then((response) => {
                                const selectedId = this.organization.selected?.id;
                                const results = response.data.data || [];
                                this.organization.results = results
                                    .filter(item => !selectedId || Number(item.id) !== Number(selectedId));
                                this.organization.loading = false;
                            }).catch(() => {
                                this.organization.results = [];
                                this.organization.loading = false;
                            });
                        });
                    },

                    /**
                     * Select case owner.
                     */
                    selectCaseOwner(user) {
                        this.caseOwner.selected = user;
                        this.caseOwner.search = '';
                        this.caseOwner.results = [];
                        this.caseOwner.open = false;
                    },

                    /**
                     * Clear case owner.
                     */
                    clearCaseOwner() {
                        this.caseOwner.selected = null;
                    },

                    /**
                     * Select organization.
                     */
                    selectOrganization(company) {
                        this.organization.selected = company;
                        this.organization.search = '';
                        this.organization.results = [];
                        this.organization.open = false;
                    },

                    /**
                     * Clear organization.
                     */
                    clearOrganization() {
                        this.organization.selected = null;
                    },

                    /**
                     * Handle organization selection from contact component.
                     *
                     * @param {Object} organization
                     *
                     * @returns {void}
                     */
                    handleOrganizationSelected(organization) {
                        if (organization && organization.name) {
                            this.selectedOrganizationName = organization.name;
                            if (organization.id) {
                                this.selectedOrganizationId = organization.id;
                                this.organization.selected = organization;
                            }
                        }
                    },

                    /**
                     * Scroll to the section.
                     *
                     * @param {String} tabId
                     *
                     * @returns {void}
                     */
                    scrollToSection(tabId) {
                        const section = document.getElementById(tabId);

                        if (section) {
                            section.scrollIntoView({ behavior: 'smooth' });
                        }
                    },
                },
            });
        </script>
    @endPushOnce

    @pushOnce('styles')
        <style>
            html {
                scroll-behavior: smooth;
            }
        </style>
    @endPushOnce
</x-admin::layouts>
