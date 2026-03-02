<x-admin::layouts>
    <!--Page title -->
    <x-slot:title>
        @lang('admin::app.contacts.persons.create.title')
    </x-slot>

    {!! view_render_event('admin.persons.create.form.before') !!}

    <!--Create Page Form -->
    <x-admin::form
        :action="route('admin.contacts.persons.store')"
        enctype="multipart/form-data"
    >
        <div class="flex flex-col gap-4">
            <!-- Header -->
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.persons.create.breadcrumbs.before') !!}

                    <!-- Breadcrumb -->
                    <x-admin::breadcrumbs name="contacts.persons.create" />

                    {!! view_render_event('admin.persons.create.breadcrumbs.after') !!}

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.contacts.persons.create.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.persons.create.create_button.before') !!}

                        <input type="hidden" name="save_action" id="save_action" value="">

                        <!-- Create button for Person -->
                        <button
                            type="submit"
                            class="primary-button"
                            onclick="document.getElementById('save_action').value = 'save'"
                        >
                            @lang('admin::app.contacts.persons.create.save-btn')
                        </button>

                        <button
                            type="submit"
                            class="secondary-button"
                            onclick="document.getElementById('save_action').value = 'new'"
                        >
                            Save &amp; Create New
                        </button>

                        {!! view_render_event('admin.persons.create.create_button.after') !!}
                    </div>
                </div>
            </div>

            <!-- Form fields -->
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                {!! view_render_event('admin.persons.create.form_controls.before') !!}

                <x-admin::attributes
                    :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        ['code', 'NOTIN', ['organization_id', 'name', 'emails', 'contact_numbers', 'job_title', 'user_id']],
                        'entity_type' => 'persons',
                    ])"
                    :custom-validations="[
                        'name' => [
                            'min:2',
                            'max:100',
                        ],
                        'job_title' => [
                            'max:100',
                        ],
                    ]"
                />

                {{-- organization lookup component will be rendered below in ABOUT section --}}

                <!-- ABOUT -->
                <div class="mt-4">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">ABOUT</p>

                    <div class="grid gap-4 md:grid-cols-2">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                Salutation
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="select"
                                id="salutation"
                                name="salutation"
                            >
                                <option value="">Select</option>
                                <option>Mr.</option>
                                <option>Ms.</option>
                                <option>Mrs.</option>
                                <option>Dr.</option>
                                <option>Prof.</option>
                                <option>Mx.</option>
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.contacts.persons.create.type')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="select"
                                id="type"
                                name="type"
                                :value="old('type', 'customer')"
                            >
                                <option value="customer" {{ old('type', 'customer') == 'customer' ? 'selected' : '' }}>@lang('admin::app.contacts.persons.create.types.customer')</option>
                                <option value="vendor" {{ old('type') == 'vendor' ? 'selected' : '' }}>@lang('admin::app.contacts.persons.create.types.vendor')</option>
                                <option value="employee" {{ old('type') == 'employee' ? 'selected' : '' }}>@lang('admin::app.contacts.persons.create.types.employee')</option>
                                <option value="partner" {{ old('type') == 'partner' ? 'selected' : '' }}>@lang('admin::app.contacts.persons.create.types.partner')</option>
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>@lang('admin::app.contacts.persons.create.types.other')</option>
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">First Name</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="first_name"
                                name="first_name"
                                :value="old('first_name')"
                                rules="required|max:100"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">Last Name</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="last_name"
                                name="last_name"
                                :value="old('last_name')"
                                rules="required|max:100"
                            />
                        </x-admin::form.control-group>

                        <v-organization
                            :preselected-organization='@json($organization ?? null)'
                        ></v-organization>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Title</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="title"
                                name="title"
                                :value="old('title')"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group class="md:col-span-2">
                            <x-admin::form.control-group.label>Description</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="textarea"
                                id="description"
                                name="description"
                                :value="old('description')"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Contact Owner</x-admin::form.control-group.label>

                            <input
                                type="text"
                                class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                value="{{ optional(auth()->user())->name }}"
                                disabled
                            />

                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Cell Phone</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="cell_phone"
                                name="cell_phone"
                                :value="old('cell_phone')"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Direct</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="direct_phone"
                                name="direct_phone"
                                :value="old('direct_phone')"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Email 2</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="email"
                                id="email_secondary"
                                name="email_secondary"
                                :value="old('email_secondary')"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Birth Date</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="date"
                                id="birth_date"
                                name="birth_date"
                                :value="old('birth_date')"
                            />
                        </x-admin::form.control-group>
                    </div>
                </div>

                <!-- GET IN TOUCH -->
                <div class="mt-4">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">GET IN TOUCH</p>

                    <div class="grid gap-4 md:grid-cols-2">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Phone</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="phone"
                                name="phone"
                                :value="$organization?->phone ?? old('phone')"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Email</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="email"
                                id="email"
                                name="email"
                                :value="$organization?->user?->email ?? old('email')"
                            />
                        </x-admin::form.control-group>
                    </div>
                </div>

                <!-- MAILING ADDRESS -->
                <div class="mt-4">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">MAILING ADDRESS</p>

                    <div class="grid gap-4 md:grid-cols-2">
                        <x-admin::form.control-group class="md:col-span-2">
                            <x-admin::form.control-group.label>Mailing Street</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="mailing_street"
                                name="mailing_street"
                                :value="$organization?->billing_street ?? old('mailing_street')"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Mailing City</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="mailing_city"
                                name="mailing_city"
                                :value="$organization?->billing_city ?? old('mailing_city')"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Mailing State/Province</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="mailing_state"
                                name="mailing_state"
                                :value="$organization?->billing_state ?? old('mailing_state')"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Mailing Zip/Postal Code</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="mailing_postcode"
                                name="mailing_postcode"
                                :value="$organization?->billing_postcode ?? old('mailing_postcode')"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Mailing Country</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="mailing_country"
                                name="mailing_country"
                                :value="$organization?->billing_country ?? old('mailing_country')"
                            />
                        </x-admin::form.control-group>
                    </div>
                </div>

                {!! view_render_event('admin.persons.create.form_controls.after') !!}
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.persons.create.form.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-organization-template"
        >
            <div>
                @if (isset($organization) && $organization)
                    <!-- Pre-selected organization (from query param) -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('admin::app.contacts.persons.create.organization')
                        </x-admin::form.control-group.label>

                        <input
                            type="text"
                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            value="{{ $organization->name }}"
                            disabled
                        />
                    </x-admin::form.control-group>

                    <input type="hidden" name="organization_id" value="{{ $organization->id }}">
                    <input type="hidden" name="organization_name" value="{{ $organization->name }}">
                @else
                    <!-- Default organization lookup attribute -->
                    <x-admin::attributes
                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                            ['code', 'IN', ['organization_id']],
                            'entity_type' => 'persons',
                        ])"
                    />

                    <template v-if="organizationNameLabel">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.contacts.persons.create.organization')
                            </x-admin::form.control-group.label>

                            <input
                                type="text"
                                class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                :value="organizationNameLabel"
                                disabled
                            />

                            <input
                                type="hidden"
                                name="organization_name"
                                :value="organizationNameLabel"
                            />
                        </x-admin::form.control-group>
                    </template>
                @endif
            </div>
        </script>

        <script type="module">
            app.component('v-organization', {
                template: '#v-organization-template',

                data() {
                    return {
                        organizationName: null,
                    };
                },

                computed: {
                    organizationNameLabel() {
                        if (typeof this.organizationName === 'string') {
                            return this.organizationName;
                        }

                        if (this.organizationName && typeof this.organizationName.name === 'string') {
                            return this.organizationName.name;
                        }

                        return '';
                    },
                },

                methods: {
                    handleLookupAdded(event) {
                        if (typeof event === 'string') {
                            this.organizationName = event;
                            return;
                        }

                        this.organizationName = event?.name || event?.label || null;
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
