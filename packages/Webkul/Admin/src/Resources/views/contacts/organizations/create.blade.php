
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.contacts.organizations.create.title')
    </x-slot>

    {!! view_render_event('admin.organizations.create.form.before') !!}

    <x-admin::form
        :action="route('admin.contacts.organizations.store')"
        method="POST"
    >

        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.organizations.create.breadcrumbs.before') !!}

                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="contacts.organizations.create" />

                    {!! view_render_event('admin.organizations.create.breadcrumbs.before') !!}

                    <div class="text-xl font-bold dark:text-gray-300">
                        @lang('admin::app.contacts.organizations.create.title')
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
                            @lang('admin::app.contacts.organizations.create.save-btn')
                        </button>

                        {!! view_render_event('admin.organizations.create.save_buttons.before') !!}
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                    Account Information
                </p>

                {!! view_render_event('admin.contacts.organizations.create.account_information.before') !!}

                <div style="display: grid !important; grid-template-columns: repeat(2, 1fr) !important; gap: 1rem !important;">
                    <!-- Account Name -->
                    <x-admin::form.control-group style="margin: 0 !important;">
                        <x-admin::form.control-group.label class="required">
                            Account Name
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            id="name"
                            name="name"
                            rules="required|max:100"
                            :value="old('name')"
                            label="Account Name"
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
                            Type
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="select"
                            id="type"
                            name="type"
                            label="Type"
                        >
                            <option value="">Select Type</option>
                            <option value="factory">Factory</option>
                            <option value="customer">Customer</option>
                            <option value="vendor">Vendor</option>
                            <option value="marketing">Marketing</option>
                            <option value="prospect">Prospect</option>
                            <option value="salesrep">Sales Rep</option>
                            <option value="other">Other</option>
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="type" />
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
                            <option value="">Select Industry</option>
                            <option value="home_apparel">Home Apparel</option>
                            <option value="other_shipping">Other Shipping</option>
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
                    />

                    <x-admin::form.control-group.error control-name="description" />
                </x-admin::form.control-group>

                {!! view_render_event('admin.contacts.organizations.create.additional_information.after') !!}
            </div>

            <!-- Address Information -->
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
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
                                :value="old('billing_country')"
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
                                :value="old('billing_state')"
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
                                :value="old('billing_city')"
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
                                :value="old('billing_postcode')"
                                label="Zip / Postal Code"
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
                                :value="old('shipping_country')"
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
                                :value="old('shipping_state')"
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
                                :value="old('shipping_city')"
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
                                :value="old('shipping_postcode')"
                                label="Zip / Postal Code"
                            />
                        </x-admin::form.control-group>
                    </div>
                </div>

                {!! view_render_event('admin.contacts.organizations.create.address_information.after') !!}
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.organizations.create.form.after') !!}

    <script>
        window.toggleShippingAddress = (checkbox) => {
            const shippingContainer = document.getElementById('shipping-address-container');
            const shippingHeading = document.getElementById('shipping-address-heading');
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

            if (!checkbox || !shippingContainer) {
                return;
            }

            if (checkbox.checked) {
                copyBillingToShipping();
                shippingContainer.style.display = 'none';
            } else {
                resetShippingReadonly();
                shippingContainer.style.display = 'block';
            }

            syncOnBillingChange();
        };

        const checkbox = document.getElementById('same-as-billing');
        const form = document.querySelector('form');

        if (checkbox) {
            window.toggleShippingAddress(checkbox);
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
