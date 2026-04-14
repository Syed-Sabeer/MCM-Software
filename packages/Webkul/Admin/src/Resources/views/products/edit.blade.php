<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.products.edit.title')
    </x-slot>

    @php
        $customersForSearch = $customers->map(fn ($customer) => [
            'id'   => (string) $customer->id,
            'name' => $customer->name,
        ])->values();

        $selectedCustomerId = (string) old('customer_organization_id', $product->customer_organization_id);
        $selectedCustomerName = $customers->firstWhere('id', (int) $selectedCustomerId)?->name ?? '';

        $colorsData = old('colors', $product->colors->map(fn ($color) => [
            'name'          => $color->name,
            'color_code'    => $color->color_code,
            'cost_price'    => $color->cost_price,
            'selling_price' => $color->selling_price,
        ])->toArray());

        $existingOtherImagesData = $product->otherImages->map(fn ($image) => [
            'id'           => $image->id,
            'path'         => $image->path,
            'url'          => $image->path ? secure_url('public/storage/'.$image->path) : null,
            'original_name'=> $image->original_name,
            'color_ref'    => $image->color ? ('new_' . $image->color->sort_order) : '',
        ])->toArray();

        $consumptionsData = old('consumptions', $product->consumptions->map(fn ($consumption) => [
            'name' => $consumption->name,
            'qty'  => $consumption->qty,
            'unit' => $consumption->unit,
        ])->toArray());

        $productionSectionsData = old('production_sections', $product->productionSections->map(fn ($section) => [
            'section_name' => $section->section_name,
            'items'        => $section->items->map(fn ($item) => [
                'name' => $item->name,
                'qty'  => $item->qty,
                'unit' => $item->unit,
            ])->toArray(),
        ])->toArray());
    @endphp

    <x-admin::form :action="route('admin.products.update', $product->id)" method="PUT" enctype="multipart/form-data">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs name="products.edit" :entity="$product" />
                    <div class="text-xl font-bold dark:text-white">@lang('admin::app.products.edit.title')</div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.products.create', ['duplicate_from' => $product->id]) }}" class="secondary-button">Duplicate</a>
                    <button type="submit" class="primary-button">@lang('admin::app.products.create.save-btn')</button>
                </div>
            </div>

            <div class="flex gap-2.5 max-xl:flex-wrap">
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Product Details</p>

                        <div class="grid gap-4" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Item Code</label>
                                <input type="text" name="sku" id="item_code" value="{{ old('sku', $product->sku) }}" required class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <x-admin::form.control-group.error control-name="sku" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Internal Code</label>
                                <input type="text" name="internal_code" id="internal_code" value="{{ old('internal_code', $product->internal_code) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <x-admin::form.control-group.error control-name="internal_code" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Product Name</label>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <x-admin::form.control-group.error control-name="name" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Size</label>
                                <input type="text" name="size" value="{{ old('size', $product->size) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <x-admin::form.control-group.error control-name="size" />
                            </div>

                            <div style="grid-column: 1 / -1;">
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Customer (Optional)</label>
                                <v-customer-lookup
                                    input-name="customer_organization_id"
                                    :customers='@json($customersForSearch)'
                                    :value='@json($selectedCustomerId ? ['id' => $selectedCustomerId, 'name' => $selectedCustomerName] : null)'
                                ></v-customer-lookup>
                                <x-admin::form.control-group.error control-name="customer_organization_id" />
                            </div>
                        </div>

                        <div class="mt-4 rounded border border-gray-200 p-3 dark:border-gray-700">
                            <p class="mb-3 text-sm font-semibold text-gray-800 dark:text-white">Pricing</p>

                            <div class="grid gap-4" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Cost Price</label>
                                    <input type="number" step="0.0001" min="0" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    <x-admin::form.control-group.error control-name="cost_price" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Selling Price</label>
                                    <input type="number" step="0.0001" min="0" name="selling_price" value="{{ old('selling_price', $product->selling_price ?? $product->price) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    <x-admin::form.control-group.error control-name="selling_price" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">Material Consumption</p>
                            <button type="button" id="add-consumption" class="secondary-button">Add Row</button>
                        </div>

                        <div class="mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400" style="display: grid; grid-template-columns: minmax(0, 1fr) 120px 120px 40px; gap: 0.5rem;">
                            <div>Material Name</div>
                            <div>Qty</div>
                            <div>Unit</div>
                            <div></div>
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

                <div class="flex w-[540px] max-w-full flex-col gap-2 max-sm:w-full">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">Color Variants</p>
                            <button type="button" id="add-color" class="secondary-button">Add Color</button>
                        </div>

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

                            @if ($product->cover_image)
                                <div class="mb-2">
                                    <img src="{{ secure_url('public/storage/'.$product->cover_image) }}" alt="Cover" class="h-20 w-20 rounded border border-gray-200 object-cover dark:border-gray-700">
                                </div>
                            @endif

                            <input type="file" name="cover_image" accept="image/*" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <x-admin::form.control-group.error control-name="cover_image" />
                        </x-admin::form.control-group>

                        <div class="mt-4">
                            <div class="mb-2 flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Existing Other Images</p>
                            </div>

                            <div class="mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400" style="display: grid; grid-template-columns: 56px minmax(0, 1.4fr) minmax(0, 1fr) 44px; gap: 0.5rem;">
                                <div>Image</div>
                                <div>File</div>
                                <div>Color</div>
                                <div></div>
                            </div>

                            <div id="existing-other-images-container" class="flex flex-col gap-2"></div>
                            <input type="hidden" name="delete_image_ids" id="delete_image_ids" value="">
                        </div>

                        <div class="mt-4">
                            <div class="mb-2 flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Add New Other Images</p>
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
                },

                data() {
                    return {
                        showPopup: false,
                        searchTerm: '',
                        isSearching: false,
                        selectedItem: this.value || { id: '', name: '' },
                    };
                },

                computed: {
                    filteredResults() {
                        const query = (this.searchTerm || '').toLowerCase().trim();

                        if (! query) {
                            return this.customers;
                        }

                        return this.customers.filter((item) => (item.name || '').toLowerCase().includes(query));
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

                    handleFocusOut(event) {
                        const lookup = this.$refs.lookup;

                        if (lookup && ! lookup.contains(event.target)) {
                            this.showPopup = false;
                        }
                    },
                },
            });

            function initProductEditForm() {
                var itemCodeInput = document.getElementById('item_code');
                var internalCodeInput = document.getElementById('internal_code');
                var colorsContainer = document.getElementById('colors-container');
                var addColorButton = document.getElementById('add-color');
                var consumptionsContainer = document.getElementById('consumptions-container');
                var sectionsContainer = document.getElementById('production-sections-container');
                var addConsumptionButton = document.getElementById('add-consumption');
                var addSectionButton = document.getElementById('add-production-section');
                var existingOtherImagesContainer = document.getElementById('existing-other-images-container');
                var otherImagesContainer = document.getElementById('other-images-container');
                var addOtherImageButton = document.getElementById('add-other-image');
                var deleteImageIdsInput = document.getElementById('delete_image_ids');

                var oldColors = @json($colorsData);
                var oldConsumptions = @json($consumptionsData);
                var oldSections = @json($productionSectionsData);
                var existingOtherImages = @json($existingOtherImagesData);
                var deletedImageIds = [];
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
                        + '<input type="text" name="colors[' + index + '][name]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Color Name" value="' + escapeHtml(valueOf(data, 'name', '')) + '">'
                        + '<div class="flex items-center gap-2">'
                        + '  <input type="text" name="colors[' + index + '][color_code]" class="color-code-input w-full rounded border border-gray-200 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="#000000" value="' + escapeHtml(colorCode) + '">'
                        + '  <input type="color" class="color-picker h-[40px] w-[40px] cursor-pointer rounded border border-gray-200 p-0 dark:border-gray-700" value="' + escapeHtml(colorCode) + '">'
                        + '</div>'
                        + '<input type="number" step="0.0001" min="0" name="colors[' + index + '][cost_price]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Cost Price" value="' + escapeHtml(costPrice) + '">'
                        + '<input type="number" step="0.0001" min="0" name="colors[' + index + '][selling_price]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Selling Price" value="' + escapeHtml(sellingPrice) + '">'
                        + '<button type="button" class="remove-color inline-flex h-10 w-10 items-center justify-center rounded border border-gray-200 text-lg text-red-600 hover:bg-red-50 dark:border-gray-700 dark:hover:bg-gray-800" aria-label="Remove color">&times;</button>';
                    colorsContainer.appendChild(row);
                    refreshImageColorOptions();
                }

                function colorOptionsMarkup(selectedValue) {
                    var options = '<option value="">No Color</option>';
                    var colorRows = colorsContainer ? colorsContainer.querySelectorAll('.color-row') : [];

                    colorRows.forEach(function (row, index) {
                        var nameInput = row.querySelector('input[name*="[name]"]');
                        var label = nameInput && nameInput.value.trim() ? nameInput.value.trim() : ('Color ' + (index + 1));
                        var value = 'new_' + index;
                        var selectedAttr = selectedValue === value ? ' selected' : '';
                        options += '<option value="' + value + '"' + selectedAttr + '>' + escapeHtml(label) + '</option>';
                    });

                    return options;
                }

                function addExistingOtherImageRow(image) {
                    if (!existingOtherImagesContainer) {
                        return;
                    }

                    var row = document.createElement('div');
                    row.className = 'existing-other-image-row items-center rounded border border-gray-200 p-2 dark:border-gray-700';
                    row.style.display = 'grid';
                    row.style.gridTemplateColumns = '56px minmax(0, 1.4fr) minmax(0, 1fr) 44px';
                    row.style.gap = '0.5rem';
                    row.setAttribute('data-image-id', image.id);
                    row.innerHTML = ''
                        + '<img src="' + escapeHtml(image.url || '') + '" class="h-16 w-16 rounded object-cover border border-gray-200 dark:border-gray-700" alt="image">'
                        + '<input type="hidden" name="existing_image_ids[]" value="' + image.id + '">'
                        + '<input type="file" name="replace_images[' + image.id + ']" accept="image/*" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">'
                        + '<select name="existing_image_colors[]" class="existing-image-color rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">'
                        + colorOptionsMarkup(image.color_ref || '')
                        + '</select>'
                        + '<button type="button" class="remove-existing-other-image inline-flex h-10 w-10 items-center justify-center rounded border border-gray-200 text-lg text-red-600 hover:bg-red-50 dark:border-gray-700 dark:hover:bg-gray-800" aria-label="Remove image">&times;</button>';

                    existingOtherImagesContainer.appendChild(row);
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
                        + '<input type="file" name="other_images[]" accept="image/*" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">'
                        + '<select name="other_image_colors[]" class="other-image-color rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">'
                        + colorOptionsMarkup(selectedColor || '')
                        + '</select>'
                        + '<button type="button" class="remove-other-image inline-flex h-10 w-10 items-center justify-center rounded border border-gray-200 text-lg text-red-600 hover:bg-red-50 dark:border-gray-700 dark:hover:bg-gray-800" aria-label="Remove image">&times;</button>';

                    otherImagesContainer.appendChild(row);
                }

                function refreshImageColorOptions() {
                    if (existingOtherImagesContainer) {
                        existingOtherImagesContainer.querySelectorAll('.existing-image-color').forEach(function (select) {
                            var selected = select.value;
                            select.innerHTML = colorOptionsMarkup(selected);
                        });
                    }

                    if (otherImagesContainer) {
                        otherImagesContainer.querySelectorAll('.other-image-color').forEach(function (select) {
                            var selected = select.value;
                            select.innerHTML = colorOptionsMarkup(selected);
                        });
                    }
                }

                function addConsumptionRow(data) {
                    var index = consumptionsContainer.querySelectorAll('.consumption-row').length;
                    var row = document.createElement('div');
                    row.className = 'consumption-row rounded border border-gray-200 p-2 dark:border-gray-700';
                    row.style.display = 'grid';
                    row.style.gridTemplateColumns = 'minmax(0, 1fr) 120px 120px 40px';
                    row.style.gap = '0.5rem';
                    row.innerHTML = ''
                        + '<input type="text" name="consumptions[' + index + '][name]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Material Name" value="' + escapeHtml(valueOf(data, 'name', '')) + '">'
                        + '<input type="number" step="0.0001" name="consumptions[' + index + '][qty]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Qty" value="' + escapeHtml(valueOf(data, 'qty', '')) + '">'
                        + '<input type="text" name="consumptions[' + index + '][unit]" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Unit" value="' + escapeHtml(valueOf(data, 'unit', '')) + '">'
                        + '<button type="button" class="remove-consumption inline-flex h-10 w-10 items-center justify-center rounded border border-gray-200 text-lg text-red-600 hover:bg-red-50 dark:border-gray-700 dark:hover:bg-gray-800" aria-label="Remove consumption">&times;</button>';
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

                    refreshImageColorOptions();
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

                    var removeExistingOtherImage = event.target.closest('.remove-existing-other-image');
                    if (removeExistingOtherImage) {
                        var existingRow = removeExistingOtherImage.closest('.existing-other-image-row');
                        var imageId = existingRow ? existingRow.getAttribute('data-image-id') : null;
                        if (imageId) {
                            deletedImageIds.push(imageId);
                            if (deleteImageIdsInput) {
                                deleteImageIdsInput.value = deletedImageIds.join(',');
                            }
                        }
                        if (existingRow) {
                            existingRow.remove();
                        }
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

                        return;
                    }

                    if (event.target.closest('.color-row') && event.target.name && event.target.name.indexOf('[name]') !== -1) {
                        refreshImageColorOptions();
                    }
                });

                if (colorsContainer) {
                    if (Array.isArray(oldColors) && oldColors.length) {
                        oldColors.forEach(addColorRow);
                    } else {
                        addColorRow(null);
                    }
                }

                if (existingOtherImagesContainer && Array.isArray(existingOtherImages)) {
                    existingOtherImages.forEach(function (image) {
                        addExistingOtherImageRow(image);
                    });
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
                initProductEditForm();
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
