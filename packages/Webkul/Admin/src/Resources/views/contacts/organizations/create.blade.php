
@component('admin::components.layouts.index')
    @php
        $currentRouteName = request()->route()?->getName() ?? 'admin.contacts.organizations.store';
        $routePrefix = str_contains($currentRouteName, 'admin.customers.')
            ? 'customers'
            : (str_contains($currentRouteName, 'admin.vendors.') ? 'vendors' : 'contacts');
        $organizationLabel = $routePrefix === 'vendors' ? 'Vendor' : 'Company';
    @endphp

    <!-- Page Title -->
    @slot('title')
        Create {{ $organizationLabel }}
    @endslot

    {!! view_render_event('admin.organizations.create.form.before') !!}

    @php
        if ($routePrefix === 'customers') {
            $storeRoute = 'admin.customers.organizations.store';
        } elseif ($routePrefix === 'vendors') {
            $storeRoute = 'admin.vendors.organizations.store';
        } else {
            $storeRoute = 'admin.contacts.organizations.store';
        }
    @endphp

    <form
        action="{{ route($storeRoute) }}"
        method="POST"
    >
        @csrf

        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.organizations.create.breadcrumbs.before') !!}

                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs :name="$routePrefix . '.organizations.create'" />

                    {!! view_render_event('admin.organizations.create.breadcrumbs.before') !!}

                    <div class="text-xl font-bold dark:text-gray-300">
                        Create {{ $organizationLabel }}
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.organizations.create.save_buttons.before') !!}

                        <!-- Create button for organization -->
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            Save {{ $organizationLabel }}
                        </button>

                        {!! view_render_event('admin.organizations.create.save_buttons.before') !!}
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                    {{ $organizationLabel }} Information
                </p>

                {!! view_render_event('admin.contacts.organizations.create.account_information.before') !!}

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
                            :value="old('name')"
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
                            value="{{ old('website') }}"
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
                            :value="old('phone')"
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
                            :value="old('fax')"
                            label="Fax"
                        />

                        <x-admin::form.control-group.error control-name="fax" />
                    </x-admin::form.control-group>
                </div>

                {!! view_render_event('admin.contacts.organizations.create.account_information.after') !!}
            </div>

            <!-- Additional Information -->
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                    Additional Information
                </p>

                {!! view_render_event('admin.contacts.organizations.create.additional_information.before') !!}

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

                            $selectedType = strtolower((string) old('organization_type', old('type') ?? $detectRouteType ?? ''));
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
                        <div class="mb-1.5 flex items-center justify-between gap-3">
                            <x-admin::form.control-group.label class="!mb-0">
                                Industry
                            </x-admin::form.control-group.label>

                            <div class="flex items-center gap-3 text-xs">
                                <button type="button" class="text-brandColor hover:underline" onclick="event.preventDefault(); var modal = document.getElementById('industry-create-modal'); var input = document.getElementById('quick_industry_name'); var error = document.getElementById('quick_industry_error'); if (input) input.value = ''; if (error) { error.textContent = ''; error.classList.add('hidden'); } if (modal) { modal.style.display = 'flex'; modal.classList.remove('hidden'); modal.classList.add('flex'); } if (input) setTimeout(function () { input.focus(); }, 0);">Add Industry</button>
                                <a href="{{ route('admin.' . $routePrefix . '.organizations.industries.index') }}" class="text-brandColor hover:underline">View Industries</a>
                            </div>
                        </div>

                        @php
                            $selectedIndustry = old('industry');
                        @endphp

                        <select
                            id="industry"
                            name="industry"
                            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-brandColor focus:ring-1 focus:ring-brandColor dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                        >
                            <option value="">Select Industry</option>
                            @foreach ($industries as $industry)
                                <option value="{{ $industry->name }}" @selected($selectedIndustry === $industry->name)>{{ $industry->name }}</option>
                            @endforeach
                        </select>

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
                            :value="old('employees')"
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
                            :value="old('annual_revenue')"
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
                        :value="old('description')"
                        label="Description"
                        rules="max:100"
                    />

                    <x-admin::form.control-group.error control-name="description" />
                </x-admin::form.control-group>

                {!! view_render_event('admin.contacts.organizations.create.additional_information.after') !!}
            </div>

            <!-- Address Information -->
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                @php
                    $addressRows = collect(old('addresses', []))
                        ->filter(fn ($row) => is_array($row) && ! in_array(strtolower((string) ($row['type'] ?? 'other')), ['billing', 'shipping']))
                        ->values()
                        ->all();
                @endphp

                {!! view_render_event('admin.contacts.organizations.create.address_information.before') !!}

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
                            :value="old('billing_street')"
                            label="Billing Street"
                        rules="max:100"
                        />
                    </x-admin::form.control-group>

                    <!-- 4-Column Layout for City, State, Postal Code, Country -->
                    <div style="display: grid !important; grid-template-columns: repeat(4, 1fr) !important; gap: 1rem !important;">
                        <x-admin::form.control-group style="margin: 0 !important;">
                            <x-admin::form.control-group.label>
                                City
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="billing_city"
                                name="billing_city"
                                :value="old('billing_city')"
                                label="City"
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
                                :value="old('billing_state')"
                                label="State / Province"
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
                                :value="old('billing_postcode')"
                                label="Zip / Postal Code"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group style="margin: 0 !important;">
                            <x-admin::form.control-group.label>
                                Country
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="billing_country"
                                name="billing_country"
                                :value="old('billing_country')"
                                label="Country"
                            />
                        </x-admin::form.control-group>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="mb-4" style="margin-top: 1rem;">
                    <div class="mt-2 flex justify-end">
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
                </div>

                <div id="shipping-address-container">
                    <p
                        id="shipping-address-heading"
                        class="mb-4 text-base font-semibold text-gray-800 dark:text-white"
                    >
                        Shipping Address Details
                    </p>

                    <x-admin::form.control-group class="mb-4">
                        <x-admin::form.control-group.label>
                            Shipping Street
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            id="shipping_street"
                            name="shipping_street"
                            :value="old('shipping_street')"
                            label="Shipping Street"
                        rules="max:100"
                        />
                    </x-admin::form.control-group>

                    <!-- 4-Column Layout for City, State, Postal Code, Country -->
                    <div style="display: grid !important; grid-template-columns: repeat(4, 1fr) !important; gap: 1rem !important;">
                        <x-admin::form.control-group style="margin: 0 !important;">
                            <x-admin::form.control-group.label>
                                City
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="shipping_city"
                                name="shipping_city"
                                :value="old('shipping_city')"
                                label="City"
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
                                :value="old('shipping_state')"
                                label="State / Province"
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
                                :value="old('shipping_postcode')"
                                label="Zip / Postal Code"
                            />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group style="margin: 0 !important;">
                            <x-admin::form.control-group.label>
                                Country
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="shipping_country"
                                name="shipping_country"
                                :value="old('shipping_country')"
                                label="Country"
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
                            onclick="window.addAddressRow && window.addAddressRow()"
                        >
                            Add Address
                        </button>
                    </div>

                    <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">
                        Add as many addresses as needed. Each address is stored in the format street, city, state, zip, country.
                    </p>

                    <div id="address-rows" class="flex flex-col gap-3" data-next-index="{{ count($addressRows) }}">
                        @foreach ($addressRows as $index => $row)
                            <div class="address-row rounded-lg border border-gray-200 p-4 dark:border-gray-700" data-index="{{ $index }}">
                                <div class="mb-4 flex items-center justify-between gap-3">
                                    <p class="text-base font-semibold text-gray-800 dark:text-white">Additional Address</p>
                                    <button type="button" class="remove-address-row secondary-button" onclick="window.confirmRemoveOrganizationAddress && window.confirmRemoveOrganizationAddress(event, this)">Remove</button>
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

                    <template id="address-row-template">
                        <div class="address-row rounded-lg border border-gray-200 p-4 dark:border-gray-700" data-index="__INDEX__">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">Additional Address</p>
                                <button type="button" class="remove-address-row secondary-button" onclick="window.confirmRemoveOrganizationAddress && window.confirmRemoveOrganizationAddress(event, this)">Remove</button>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Address Type</label>
                                    <select name="addresses[__INDEX__][type]" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                        <option value="billing">Billing</option>
                                        <option value="shipping">Shipping</option>
                                        <option value="other" selected>Other</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Street</label>
                                    <input type="text" name="addresses[__INDEX__][street]" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">City</label>
                                    <input type="text" name="addresses[__INDEX__][city]" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">State</label>
                                    <input type="text" name="addresses[__INDEX__][state]" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Zip</label>
                                    <input type="text" name="addresses[__INDEX__][postcode]" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Country</label>
                                    <input type="text" name="addresses[__INDEX__][country]" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {!! view_render_event('admin.contacts.organizations.create.address_information.after') !!}
            </div>

            @if ($routePrefix !== 'vendors')
                @include('admin::contacts.organizations.portal-access-fields')
            @endif
        </div>
        <div id="industry-create-modal" class="hidden" style="display: none; position: fixed; inset: 0; z-index: 100000; align-items: center; justify-content: center; padding: 24px; background: rgba(15, 23, 42, 0.45);">
            <div class="dark:bg-gray-900" style="width: 100%; max-width: 480px; max-height: calc(100vh - 48px); overflow-y: auto; border-radius: 8px; background: #fff; padding: 24px; box-shadow: 0 24px 64px rgba(15, 23, 42, 0.24);">
                <div style="margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                    <p class="text-lg font-bold text-gray-800 dark:text-white" style="font-size: 18px; font-weight: 700;">Add Industry</p>
                    <button type="button" class="text-gray-500" style="border: 0; background: transparent; font-size: 28px; line-height: 1; cursor: pointer;" data-industry-modal-close onclick="event.preventDefault(); var modal = document.getElementById('industry-create-modal'); if (modal) { modal.style.display = 'none'; modal.classList.add('hidden'); modal.classList.remove('flex'); }">&times;</button>
                </div>

                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600;">Industry Name</label>
                <input type="text" id="quick_industry_name" class="dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" style="width: 100%; border: 1px solid #d9dee7; border-radius: 6px; padding: 10px 12px; font-size: 14px; outline: none;">
                <p id="quick_industry_error" class="hidden text-xs text-red-600" style="margin-top: 8px; font-size: 12px; color: #dc2626;"></p>

                <div style="margin-top: 22px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="secondary-button" data-industry-modal-close onclick="event.preventDefault(); var modal = document.getElementById('industry-create-modal'); if (modal) { modal.style.display = 'none'; modal.classList.add('hidden'); modal.classList.remove('flex'); }">Cancel</button>
                    <button type="button" class="primary-button" id="quick_industry_save" onclick="window.saveQuickIndustry && window.saveQuickIndustry(event)">Save Industry</button>
                </div>
            </div>
        </div>
    </form>

    {!! view_render_event('admin.organizations.create.form.after') !!}

    <script>
        window.saveQuickIndustry = function (event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            var input = document.getElementById('quick_industry_name');
            var error = document.getElementById('quick_industry_error');
            var saveButton = document.getElementById('quick_industry_save');
            var industrySelect = document.getElementById('industry');
            var modal = document.getElementById('industry-create-modal');
            var name = input && input.value ? input.value.replace(/^\s+|\s+$/g, '') : '';

            if (error) {
                error.textContent = '';
                error.classList.add('hidden');
            }

            if (! name) {
                if (error) {
                    error.textContent = 'Industry name is required.';
                    error.classList.remove('hidden');
                }

                return false;
            }

            if (saveButton) {
                saveButton.disabled = true;
                saveButton.textContent = 'Saving...';
            }

            var request = new XMLHttpRequest();

            request.open('POST', "{{ route('admin.' . $routePrefix . '.organizations.industries.store') }}", true);
            request.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
            request.setRequestHeader('Accept', 'application/json');
            request.setRequestHeader('X-CSRF-TOKEN', "{{ csrf_token() }}");

            request.onreadystatechange = function () {
                if (request.readyState !== 4) {
                    return;
                }

                var payload = {};

                try {
                    payload = JSON.parse(request.responseText || '{}');
                } catch (e) {
                    payload = {};
                }

                if (request.status < 200 || request.status >= 300) {
                    var message = payload.message || (
                        payload.errors && payload.errors.name && payload.errors.name[0]
                            ? payload.errors.name[0]
                            : 'Unable to save industry.'
                    );

                    if (error) {
                        error.textContent = message;
                        error.classList.remove('hidden');
                    }

                    if (saveButton) {
                        saveButton.disabled = false;
                        saveButton.textContent = 'Save Industry';
                    }

                    return;
                }

                if (industrySelect && payload.name) {
                    var exists = false;

                    for (var index = 0; index < industrySelect.options.length; index++) {
                        if (industrySelect.options[index].value === payload.name) {
                            exists = true;
                            break;
                        }
                    }

                    if (! exists) {
                        industrySelect.add(new Option(payload.name, payload.name));
                    }

                    industrySelect.value = payload.name;
                }

                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                if (saveButton) {
                    saveButton.disabled = false;
                    saveButton.textContent = 'Save Industry';
                }

                return false;
            };

            request.send(JSON.stringify({ name: name }));

            return false;
        };
    </script>

    <script>
        (function () {
            const modal = document.getElementById('industry-create-modal');
            const input = document.getElementById('quick_industry_name');
            const error = document.getElementById('quick_industry_error');
            const saveButton = document.getElementById('quick_industry_save');
            const industrySelect = document.getElementById('industry');

            function setOpen(open) {
                if (! modal) return;
                modal.style.display = open ? 'flex' : 'none';
                modal.classList.toggle('hidden', !open);
                modal.classList.toggle('flex', open);
                if (open && input) {
                    setTimeout(() => input.focus(), 0);
                }
            }

            function setError(message) {
                if (! error) return;
                error.textContent = message || '';
                error.classList.toggle('hidden', !message);
            }

            function openModal() {
                if (input) input.value = '';
                setError('');
                setOpen(true);
            }

            window.openIndustryModal = function (event) {
                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                openModal();
            };

            window.closeIndustryModal = function (event) {
                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                setOpen(false);
            };

            document.addEventListener('click', function (event) {
                if (event.target.closest('[data-industry-modal-close]')) {
                    window.closeIndustryModal(event);
                }
            });

        })();

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

        const getNextAddressIndex = (addressRows) => {
            if (!addressRows) {
                return 0;
            }

            const rows = Array.from(addressRows.querySelectorAll('.address-row'));

            if (! rows.length) {
                return 0;
            }

            return Math.max(...rows.map((row) => Number(row.dataset.index || 0))) + 1;
        };

        const buildAddressRowHtml = (index) => {
            return `
                <div class="address-row rounded-lg border border-gray-200 p-4 dark:border-gray-700" data-index="${index}">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <p class="text-base font-semibold text-gray-800 dark:text-white">Additional Address</p>
                        <button type="button" class="remove-address-row secondary-button" onclick="window.confirmRemoveOrganizationAddress && window.confirmRemoveOrganizationAddress(event, this)">Remove</button>
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
                </div>
            `;
        };

        window.confirmRemoveOrganizationAddress = (event, button) => {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            const removeAddress = () => {
                const row = button ? button.closest('.address-row') : null;

                if (row) {
                    row.remove();
                }
            };

            if (window.emitter) {
                window.emitter.emit('open-confirm-modal', {
                    title: 'Confirm Removal',
                    message: 'Remove this address?',
                    options: {
                        btnDisagree: 'Cancel',
                        btnAgree: 'Remove',
                    },
                    agree: removeAddress,
                });

                return;
            }

            if (confirm('Remove this address?')) {
                removeAddress();
            }
        };

        window.addAddressRow = () => {
            const addressRows = document.getElementById('address-rows');

            if (! addressRows) {
                return;
            }

            const index = getNextAddressIndex(addressRows);
            addressRows.insertAdjacentHTML('beforeend', buildAddressRowHtml(index));
        };

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
@endcomponent
