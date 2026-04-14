
<x-admin::layouts>
    @php
        $currentRouteName = request()->route()?->getName() ?? 'admin.contacts.organizations.update';
        $routePrefix = str_contains($currentRouteName, 'admin.customers.')
            ? 'customers'
            : (str_contains($currentRouteName, 'admin.vendors.') ? 'vendors' : 'contacts');
        $organizationLabel = $routePrefix === 'vendors' ? 'Vendor' : 'Company';
    @endphp

    <!-- Page Title -->
    <x-slot:title>
        Edit {{ $organizationLabel }}
    </x-slot>

    {!! view_render_event('admin.organizations.edit.form.before') !!}

    @php
        if ($routePrefix === 'customers') {
            $updateRoute = 'admin.customers.organizations.update';
        } elseif ($routePrefix === 'vendors') {
            $updateRoute = 'admin.vendors.organizations.update';
        } else {
            $updateRoute = 'admin.contacts.organizations.update';
        }
    @endphp

    <x-admin::form
        :action="route($updateRoute, $organization->id)"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.organizations.edit.breadcrumbs.before', ['organization' => $organization]) !!}

                    <x-admin::breadcrumbs
                        :name="$routePrefix . '.organizations.edit'"
                        :entity="$organization"
                    />

                    {!! view_render_event('admin.organizations.edit.breadcrumbs.before', ['organization' => $organization]) !!}

                    <div class="text-xl font-bold dark:text-gray-300">
                        Edit {{ $organizationLabel }}
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.organizations.edit.save_button.before', ['organization' => $organization]) !!}

                        <!-- Save button for organization -->
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            Save {{ $organizationLabel }}
                        </button>

                        {!! view_render_event('admin.organizations.edit.save_button.after', ['organization' => $organization]) !!}
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                    {{ $organizationLabel }} Information
                </p>

                {!! view_render_event('admin.contacts.organizations.edit.account_information.before', ['organization' => $organization]) !!}

                <div style="display: grid !important; grid-template-columns: repeat(2, 1fr) !important; gap: 1rem !important;">
                    <!-- Account Name -->
                    <x-admin::form.control-group style="margin: 0 !important;">
                        <x-admin::form.control-group.label class="required">
                            {{ $organizationLabel }} Name
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            id="name"
                            name="name"
                            rules="required|max:100"
                            :value="old('name', $organization->name)"
                            label="{{ $organizationLabel }} Name"
                        />

                        <x-admin::form.control-group.error control-name="name" />
                    </x-admin::form.control-group>

                    <!-- Website -->
                    <x-admin::form.control-group style="margin: 0 !important;">
                        <x-admin::form.control-group.label>
                            Website
                        </x-admin::form.control-group.label>

                        <input
                            type="url"
                            id="website"
                            name="website"
                            value="{{ old('website', $organization->website) }}"
                            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-brandColor focus:ring-1 focus:ring-brandColor dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            placeholder="https://example.com"
                        />

                        <x-admin::form.control-group.error control-name="website" />
                    </x-admin::form.control-group>

                    <!-- Phone -->
                    <x-admin::form.control-group style="margin: 0 !important;">
                        <x-admin::form.control-group.label>
                            Phone
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            id="phone"
                            name="phone"
                            :value="old('phone', $organization->phone)"
                            label="Phone"
                        />

                        <x-admin::form.control-group.error control-name="phone" />
                    </x-admin::form.control-group>

                    <!-- Fax -->
                    <x-admin::form.control-group style="margin: 0 !important;">
                        <x-admin::form.control-group.label>
                            Fax
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            id="fax"
                            name="fax"
                            :value="old('fax', $organization->fax)"
                            label="Fax"
                        />

                        <x-admin::form.control-group.error control-name="fax" />
                    </x-admin::form.control-group>
                </div>

                {!! view_render_event('admin.contacts.organizations.edit.account_information.after', ['organization' => $organization]) !!}
            </div>

            <!-- Additional Information -->
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                    Additional Information
                </p>

                {!! view_render_event('admin.contacts.organizations.edit.additional_information.before', ['organization' => $organization]) !!}

                <div style="display: grid !important; grid-template-columns: repeat(2, 1fr) !important; gap: 1rem !important;">
                    <!-- Type -->
                    <x-admin::form.control-group style="margin: 0 !important;">
                        <x-admin::form.control-group.label>
                            {{ $organizationLabel }} Type
                        </x-admin::form.control-group.label>

                        @php
                            // Detect route type from current route
                            $detectRouteType = null;
                            if ($routePrefix === 'customers') {
                                $detectRouteType = 'customer';
                            } elseif ($routePrefix === 'vendors') {
                                $detectRouteType = 'vendor';
                            }

                            $selectedType = strtolower((string) old('organization_type', old('type', $organization->type ?? '')));
                        @endphp

                        <select
                            id="organization_type"
                            name="organization_type"
                            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-brandColor focus:ring-1 focus:ring-brandColor dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            {{ $detectRouteType ? 'disabled' : '' }}
                        >
                            @unless($detectRouteType)
                                <option value="">-- Select Type --</option>
                            @endunless
                            <option value="customer" {{ $selectedType === 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="vendor" {{ $selectedType === 'vendor' ? 'selected' : '' }}>Vendor</option>
                        </select>

                        <!-- Hidden input to ensure type is submitted when dropdown is disabled -->
                        @if($detectRouteType)
                            <input type="hidden" name="organization_type" value="{{ $detectRouteType }}" />
                        @endif

                        <x-admin::form.control-group.error control-name="organization_type" />
                    </x-admin::form.control-group>

                    <!-- Industry -->
                    <x-admin::form.control-group style="margin: 0 !important;">
                        <x-admin::form.control-group.label>
                            Industry
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="select"
                            id="industry"
                            name="industry"
                            label="Industry"
                        >
                            @php
                                $selectedIndustry = old('industry', $organization->industry);
                            @endphp

                            <option value="">Select Industry</option>
                            <option value="home_apparel" {{ $selectedIndustry === 'home_apparel' ? 'selected' : '' }}>Home Apparel</option>
                            <option value="other_shipping" {{ $selectedIndustry === 'other_shipping' ? 'selected' : '' }}>Other Shipping</option>
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="industry" />
                    </x-admin::form.control-group>

                    <!-- Employees -->
                    <x-admin::form.control-group style="margin: 0 !important;">
                        <x-admin::form.control-group.label>
                            Employees
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="number"
                            id="employees"
                            name="employees"
                            :value="old('employees', $organization->employees)"
                            label="Employees"
                        />

                        <x-admin::form.control-group.error control-name="employees" />
                    </x-admin::form.control-group>

                    <!-- Annual Revenue -->
                    <x-admin::form.control-group style="margin: 0 !important;">
                        <x-admin::form.control-group.label>
                            Annual Revenue
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="number"
                            step="0.01"
                            id="annual_revenue"
                            name="annual_revenue"
                            :value="old('annual_revenue', $organization->annual_revenue)"
                            label="Annual Revenue"
                        />

                        <x-admin::form.control-group.error control-name="annual_revenue" />
                    </x-admin::form.control-group>
                </div>

                <!-- Description -->
                <x-admin::form.control-group class="mt-4">
                    <x-admin::form.control-group.label>
                        Description
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="textarea"
                        id="description"
                        name="description"
                        :value="old('description', $organization->description)"
                        label="Description"
                        rules="max:100"
                    />

                    <x-admin::form.control-group.error control-name="description" />
                </x-admin::form.control-group>

                {!! view_render_event('admin.contacts.organizations.edit.additional_information.after', ['organization' => $organization]) !!}
            </div>

            <!-- Address Information -->
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                @php
                    $addressRows = collect(old('addresses', $organization->address ?? []))
                        ->filter(fn ($row) => is_array($row) && ! in_array(strtolower((string) ($row['type'] ?? 'other')), ['billing', 'shipping']))
                        ->values()
                        ->all();
                @endphp

                {!! view_render_event('admin.contacts.organizations.edit.address_information.before', ['organization' => $organization]) !!}

                <!-- Billing Address -->
                <div class="mb-8">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        Billing Address Details
                    </p>

                    <x-admin::form.control-group class="mb-4">
                        <x-admin::form.control-group.label>
                            Billing Street
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            id="billing_street"
                            name="billing_street"
                            :value="old('billing_street', $organization->billing_street)"
                            label="Billing Street"
                        rules="max:100"
                        />
                    </x-admin::form.control-group>

                    <!-- 4-Column Layout for Country, State, City, Postal Code -->
                    <div style="display: grid !important; grid-template-columns: repeat(4, 1fr) !important; gap: 1rem !important;">
                        <x-admin::form.control-group style="margin: 0 !important;">
                            <x-admin::form.control-group.label>
                                Country
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="billing_country"
                                name="billing_country"
                                :value="old('billing_country', $organization->billing_country)"
                                label="Country"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group style="margin: 0 !important;">
                            <x-admin::form.control-group.label>
                                State / Province
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="billing_state"
                                name="billing_state"
                                :value="old('billing_state', $organization->billing_state)"
                                label="State / Province"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group style="margin: 0 !important;">
                            <x-admin::form.control-group.label>
                                City
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="billing_city"
                                name="billing_city"
                                :value="old('billing_city', $organization->billing_city)"
                                label="City"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group style="margin: 0 !important;">
                            <x-admin::form.control-group.label>
                                Zip / Postal Code
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="billing_postcode"
                                name="billing_postcode"
                                :value="old('billing_postcode', $organization->billing_postcode)"
                                label="Zip / Postal Code"
                            />
                        </x-admin::form.control-group>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="mb-4 flex items-center justify-between" style="margin-top: 1rem;">
                    <p id="shipping-address-heading" class="text-base font-semibold text-gray-800 dark:text-white">
                        Shipping Address Details
                    </p>

                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input
                            type="checkbox"
                            name="same_as_billing"
                            id="same-as-billing"
                            value="1"
                            {{ old('same_as_billing') ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-brandColor focus:ring-brandColor"
                            onchange="window.toggleShippingAddress && window.toggleShippingAddress(this)"
                        >

                        <span>Same as billing address</span>
                    </label>
                </div>

                <div id="shipping-address-container">
                    <x-admin::form.control-group class="mb-4">
                        <x-admin::form.control-group.label>
                            Shipping Street
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            id="shipping_street"
                            name="shipping_street"
                            :value="old('shipping_street', $organization->shipping_street)"
                            label="Shipping Street"
                        rules="max:100"
                        />
                    </x-admin::form.control-group>

                    <!-- 4-Column Layout for Country, State, City, Postal Code -->
                    <div style="display: grid !important; grid-template-columns: repeat(4, 1fr) !important; gap: 1rem !important;">
                        <x-admin::form.control-group style="margin: 0 !important;">
                            <x-admin::form.control-group.label>
                                Country
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="shipping_country"
                                name="shipping_country"
                                :value="old('shipping_country', $organization->shipping_country)"
                                label="Country"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group style="margin: 0 !important;">
                            <x-admin::form.control-group.label>
                                State / Province
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="shipping_state"
                                name="shipping_state"
                                :value="old('shipping_state', $organization->shipping_state)"
                                label="State / Province"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group style="margin: 0 !important;">
                            <x-admin::form.control-group.label>
                                City
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="shipping_city"
                                name="shipping_city"
                                :value="old('shipping_city', $organization->shipping_city)"
                                label="City"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group style="margin: 0 !important;">
                            <x-admin::form.control-group.label>
                                Zip / Postal Code
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="shipping_postcode"
                                name="shipping_postcode"
                                :value="old('shipping_postcode', $organization->shipping_postcode)"
                                label="Zip / Postal Code"
                            />
                        </x-admin::form.control-group>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 pt-6 dark:border-gray-700">
                    <div class="mb-4 flex items-center justify-between">
                        <p class="text-base font-semibold text-gray-800 dark:text-white">
                            Additional Addresses
                        </p>

                        <button
                            type="button"
                            id="add-address-row"
                            class="secondary-button"
                            onclick="window.addOrganizationAddressRow && window.addOrganizationAddressRow()"
                        >
                            Add Address
                        </button>
                    </div>

                    <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">
                        Add as many addresses as needed. Each address is stored in the format city, state, zip, country.
                    </p>

                    <div id="address-rows" class="flex flex-col gap-3">
                        @foreach ($addressRows as $index => $row)
                            <div class="address-row rounded-lg border border-gray-200 p-4 dark:border-gray-700" data-index="{{ $index }}">
                                <div class="mb-4 flex items-center justify-between gap-3">
                                    <p class="text-base font-semibold text-gray-800 dark:text-white">Additional Address</p>
                                    <button type="button" class="remove-address-row secondary-button" onclick="window.removeOrganizationAddressRow && window.removeOrganizationAddressRow(this)">Remove</button>
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Address Type</label>
                                        <select name="addresses[{{ $index }}][type]" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                            <option value="billing" {{ (($row['type'] ?? 'other') === 'billing') ? 'selected' : '' }}>Billing</option>
                                            <option value="shipping" {{ (($row['type'] ?? 'other') === 'shipping') ? 'selected' : '' }}>Shipping</option>
                                            <option value="other" {{ (($row['type'] ?? 'other') === 'other') ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Street</label>
                                        <input type="text" name="addresses[{{ $index }}][street]" value="{{ $row['street'] ?? '' }}" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">City</label>
                                        <input type="text" name="addresses[{{ $index }}][city]" value="{{ $row['city'] ?? '' }}" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">State</label>
                                        <input type="text" name="addresses[{{ $index }}][state]" value="{{ $row['state'] ?? '' }}" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Zip</label>
                                        <input type="text" name="addresses[{{ $index }}][postcode]" value="{{ $row['postcode'] ?? '' }}" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Country</label>
                                        <input type="text" name="addresses[{{ $index }}][country]" value="{{ $row['country'] ?? '' }}" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {!! view_render_event('admin.contacts.organizations.edit.address_information.after', ['organization' => $organization]) !!}
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.organizations.edit.form.after') !!}

    <script>
        window.toggleShippingAddress = (checkbox) => {
            const shippingContainer = document.getElementById('shipping-address-container');
            const shippingHeading = document.getElementById('shipping-address-heading');
            const shippingFields = ['shipping_street', 'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_country'];
            const fieldMappings = {
                'billing_street': 'shipping_street',
                'billing_city': 'shipping_city',
                'billing_state': 'shipping_state',
                'billing_postcode': 'shipping_postcode',
                'billing_country': 'shipping_country',
            };

            const copyBillingToShipping = () => {
                Object.entries(fieldMappings).forEach(([billingFieldId, shippingFieldId]) => {
                    const billingInput = document.getElementById(billingFieldId);
                    const shippingInput = document.getElementById(shippingFieldId);

                    if (billingInput && shippingInput) {
                        shippingInput.value = billingInput.value;
                        shippingInput.setAttribute('readonly', 'readonly');
                    }
                });

            };

            const syncOnBillingChange = () => {
                Object.keys(fieldMappings).forEach((billingFieldId) => {
                    const billingInput = document.getElementById(billingFieldId);

                    if (billingInput) {
                        billingInput.addEventListener('input', () => {
                            if (checkbox.checked) {
                                copyBillingToShipping();
                            }
                        });
                    }
                });
            };

            const resetShippingReadonly = () => {
                Object.values(fieldMappings).forEach((shippingFieldId) => {
                    const shippingInput = document.getElementById(shippingFieldId);

                    if (shippingInput) {
                        shippingInput.removeAttribute('readonly');
                    }
                });
            };

            const clearShippingValues = () => {
                shippingFields.forEach((shippingFieldId) => {
                    const shippingInput = document.getElementById(shippingFieldId);

                    if (shippingInput) {
                        shippingInput.value = '';
                    }
                });
            };

            if (!checkbox || !shippingContainer) {
                return;
            }

            if (checkbox.checked) {
                copyBillingToShipping();
                shippingContainer.style.display = 'none';
                if (shippingHeading) {
                    shippingHeading.style.display = 'none';
                }
            } else {
                resetShippingReadonly();
                shippingContainer.style.display = 'block';
                if (shippingHeading) {
                    shippingHeading.style.display = 'block';
                }

                if (! checkbox.dataset.userEditedShipping) {
                    clearShippingValues();
                }
            }

            if (! checkbox.dataset.synced) {
                syncOnBillingChange();
                checkbox.dataset.synced = '1';
            }
        };

        const checkbox = document.getElementById('same-as-billing');
        const form = document.querySelector('form');
        const addressRows = document.getElementById('address-rows');
        const addAddressRowButton = document.getElementById('add-address-row');

        const getNextAddressIndex = () => {
            if (!addressRows) {
                return 0;
            }

            const rows = Array.from(addressRows.querySelectorAll('.address-row'));

            if (! rows.length) {
                return 0;
            }

            return Math.max(...rows.map((row) => Number(row.dataset.index || 0))) + 1;
        };

        const createAddressRow = (index) => {
            const row = document.createElement('div');
            row.className = 'address-row rounded-lg border border-gray-200 p-4 dark:border-gray-700';
            row.dataset.index = index;
            row.innerHTML = `
                <div class="mb-4 flex items-center justify-between gap-3">
                    <p class="text-base font-semibold text-gray-800 dark:text-white">Additional Address</p>
                    <button type="button" class="remove-address-row secondary-button" onclick="window.removeOrganizationAddressRow && window.removeOrganizationAddressRow(this)">Remove</button>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Address Type</label>
                        <select name="addresses[${index}][type]" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="billing">Billing</option>
                            <option value="shipping">Shipping</option>
                            <option value="other" selected>Other</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Street</label>
                        <input type="text" name="addresses[${index}][street]" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">City</label>
                        <input type="text" name="addresses[${index}][city]" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">State</label>
                        <input type="text" name="addresses[${index}][state]" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Zip</label>
                        <input type="text" name="addresses[${index}][postcode]" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Country</label>
                        <input type="text" name="addresses[${index}][country]" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                    </div>
                </div>
            `;

            return row;
        };

        window.addOrganizationAddressRow = () => {
            if (! addressRows) {
                return;
            }

            const index = getNextAddressIndex();
            addressRows.appendChild(createAddressRow(index));
        };

        window.removeOrganizationAddressRow = (button) => {
            const row = button?.closest('.address-row');

            if (row) {
                row.remove();
            }
        };

        if (addAddressRowButton && addressRows) {
            const appendAddressRow = () => {
                const index = getNextAddressIndex();
                addressRows.appendChild(createAddressRow(index));
            };

            addAddressRowButton.addEventListener('click', appendAddressRow);
            addAddressRowButton.onclick = appendAddressRow;

            addressRows.addEventListener('click', (event) => {
                const target = event.target.closest('.remove-address-row');

                if (!target) {
                    return;
                }

                const row = target.closest('.address-row');

                if (row) {
                    row.remove();
                }
            });
        }

        if (checkbox) {
            window.toggleShippingAddress(checkbox);

            ['shipping_street', 'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_country'].forEach((fieldId) => {
                const field = document.getElementById(fieldId);

                if (field) {
                    field.addEventListener('input', () => {
                        checkbox.dataset.userEditedShipping = '1';
                    });
                }
            });
        }

        if (checkbox && form) {
            form.addEventListener('submit', () => {
                if (checkbox.checked) {
                    window.toggleShippingAddress(checkbox);
                }
            });
        }
    </script>
</x-admin::layouts>
