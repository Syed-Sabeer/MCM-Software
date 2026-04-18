
<x-admin::layouts>
    @php
        $currentRouteName = request()->route()?->getName() ?? 'admin.contacts.persons.edit';
        $routePrefix = str_contains($currentRouteName, 'admin.customers.')
            ? 'customers'
            : (str_contains($currentRouteName, 'admin.vendors.')
                ? 'vendors'
                : (str_contains($currentRouteName, 'admin.employees.') ? 'employees' : 'contacts'));
        $isEmployeeRoute = $isEmployeeRoute ?? ($routePrefix === 'employees' || $person->type === 'employee');
        $contactLabel = $isEmployeeRoute ? 'Employee' : 'Contact';
        $routeType = $routeType ?? ($routePrefix === 'vendors' ? 'vendor' : ($routePrefix === 'customers' ? 'customer' : null));
        $employeeFullName = trim(($person->first_name ?? '') . ' ' . ($person->last_name ?? '')) ?: $person->name;
    @endphp

    <!-- Page Title -->
    <x-slot:title>
        Edit {{ $contactLabel }}
    </x-slot>

    {!! view_render_event('admin.persons.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.' . $routePrefix . '.persons.update', $person->id)"
        method="PUT"
        enctype="multipart/form-data"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.persons.edit.breadcrumbs.before') !!}

                    <x-admin::breadcrumbs
                        :name="$routePrefix . '.persons.edit'"
                        :entity="$person"
                    />

                    {!! view_render_event('admin.persons.edit.breadcrumbs.after') !!}

                    <div class="text-xl font-bold dark:text-white">
                        Edit {{ $contactLabel }}
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!--  Save button for Person -->
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.persons.edit.save_button.before') !!}

                        <button
                            type="submit"
                            class="primary-button"
                        >
                            Save {{ $contactLabel }}
                        </button>

                        {!! view_render_event('admin.persons.edit.save_button.after') !!}
                    </div>
                </div>
            </div>

            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                {!! view_render_event('admin.contacts.persons.edit.form_controls.before') !!}

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
                        :entity="$person"
                    />
                @endunless

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
                                    value="{{ old('full_name', $employeeFullName) }}"
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
                                    :value="old('role_id', optional($person->user)->role_id)"
                                    rules="required"
                                >
                                    <option value="">Select Role</option>

                                    @foreach ($employeeRoles as $employeeRole)
                                        <option value="{{ $employeeRole->id }}" {{ (string) old('role_id', optional($person->user)->role_id) === (string) $employeeRole->id ? 'selected' : '' }}>
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
                                    value="{{ old('birth_date', optional($person->birth_date)->format('Y-m-d')) }}"
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
                                    :value="old('salutation', $person->salutation)"
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
                                    @lang('admin::app.contacts.persons.edit.type')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="select"
                                    id="type"
                                    name="type"
                                    :disabled="(bool) $routeType"
                                >
                                    <option value="customer" {{ old('type', $routeType ?? $person->type) == 'customer' ? 'selected' : '' }}>@lang('admin::app.contacts.persons.edit.types.customer')</option>
                                    <option value="vendor" {{ old('type', $routeType ?? $person->type) == 'vendor' ? 'selected' : '' }}>@lang('admin::app.contacts.persons.edit.types.vendor')</option>
                                    <option value="employee" {{ old('type', $person->type) == 'employee' ? 'selected' : '' }}>@lang('admin::app.contacts.persons.edit.types.employee')</option>
                                    <option value="partner" {{ old('type', $person->type) == 'partner' ? 'selected' : '' }}>@lang('admin::app.contacts.persons.edit.types.partner')</option>
                                    <option value="other" {{ old('type', $person->type) == 'other' ? 'selected' : '' }}>@lang('admin::app.contacts.persons.edit.types.other')</option>
                                </x-admin::form.control-group.control>
                                @if ($routeType)
                                    <input type="hidden" name="type" value="{{ $routeType }}">
                                @endif
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">First Name</x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="first_name"
                                    name="first_name"
                                    value="{{ old('first_name', $person->first_name) }}"
                                    rules="required|max:100"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">Last Name</x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="last_name"
                                    name="last_name"
                                    value="{{ old('last_name', $person->last_name) }}"
                                    rules="required|max:100"
                                />
                            </x-admin::form.control-group>

                            <v-organization></v-organization>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Title</x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="title"
                                    name="title"
                                    value="{{ old('title', $person->title) }}"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group class="md:col-span-2">
                                <x-admin::form.control-group.label>Description</x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    id="description"
                                    name="description"
                                    :value="old('description', $person->description)"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Contact Owner</x-admin::form.control-group.label>

                                <input
                                    type="text"
                                    class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                    value="{{ optional($person->user)->name }}"
                                    disabled
                                />

                                <input type="hidden" name="user_id" value="{{ $person->user_id }}">
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Cell Phone</x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="cell_phone"
                                    name="cell_phone"
                                    value="{{ old('cell_phone', $person->cell_phone) }}"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Direct</x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    id="direct_phone"
                                    name="direct_phone"
                                    value="{{ old('direct_phone', $person->direct_phone) }}"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Email 2</x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="email"
                                    id="email_secondary"
                                    name="email_secondary"
                                    value="{{ old('email_secondary', $person->email_secondary) }}"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Birth Date</x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="date"
                                    id="birth_date"
                                    name="birth_date"
                                    value="{{ old('birth_date', optional($person->birth_date)->format('Y-m-d')) }}"
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
                                value="{{ old('phone', $person->phone) }}"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="{{ $isEmployeeRoute ? 'required' : '' }}">Email</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $person->email) }}"
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
                                value="{{ old('mailing_street', $person->mailing_street) }}"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Mailing City</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="mailing_city"
                                name="mailing_city"
                                value="{{ old('mailing_city', $person->mailing_city) }}"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Mailing State/Province</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="mailing_state"
                                name="mailing_state"
                                value="{{ old('mailing_state', $person->mailing_state) }}"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Mailing Zip/Postal Code</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="mailing_postcode"
                                name="mailing_postcode"
                                value="{{ old('mailing_postcode', $person->mailing_postcode) }}"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Mailing Country</x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="mailing_country"
                                name="mailing_country"
                                value="{{ old('mailing_country', $person->mailing_country) }}"
                            />
                        </x-admin::form.control-group>
                    </div>
                </div>

                {!! view_render_event('admin.contacts.persons.edit.form_controls.after') !!}
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.persons.edit.form.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-organization-template"
        >
            <div>
                <x-admin::attributes
                    :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        ['code', 'IN', ['organization_id']],
                        'entity_type' => 'persons',
                    ])"
                    :entity="$person"
                />

                <template v-if="organizationName">
                    <x-admin::form.control-group.control
                        type="hidden"
                        name="organization_name"
                        v-model="organizationName"
                    />
                </template>
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

                methods: {
                    handleLookupAdded(event) {
                        this.organizationName = event?.name || null;
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
