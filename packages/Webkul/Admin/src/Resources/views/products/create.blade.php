@php
    $customersForSearch = $customers->map(fn ($customer) => [
        'id'   => (string) $customer->id,
        'name' => $customer->name,
    ])->values();

    $selectedCustomerId = (string) old('customer_organization_id', '');
    $selectedCustomerName = $customers->firstWhere('id', (int) $selectedCustomerId)?->name ?? '';
@endphp

<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.products.create.title')
    </x-slot>

    <x-admin::form :action="route('admin.products.store')" method="POST" enctype="multipart/form-data">
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

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">Item Code</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="sku" id="item_code" :value="old('sku')" />
                                <x-admin::form.control-group.error control-name="sku" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Internal Code</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="internal_code" id="internal_code" :value="old('internal_code')" />
                                <x-admin::form.control-group.error control-name="internal_code" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">Product Name</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="name" :value="old('name')" />
                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Customer (Optional)</x-admin::form.control-group.label>
                                <input
                                    type="text"
                                    id="customer_search"
                                    list="product-customer-list"
                                    value="{{ old('customer_name', $selectedCustomerName) }}"
                                    class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm text-gray-800 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                    placeholder="Search customer"
                                    autocomplete="off"
                                >
                                <datalist id="product-customer-list">
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->name }}"></option>
                                    @endforeach
                                </datalist>
                                <input type="hidden" name="customer_organization_id" id="customer_organization_id" value="{{ $selectedCustomerId }}">
                                <x-admin::form.control-group.error control-name="customer_organization_id" />
                            </x-admin::form.control-group>
                        </div>

                        <x-admin::form.control-group class="mt-4">
                            <x-admin::form.control-group.label>Size</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" name="size" :value="old('size')" />
                            <x-admin::form.control-group.error control-name="size" />
                        </x-admin::form.control-group>

                        <div class="mt-4 rounded border border-gray-200 p-3 dark:border-gray-700">
                            <p class="mb-3 text-sm font-semibold text-gray-800 dark:text-white">Pricing</p>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <x-admin::form.control-group class="!mb-0">
                                    <x-admin::form.control-group.label>Cost Price</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.control type="number" step="0.0001" min="0" name="cost_price" :value="old('cost_price')" />
                                    <x-admin::form.control-group.error control-name="cost_price" />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="!mb-0">
                                    <x-admin::form.control-group.label>Selling Price</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.control type="number" step="0.0001" min="0" name="selling_price" :value="old('selling_price', old('price'))" />
                                    <x-admin::form.control-group.error control-name="selling_price" />
                                </x-admin::form.control-group>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">Material Consumption</p>
                            <button type="button" id="add-consumption" class="secondary-button">Add Row</button>
                        </div>

                        <div id="consumptions-container" class="flex flex-col gap-2"></div>
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

                <div class="flex w-[420px] max-w-full flex-col gap-2 max-sm:w-full">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">Color Variants</p>
                            <button type="button" id="add-color" class="secondary-button">Add Color</button>
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

                            <div id="other-images-container" class="flex flex-col gap-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-admin::form>

    @pushOnce('scripts')
        <script>
            function initProductCreateForm() {
                var itemCodeInput = document.getElementById('item_code');
                var internalCodeInput = document.getElementById('internal_code');
                var customerSearchInput = document.getElementById('customer_search');
                var customerIdInput = document.getElementById('customer_organization_id');
                var colorsContainer = document.getElementById('colors-container');
                var addColorButton = document.getElementById('add-color');
                var consumptionsContainer = document.getElementById('consumptions-container');
                var sectionsContainer = document.getElementById('production-sections-container');
                var addConsumptionButton = document.getElementById('add-consumption');
                var addSectionButton = document.getElementById('add-production-section');
                var otherImagesContainer = document.getElementById('other-images-container');
                var addOtherImageButton = document.getElementById('add-other-image');

                var oldColors = @json(old('colors', []));
                var oldConsumptions = @json(old('consumptions', []));
                var oldSections = @json(old('production_sections', []));
                var customers = @json($customersForSearch);
                var lastAutoInternalCode = internalCodeInput ? (internalCodeInput.value || '') : '';

                function escapeHtml(value) {
                    return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                }

                function valueOf(obj, key, fallback) {
                    if (obj && typeof obj === 'object' && obj[key] !== undefined && obj[key] !== null) {
                        return obj[key];
                    }

                    return fallback;
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

                function syncCustomerId() {
                    if (!customerSearchInput || !customerIdInput) {
                        return;
                    }

                    var selectedName = (customerSearchInput.value || '').trim().toLowerCase();
                    var selectedCustomer = customers.find(function (customer) {
                        return (customer.name || '').trim().toLowerCase() === selectedName;
                    });

                    customerIdInput.value = selectedCustomer ? selectedCustomer.id : '';
                }

                function addColorRow(data) {
                    var index = colorsContainer.querySelectorAll('.color-row').length;
                    var colorCode = valueOf(data, 'color_code', '#000000');
                    var row = document.createElement('div');
                    row.className = 'color-row grid grid-cols-1 gap-2 rounded border border-gray-200 p-2 md:grid-cols-[1fr_180px_100px_auto] dark:border-gray-700';
                    row.innerHTML = ''
                        + '<input type="text" name="colors[' + index + '][name]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Color Name" value="' + escapeHtml(valueOf(data, 'name', '')) + '">'
                        + '<input type="text" name="colors[' + index + '][color_code]" class="color-code-input rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="#000000" value="' + escapeHtml(colorCode) + '">'
                        + '<input type="color" class="color-picker h-[40px] w-full cursor-pointer rounded border border-gray-200 dark:border-gray-700" value="' + escapeHtml(colorCode) + '">'
                        + '<button type="button" class="remove-color secondary-button">Remove</button>';
                    colorsContainer.appendChild(row);
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
                    row.className = 'other-image-row grid grid-cols-1 gap-2 rounded border border-gray-200 p-2 md:grid-cols-[1fr_180px_auto] dark:border-gray-700';
                    row.innerHTML = ''
                        + '<input type=\"file\" name=\"other_images[]\" accept=\"image/*\" class=\"rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300\">'
                        + '<select name=\"other_image_colors[]\" class=\"other-image-color rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300\">'
                        + colorOptionsMarkup(selectedColor || '')
                        + '</select>'
                        + '<button type=\"button\" class=\"remove-other-image secondary-button\">Remove</button>';
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
                    row.className = 'consumption-row grid grid-cols-1 gap-2 rounded border border-gray-200 p-2 md:grid-cols-[1fr_180px_180px_auto] dark:border-gray-700';
                    row.innerHTML = ''
                        + '<input type="text" name="consumptions[' + index + '][name]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Material Name" value="' + escapeHtml(valueOf(data, 'name', '')) + '">'
                        + '<input type="number" step="0.0001" name="consumptions[' + index + '][qty]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Qty" value="' + escapeHtml(valueOf(data, 'qty', '')) + '">'
                        + '<input type="text" name="consumptions[' + index + '][unit]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Unit" value="' + escapeHtml(valueOf(data, 'unit', '')) + '">'
                        + '<button type="button" class="remove-consumption secondary-button">Remove</button>';
                    consumptionsContainer.appendChild(row);
                }

                function addSection(sectionData) {
                    var sectionIndex = sectionsContainer.querySelectorAll('.production-section').length;
                    var section = document.createElement('div');
                    section.className = 'production-section rounded border border-gray-200 p-3 dark:border-gray-700';
                    section.innerHTML = ''
                        + '<div class="mb-2 flex items-center gap-2">'
                        + '  <input type="text" name="production_sections[' + sectionIndex + '][section_name]" class="flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Section Name" value="' + escapeHtml(valueOf(sectionData, 'section_name', '')) + '">'
                        + '  <button type="button" class="add-section-item secondary-button">Add Row</button>'
                        + '  <button type="button" class="remove-section secondary-button">Delete Section</button>'
                        + '</div>'
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
                    row.className = 'section-item grid grid-cols-1 gap-2 md:grid-cols-[1fr_180px_180px_auto]';
                    row.innerHTML = ''
                        + '<input type="text" name="production_sections[' + sectionIndex + '][items][' + itemIndex + '][name]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Name" value="' + escapeHtml(valueOf(itemData, 'name', '')) + '">'
                        + '<input type="number" step="0.0001" name="production_sections[' + sectionIndex + '][items][' + itemIndex + '][qty]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Qty" value="' + escapeHtml(valueOf(itemData, 'qty', '')) + '">'
                        + '<input type="text" name="production_sections[' + sectionIndex + '][items][' + itemIndex + '][unit]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Unit" value="' + escapeHtml(valueOf(itemData, 'unit', '')) + '">'
                        + '<button type="button" class="remove-section-item secondary-button">Remove</button>';
                    itemsContainer.appendChild(row);
                }

                function renumber() {
                    colorsContainer.querySelectorAll('.color-row').forEach(function (row, index) {
                        var fields = row.querySelectorAll('input');
                        if (fields[0]) fields[0].name = 'colors[' + index + '][name]';
                        if (fields[1]) fields[1].name = 'colors[' + index + '][color_code]';
                    });

                    refreshOtherImageColorOptions();

                    consumptionsContainer.querySelectorAll('.consumption-row').forEach(function (row, index) {
                        var fields = row.querySelectorAll('input');
                        if (fields[0]) fields[0].name = 'consumptions[' + index + '][name]';
                        if (fields[1]) fields[1].name = 'consumptions[' + index + '][qty]';
                        if (fields[2]) fields[2].name = 'consumptions[' + index + '][unit]';
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
                        if (!c0 && !c1) row.remove();
                    });

                    consumptionsContainer.querySelectorAll('.consumption-row').forEach(function (row) {
                        var fields = row.querySelectorAll('input');
                        var v0 = fields[0] ? fields[0].value.trim() : '';
                        var v1 = fields[1] ? String(fields[1].value || '').trim() : '';
                        var v2 = fields[2] ? fields[2].value.trim() : '';
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

                if (customerSearchInput) {
                    customerSearchInput.addEventListener('change', syncCustomerId);
                    customerSearchInput.addEventListener('blur', syncCustomerId);
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

                if (addSectionButton) {
                    addSectionButton.addEventListener('click', function () {
                        addSection(null);
                        renumber();
                    });
                }

                document.body.addEventListener('click', function (event) {
                    var removeColor = event.target.closest('.remove-color');
                    if (removeColor) {
                        removeColor.closest('.color-row').remove();
                        renumber();
                        return;
                    }

                    var removeOtherImage = event.target.closest('.remove-other-image');
                    if (removeOtherImage) {
                        removeOtherImage.closest('.other-image-row').remove();
                        return;
                    }

                    var removeConsumption = event.target.closest('.remove-consumption');
                    if (removeConsumption) {
                        removeConsumption.closest('.consumption-row').remove();
                        renumber();
                        return;
                    }

                    var removeSection = event.target.closest('.remove-section');
                    if (removeSection) {
                        removeSection.closest('.production-section').remove();
                        renumber();
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
                        var item = removeSectionItem.closest('.section-item');
                        var container = item.closest('.section-items');
                        if (container.querySelectorAll('.section-item').length > 1) {
                            item.remove();
                            renumber();
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
                syncCustomerId();
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
