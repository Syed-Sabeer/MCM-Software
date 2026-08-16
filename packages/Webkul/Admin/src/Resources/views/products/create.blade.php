@php
    $customersForSearch = $customers->map(fn ($customer) => [
        'id'   => (string) $customer->id,
        'name' => $customer->name,
    ])->values();

    $selectedCustomerId = (string) old('customer_organization_id', $duplicateDraft['customer_organization_id'] ?? '');
    $selectedCustomerName = $customers->firstWhere('id', (int) $selectedCustomerId)?->name ?? '';
    $duplicateDraftJson = $duplicateDraft ?? null;
    $initialColorRows = old('colors', $duplicateDraft['colors'] ?? []);
    $initialConsumptions = old('consumptions', $duplicateDraft['consumptions'] ?? []);
    $initialSections = old('production_sections', $duplicateDraft['production_sections'] ?? []);
    $colorReferenceOptions = $colorReferences->map(fn ($color) => [
        'name' => $color->name,
        'code' => $color->code,
    ])->values();
    $materialReferenceOptions = $materialReferences->map(fn ($material) => [
        'id' => $material->id,
        'name' => $material->name,
        'qty' => $material->qty,
        'unit' => $material->unit,
        'color_name' => $material->color_name,
        'color_code' => $material->color_code,
        'vendor_ids' => $material->vendors->pluck('id')->map(fn ($id) => (int) $id)->values(),
        'vendor_names' => $material->vendors->pluck('name')->values(),
    ])->values();
    $vendorOptions = $vendors->map(fn ($vendor) => [
        'id' => (int) $vendor->id,
        'name' => $vendor->name,
    ])->values();
    $unitOptions = $units->map(fn ($unit) => ['name' => $unit->name])->values();
@endphp

<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.products.create.title')
    </x-slot>

    <x-admin::form :action="route('admin.products.store')" method="POST" enctype="multipart/form-data">
        @if ($duplicateDraftJson)
            <input type="hidden" name="duplicate_from" value="{{ request('duplicate_from') }}">
        @endif

        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs name="products.create" />
                    <div class="text-xl font-bold dark:text-white">@lang('admin::app.products.create.title')</div>
                </div>

                @if (bouncer()->hasPermission('products.create'))
                    <button type="submit" class="primary-button">@lang('admin::app.products.create.save-btn')</button>
                @endif
            </div>

            <div class="flex gap-2.5 max-xl:flex-wrap">
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Product Details</p>

                        <div class="grid gap-4" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Item Code</label>
                                <input type="text" name="sku" id="item_code" value="{{ old('sku', $duplicateDraft['sku'] ?? '') }}" required class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <x-admin::form.control-group.error control-name="sku" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Internal Code</label>
                                <input type="text" name="internal_code" id="internal_code" value="{{ old('internal_code', $duplicateDraft['internal_code'] ?? '') }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <x-admin::form.control-group.error control-name="internal_code" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Product Name</label>
                                <input type="text" name="name" id="product_name" value="{{ old('name', $duplicateDraft['name'] ?? '') }}" required class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <x-admin::form.control-group.error control-name="name" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Size</label>
                                <input type="text" name="size" value="{{ old('size', $duplicateDraft['size'] ?? '') }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <x-admin::form.control-group.error control-name="size" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Weight</label>
                                <input type="number" step="0.01" min="0" name="weight" value="{{ old('weight', $duplicateDraft['weight'] ?? '') }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <x-admin::form.control-group.error control-name="weight" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Weight Unit</label>
                                <select name="weight_unit" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    <option value="">Select Unit</option>
                                    <option value="gsm" @selected(old('weight_unit', $duplicateDraft['weight_unit'] ?? '') === 'gsm')>GSM</option>
                                    <option value="oz" @selected(old('weight_unit', $duplicateDraft['weight_unit'] ?? '') === 'oz')>OZ</option>
                                </select>
                                <x-admin::form.control-group.error control-name="weight_unit" />
                            </div>

                            <div style="grid-column: 1 / -1;">
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Customer (Optional)</label>
                                <v-customer-lookup
                                    input-name="customer_organization_id"
                                    :customers='@json($customersForSearch)'
                                    :value='@json($selectedCustomerId ? ['id' => $selectedCustomerId, 'name' => $selectedCustomerName] : null)'
                                    create-url="{{ route('admin.customers.organizations.quick_create') }}"
                                ></v-customer-lookup>
                                <x-admin::form.control-group.error control-name="customer_organization_id" />
                            </div>
                        </div>

                        <div class="mt-4 rounded border border-gray-200 p-3 dark:border-gray-700">
                            <p class="mb-3 text-sm font-semibold text-gray-800 dark:text-white">Pricing</p>

                            <div class="grid gap-4" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Cost Price</label>
                                    <input type="number" step="0.0001" min="0" name="cost_price" value="{{ old('cost_price', $duplicateDraft['cost_price'] ?? '') }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    <x-admin::form.control-group.error control-name="cost_price" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Selling Price</label>
                                    <input type="number" step="0.0001" min="0" name="selling_price" value="{{ old('selling_price', old('price', $duplicateDraft['selling_price'] ?? $duplicateDraft['price'] ?? '')) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    <x-admin::form.control-group.error control-name="selling_price" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">Material Consumption</p>
                            <div class="flex flex-wrap items-center justify-end gap-3">
                                <button type="button" id="quick-add-material" class="secondary-button inline-flex items-center gap-1.5">
                                    <span class="icon-add text-base"></span>
                                    New Material
                                </button>

                                <button type="button" data-quick-add-unit class="text-sm font-medium text-red-600 hover:underline">
                                    Add Unit
                                </button>

                                <a href="{{ route('admin.settings.units.index') }}" class="text-sm font-medium text-red-600 hover:underline">View Units</a>

                                <button type="button" id="add-consumption" class="secondary-button">Add Row</button>
                            </div>
                        </div>

                        <datalist id="product-material-reference-options">
                            @foreach ($materialReferenceOptions as $materialReference)
                                <option value="{{ $materialReference['name'] }}">{{ $materialReference['unit'] }}</option>
                            @endforeach
                        </datalist>

                        <p class="mb-2 px-1 text-xs text-gray-500 dark:text-gray-400">
                            Search and pick a material, then complete material name, color, quantity, unit, and vendor in a single row.
                        </p>

                        <div id="consumptions-container" class="flex flex-col gap-3"></div>
                        <x-admin::form.control-group.error control-name="consumptions" />
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">Production Sections</p>
                            <button type="button" id="add-production-section" class="secondary-button">Add Section</button>
                        </div>

                        <div id="production-sections-container" class="flex flex-col gap-3"></div>
                        <x-admin::form.control-group.error control-name="production_sections" />
                    </div>
                </div>

                <div class="flex w-[540px] max-w-full flex-col gap-2 max-sm:w-full">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">Color Variants</p>
                            <button type="button" id="add-color" class="secondary-button">Add Color</button>
                        </div>

                        <datalist id="product-color-reference-options">
                            @foreach ($colorReferenceOptions as $colorReference)
                                <option value="{{ $colorReference['name'] }}">{{ $colorReference['code'] }}</option>
                            @endforeach
                        </datalist>

                        <div class="mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400" style="display: grid; grid-template-columns: minmax(0, 1fr) 88px 140px 140px 44px; gap: 0.5rem;">
                            <div>Color Name</div>
                            <div>Color</div>
                            <div>Cost Price</div>
                            <div>Selling Price</div>
                            <div></div>
                        </div>

                        <div id="colors-container" class="flex flex-col gap-2"></div>
                        <x-admin::form.control-group.error control-name="colors" />
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-3 text-base font-semibold text-gray-800 dark:text-white">Images</p>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Cover Image</x-admin::form.control-group.label>
                            <input type="file" name="cover_image" accept="image/*" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <x-admin::form.control-group.error control-name="cover_image" />
                        </x-admin::form.control-group>

                        <div class="mt-4">
                            <div class="mb-2 flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Other Images</p>
                                <button type="button" id="add-other-image" class="secondary-button">Add More</button>
                            </div>

                            <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">Add images and optionally link to a color:</p>

                            <div class="mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400" style="display: grid; grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr) 44px; gap: 0.5rem;">
                                <div>Image</div>
                                <div>Color</div>
                                <div></div>
                            </div>

                            <div id="other-images-container" class="flex flex-col gap-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-admin::form>

    <div id="product-quick-material-modal" class="hidden" style="display: none; position: fixed; inset: 0; z-index: 100001; align-items: center; justify-content: center; padding: 24px; background: rgba(15, 23, 42, 0.45);">
        <div class="dark:bg-gray-900" style="width: 100%; max-width: 560px; max-height: calc(100vh - 48px); overflow-y: auto; border-radius: 8px; background: #fff; box-shadow: 0 24px 64px rgba(15, 23, 42, 0.24);">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <p class="text-lg font-bold text-gray-800 dark:text-white">Add Material</p>
                <button type="button" id="product-quick-material-close" class="icon-cross-large text-2xl text-gray-500" aria-label="Close"></button>
            </div>

            <div class="space-y-4 p-5">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Material Name <span class="text-red-600">*</span></label>
                    <input type="text" id="product_quick_material_name" maxlength="255" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Material Name">
                </div>

                <div class="grid grid-cols-2 gap-4 max-sm:grid-cols-1">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Qty <span class="text-red-600">*</span></label>
                        <input type="number" id="product_quick_material_qty" min="0" step="0.001" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="0.002">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Unit <span class="text-red-600">*</span></label>
                        <select id="product_quick_material_unit" class="product-unit-select w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <option value="">Select Unit</option>
                            @foreach ($unitOptions as $unit)
                                <option value="{{ $unit['name'] }}">{{ $unit['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Vendors</label>
                    <input type="text" id="product_quick_material_vendor_search" class="mb-2 w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Search vendors...">
                    <div id="product_quick_material_vendors" class="overflow-y-auto rounded border border-gray-300 p-2 pr-1 dark:border-gray-700" style="max-height: 160px; scrollbar-width: thin;"></div>
                </div>

                <p id="product_quick_material_error" class="hidden rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/30 dark:text-red-300"></p>
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-200 px-5 py-4 dark:border-gray-700">
                <button type="button" id="product-quick-material-cancel" class="secondary-button">Cancel</button>
                <button type="button" id="product-quick-material-save" class="primary-button">Save Material</button>
            </div>
        </div>
    </div>

    <div id="product-quick-organization-modal" class="hidden" style="display: none; position: fixed; inset: 0; z-index: 100000; align-items: center; justify-content: center; padding: 24px; background: rgba(15, 23, 42, 0.45);">
        <div class="dark:bg-gray-900" style="width: 100%; max-width: 480px; max-height: calc(100vh - 48px); overflow-y: auto; border-radius: 8px; background: #fff; padding: 24px; box-shadow: 0 24px 64px rgba(15, 23, 42, 0.24);">
            <div style="margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                <p id="product-quick-organization-title" class="text-lg font-bold text-gray-800 dark:text-white" style="font-size: 18px; font-weight: 700;">Add Company</p>
                <button type="button" class="text-gray-500" style="border: 0; background: transparent; font-size: 28px; line-height: 1; cursor: pointer;" onclick="window.closeProductQuickOrganizationModal && window.closeProductQuickOrganizationModal(event)">&times;</button>
            </div>

            <label id="product-quick-organization-label" class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600;">Company Name</label>
            <input type="text" id="product_quick_organization_name" class="dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" style="width: 100%; border: 1px solid #d9dee7; border-radius: 6px; padding: 10px 12px; font-size: 14px; outline: none;">
            <p id="product_quick_organization_error" class="hidden text-xs text-red-600" style="margin-top: 8px; font-size: 12px; color: #dc2626;"></p>

            <div style="margin-top: 22px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="secondary-button" onclick="window.closeProductQuickOrganizationModal && window.closeProductQuickOrganizationModal(event)">Cancel</button>
                <button type="button" class="primary-button" id="product_quick_organization_save" onclick="window.saveProductQuickOrganization && window.saveProductQuickOrganization(event)">Save</button>
            </div>
        </div>
    </div>

    @if ($duplicateDraftJson)
        <x-admin::modal ref="productDuplicateModal" is-active="true">
            <x-slot:header>
                <p class="text-lg font-bold text-gray-800 dark:text-white">Duplicate Product</p>
            </x-slot:header>

            <x-slot:content>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                    Enter the new Item Code. Internal Code will mirror it in real time until you edit it manually. Product Name is prefilled and remains editable.
                </p>

                <div class="grid gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Item Code</label>
                        <input type="text" id="duplicate_modal_item_code" required class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Internal Code</label>
                        <input type="text" id="duplicate_modal_internal_code" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Product Name</label>
                        <input type="text" id="duplicate_modal_name" required class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    </div>
                </div>
            </x-slot:content>

            <x-slot:footer>
                <button type="button" id="apply_duplicate_modal" class="primary-button">Apply</button>
            </x-slot:footer>
        </x-admin::modal>
    @endif

    <script>
        window.productQuickOrganizationState = {
            type: '',
            url: '',
            onSaved: null,
        };

        window.openProductQuickOrganizationModal = function (options) {
            options = options || {};

            var modal = document.getElementById('product-quick-organization-modal');
            var title = document.getElementById('product-quick-organization-title');
            var label = document.getElementById('product-quick-organization-label');
            var input = document.getElementById('product_quick_organization_name');
            var error = document.getElementById('product_quick_organization_error');
            var saveButton = document.getElementById('product_quick_organization_save');
            var type = options.type === 'vendor' ? 'vendor' : 'customer';
            var name = type === 'vendor' ? 'Vendor' : 'Customer';

            window.productQuickOrganizationState = {
                type: type,
                url: options.url || '',
                onSaved: typeof options.onSaved === 'function' ? options.onSaved : null,
            };

            if (title) {
                title.textContent = 'Add ' + name;
            }

            if (label) {
                label.textContent = name + ' Company Name';
            }

            if (saveButton) {
                saveButton.textContent = 'Save ' + name;
                saveButton.disabled = false;
            }

            if (input) {
                input.value = '';
            }

            if (error) {
                error.textContent = '';
                error.classList.add('hidden');
            }

            if (modal) {
                modal.style.display = 'flex';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            if (input) {
                setTimeout(function () {
                    input.focus();
                }, 0);
            }
        };

        window.closeProductQuickOrganizationModal = function (event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            var modal = document.getElementById('product-quick-organization-modal');

            if (modal) {
                modal.style.display = 'none';
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        };

        window.saveProductQuickOrganization = function (event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            var state = window.productQuickOrganizationState || {};
            var input = document.getElementById('product_quick_organization_name');
            var error = document.getElementById('product_quick_organization_error');
            var saveButton = document.getElementById('product_quick_organization_save');
            var type = state.type === 'vendor' ? 'vendor' : 'customer';
            var label = type === 'vendor' ? 'Vendor' : 'Customer';
            var name = input && input.value ? input.value.replace(/^\s+|\s+$/g, '') : '';

            if (error) {
                error.textContent = '';
                error.classList.add('hidden');
            }

            if (! name) {
                if (error) {
                    error.textContent = 'Company name is required.';
                    error.classList.remove('hidden');
                }

                return false;
            }

            if (! state.url) {
                if (error) {
                    error.textContent = 'Unable to save ' + label.toLowerCase() + '.';
                    error.classList.remove('hidden');
                }

                return false;
            }

            if (saveButton) {
                saveButton.disabled = true;
                saveButton.textContent = 'Saving...';
            }

            var request = new XMLHttpRequest();

            request.open('POST', state.url, true);
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
                            : 'Unable to save ' + label.toLowerCase() + '.'
                    );

                    if (error) {
                        error.textContent = message;
                        error.classList.remove('hidden');
                    }

                    if (saveButton) {
                        saveButton.disabled = false;
                        saveButton.textContent = 'Save ' + label;
                    }

                    return;
                }

                if (state.onSaved) {
                    state.onSaved(payload);
                }

                window.closeProductQuickOrganizationModal();

                if (saveButton) {
                    saveButton.disabled = false;
                    saveButton.textContent = 'Save ' + label;
                }
            };

            request.send(JSON.stringify({ name: name, type: type }));

            return false;
        };
    </script>

    @include('admin::products.partials.quick-unit-modal', ['unitOptions' => $unitOptions])

    @pushOnce('scripts')
        <script type="module">
            app.component('v-customer-lookup', {
                template: `
                    <div class="relative" ref="lookup">
                        <div class="relative inline-block w-full" @click="toggle">
                            <div class="relative flex items-center justify-between rounded border border-gray-200 p-2 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:text-gray-300">
                                <span class="overflow-hidden text-ellipsis" :title="selectedItem?.name">
                                    @{{ selectedItem?.name !== '' ? selectedItem?.name : 'Click to add' }}
                                </span>

                                <div class="flex items-center gap-2">
                                    <i
                                        v-if="selectedItem?.name && ! isSearching"
                                        class="icon-cross-large cursor-pointer text-xl text-gray-600"
                                        @click.stop="remove"
                                    ></i>

                                    <i class="text-2xl text-gray-600" :class="showPopup ? 'icon-up-arrow' : 'icon-down-arrow'"></i>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" :name="inputName" :value="selectedItem.id || ''">

                        <div
                            v-if="showPopup"
                            class="absolute top-full z-10 mt-1 flex w-full origin-top transform flex-col gap-2 rounded-lg border border-gray-200 bg-white p-2 shadow-lg transition-transform dark:border-gray-900 dark:bg-gray-800"
                        >
                            <div class="relative flex items-center">
                                <input
                                    type="text"
                                    v-model="searchTerm"
                                    class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                    placeholder="Search"
                                    ref="searchInput"
                                />
                            </div>

                            <ul class="max-h-40 divide-y divide-gray-100 overflow-y-auto">
                                <li
                                    class="cursor-pointer px-4 py-2 text-gray-800 transition-colors hover:bg-blue-100 dark:text-white dark:hover:bg-gray-900"
                                    @click="selectItem({ id: '', name: '' })"
                                >
                                    Global Product
                                </li>

                                <li
                                    v-for="item in filteredResults"
                                    :key="item.id"
                                    class="cursor-pointer px-4 py-2 text-gray-800 transition-colors hover:bg-blue-100 dark:text-white dark:hover:bg-gray-900"
                                    @click="selectItem(item)"
                                >
                                    @{{ item.name }}
                                </li>

                                <li v-if="! filteredResults.length" class="px-4 py-2 text-gray-500">
                                    No results
                                </li>
                            </ul>

                            <div class="mt-2 border-t border-gray-100 pt-2 dark:border-gray-700">
                                <button type="button" class="text-xs font-medium text-brandColor hover:underline" @click.stop="openCreateCustomerModal">
                                    Add Customer
                                </button>
                            </div>
                        </div>
                    </div>
                `,

                props: {
                    customers: {
                        type: Array,
                        default: () => [],
                    },
                    inputName: {
                        type: String,
                        required: true,
                    },
                    value: {
                        type: Object,
                        default: null,
                    },
                    createUrl: {
                        type: String,
                        default: '',
                    },
                },

                data() {
                    return {
                        showPopup: false,
                        searchTerm: '',
                        isSearching: false,
                        selectedItem: this.value || { id: '', name: '' },
                        localCustomers: this.customers.slice(),
                    };
                },

                computed: {
                    filteredResults() {
                        const query = (this.searchTerm || '').toLowerCase().trim();

                        if (! query) {
                            return this.localCustomers;
                        }

                        return this.localCustomers.filter((item) => (item.name || '').toLowerCase().includes(query));
                    },
                },

                mounted() {
                    window.addEventListener('click', this.handleFocusOut);
                },

                beforeUnmount() {
                    window.removeEventListener('click', this.handleFocusOut);
                },

                methods: {
                    toggle() {
                        this.showPopup = ! this.showPopup;

                        if (this.showPopup) {
                            this.$nextTick(() => this.$refs.searchInput.focus());
                        }
                    },

                    selectItem(item) {
                        this.selectedItem = item || { id: '', name: '' };
                        this.searchTerm = '';
                        this.showPopup = false;
                    },

                    remove() {
                        this.selectedItem = { id: '', name: '' };
                        this.searchTerm = '';
                    },

                    openCreateCustomerModal() {
                        window.openProductQuickOrganizationModal({
                            type: 'customer',
                            url: this.createUrl,
                            onSaved: (customer) => {
                                const item = { id: String(customer.id), name: customer.name };
                                const exists = this.localCustomers.some((existing) => String(existing.id) === String(item.id));

                                if (! exists) {
                                    this.localCustomers.push(item);
                                    this.localCustomers.sort((a, b) => String(a.name).localeCompare(String(b.name)));
                                }

                                this.selectItem(item);
                            },
                        });
                    },

                    handleFocusOut(event) {
                        const lookup = this.$refs.lookup;

                        if (lookup && ! lookup.contains(event.target)) {
                            this.showPopup = false;
                        }
                    },
                },
            });

            function initProductCreateForm() {
                var itemCodeInput = document.getElementById('item_code');
                var internalCodeInput = document.getElementById('internal_code');
                var colorsContainer = document.getElementById('colors-container');
                var addColorButton = document.getElementById('add-color');
                var consumptionsContainer = document.getElementById('consumptions-container');
                var sectionsContainer = document.getElementById('production-sections-container');
                var addConsumptionButton = document.getElementById('add-consumption');
                var quickAddMaterialButton = document.getElementById('quick-add-material');
                var addSectionButton = document.getElementById('add-production-section');
                var otherImagesContainer = document.getElementById('other-images-container');
                var addOtherImageButton = document.getElementById('add-other-image');
                var quickMaterialModal = document.getElementById('product-quick-material-modal');
                var quickMaterialName = document.getElementById('product_quick_material_name');
                var quickMaterialQty = document.getElementById('product_quick_material_qty');
                var quickMaterialUnit = document.getElementById('product_quick_material_unit');
                var quickMaterialVendorSearch = document.getElementById('product_quick_material_vendor_search');
                var quickMaterialVendors = document.getElementById('product_quick_material_vendors');
                var quickMaterialError = document.getElementById('product_quick_material_error');
                var quickMaterialSave = document.getElementById('product-quick-material-save');

                var oldColors = @json($initialColorRows);
                var oldConsumptions = @json($initialConsumptions);
                var oldSections = @json($initialSections);
                var duplicateDraft = @json($duplicateDraftJson);
                var colorReferences = @json($colorReferenceOptions);
                var materialReferences = @json($materialReferenceOptions);
                var vendorOptions = @json($vendorOptions);
                var vendorQuickCreateUrl = "{{ route('admin.vendors.organizations.quick_create') }}";
                var materialQuickCreateUrl = "{{ route('admin.settings.material_references.store') }}";
                var lastAutoInternalCode = internalCodeInput ? (internalCodeInput.value || '') : '';
                var colorReferenceMap = {};
                var materialReferenceMap = {};
                var materialReferenceIdMap = {};
                var quickMaterialVendorIds = [];
                var quickMaterialOnSaved = null;

                colorReferences.forEach(function (reference) {
                    if (!reference || !reference.name) {
                        return;
                    }

                    colorReferenceMap[String(reference.name).trim().toLowerCase()] = reference.code || '';
                });

                materialReferences.forEach(function (reference) {
                    if (!reference || !reference.name) {
                        return;
                    }

                    materialReferenceMap[String(reference.name).trim().toLowerCase()] = reference;
                    if (reference.id !== undefined && reference.id !== null) {
                        materialReferenceIdMap[String(reference.id)] = reference;
                    }
                });

                function escapeHtml(value) {
                    return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                }

                function valueOf(obj, key, fallback) {
                    if (obj && typeof obj === 'object' && obj[key] !== undefined && obj[key] !== null) {
                        return obj[key];
                    }

                    return fallback;
                }

                function confirmProductRemoval(message, onAgree) {
                    var confirmConfig = {
                        title: 'Confirm Removal',
                        message: message || 'Are you sure you want to remove this item?',
                        options: {
                            btnDisagree: 'Cancel',
                            btnAgree: 'Remove',
                        },
                        agree: onAgree,
                    };

                    if (window.emitter) {
                        window.emitter.emit('open-confirm-modal', confirmConfig);

                        return;
                    }

                    if (confirm(confirmConfig.message)) {
                        onAgree();
                    }
                }

                function updateInternalCodeFromItemCode() {
                    if (!itemCodeInput || !internalCodeInput) {
                        return;
                    }

                    var currentInternalCode = internalCodeInput.value || '';

                    if (currentInternalCode === '' || currentInternalCode === lastAutoInternalCode) {
                        internalCodeInput.value = itemCodeInput.value;
                        lastAutoInternalCode = internalCodeInput.value;
                    }
                }

                function normalizeColorCode(value) {
                    var trimmed = String(value || '').trim();

                    if (!trimmed) {
                        return '';
                    }

                    return trimmed.charAt(0) === '#' ? trimmed : ('#' + trimmed);
                }

                function getColorReferenceCode(name) {
                    return colorReferenceMap[String(name || '').trim().toLowerCase()] || '';
                }

                function getMaterialReference(name) {
                    return materialReferenceMap[String(name || '').trim().toLowerCase()] || null;
                }

                function setQuickMaterialError(message) {
                    if (!quickMaterialError) {
                        return;
                    }

                    quickMaterialError.textContent = message || '';
                    quickMaterialError.classList.toggle('hidden', !message);
                }

                function renderQuickMaterialVendors(term) {
                    if (!quickMaterialVendors) {
                        return;
                    }

                    var query = String(term || '').trim().toLowerCase();
                    var filtered = vendorOptions.filter(function (vendor) {
                        return !query || String(vendor.name || '').toLowerCase().includes(query);
                    });

                    quickMaterialVendors.innerHTML = '';

                    if (!filtered.length) {
                        quickMaterialVendors.innerHTML = '<p class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400">No vendors found.</p>';
                        return;
                    }

                    filtered.forEach(function (vendor) {
                        var checked = quickMaterialVendorIds.includes(String(vendor.id)) ? ' checked' : '';
                        quickMaterialVendors.innerHTML += '<label class="mb-1 flex cursor-pointer items-center gap-2 rounded px-2 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-800"><input type="checkbox" class="product-quick-material-vendor h-4 w-4" value="' + escapeHtml(vendor.id) + '"' + checked + '><span>' + escapeHtml(vendor.name) + '</span></label>';
                    });
                }

                function closeQuickMaterialModal() {
                    if (!quickMaterialModal) {
                        return;
                    }

                    quickMaterialModal.style.display = 'none';
                    quickMaterialModal.classList.add('hidden');
                    quickMaterialModal.classList.remove('flex');
                    quickMaterialOnSaved = null;
                }

                function openQuickMaterialModal(onSaved) {
                    quickMaterialOnSaved = typeof onSaved === 'function' ? onSaved : null;
                    quickMaterialVendorIds = [];

                    if (quickMaterialName) quickMaterialName.value = '';
                    if (quickMaterialQty) quickMaterialQty.value = '';
                    if (quickMaterialUnit) quickMaterialUnit.value = '';
                    if (quickMaterialVendorSearch) quickMaterialVendorSearch.value = '';
                    if (quickMaterialSave) {
                        quickMaterialSave.disabled = false;
                        quickMaterialSave.textContent = 'Save Material';
                    }

                    setQuickMaterialError('');
                    renderQuickMaterialVendors('');

                    if (quickMaterialModal) {
                        quickMaterialModal.style.display = 'flex';
                        quickMaterialModal.classList.remove('hidden');
                        quickMaterialModal.classList.add('flex');
                    }

                    if (quickMaterialName) {
                        setTimeout(function () { quickMaterialName.focus(); }, 0);
                    }
                }

                function registerMaterialReference(savedMaterial) {
                    var savedVendors = Array.isArray(savedMaterial.vendors) ? savedMaterial.vendors : [];
                    var reference = {
                        id: Number(savedMaterial.id),
                        name: savedMaterial.name || '',
                        qty: savedMaterial.qty || '',
                        unit: savedMaterial.unit || '',
                        color_name: savedMaterial.color_name || '',
                        color_code: savedMaterial.color_code || '',
                        vendor_ids: savedVendors.map(function (vendor) { return Number(vendor.id); }),
                        vendor_names: savedVendors.map(function (vendor) { return vendor.name; }),
                    };

                    materialReferences.push(reference);
                    materialReferences.sort(function (a, b) {
                        return String(a.name || '').localeCompare(String(b.name || ''));
                    });
                    materialReferenceMap[String(reference.name).trim().toLowerCase()] = reference;
                    materialReferenceIdMap[String(reference.id)] = reference;

                    var datalist = document.getElementById('product-material-reference-options');
                    if (datalist) {
                        var option = document.createElement('option');
                        option.value = reference.name;
                        option.textContent = reference.unit || '';
                        datalist.appendChild(option);
                    }

                    return reference;
                }

                function saveQuickMaterial() {
                    var name = String(quickMaterialName ? quickMaterialName.value : '').trim();
                    var qty = String(quickMaterialQty ? quickMaterialQty.value : '').trim();
                    var unit = String(quickMaterialUnit ? quickMaterialUnit.value : '').trim();

                    if (!name || qty === '' || !unit) {
                        setQuickMaterialError('Material name, quantity, and unit are required.');
                        return;
                    }

                    setQuickMaterialError('');
                    if (quickMaterialSave) {
                        quickMaterialSave.disabled = true;
                        quickMaterialSave.textContent = 'Saving...';
                    }

                    fetch(materialQuickCreateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        },
                        body: JSON.stringify({
                            name: name,
                            qty: qty,
                            unit: unit,
                            vendor_ids: quickMaterialVendorIds,
                        }),
                    })
                        .then(function (response) {
                            return response.json().catch(function () { return {}; }).then(function (payload) {
                                if (!response.ok) {
                                    var errors = payload.errors || {};
                                    var firstError = Object.keys(errors).length && Array.isArray(errors[Object.keys(errors)[0]])
                                        ? errors[Object.keys(errors)[0]][0]
                                        : '';
                                    throw new Error(firstError || payload.message || 'Unable to save material.');
                                }

                                return payload;
                            });
                        })
                        .then(function (payload) {
                            var reference = registerMaterialReference(payload.data || payload);
                            var onSaved = quickMaterialOnSaved;
                            closeQuickMaterialModal();

                            if (onSaved) {
                                onSaved(reference);
                            }

                            if (window.emitter) {
                                window.emitter.emit('add-flash', { type: 'success', message: payload.message || 'Material created successfully.' });
                            }
                        })
                        .catch(function (error) {
                            setQuickMaterialError(error.message || 'Unable to save material.');
                        })
                        .finally(function () {
                            if (quickMaterialSave) {
                                quickMaterialSave.disabled = false;
                                quickMaterialSave.textContent = 'Save Material';
                            }
                        });
                }

                function bindColorRow(row) {
                    if (!row) {
                        return;
                    }

                    var nameInput = row.querySelector('.color-name-input');
                    var colorCodeInput = row.querySelector('.color-code-input');
                    var colorPicker = row.querySelector('.color-picker');

                    if (!nameInput || !colorCodeInput || !colorPicker) {
                        return;
                    }

                    function syncPickerFromCode() {
                        var normalized = normalizeColorCode(colorCodeInput.value);

                        if (/^#[0-9A-Fa-f]{6}$/.test(normalized)) {
                            colorCodeInput.value = normalized;
                            colorPicker.value = normalized;
                        }
                    }

                    function applyReferenceColor() {
                        var matchedCode = getColorReferenceCode(nameInput.value);

                        if (!matchedCode) {
                            refreshOtherImageColorOptions();
                            return;
                        }

                        var normalized = normalizeColorCode(matchedCode);

                        if (/^#[0-9A-Fa-f]{6}$/.test(normalized)) {
                            colorCodeInput.value = normalized;
                            colorPicker.value = normalized;
                        }

                        refreshOtherImageColorOptions();
                    }

                    nameInput.addEventListener('input', refreshOtherImageColorOptions);
                    nameInput.addEventListener('change', applyReferenceColor);
                    nameInput.addEventListener('blur', applyReferenceColor);

                    colorCodeInput.addEventListener('input', syncPickerFromCode);
                    colorCodeInput.addEventListener('change', syncPickerFromCode);

                    colorPicker.addEventListener('input', function () {
                        colorCodeInput.value = colorPicker.value;
                    });

                    syncPickerFromCode();
                    applyReferenceColor();
                }

                function addColorRow(data) {
                    var index = colorsContainer.querySelectorAll('.color-row').length;
                    var colorCode = valueOf(data, 'color_code', '#000000');
                    var costPrice = valueOf(data, 'cost_price', '');
                    var sellingPrice = valueOf(data, 'selling_price', '');
                    var row = document.createElement('div');
                    row.className = 'color-row items-center rounded border border-gray-200 p-2 dark:border-gray-700';
                    row.style.display = 'grid';
                    row.style.gridTemplateColumns = 'minmax(0, 1fr) 88px 140px 140px 44px';
                    row.style.gap = '0.5rem';
                    row.innerHTML = ''
                        + '<input type="text" name="colors[' + index + '][name]" list="product-color-reference-options" class="color-name-input rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Color Name" value="' + escapeHtml(valueOf(data, 'name', '')) + '">'
                        + '<div class="flex items-center gap-2">'
                        + '  <input type="text" name="colors[' + index + '][color_code]" class="color-code-input w-full rounded border border-gray-200 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="#000000" value="' + escapeHtml(colorCode) + '">'
                        + '  <input type="color" class="color-picker h-[40px] w-[40px] cursor-pointer rounded border border-gray-200 p-0 dark:border-gray-700" value="' + escapeHtml(colorCode) + '">'
                        + '</div>'
                        + '<input type="number" step="0.0001" min="0" name="colors[' + index + '][cost_price]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Cost Price" value="' + escapeHtml(costPrice) + '">'
                        + '<input type="number" step="0.0001" min="0" name="colors[' + index + '][selling_price]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Selling Price" value="' + escapeHtml(sellingPrice) + '">'
                        + '<button type="button" class="remove-color inline-flex h-10 w-10 items-center justify-center rounded border border-gray-200 text-lg text-red-600 hover:bg-red-50 dark:border-gray-700 dark:hover:bg-gray-800" aria-label="Remove color">&times;</button>';
                    colorsContainer.appendChild(row);
                    bindColorRow(row);
                    refreshOtherImageColorOptions();
                }

                function colorOptionsMarkup(selectedValue) {
                    var options = '<option value=\"\">No Color</option>';
                    var colorRows = colorsContainer ? colorsContainer.querySelectorAll('.color-row') : [];

                    colorRows.forEach(function (row, index) {
                        var nameInput = row.querySelector('input[name*=\"[name]\"]');
                        var label = nameInput && nameInput.value.trim() ? nameInput.value.trim() : ('Color ' + (index + 1));
                        var value = 'new_' + index;
                        var selectedAttr = selectedValue === value ? ' selected' : '';
                        options += '<option value=\"' + value + '\"' + selectedAttr + '>' + escapeHtml(label) + '</option>';
                    });

                    return options;
                }

                function addOtherImageRow(selectedColor) {
                    if (!otherImagesContainer) {
                        return;
                    }

                    var row = document.createElement('div');
                    row.className = 'other-image-row items-center rounded border border-gray-200 p-2 dark:border-gray-700';
                    row.style.display = 'grid';
                    row.style.gridTemplateColumns = 'minmax(0, 1.5fr) minmax(0, 1fr) 44px';
                    row.style.gap = '0.5rem';
                    row.innerHTML = ''
                        + '<input type=\"file\" name=\"other_images[]\" accept=\"image/*\" class=\"rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300\">'
                        + '<select name=\"other_image_colors[]\" class=\"other-image-color rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300\">'
                        + colorOptionsMarkup(selectedColor || '')
                        + '</select>'
                        + '<button type=\"button\" class=\"remove-other-image inline-flex h-10 w-10 items-center justify-center rounded border border-gray-200 text-lg text-red-600 hover:bg-red-50 dark:border-gray-700 dark:hover:bg-gray-800\" aria-label=\"Remove image\">&times;</button>';
                    otherImagesContainer.appendChild(row);
                }

                function refreshOtherImageColorOptions() {
                    if (!otherImagesContainer) {
                        return;
                    }

                    otherImagesContainer.querySelectorAll('.other-image-color').forEach(function (select) {
                        var selected = select.value;
                        select.innerHTML = colorOptionsMarkup(selected);
                    });
                }

                                function addConsumptionRow(data) {
                    var index = consumptionsContainer.querySelectorAll('.consumption-row').length;
                    var row = document.createElement('div');
                    var selectedVendorIdsRaw = valueOf(data, 'vendor_ids', []);
                    var selectedVendorIds = Array.isArray(selectedVendorIdsRaw)
                        ? selectedVendorIdsRaw.map(function (id) { return String(id); }).filter(Boolean)
                        : [];
                    var selectedVendorIdsCsv = selectedVendorIds.join(',');
                    var initialMaterialLabel = valueOf(data, 'name', '') || 'Click to select material';

                    row.className = 'consumption-row rounded-lg border border-gray-200 bg-gray-50 p-3 shadow-sm dark:border-gray-700 dark:bg-gray-900/40';
                    row.setAttribute('data-selected-vendors', selectedVendorIdsCsv);
                    row.innerHTML = ''
                        + '<input type="hidden" name="consumptions[' + index + '][material_reference_id]" class="consumption-reference-id" value="' + escapeHtml(valueOf(data, 'material_reference_id', '')) + '">'
                        + '<div class="grid gap-3" style="display:grid;grid-template-columns:minmax(0,0.95fr) minmax(0,1.05fr);gap:0.75rem;">'
                        + '  <div class="min-w-0">'
                        + '    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Select Material</label>'
                        + '    <div class="consumption-reference-lookup relative">'
                        + '      <button type="button" class="consumption-reference-toggle flex w-full items-center justify-between rounded border border-gray-200 bg-white px-3 py-2 text-left text-sm hover:border-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">'
                        + '        <span class="consumption-reference-selected overflow-hidden text-ellipsis whitespace-nowrap" title="' + escapeHtml(initialMaterialLabel) + '">' + escapeHtml(initialMaterialLabel) + '</span>'
                        + '        <span class="text-xs text-gray-500 dark:text-gray-400">v</span>'
                        + '      </button>'
                        + '      <div class="consumption-reference-popup absolute left-0 right-0 top-full z-10 mt-1 hidden rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-800">'
                        + '        <input type="text" class="consumption-reference-search w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="Search material...">'
                        + '        <div class="consumption-reference-options mt-2 max-h-44 overflow-y-auto rounded border border-gray-200 p-1 dark:border-gray-700"></div>'
                        + '        <div class="mt-2 border-t border-gray-100 pt-2 dark:border-gray-700">'
                        + '          <button type="button" class="consumption-material-create-toggle text-xs font-medium text-brandColor hover:underline">New Material</button>'
                        + '        </div>'
                        + '      </div>'
                        + '    </div>'
                        + '  </div>'
                        + '  <div class="min-w-0">'
                        + '    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Vendors</label>'
                        + '    <div class="consumption-vendor-wrapper relative rounded border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800" data-selected-vendors="' + escapeHtml(selectedVendorIdsCsv) + '">'
                        + '      <button type="button" class="consumption-vendor-toggle flex w-full items-center justify-between rounded border border-gray-200 bg-white px-3 py-2 text-left text-sm hover:border-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" aria-expanded="false">'
                        + '        <span class="consumption-vendor-selected overflow-hidden text-ellipsis whitespace-nowrap">Select vendors</span>'
                        + '        <span class="text-xs text-gray-500 dark:text-gray-400">v</span>'
                        + '      </button>'
                        + '      <div class="consumption-vendor-popup absolute left-0 right-0 top-full z-20 mt-1 hidden rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-800">'
                        + '        <input type="text" class="consumption-vendor-search w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="Search vendors...">'
                        + '        <div class="consumption-vendor-options mt-2 max-h-44 overflow-y-auto rounded border border-gray-200 p-1 dark:border-gray-700"></div>'
                        + '        <p class="consumption-vendor-summary mt-2 text-xs font-medium text-gray-600 dark:text-gray-300">No vendor selected.</p>'
                        + '        <p class="consumption-vendor-empty mt-1 hidden text-xs text-amber-600">No vendors available. Add vendors in Contacts first.</p>'
                        + '        <div class="mt-2 border-t border-gray-100 pt-2 dark:border-gray-700">'
                        + '          <button type="button" class="consumption-vendor-create-toggle text-xs font-medium text-brandColor hover:underline">Add Vendor</button>'
                        + '        </div>'
                        + '      </div>'
                        + '      <div class="consumption-vendor-hidden"></div>'
                        + '    </div>'
                        + '  </div>'
                        + '</div>'
                        + '<div class="mt-3 grid gap-3" style="display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,0.65fr) 110px 110px 44px;gap:0.75rem;">'
                        + '  <div>'
                        + '    <input type="text" name="consumptions[' + index + '][name]" class="consumption-name-input w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Material Name" value="' + escapeHtml(valueOf(data, 'name', '')) + '">'
                        + '  </div>'
                        + '  <div>'
                        + '    <div class="flex items-center gap-2">'
                        + '      <input type="text" list="product-color-reference-options" name="consumptions[' + index + '][color_name]" class="consumption-color-name w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Color" value="' + escapeHtml(valueOf(data, 'color_name', '')) + '">'
                        + '      <input type="hidden" name="consumptions[' + index + '][color_code]" class="consumption-color-code" value="' + escapeHtml(normalizeColorCode(valueOf(data, 'color_code', '')) || '') + '">'
                        + '      <input type="color" class="consumption-color-picker h-[40px] w-[40px] cursor-pointer rounded border border-gray-200 p-0 dark:border-gray-700" value="' + escapeHtml(normalizeColorCode(valueOf(data, 'color_code', '#000000')) || '#000000') + '">'
                        + '    </div>'
                        + '  </div>'
                        + '  <div>'
                        + '    <input type="number" step="0.001" name="consumptions[' + index + '][qty]" class="consumption-qty-input w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Qty" value="' + escapeHtml(valueOf(data, 'qty', '')) + '">'
                        + '  </div>'
                        + '  <div>'
                        + '    <select name="consumptions[' + index + '][unit]" class="consumption-unit-input product-unit-select w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">' + window.productUnitOptionsMarkup(valueOf(data, 'unit', '')) + '</select>'
                        + '  </div>'
                        + '  <div class="flex items-end">'
                        + '    <button type="button" class="remove-consumption inline-flex h-10 w-10 items-center justify-center rounded border border-gray-200 text-lg text-red-600 hover:bg-red-50 dark:border-gray-700 dark:hover:bg-gray-800" aria-label="Remove consumption">&times;</button>'
                        + '  </div>'
                        + '</div>';
                    consumptionsContainer.appendChild(row);
                    bindConsumptionRow(row);
                }

                function updateConsumptionVendorInputs(row, index) {
                    var hiddenContainer = row.querySelector('.consumption-vendor-hidden');
                    if (!hiddenContainer) {
                        return;
                    }

                    var selectedCsv = String(row.getAttribute('data-selected-vendors') || '');
                    var selected = selectedCsv
                        ? selectedCsv.split(',').map(function (id) { return String(id).trim(); }).filter(Boolean)
                        : [];

                    hiddenContainer.innerHTML = '';

                    selected.forEach(function (vendorId) {
                        hiddenContainer.innerHTML += '<input type="hidden" name="consumptions[' + index + '][vendor_ids][]" value="' + escapeHtml(vendorId) + '">';
                    });
                }

                function bindConsumptionRow(row) {
                    var referenceLookup = row.querySelector('.consumption-reference-lookup');
                    var referenceToggle = row.querySelector('.consumption-reference-toggle');
                    var referencePopup = row.querySelector('.consumption-reference-popup');
                    var referenceSelected = row.querySelector('.consumption-reference-selected');
                    var referenceSearch = row.querySelector('.consumption-reference-search');
                    var referenceOptions = row.querySelector('.consumption-reference-options');
                    var materialCreateToggle = row.querySelector('.consumption-material-create-toggle');
                    var referenceIdInput = row.querySelector('.consumption-reference-id');
                    var nameInput = row.querySelector('.consumption-name-input');
                    var qtyInput = row.querySelector('.consumption-qty-input');
                    var unitInput = row.querySelector('.consumption-unit-input');
                    var vendorWrapper = row.querySelector('.consumption-vendor-wrapper');
                    var vendorToggle = row.querySelector('.consumption-vendor-toggle');
                    var vendorSearch = row.querySelector('.consumption-vendor-search');
                    var vendorOptionsWrap = row.querySelector('.consumption-vendor-options');
                    var vendorPopup = row.querySelector('.consumption-vendor-popup');
                    var vendorSelected = row.querySelector('.consumption-vendor-selected');
                    var vendorSummary = row.querySelector('.consumption-vendor-summary');
                    var vendorEmptyState = row.querySelector('.consumption-vendor-empty');
                    var vendorCreateToggle = row.querySelector('.consumption-vendor-create-toggle');
                    var vendorCreatePanel = row.querySelector('.consumption-vendor-create-panel');
                    var vendorCreateName = row.querySelector('.consumption-vendor-create-name');
                    var vendorCreateError = row.querySelector('.consumption-vendor-create-error');
                    var vendorCreateSave = row.querySelector('.consumption-vendor-create-save');
                    var colorNameInput = row.querySelector('.consumption-color-name');
                    var colorCodeInput = row.querySelector('.consumption-color-code');
                    var colorPicker = row.querySelector('.consumption-color-picker');

                    function getSelectedVendorIds() {
                        var selectedCsv = String(row.getAttribute('data-selected-vendors') || '');

                        return selectedCsv
                            ? selectedCsv.split(',').map(function (id) { return String(id).trim(); }).filter(Boolean)
                            : [];
                    }

                    function setSelectedVendorIds(ids) {
                        var selected = Array.isArray(ids) ? ids.map(function (id) { return String(id).trim(); }).filter(Boolean) : [];

                        row.setAttribute('data-selected-vendors', selected.join(','));
                        updateConsumptionVendorInputs(row, Array.prototype.indexOf.call(consumptionsContainer.querySelectorAll('.consumption-row'), row));
                        syncVendorDisplay();
                    }

                    function setVendorPopupOpen(isOpen) {
                        if (vendorPopup) {
                            vendorPopup.classList.toggle('hidden', !isOpen);
                        }

                        if (vendorToggle) {
                            vendorToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                        }
                    }

                    function syncVendorDisplay() {
                        var selectedIds = getSelectedVendorIds();
                        var selectedNames = vendorOptions
                            .filter(function (vendor) { return selectedIds.includes(String(vendor.id)); })
                            .map(function (vendor) { return vendor.name; })
                            .filter(Boolean);
                        var label = selectedNames.length ? selectedNames.join(', ') : 'Select vendors';

                        if (vendorSelected) {
                            vendorSelected.textContent = label;
                            vendorSelected.setAttribute('title', label);
                        }

                        if (vendorSummary) {
                            vendorSummary.textContent = selectedNames.length ? label : 'No vendor selected.';
                        }
                    }

                    function setSelectedReferenceLabel(label) {
                        var safeLabel = String(label || '').trim() || 'Click to select material';

                        if (referenceSelected) {
                            referenceSelected.textContent = safeLabel;
                            referenceSelected.setAttribute('title', safeLabel);
                        }
                    }

                    function renderMaterialOptions(term) {
                        var query = String(term || '').trim().toLowerCase();
                        var filtered = materialReferences.filter(function (reference) {
                            return !query || String(reference.name || '').toLowerCase().includes(query);
                        }).slice(0, 8);

                        referenceOptions.innerHTML = '';

                        if (!filtered.length) {
                            referenceOptions.innerHTML = '<p class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400">No materials found.</p>';
                            return;
                        }

                        filtered.forEach(function (reference) {
                            referenceOptions.innerHTML += '<button type="button" class="consumption-reference-option block w-full rounded px-2 py-1 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-800" data-id="' + escapeHtml(reference.id) + '">' + escapeHtml(reference.name) + ' <span class="text-xs text-gray-500">(' + escapeHtml(reference.unit || '-') + ')</span></button>';
                        });
                    }

                    function applyReference(reference) {
                        if (!reference) {
                            return;
                        }

                        referenceIdInput.value = reference.id || '';
                        nameInput.value = reference.name || nameInput.value || '';
                        setSelectedReferenceLabel(reference.name || nameInput.value || '');

                        if (!qtyInput.value) {
                            qtyInput.value = reference.qty || '';
                        }

                        if (!unitInput.value) {
                            unitInput.value = reference.unit || '';
                        }

                        if (Array.isArray(reference.vendor_ids)) {
                            setSelectedVendorIds(reference.vendor_ids);
                            renderVendorOptions(vendorSearch ? vendorSearch.value : '');
                        }

                        if (!colorNameInput.value && reference.color_name) {
                            colorNameInput.value = reference.color_name;
                        }

                        if (!colorCodeInput.value && reference.color_code) {
                            colorCodeInput.value = normalizeColorCode(reference.color_code);
                            colorPicker.value = normalizeColorCode(reference.color_code);
                        }
                    }

                    function applyReferenceFromName() {
                        var reference = getMaterialReference(nameInput.value);

                        if (!reference) {
                            setSelectedReferenceLabel(nameInput.value || '');
                            return;
                        }

                        applyReference(reference);
                    }

                    function syncColorFromName() {
                        var matchedColorCode = getColorReferenceCode(colorNameInput.value);

                        if (!matchedColorCode) {
                            return;
                        }

                        colorCodeInput.value = normalizeColorCode(matchedColorCode);
                        colorPicker.value = normalizeColorCode(matchedColorCode);
                    }

                    function renderVendorOptions(term) {
                        var query = String(term || '').trim().toLowerCase();
                        var selected = getSelectedVendorIds();
                        var filtered = vendorOptions.filter(function (vendor) {
                            return !query || String(vendor.name || '').toLowerCase().includes(query);
                        });

                        if (vendorOptionsWrap) {
                            vendorOptionsWrap.innerHTML = '';
                        }

                        if (!filtered.length) {
                            if (vendorOptionsWrap) {
                                vendorOptionsWrap.innerHTML = '<p class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400">No vendors found.</p>';
                            }
                            if (vendorEmptyState) {
                                vendorEmptyState.classList.toggle('hidden', vendorOptions.length > 0);
                            }
                            syncVendorDisplay();
                            return;
                        }

                        if (vendorEmptyState) {
                            vendorEmptyState.classList.add('hidden');
                        }

                        filtered.forEach(function (vendor) {
                            var checked = selected.includes(String(vendor.id)) ? ' checked' : '';
                            if (vendorOptionsWrap) {
                                vendorOptionsWrap.innerHTML += '<label class="mb-1 flex items-center gap-2 rounded px-2 py-1 text-sm hover:bg-gray-100 dark:hover:bg-gray-800"><input type="checkbox" class="consumption-vendor-option h-4 w-4" value="' + escapeHtml(vendor.id) + '"' + checked + '><span>' + escapeHtml(vendor.name) + '</span></label>';
                            }
                        });

                        syncVendorDisplay();
                    }

                    function setVendorCreateError(message) {
                        if (!vendorCreateError) {
                            return;
                        }

                        vendorCreateError.textContent = message || '';
                        vendorCreateError.classList.toggle('hidden', !message);
                    }

                    function createVendor() {
                        var name = String(vendorCreateName ? vendorCreateName.value : '').trim();

                        if (!name) {
                            setVendorCreateError('Company name is required.');
                            return;
                        }

                        if (vendorCreateSave) {
                            vendorCreateSave.disabled = true;
                            vendorCreateSave.textContent = 'Saving...';
                        }

                        setVendorCreateError('');

                        fetch(vendorQuickCreateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            },
                            body: JSON.stringify({ name: name, type: 'vendor' }),
                        })
                            .then(function (response) {
                                return response.json().catch(function () { return {}; }).then(function (payload) {
                                    if (!response.ok) {
                                        throw new Error(payload && (payload.message || (payload.errors && payload.errors.name && payload.errors.name[0])) || 'Unable to save vendor.');
                                    }

                                    return payload;
                                });
                            })
                            .then(function (vendor) {
                                var item = { id: Number(vendor.id), name: vendor.name };
                                var exists = vendorOptions.some(function (existing) {
                                    return Number(existing.id) === Number(item.id);
                                });

                                if (!exists) {
                                    vendorOptions.push(item);
                                    vendorOptions.sort(function (a, b) {
                                        return String(a.name).localeCompare(String(b.name));
                                    });
                                }

                                var selected = getSelectedVendorIds();
                                if (!selected.includes(String(item.id))) {
                                    selected.push(String(item.id));
                                }

                                setSelectedVendorIds(selected);
                                if (vendorCreateName) {
                                    vendorCreateName.value = '';
                                }
                                if (vendorCreatePanel) {
                                    vendorCreatePanel.classList.add('hidden');
                                    vendorCreatePanel.classList.remove('flex');
                                }
                                renderVendorOptions(vendorSearch ? vendorSearch.value : '');
                            })
                            .catch(function (error) {
                                setVendorCreateError(error.message);
                            })
                            .finally(function () {
                                if (vendorCreateSave) {
                                    vendorCreateSave.disabled = false;
                                    vendorCreateSave.textContent = 'Save Vendor';
                                }
                            });
                    }

                    if (referenceToggle) {
                        referenceToggle.addEventListener('click', function () {
                            if (referencePopup) {
                                referencePopup.classList.toggle('hidden');
                            }

                            if (referenceSearch) {
                                referenceSearch.value = '';
                                renderMaterialOptions('');
                                setTimeout(function () {
                                    referenceSearch.focus();
                                }, 0);
                            }
                        });
                    }

                    if (referenceSearch) {
                        referenceSearch.addEventListener('input', function () {
                            renderMaterialOptions(referenceSearch.value);
                        });
                    }

                    if (materialCreateToggle) {
                        materialCreateToggle.addEventListener('click', function (event) {
                            event.preventDefault();
                            event.stopPropagation();
                            if (referencePopup) referencePopup.classList.add('hidden');

                            openQuickMaterialModal(function (reference) {
                                applyReference(reference);
                            });
                        });
                    }

                    referenceOptions.addEventListener('click', function (event) {
                        var option = event.target.closest('.consumption-reference-option');
                        if (!option) {
                            return;
                        }

                        var reference = materialReferenceIdMap[String(option.getAttribute('data-id'))] || null;
                        applyReference(reference);
                        if (referencePopup) {
                            referencePopup.classList.add('hidden');
                        }
                    });

                    nameInput.addEventListener('change', applyReferenceFromName);
                    nameInput.addEventListener('blur', applyReferenceFromName);

                    if (vendorToggle) {
                        vendorToggle.addEventListener('click', function (event) {
                            event.preventDefault();
                            event.stopPropagation();
                            setVendorPopupOpen(vendorPopup ? vendorPopup.classList.contains('hidden') : false);

                            if (vendorSearch) {
                                vendorSearch.value = '';
                                renderVendorOptions('');
                                setTimeout(function () {
                                    vendorSearch.focus();
                                }, 0);
                            }
                        });
                    }

                    if (vendorSearch) {
                        vendorSearch.addEventListener('input', function () {
                            renderVendorOptions(vendorSearch.value);
                        });
                    }

                    if (vendorOptionsWrap) {
                        vendorOptionsWrap.addEventListener('change', function () {
                            var selected = Array.from(row.querySelectorAll('.consumption-vendor-option:checked')).map(function (checkbox) {
                                return String(checkbox.value);
                            });

                            setSelectedVendorIds(selected);
                            renderVendorOptions(vendorSearch ? vendorSearch.value : '');
                        });
                    }

                    if (vendorCreateToggle) {
                        vendorCreateToggle.addEventListener('click', function (event) {
                            event.preventDefault();
                            event.stopPropagation();

                            window.openProductQuickOrganizationModal({
                                type: 'vendor',
                                url: vendorQuickCreateUrl,
                                onSaved: function (vendor) {
                                    var item = { id: Number(vendor.id), name: vendor.name };
                                    var exists = vendorOptions.some(function (existing) {
                                        return Number(existing.id) === Number(item.id);
                                    });

                                    if (!exists) {
                                        vendorOptions.push(item);
                                        vendorOptions.sort(function (a, b) {
                                            return String(a.name).localeCompare(String(b.name));
                                        });
                                    }

                                    var selected = getSelectedVendorIds();

                                    if (!selected.includes(String(item.id))) {
                                        selected.push(String(item.id));
                                    }

                                    setSelectedVendorIds(selected);
                                    renderVendorOptions(vendorSearch ? vendorSearch.value : '');
                                },
                            });
                        });
                    }

                    colorNameInput.addEventListener('change', syncColorFromName);
                    colorNameInput.addEventListener('blur', syncColorFromName);
                    colorPicker.addEventListener('input', function () {
                        colorCodeInput.value = colorPicker.value;
                    });

                    renderMaterialOptions('');
                    renderVendorOptions('');
                    setSelectedReferenceLabel(nameInput.value || '');

                    var existingReference = null;
                    if (referenceIdInput.value && materialReferenceIdMap[String(referenceIdInput.value)]) {
                        existingReference = materialReferenceIdMap[String(referenceIdInput.value)];
                    } else {
                        existingReference = getMaterialReference(nameInput.value);
                    }

                    if (existingReference) {
                        applyReference(existingReference);
                    }

                    document.addEventListener('click', function (event) {
                        if (referenceLookup && referencePopup && !referenceLookup.contains(event.target)) {
                            referencePopup.classList.add('hidden');
                        }

                        if (vendorWrapper && !vendorWrapper.contains(event.target)) {
                            setVendorPopupOpen(false);
                        }
                    });

                    syncVendorDisplay();
                    renderVendorOptions('');
                    updateConsumptionVendorInputs(row, Array.prototype.indexOf.call(consumptionsContainer.querySelectorAll('.consumption-row'), row));
                    syncColorFromName();
                }

                function addSection(sectionData) {
                    var sectionIndex = sectionsContainer.querySelectorAll('.production-section').length;
                    var section = document.createElement('div');
                    section.className = 'production-section rounded border border-gray-200 p-3 dark:border-gray-700';
                    section.innerHTML = ''
                        + '<div class="mb-2 flex items-center gap-2">'
                        + '  <input type="text" name="production_sections[' + sectionIndex + '][section_name]" class="flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Section Name" value="' + escapeHtml(valueOf(sectionData, 'section_name', '')) + '">'
                        + '  <button type="button" class="add-section-item secondary-button">Add Row</button>'
                        + '  <button type="button" class="remove-section inline-flex h-10 w-10 items-center justify-center rounded border border-gray-200 text-lg text-red-600 hover:bg-red-50 dark:border-gray-700 dark:hover:bg-gray-800" aria-label="Remove section">&times;</button>'
                        + '</div>'
                        + '<div class="mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400" style="display:grid;grid-template-columns:minmax(0, 1fr) 120px 120px 40px;gap:0.5rem;"><div>Name</div><div>Qty</div><div>Unit</div><div></div></div>'
                        + '<div class="section-items flex flex-col gap-2"></div>';

                    sectionsContainer.appendChild(section);

                    var itemsContainer = section.querySelector('.section-items');
                    var items = (sectionData && Array.isArray(sectionData.items)) ? sectionData.items : [];

                    if (!items.length) {
                        addSectionItem(itemsContainer, sectionIndex, null);
                    } else {
                        items.forEach(function (item) {
                            addSectionItem(itemsContainer, sectionIndex, item);
                        });
                    }
                }

                function addSectionItem(itemsContainer, sectionIndex, itemData) {
                    var itemIndex = itemsContainer.querySelectorAll('.section-item').length;
                    var row = document.createElement('div');
                    row.className = 'section-item';
                    row.style.display = 'grid';
                    row.style.gridTemplateColumns = 'minmax(0, 1fr) 120px 120px 40px';
                    row.style.gap = '0.5rem';
                    row.innerHTML = ''
                        + '<input type="text" name="production_sections[' + sectionIndex + '][items][' + itemIndex + '][name]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Name" value="' + escapeHtml(valueOf(itemData, 'name', '')) + '">'
                        + '<input type="number" step="0.0001" name="production_sections[' + sectionIndex + '][items][' + itemIndex + '][qty]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Qty" value="' + escapeHtml(valueOf(itemData, 'qty', '')) + '">'
                        + '<input type="text" name="production_sections[' + sectionIndex + '][items][' + itemIndex + '][unit]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Unit" value="' + escapeHtml(valueOf(itemData, 'unit', '')) + '">'
                        + '<button type="button" class="remove-section-item inline-flex h-10 w-10 items-center justify-center rounded border border-gray-200 text-lg text-red-600 hover:bg-red-50 dark:border-gray-700 dark:hover:bg-gray-800" aria-label="Remove section row">&times;</button>';
                    itemsContainer.appendChild(row);
                }

                function renumber() {
                    colorsContainer.querySelectorAll('.color-row').forEach(function (row, index) {
                        var fields = row.querySelectorAll('input');
                        if (fields[0]) fields[0].name = 'colors[' + index + '][name]';
                        if (fields[1]) fields[1].name = 'colors[' + index + '][color_code]';
                        if (fields[3]) fields[3].name = 'colors[' + index + '][cost_price]';
                        if (fields[4]) fields[4].name = 'colors[' + index + '][selling_price]';
                    });

                    refreshOtherImageColorOptions();

                    consumptionsContainer.querySelectorAll('.consumption-row').forEach(function (row, index) {
                        var referenceIdInput = row.querySelector('.consumption-reference-id');
                        var nameInput = row.querySelector('.consumption-name-input');
                        var qtyInput = row.querySelector('.consumption-qty-input');
                        var unitInput = row.querySelector('.consumption-unit-input');
                        var colorNameInput = row.querySelector('.consumption-color-name');
                        var colorCodeInput = row.querySelector('.consumption-color-code');

                        if (referenceIdInput) referenceIdInput.name = 'consumptions[' + index + '][material_reference_id]';
                        if (nameInput) nameInput.name = 'consumptions[' + index + '][name]';
                        if (qtyInput) qtyInput.name = 'consumptions[' + index + '][qty]';
                        if (unitInput) unitInput.name = 'consumptions[' + index + '][unit]';
                        updateConsumptionVendorInputs(row, index);
                        if (colorNameInput) colorNameInput.name = 'consumptions[' + index + '][color_name]';
                        if (colorCodeInput) colorCodeInput.name = 'consumptions[' + index + '][color_code]';
                    });

                    sectionsContainer.querySelectorAll('.production-section').forEach(function (section, sectionIndex) {
                        var sectionNameInput = section.querySelector('input[name*="[section_name]"]');
                        if (sectionNameInput) {
                            sectionNameInput.name = 'production_sections[' + sectionIndex + '][section_name]';
                        }

                        section.querySelectorAll('.section-item').forEach(function (item, itemIndex) {
                            var fields = item.querySelectorAll('input');
                            if (fields[0]) fields[0].name = 'production_sections[' + sectionIndex + '][items][' + itemIndex + '][name]';
                            if (fields[1]) fields[1].name = 'production_sections[' + sectionIndex + '][items][' + itemIndex + '][qty]';
                            if (fields[2]) fields[2].name = 'production_sections[' + sectionIndex + '][items][' + itemIndex + '][unit]';
                        });
                    });
                }

                function removeBlankRowsBeforeSubmit() {
                    colorsContainer.querySelectorAll('.color-row').forEach(function (row) {
                        var fields = row.querySelectorAll('input');
                        var c0 = fields[0] ? fields[0].value.trim() : '';
                        var c1 = fields[1] ? fields[1].value.trim() : '';
                        var c2 = fields[3] ? String(fields[3].value || '').trim() : '';
                        var c3 = fields[4] ? String(fields[4].value || '').trim() : '';
                        if (!c0 && !c1 && !c2 && !c3) row.remove();
                    });

                    consumptionsContainer.querySelectorAll('.consumption-row').forEach(function (row) {
                        var v0 = row.querySelector('.consumption-name-input') ? row.querySelector('.consumption-name-input').value.trim() : '';
                        var v1 = row.querySelector('.consumption-qty-input') ? String(row.querySelector('.consumption-qty-input').value || '').trim() : '';
                        var v2 = row.querySelector('.consumption-unit-input') ? row.querySelector('.consumption-unit-input').value.trim() : '';
                        if (!v0 && !v1 && !v2) row.remove();
                    });

                    sectionsContainer.querySelectorAll('.production-section').forEach(function (section) {
                        var sectionNameInput = section.querySelector('input[name*="[section_name]"]');

                        section.querySelectorAll('.section-item').forEach(function (item) {
                            var fields = item.querySelectorAll('input');
                            var i0 = fields[0] ? fields[0].value.trim() : '';
                            var i1 = fields[1] ? String(fields[1].value || '').trim() : '';
                            var i2 = fields[2] ? fields[2].value.trim() : '';
                            if (!i0 && !i1 && !i2) item.remove();
                        });

                        var hasSectionName = !!(sectionNameInput && sectionNameInput.value.trim());
                        var hasItems = section.querySelectorAll('.section-item').length > 0;

                        if (!hasSectionName && !hasItems) {
                            section.remove();
                        }
                    });

                    renumber();
                }

                if (itemCodeInput) {
                    itemCodeInput.addEventListener('input', updateInternalCodeFromItemCode);
                }

                if (internalCodeInput) {
                    internalCodeInput.addEventListener('input', function () {
                        if ((internalCodeInput.value || '').trim() === '') {
                            lastAutoInternalCode = '';
                        }
                    });
                }

                if (duplicateDraft) {
                    var duplicateModalItemCode = document.getElementById('duplicate_modal_item_code');
                    var duplicateModalInternalCode = document.getElementById('duplicate_modal_internal_code');
                    var duplicateModalName = document.getElementById('duplicate_modal_name');
                    var duplicateModalApply = document.getElementById('apply_duplicate_modal');
                    var productNameInput = document.getElementById('product_name');

                    if (duplicateModalItemCode && duplicateModalInternalCode && duplicateModalName) {
                        duplicateModalName.value = productNameInput ? productNameInput.value : '';
                        duplicateModalItemCode.value = itemCodeInput ? itemCodeInput.value : '';
                        duplicateModalInternalCode.value = internalCodeInput ? internalCodeInput.value : '';

                        duplicateModalItemCode.addEventListener('input', function () {
                            if (duplicateModalInternalCode.dataset.manual !== '1') {
                                duplicateModalInternalCode.value = duplicateModalItemCode.value;
                            }
                        });

                        duplicateModalInternalCode.addEventListener('input', function () {
                            duplicateModalInternalCode.dataset.manual = '1';
                        });

                        if (duplicateModalApply) {
                            duplicateModalApply.addEventListener('click', function () {
                                duplicateModalItemCode.value = duplicateModalItemCode.value.trim();
                                duplicateModalInternalCode.value = duplicateModalInternalCode.value.trim();
                                duplicateModalName.value = duplicateModalName.value.trim();

                                if (!duplicateModalItemCode.reportValidity()) {
                                    return;
                                }

                                if (!duplicateModalName.reportValidity()) {
                                    return;
                                }

                                if (itemCodeInput) itemCodeInput.value = duplicateModalItemCode.value;
                                if (internalCodeInput) {
                                    internalCodeInput.value = duplicateModalInternalCode.value;
                                    lastAutoInternalCode = internalCodeInput.value;
                                }
                                if (productNameInput) productNameInput.value = duplicateModalName.value;

                                if (itemCodeInput && !itemCodeInput.checkValidity()) {
                                    itemCodeInput.reportValidity();
                                    return;
                                }

                                if (productNameInput && !productNameInput.checkValidity()) {
                                    productNameInput.reportValidity();
                                    return;
                                }

                                var duplicateModalClose = duplicateModalApply.closest('.box-shadow')?.querySelector('.icon-cross-large');

                                if (duplicateModalClose) {
                                    duplicateModalClose.click();
                                }
                            });
                        }

                    }
                }

                if (addColorButton) {
                    addColorButton.addEventListener('click', function () {
                        addColorRow(null);
                        renumber();
                    });
                }

                if (addConsumptionButton) {
                    addConsumptionButton.addEventListener('click', function () {
                        addConsumptionRow(null);
                        renumber();
                    });
                }

                if (quickAddMaterialButton) {
                    quickAddMaterialButton.addEventListener('click', function () {
                        openQuickMaterialModal(function (reference) {
                            var existingRows = Array.from(consumptionsContainer.querySelectorAll('.consumption-row'));
                            var blankRows = existingRows.filter(function (row) {
                                var name = row.querySelector('.consumption-name-input');
                                var qty = row.querySelector('.consumption-qty-input');
                                var unit = row.querySelector('.consumption-unit-input');

                                return !String(name ? name.value : '').trim()
                                    && !String(qty ? qty.value : '').trim()
                                    && !String(unit ? unit.value : '').trim();
                            });

                            if (existingRows.length === 1 && blankRows.length === 1) {
                                blankRows[0].remove();
                            }

                            addConsumptionRow({
                                material_reference_id: reference.id,
                                name: reference.name,
                                qty: reference.qty,
                                unit: reference.unit,
                                vendor_ids: reference.vendor_ids,
                                color_name: reference.color_name,
                                color_code: reference.color_code,
                            });
                            renumber();
                        });
                    });
                }

                if (quickMaterialVendorSearch) {
                    quickMaterialVendorSearch.addEventListener('input', function () {
                        renderQuickMaterialVendors(quickMaterialVendorSearch.value);
                    });
                }

                if (quickMaterialVendors) {
                    quickMaterialVendors.addEventListener('change', function (event) {
                        if (!event.target.classList.contains('product-quick-material-vendor')) {
                            return;
                        }

                        var vendorId = String(event.target.value);

                        if (event.target.checked && !quickMaterialVendorIds.includes(vendorId)) {
                            quickMaterialVendorIds.push(vendorId);
                        } else if (!event.target.checked) {
                            quickMaterialVendorIds = quickMaterialVendorIds.filter(function (id) { return id !== vendorId; });
                        }
                    });
                }

                if (quickMaterialSave) {
                    quickMaterialSave.addEventListener('click', saveQuickMaterial);
                }

                ['product-quick-material-close', 'product-quick-material-cancel'].forEach(function (id) {
                    var button = document.getElementById(id);
                    if (button) button.addEventListener('click', closeQuickMaterialModal);
                });

                if (quickMaterialModal) {
                    quickMaterialModal.addEventListener('click', function (event) {
                        if (event.target === quickMaterialModal) closeQuickMaterialModal();
                    });
                }

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && quickMaterialModal && !quickMaterialModal.classList.contains('hidden')) {
                        closeQuickMaterialModal();
                    }
                });

                if (addSectionButton) {
                    addSectionButton.addEventListener('click', function () {
                        addSection(null);
                        renumber();
                    });
                }

                document.body.addEventListener('click', function (event) {
                    var removeColor = event.target.closest('.remove-color');
                    if (removeColor) {
                        event.preventDefault();
                        confirmProductRemoval('Remove this color variant?', function () {
                            var row = removeColor.closest('.color-row');

                            if (row) {
                                row.remove();
                                renumber();
                            }
                        });

                        return;
                    }

                    var removeOtherImage = event.target.closest('.remove-other-image');
                    if (removeOtherImage) {
                        event.preventDefault();
                        confirmProductRemoval('Remove this image row?', function () {
                            var row = removeOtherImage.closest('.other-image-row');

                            if (row) {
                                row.remove();
                            }
                        });

                        return;
                    }

                    var removeConsumption = event.target.closest('.remove-consumption');
                    if (removeConsumption) {
                        event.preventDefault();
                        confirmProductRemoval('Remove this material consumption row?', function () {
                            var row = removeConsumption.closest('.consumption-row');

                            if (row) {
                                row.remove();
                                renumber();
                            }
                        });

                        return;
                    }

                    var removeSection = event.target.closest('.remove-section');
                    if (removeSection) {
                        event.preventDefault();
                        confirmProductRemoval('Remove this production section and its rows?', function () {
                            var section = removeSection.closest('.production-section');

                            if (section) {
                                section.remove();
                                renumber();
                            }
                        });

                        return;
                    }

                    var addSectionItemButton = event.target.closest('.add-section-item');
                    if (addSectionItemButton) {
                        var section = addSectionItemButton.closest('.production-section');
                        var sectionIndex = Array.prototype.indexOf.call(sectionsContainer.querySelectorAll('.production-section'), section);
                        addSectionItem(section.querySelector('.section-items'), sectionIndex, null);
                        renumber();
                        return;
                    }

                    var removeSectionItem = event.target.closest('.remove-section-item');
                    if (removeSectionItem) {
                        event.preventDefault();
                        var item = removeSectionItem.closest('.section-item');
                        var container = item.closest('.section-items');

                        if (container.querySelectorAll('.section-item').length > 1) {
                            confirmProductRemoval('Remove this section row?', function () {
                                item.remove();
                                renumber();
                            });
                        }

                        return;
                    }

                });

                document.body.addEventListener('input', function (event) {
                    if (event.target.classList.contains('color-code-input')) {
                        var value = event.target.value.trim();
                        var row = event.target.closest('.color-row');
                        var picker = row ? row.querySelector('.color-picker') : null;

                        if (picker && /^#[0-9A-Fa-f]{6}$/.test(value)) {
                            picker.value = value;
                        }

                        return;
                    }

                    if (event.target.classList.contains('color-picker')) {
                        var colorRow = event.target.closest('.color-row');
                        var colorCodeInput = colorRow ? colorRow.querySelector('.color-code-input') : null;
                        if (colorCodeInput) {
                            colorCodeInput.value = event.target.value;
                        }
                    }

                    if (event.target.closest('.color-row') && event.target.name && event.target.name.indexOf('[name]') !== -1) {
                        refreshOtherImageColorOptions();
                    }
                });

                if (colorsContainer) {
                    if (Array.isArray(oldColors) && oldColors.length) {
                        oldColors.forEach(addColorRow);
                    } else {
                        addColorRow(null);
                    }
                }

                if (otherImagesContainer && addOtherImageButton) {
                    addOtherImageButton.addEventListener('click', function () {
                        addOtherImageRow('');
                    });

                    addOtherImageRow('');
                }

                if (consumptionsContainer) {
                    if (Array.isArray(oldConsumptions) && oldConsumptions.length) {
                        oldConsumptions.forEach(addConsumptionRow);
                    } else {
                        addConsumptionRow(null);
                    }
                }

                if (sectionsContainer) {
                    if (Array.isArray(oldSections) && oldSections.length) {
                        oldSections.forEach(addSection);
                    } else {
                        addSection(null);
                    }
                }

                renumber();
                updateInternalCodeFromItemCode();

                var form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', removeBlankRowsBeforeSubmit);
                }
            }

            function runInit() {
                initProductCreateForm();
            }

            if (document.readyState === 'complete') {
                setTimeout(runInit, 0);
            } else {
                window.addEventListener('load', function () {
                    setTimeout(runInit, 0);
                });
            }
        </script>
    @endPushOnce
</x-admin::layouts>
