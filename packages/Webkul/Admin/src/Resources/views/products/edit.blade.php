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
            'material_reference_id' => $consumption->material_reference_id,
            'name' => $consumption->name,
            'qty'  => $consumption->qty,
            'unit' => $consumption->unit,
            'vendor_ids' => $consumption->vendor_ids ?? [],
            'color_name' => $consumption->color_name,
            'color_code' => $consumption->color_code,
        ])->toArray());

        $productionSectionsData = old('production_sections', $product->productionSections->map(fn ($section) => [
            'section_name' => $section->section_name,
            'items'        => $section->items->map(fn ($item) => [
                'name' => $item->name,
                'qty'  => $item->qty,
                'unit' => $item->unit,
            ])->toArray(),
        ])->toArray());

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

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Weight</label>
                                <input type="number" step="0.01" min="0" name="weight" value="{{ old('weight', $product->weight) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <x-admin::form.control-group.error control-name="weight" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Weight Unit</label>
                                <select name="weight_unit" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    <option value="">Select Unit</option>
                                    <option value="gsm" @selected(old('weight_unit', $product->weight_unit) === 'gsm')>GSM</option>
                                    <option value="oz" @selected(old('weight_unit', $product->weight_unit) === 'oz')>OZ</option>
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
                            <button type="button" id="add-consumption" class="secondary-button">Add Material +</button>
                        </div>

                        <datalist id="product-material-reference-options">
                            @foreach ($materialReferenceOptions as $materialReference)
                                <option value="{{ $materialReference['name'] }}">{{ $materialReference['unit'] }}</option>
                            @endforeach
                        </datalist>

                        <p class="mb-2 px-1 text-xs text-gray-500 dark:text-gray-400">
                            Select a material, then fill material name, color, quantity, unit, and vendors in a single row.
                        </p>

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

                            <div class="mt-2 border-t border-gray-100 pt-2 dark:border-gray-700">
                                <button type="button" class="text-xs font-medium text-brandColor hover:underline" @click.stop="showCreate = ! showCreate">
                                    Add Customer
                                </button>

                                <div v-if="showCreate" class="mt-2 flex flex-col gap-2">
                                    <input
                                        type="text"
                                        v-model="quickName"
                                        class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                        placeholder="Company name"
                                        @keydown.enter.prevent="createCustomer"
                                    >
                                    <p v-if="quickError" class="text-xs text-red-600">@{{ quickError }}</p>
                                    <button type="button" class="primary-button justify-center" :disabled="isCreating" @click.stop="createCustomer">
                                        @{{ isCreating ? 'Saving...' : 'Save Customer' }}
                                    </button>
                                </div>
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
                        showCreate: false,
                        searchTerm: '',
                        quickName: '',
                        quickError: '',
                        isSearching: false,
                        isCreating: false,
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

                    createCustomer() {
                        const name = (this.quickName || '').trim();

                        if (! name) {
                            this.quickError = 'Company name is required.';
                            return;
                        }

                        this.isCreating = true;
                        this.quickError = '';

                        fetch(this.createUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            },
                            body: JSON.stringify({ name, type: 'customer' }),
                        })
                            .then(async (response) => {
                                const payload = await response.json().catch(() => ({}));
                                if (! response.ok) {
                                    throw new Error(payload?.message || payload?.errors?.name?.[0] || 'Unable to save customer.');
                                }
                                return payload;
                            })
                            .then((customer) => {
                                const item = { id: String(customer.id), name: customer.name };
                                const exists = this.localCustomers.some((existing) => String(existing.id) === String(item.id));
                                if (! exists) {
                                    this.localCustomers.push(item);
                                    this.localCustomers.sort((a, b) => String(a.name).localeCompare(String(b.name)));
                                }
                                this.selectItem(item);
                                this.quickName = '';
                                this.showCreate = false;
                            })
                            .catch((error) => {
                                this.quickError = error.message;
                            })
                            .finally(() => {
                                this.isCreating = false;
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
                var colorReferences = @json($colorReferenceOptions);
                var materialReferences = @json($materialReferenceOptions);
                var vendorOptions = @json($vendorOptions);
                var vendorQuickCreateUrl = "{{ route('admin.vendors.organizations.quick_create') }}";
                var deletedImageIds = [];
                var lastAutoInternalCode = internalCodeInput ? (internalCodeInput.value || '') : '';
                var colorReferenceMap = {};
                var materialReferenceMap = {};
                var materialReferenceIdMap = {};

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
                            refreshImageColorOptions();
                            return;
                        }

                        var normalized = normalizeColorCode(matchedCode);

                        if (/^#[0-9A-Fa-f]{6}$/.test(normalized)) {
                            colorCodeInput.value = normalized;
                            colorPicker.value = normalized;
                        }

                        refreshImageColorOptions();
                    }

                    nameInput.addEventListener('input', refreshImageColorOptions);
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
                        + '          <div class="consumption-vendor-create-panel mt-2 hidden flex-col gap-2">'
                        + '            <input type="text" class="consumption-vendor-create-name w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="Company name">'
                        + '            <p class="consumption-vendor-create-error hidden text-xs text-red-600"></p>'
                        + '            <button type="button" class="consumption-vendor-create-save primary-button justify-center">Save Vendor</button>'
                        + '          </div>'
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
                        + '    <input type="text" name="consumptions[' + index + '][unit]" class="consumption-unit-input w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Unit" value="' + escapeHtml(valueOf(data, 'unit', '')) + '">'
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

                    var selected = Array.from(row.querySelectorAll('.consumption-vendor-option:checked')).map(function (checkbox) {
                        return checkbox.value;
                    });

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
                    var referenceIdInput = row.querySelector('.consumption-reference-id');
                    var nameInput = row.querySelector('.consumption-name-input');
                    var qtyInput = row.querySelector('.consumption-qty-input');
                    var unitInput = row.querySelector('.consumption-unit-input');
                    var vendorToggle = row.querySelector('.consumption-vendor-toggle');
                    var vendorSearch = row.querySelector('.consumption-vendor-search');
                    var vendorOptionsWrap = row.querySelector('.consumption-vendor-options');
                    var vendorWrapper = row.querySelector('.consumption-vendor-wrapper');
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
                        var selectedCsv = String(vendorWrapper.getAttribute('data-selected-vendors') || '');

                        return selectedCsv ? selectedCsv.split(',').map(function (id) { return String(id).trim(); }).filter(Boolean) : [];
                    }

                    function setSelectedVendorIds(ids) {
                        var selected = Array.isArray(ids) ? ids.map(function (id) { return String(id).trim(); }).filter(Boolean) : [];

                        vendorWrapper.setAttribute('data-selected-vendors', selected.join(','));
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

                        vendorOptionsWrap.innerHTML = '';

                        if (!filtered.length) {
                            vendorOptionsWrap.innerHTML = '<p class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400">No vendors found.</p>';
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
                            vendorOptionsWrap.innerHTML += '<label class="mb-1 flex items-center gap-2 rounded px-2 py-1 text-sm hover:bg-gray-100 dark:hover:bg-gray-800"><input type="checkbox" class="consumption-vendor-option h-4 w-4" value="' + escapeHtml(vendor.id) + '"' + checked + '><span>' + escapeHtml(vendor.name) + '</span></label>';
                        });
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

                    referenceSearch.addEventListener('input', function () {
                        renderMaterialOptions(referenceSearch.value);
                    });

                    referenceOptions.addEventListener('click', function (event) {
                        var option = event.target.closest('.consumption-reference-option');
                        if (!option) {
                            return;
                        }

                        var reference = materialReferenceIdMap[String(option.getAttribute('data-id'))] || null;
                        applyReference(reference);
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

                    vendorOptionsWrap.addEventListener('change', function () {
                        var selected = Array.from(row.querySelectorAll('.consumption-vendor-option:checked')).map(function (checkbox) {
                            return String(checkbox.value);
                        });
                        setSelectedVendorIds(selected);
                        renderVendorOptions(vendorSearch ? vendorSearch.value : '');
                    });

                    if (vendorCreateToggle && vendorCreatePanel) {
                        vendorCreateToggle.addEventListener('click', function (event) {
                            event.preventDefault();
                            event.stopPropagation();
                            var willOpen = vendorCreatePanel.classList.contains('hidden');
                            vendorCreatePanel.classList.toggle('hidden', !willOpen);
                            vendorCreatePanel.classList.toggle('flex', willOpen);
                            setVendorCreateError('');
                            if (willOpen && vendorCreateName) {
                                setTimeout(function () { vendorCreateName.focus(); }, 0);
                            }
                        });
                    }

                    if (vendorCreateSave) {
                        vendorCreateSave.addEventListener('click', function (event) {
                            event.preventDefault();
                            event.stopPropagation();
                            createVendor();
                        });
                    }

                    if (vendorCreateName) {
                        vendorCreateName.addEventListener('keydown', function (event) {
                            if (event.key === 'Enter') {
                                event.preventDefault();
                                createVendor();
                            }
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

                    var removeExistingOtherImage = event.target.closest('.remove-existing-other-image');
                    if (removeExistingOtherImage) {
                        event.preventDefault();
                        confirmProductRemoval('Remove this existing image?', function () {
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
