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

            <!-- Lead Create Component -->
            <v-lead-create
                :prefill-organization-id="{{ request('organization_id') ?? 'null' }}"
                prefill-organization-name="{{ $organization->name ?? '' }}"
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
                                <x-admin::form.control-group.label class="required">
                                    Status
                                </x-admin::form.control-group.label>

                                <select
                                    name="lead_pipeline_stage_id"
                                    class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                    required
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

                            <!-- Case Owner -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    Case Owner
                                </x-admin::form.control-group.label>

                                <input
                                    type="text"
                                    class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                    value="{{ optional(auth()->user())->name }}"
                                    disabled
                                />

                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            </x-admin::form.control-group>

                            <x-admin::form.control-group v-if="selectedOrganizationName">
                                <x-admin::form.control-group.label>
                                    Organization
                                </x-admin::form.control-group.label>

                                <input
                                    type="text"
                                    class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                    :value="selectedOrganizationName"
                                    disabled
                                />
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

                props: ['prefillOrganizationId', 'prefillOrganizationName'],

                data() {
                    return {
                        activeTab: 'case-information',

                        tabs: [
                            { id: 'case-information', label: 'Case Information' },
                            { id: 'contact-information', label: 'Contact Information' },
                            { id: 'description-information', label: 'Description Information' },
                        ],

                        selectedOrganizationName: this.prefillOrganizationName || '',
                    };
                },

                methods: {
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
