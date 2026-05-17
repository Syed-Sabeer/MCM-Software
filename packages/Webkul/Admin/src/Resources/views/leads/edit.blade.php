<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.leads.edit.title')
    </x-slot>

    {!! view_render_event('admin.leads.edit.form_controls.before', ['lead' => $lead]) !!}

    <!-- Edit Lead Form -->
    <x-admin::form
        :action="route('admin.leads.update', $lead->id)"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs
                        name="leads.edit"
                        :entity="$lead"
                    />

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.leads.edit.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('admin.leads.edit.save_button.before', ['lead' => $lead]) !!}

                    <!-- Save button for Editing Lead -->
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.leads.edit.form_buttons.before') !!}

                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.leads.edit.save-btn')
                        </button>

                        {!! view_render_event('admin.leads.edit.form_buttons.after') !!}
                    </div>

                    {!! view_render_event('admin.leads.edit.save_button.after', ['lead' => $lead]) !!}
                </div>
            </div>

            <!-- Lead Edit Component -->
            <v-lead-edit :lead="{{ json_encode($lead) }}">
                <x-admin::shimmer.leads.datagrid />
            </v-lead-edit>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.leads.edit.form_controls.after', ['lead' => $lead]) !!}

    @php
        $leadEditOrganizationId = $lead->person?->organization_id ?? $lead->organization_id ?? null;

        $leadEditOrganizationName = $lead->person?->organization?->name ?? $lead->organization?->name ?? '';

        $leadEditOwner = $lead->user
            ? [
                'id'    => $lead->user->id,
                'name'  => $lead->user->name,
                'email' => $lead->user->email,
            ]
            : null;

        $leadEditInitialContacts = $lead->persons->isNotEmpty()
            ? $lead->persons->map(fn ($person) => [
                'id'                => $person->id,
                'name'              => trim(($person->first_name ?? '').' '.($person->last_name ?? '')) ?: $person->name,
                'organization_id'   => $person->organization_id,
                'organization_name' => $person?->organization?->name,
            ])->values()->all()
            : ($lead->person
                ? [[
                'id'                => $lead->person->id,
                'name'              => trim(($lead->person->first_name ?? '').' '.($lead->person->last_name ?? '')) ?: $lead->person->name,
                'organization_id'   => $lead->person->organization_id,
                'organization_name' => $lead->person?->organization?->name,
                ]]
                : []);

        $leadEditInitialContact = $leadEditInitialContacts[0] ?? null;
    @endphp

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-lead-edit-template"
        >
            <div class="box-shadow flex flex-col gap-4 rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="flex gap-2 border-b border-gray-200 dark:border-gray-800">
                    <!-- Tabs -->
                    <template v-for="tab in tabs" :key="tab.id">
                        {!! view_render_event('admin.leads.edit.tabs.before', ['lead' => $lead]) !!}

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
                        ></a>

                        {!! view_render_event('admin.leads.edit.tabs.after', ['lead' => $lead]) !!}
                    </template>
                </div>

                <div class="flex flex-col gap-4 px-4 py-2">
                    {!! view_render_event('admin.leads.edit.lead_details.before', ['lead' => $lead]) !!}

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
                            {!! view_render_event('admin.leads.edit.lead_details.attributes.before', ['lead' => $lead]) !!}

                            <!-- Case No -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    Case No
                                </x-admin::form.control-group.label>

                                <input
                                    type="text"
                                    name="case_no"
                                    value="{{ $lead->case_no ?? '' }}"
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
                                        <option value="{{ $stage->id }}" @selected($lead->lead_pipeline_stage_id == $stage->id)>
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
                                        <option value="{{ $source->id }}" @selected($lead->lead_source_id == $source->id)>
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
                                    @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $value => $label)
                                        <option value="{{ $value }}" @selected($lead->priority === $value)>{{ $label }}</option>
                                    @endforeach
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

                            {!! view_render_event('admin.leads.edit.lead_details.attributes.after', ['lead' => $lead]) !!}
                        </div>
                    </div>

                    {!! view_render_event('admin.leads.edit.lead_details.after', ['lead' => $lead]) !!}

                    {!! view_render_event('admin.leads.edit.contact_person.before', ['lead' => $lead]) !!}

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

                    {!! view_render_event('admin.leads.edit.contact_person.after', ['lead' => $lead]) !!}

                    {!! view_render_event('admin.leads.edit.description_information.before', ['lead' => $lead]) !!}

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
                            <x-admin::attributes
                                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    ['code', 'IN', ['title']],
                                    'entity_type' => 'leads',
                                    'quick_add'   => 1
                                ])"
                                :entity="$lead"
                            />

                            <x-admin::attributes
                                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    ['code', 'IN', ['description']],
                                    'entity_type' => 'leads',
                                    'quick_add'   => 1
                                ])"
                                :entity="$lead"
                            />
                        </div>
                    </div>

                    {!! view_render_event('admin.leads.edit.description_information.after', ['lead' => $lead]) !!}
                </div>

                {!! view_render_event('admin.leads.form_controls.after') !!}
            </div>
        </script>

        <script type="module">
            app.component('v-lead-edit', {
                template: '#v-lead-edit-template',

                data() {
                    return {
                        activeTab: 'case-information',

                        lead:  @json($lead),

                        person:  @json($lead->person),

                        products: @json($lead->products),

                        prefillOrganizationId: @json($leadEditOrganizationId),

                        selectedOrganizationName: @json($leadEditOrganizationName),

                        selectedOrganizationId: @json($leadEditOrganizationId),

                        initialContact: @json($leadEditInitialContact),

                        initialContacts: @json($leadEditInitialContacts),

                        caseOwner: {
                            selected: @json($leadEditOwner),
                            search: '',
                            results: [],
                            loading: false,
                            open: false,
                        },

                        organization: {
                            selected: @json($leadEditOrganizationId ? ['id' => $leadEditOrganizationId, 'name' => $leadEditOrganizationName] : null),
                            search: '',
                            results: [],
                            loading: false,
                            open: false,
                        },

                        searchRequestTimeout: null,

                        tabs: [
                            { id: 'case-information', label: 'Case Information' },
                            { id: 'contact-information', label: 'Contact Information' },
                            { id: 'description-information', label: 'Description Information' },
                        ],
                    };
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
                     * Search case owners.
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
                                    .filter(item => ! selectedId || Number(item.id) !== Number(selectedId));
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
                                this.organization.results = (response.data.data || [])
                                    .filter(item => ! selectedId || Number(item.id) !== Number(selectedId));
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

                    /**
                     * Keep organization values in sync when contact changes.
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
