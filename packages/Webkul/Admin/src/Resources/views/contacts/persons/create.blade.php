<x-admin::layouts>
    @php
        $currentRouteName = request()->route()?->getName() ?? 'admin.contacts.persons.create';
        $routePrefix = str_contains($currentRouteName, 'admin.customers.')
            ? 'customers'
            : (str_contains($currentRouteName, 'admin.vendors.')
                ? 'vendors'
                : (str_contains($currentRouteName, 'admin.employees.') ? 'employees' : 'contacts'));
        $isEmployeeRoute = $isEmployeeRoute ?? ($routePrefix === 'employees');
        $contactLabel = $isEmployeeRoute ? 'Employee' : 'Contact';
        $routeType = $routeType ?? ($routePrefix === 'vendors' ? 'vendor' : ($routePrefix === 'customers' ? 'customer' : null));
    @endphp

    <!--Page title -->
    <x-slot:title>
        Create {{ $contactLabel }}
    </x-slot>

    {!! view_render_event('admin.persons.create.form.before') !!}

    <!--Create Page Form -->
    <x-admin::form
        :action="route('admin.' . $routePrefix . '.persons.store')"
        enctype="multipart/form-data"
    >
        <div class="flex flex-col gap-4">
            <!-- Header -->
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.persons.create.breadcrumbs.before') !!}

                    <!-- Breadcrumb -->
                    <x-admin::breadcrumbs :name="$routePrefix . '.persons.create'" />

                    {!! view_render_event('admin.persons.create.breadcrumbs.after') !!}

                    <div class="text-xl font-bold dark:text-white">
                        Create {{ $contactLabel }}
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
                            Save {{ $contactLabel }}
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

                @unless ($isEmployeeRoute)
                    <div class="mb-4 grid gap-4 md:grid-cols-2">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.contacts.persons.create.type')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="select"
                                id="type"
                                name="type"
                                :value="old('type', $routeType ?? 'customer')"
                                :disabled="(bool) $routeType"
                            >
                                <option value="customer" {{ old('type', $routeType ?? 'customer') == 'customer' ? 'selected' : '' }}>@lang('admin::app.contacts.persons.create.types.customer')</option>
                                <option value="vendor" {{ old('type', $routeType) == 'vendor' ? 'selected' : '' }}>@lang('admin::app.contacts.persons.create.types.vendor')</option>
                            </x-admin::form.control-group.control>

                            @if ($routeType)
                                <input type="hidden" name="type" value="{{ $routeType }}">
                            @endif
                        </x-admin::form.control-group>
                    </div>
                @endunless

                @unless ($isEmployeeRoute)
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
                @endunless

                {{-- organization lookup component will be rendered below in ABOUT section --}}

                <!-- ABOUT -->
                <div class="mt-4">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">ABOUT</p>

                    <div class="grid gap-4 md:grid-cols-2">
                        @if ($isEmployeeRoute)
                            <input type="hidden" name="type" value="employee">

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">Full Name</x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="full_name"
                                    name="full_name"
                                    :value="old('full_name')"
                                    rules="required|max:150"
                                />

                                <x-admin::form.control-group.error control-name="full_name" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">Role</x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    id="role_id"
                                    name="role_id"
                                    :value="old('role_id')"
                                    rules="required"
                                >
                                    <option value="">Select Role</option>

                                    @foreach ($employeeRoles as $employeeRole)
                                        <option value="{{ $employeeRole->id }}" {{ (string) old('role_id') === (string) $employeeRole->id ? 'selected' : '' }}>
                                            {{ $employeeRole->name }} - {{ $employeeRole->description }}
                                        </option>
                                    @endforeach
                                </x-admin::form.control-group.control>

                                <x-admin::form.control-group.error control-name="role_id" />
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
                        @else
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
                        @endif
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
                            <x-admin::form.control-group.label class="{{ $isEmployeeRoute ? 'required' : '' }}">Email</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="email"
                                id="email"
                                name="email"
                                :value="$organization?->user?->email ?? old('email')"
                                rules="{{ $isEmployeeRoute ? 'required|email' : 'email' }}"
                            />

                            @if ($isEmployeeRoute)
                                <x-admin::form.control-group.error control-name="email" />
                            @endif
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
                                :value="old('mailing_street', $organization?->billing_street)"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Mailing City</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="mailing_city"
                                name="mailing_city"
                                :value="old('mailing_city', $organization?->billing_city)"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Mailing State/Province</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="mailing_state"
                                name="mailing_state"
                                :value="old('mailing_state', $organization?->billing_state)"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Mailing Zip/Postal Code</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="mailing_postcode"
                                name="mailing_postcode"
                                :value="old('mailing_postcode', $organization?->billing_postcode)"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Mailing Country</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="mailing_country"
                                name="mailing_country"
                                :value="old('mailing_country', $organization?->billing_country)"
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
                <template v-if="hasPreselectedOrganization">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('admin::app.contacts.persons.create.organization')
                        </x-admin::form.control-group.label>

                        <input
                            type="text"
                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            :value="preselectedOrganization.name"
                            disabled
                        />
                    </x-admin::form.control-group>

                    <input type="hidden" name="organization_id" :value="preselectedOrganization.id">
                    <input type="hidden" name="organization_name" :value="preselectedOrganization.name">
                </template>

                <template v-else>
                    <x-admin::attributes
                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                            ['code', 'IN', ['organization_id']],
                            'entity_type' => 'persons',
                        ])"
                    />

                    <input
                        v-if="organizationNameLabel"
                        type="hidden"
                        name="organization_name"
                        :value="organizationNameLabel"
                    />
                </template>
            </div>
        </script>

        <script type="module">
            app.component('v-organization', {
                template: '#v-organization-template',

                props: {
                    preselectedOrganization: {
                        type: Object,
                        default: null,
                    },
                },

                data() {
                    return {
                        organizationName: null,
                    };
                },

                computed: {
                    hasPreselectedOrganization() {
                        return !! (this.preselectedOrganization && this.preselectedOrganization.id);
                    },

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

                        if (event?.id) {
                            this.fetchOrganizationAddress(event.id);
                        }
                    },

                    fetchOrganizationAddress(id) {
                        fetch("{{ route('admin.' . ($routePrefix === 'employees' ? 'contacts' : $routePrefix) . '.organizations.fetch', 0) }}".replace(/0$/, id), {
                            headers: {
                                'Accept': 'application/json',
                            },
                        })
                            .then((response) => response.ok ? response.json() : null)
                            .then((organization) => {
                                if (organization) {
                                    this.applyOrganizationAddress(organization);
                                }
                            })
                            .catch(() => {});
                    },

                    applyOrganizationAddress(organization) {
                        const mapping = {
                            phone: organization.phone || '',
                            mailing_street: organization.billing_street || organization.shipping_street || '',
                            mailing_city: organization.billing_city || organization.shipping_city || '',
                            mailing_state: organization.billing_state || organization.shipping_state || '',
                            mailing_postcode: organization.billing_postcode || organization.shipping_postcode || '',
                            mailing_country: organization.billing_country || organization.shipping_country || '',
                        };

                        Object.keys(mapping).forEach((fieldId) => {
                            const field = document.getElementById(fieldId);

                            if (field && ! field.value) {
                                field.value = mapping[fieldId] || '';
                            }
                        });
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
