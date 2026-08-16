<x-admin::layouts>
    <x-slot:title>Material Inventory</x-slot>

    @php
        $formatQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 4, '.', ','), '0'), '.');
        $typeLabels = array_merge(
            \Webkul\PurchaseOrder\Services\MaterialInventoryService::MANUAL_TYPES,
            [
                'job_order_issue' => 'Job Order Issue',
                'job_order_adjustment' => 'Job Order Adjustment',
                'goods_receipt' => 'Goods Receipt',
                'goods_receipt_adjustment' => 'Goods Receipt Adjustment',
            ]
        );
        $initialStats = [
            'material_count' => (int) $totals->material_count,
            'stock_value' => core()->formatBasePrice($totals->stock_value, 2),
            'low_stock_count' => (int) $totals->low_stock_count,
            'out_of_stock_count' => (int) $totals->out_of_stock_count,
        ];
    @endphp

    <div class="flex flex-col gap-4">
        <v-material-inventory
            :materials='@json($materials)'
            :units='@json($units)'
            :vendors='@json($vendors)'
            :initial-stats='@json($initialStats)'
            :movement-types='@json(\Webkul\PurchaseOrder\Services\MaterialInventoryService::MANUAL_TYPES)'
            movement-url="{{ route('admin.inventory.movements.store') }}"
            material-url="{{ route('admin.settings.material_references.store') }}"
            initial-date="{{ now()->format('Y-m-d\TH:i') }}"
        >
            <x-admin::shimmer.datagrid />
        </v-material-inventory>

        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-800">
                <h2 class="font-semibold text-gray-900 dark:text-white">Recent Movements</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-gray-500 dark:bg-gray-950 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Material</th>
                            <th class="px-4 py-3">Movement</th>
                            <th class="px-4 py-3 text-right">Quantity</th>
                            <th class="px-4 py-3 text-right">Balance</th>
                            <th class="px-4 py-3">Recorded By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($recentTransactions as $transaction)
                            <tr>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $transaction->occurred_at->format('d M Y, H:i') }}</td>
                                <td class="px-4 py-3"><a href="{{ route('admin.inventory.view', $transaction->material_reference_id) }}" class="font-medium text-brandColor">{{ $transaction->material?->name }}</a></td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $typeLabels[$transaction->type] ?? \Illuminate\Support\Str::headline($transaction->type) }}</td>
                                <td class="px-4 py-3 text-right font-semibold {{ (float) $transaction->quantity >= 0 ? 'text-green-700' : 'text-red-700' }}">{{ (float) $transaction->quantity > 0 ? '+' : '' }}{{ $formatQty($transaction->quantity) }} {{ $transaction->material?->unit }}</td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">{{ $formatQty($transaction->balance_after) }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $transaction->creator?->name ?: 'System' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No inventory movements recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @pushOnce('styles')
        <style>
            .inventory-modal-control {
                box-sizing: border-box;
                display: block;
                width: 100%;
                min-height: 44px;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                background: #fff;
                padding: 9px 12px;
                color: #1f2937;
                font-size: 14px;
                line-height: 20px;
                outline: none;
            }

            .inventory-modal-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                column-gap: 24px;
                row-gap: 22px;
                padding: 4px 2px 8px;
            }

            .inventory-modal-field {
                display: flex;
                min-width: 0;
                flex-direction: column;
                gap: 8px;
            }

            .inventory-modal-field-full {
                grid-column: 1 / -1;
            }

            .inventory-modal-label {
                display: block;
                color: #1f2937;
                font-size: 14px;
                font-weight: 600;
                line-height: 20px;
            }

            .inventory-material-picker {
                position: relative;
            }

            .inventory-material-picker-panel {
                position: absolute;
                z-index: 20;
                top: calc(100% + 6px);
                right: 0;
                left: 0;
                overflow: hidden;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                background: #fff;
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14);
            }

            .inventory-material-options {
                max-height: 216px;
                overflow-y: auto;
                padding: 6px;
                scrollbar-width: thin;
            }

            .inventory-search-icon {
                position: absolute;
                top: 50%;
                left: 13px;
                z-index: 1;
                color: #9ca3af;
                font-size: 18px;
                line-height: 1;
                pointer-events: none;
                transform: translateY(-50%);
            }

            .inventory-modal-control.inventory-search-input {
                padding-left: 42px;
            }

            .inventory-quantity-control {
                display: flex;
                min-width: 0;
            }

            .inventory-quantity-control .inventory-modal-control {
                min-width: 0;
                border-radius: 6px 0 0 6px;
            }

            .inventory-unit-addon {
                display: flex;
                min-width: 76px;
                align-items: center;
                justify-content: center;
                border: 1px solid #d1d5db;
                border-left: 0;
                border-radius: 0 6px 6px 0;
                background: #f3f4f6;
                padding: 9px 12px;
                color: #4b5563;
                font-size: 13px;
                font-weight: 600;
            }

            .inventory-modal-actions {
                display: flex;
                width: 100%;
                align-items: center;
                justify-content: flex-end;
                gap: 12px;
            }

            .inventory-modal-control:focus {
                border-color: var(--brand-color, #be1e2d);
                box-shadow: 0 0 0 1px var(--brand-color, #be1e2d);
            }

            .inventory-modal-control:disabled {
                background: #f3f4f6;
                color: #4b5563;
            }

            .dark .inventory-modal-control {
                border-color: #374151;
                background: #030712;
                color: #fff;
            }

            .dark .inventory-modal-control:disabled {
                background: #1f2937;
                color: #d1d5db;
            }

            .dark .inventory-modal-label {
                color: #e5e7eb;
            }

            .dark .inventory-material-picker-panel {
                border-color: #374151;
                background: #111827;
            }

            .dark .inventory-unit-addon {
                border-color: #374151;
                background: #1f2937;
                color: #d1d5db;
            }

            @media (max-width: 640px) {
                .inventory-modal-grid {
                    grid-template-columns: minmax(0, 1fr);
                    row-gap: 18px;
                }

                .inventory-modal-field-full {
                    grid-column: auto;
                }

                .inventory-modal-actions > button {
                    flex: 1 1 0;
                }
            }

            .inventory-button-spinner {
                width: 16px;
                height: 16px;
                border: 2px solid currentColor;
                border-right-color: transparent;
                border-radius: 50%;
                animation: inventory-spin 0.65s linear infinite;
            }

            @keyframes inventory-spin {
                to { transform: rotate(360deg); }
            }
        </style>
    @endPushOnce

    @pushOnce('scripts')
        <script type="text/x-template" id="material-inventory-template">
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between gap-4 rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Material Inventory</h1>
                        <p class="text-sm text-gray-500">Stock balances and material valuation</p>
                    </div>

                    <button type="button" class="primary-button inline-flex items-center gap-2" @click="openMovementModal">
                        <span class="icon-add"></span>
                        Record Movement
                    </button>
                </div>

                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Inventory summary">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="text-xs font-medium uppercase text-gray-500">Materials</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">@{{ Number(stats.material_count).toLocaleString() }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="text-xs font-medium uppercase text-gray-500">Stock Value</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">@{{ stats.stock_value }}</p>
                    </div>
                    <div class="rounded-lg border border-amber-200 bg-white p-4 dark:border-amber-900 dark:bg-gray-900">
                        <p class="text-xs font-medium uppercase text-amber-700 dark:text-amber-400">Low Stock</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">@{{ Number(stats.low_stock_count).toLocaleString() }}</p>
                    </div>
                    <div class="rounded-lg border border-red-200 bg-white p-4 dark:border-red-900 dark:bg-gray-900">
                        <p class="text-xs font-medium uppercase text-red-700 dark:text-red-400">Out of Stock</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">@{{ Number(stats.out_of_stock_count).toLocaleString() }}</p>
                    </div>
                </section>

                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-800">
                        <h2 class="font-semibold text-gray-900 dark:text-white">Material Balances</h2>
                    </div>
                    <x-admin::datagrid :src="route('admin.inventory.index')" ref="datagrid" />
                </section>

                <x-admin::modal ref="inventoryModal">
                    <x-slot:header>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">
                            @{{ isEditing ? 'Edit Stock' : 'Record Inventory Movement' }}
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <form id="inventory-movement-form" class="inventory-modal-grid" @submit.prevent="saveMovement">
                            <div class="inventory-modal-field inventory-modal-field-full">
                                <div class="flex items-center justify-between gap-3">
                                    <label class="inventory-modal-label">Material *</label>
                                    <button v-if="! isEditing" type="button" class="text-sm font-medium text-brandColor hover:underline" @click="openMaterialModal">
                                        Add Material
                                    </button>
                                </div>
                                <input v-if="isEditing" type="text" :value="materialLabel" class="inventory-modal-control" disabled>
                                <div v-else class="inventory-material-picker">
                                    <button
                                        type="button"
                                        class="inventory-modal-control flex items-center justify-between gap-3 text-left"
                                        :aria-expanded="materialPickerOpen"
                                        @click.stop="toggleMaterialPicker"
                                    >
                                        <span :class="selectedMaterial ? 'text-gray-800 dark:text-gray-100' : 'text-gray-400'">
                                            @{{ selectedMaterial ? `${selectedMaterial.name} (${selectedMaterial.unit})` : 'Select material' }}
                                        </span>
                                        <span class="icon-down-arrow shrink-0 text-base text-gray-500"></span>
                                    </button>

                                    <div v-if="materialPickerOpen" class="inventory-material-picker-panel" @click.stop>
                                        <div class="border-b border-gray-200 p-2 dark:border-gray-700">
                                            <div class="relative">
                                                <span class="icon-search inventory-search-icon"></span>
                                                <input
                                                    ref="materialSearch"
                                                    v-model="materialSearch"
                                                    type="search"
                                                    class="inventory-modal-control inventory-search-input"
                                                    placeholder="Search material..."
                                                    @keydown.esc="materialPickerOpen = false"
                                                >
                                            </div>
                                        </div>

                                        <div class="inventory-material-options">
                                            <button
                                                v-for="material in filteredMaterials"
                                                :key="material.id"
                                                type="button"
                                                class="flex w-full items-center justify-between gap-3 rounded px-3 py-2 text-left text-sm hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800"
                                                @click="selectMaterial(material)"
                                            >
                                                <span class="truncate">@{{ material.name }}</span>
                                                <span class="shrink-0 text-xs font-medium text-gray-500">@{{ material.unit }}</span>
                                            </button>
                                            <p v-if="! filteredMaterials.length" class="px-3 py-5 text-center text-sm text-gray-500">No materials found.</p>
                                        </div>
                                    </div>
                                </div>
                                <p v-if="fieldError('material_reference_id')" class="mt-1 text-xs text-red-600">@{{ fieldError('material_reference_id') }}</p>
                            </div>

                            <div class="inventory-modal-field">
                                <label class="inventory-modal-label">Movement *</label>
                                <input v-if="isEditing" type="text" value="Update Quantity & Price" class="inventory-modal-control" disabled>
                                <select v-else v-model="form.type" class="inventory-modal-control" required>
                                    <option v-for="(label, value) in movementTypes" :key="value" :value="value">@{{ label }}</option>
                                </select>
                                <p v-if="fieldError('type')" class="mt-1 text-xs text-red-600">@{{ fieldError('type') }}</p>
                            </div>

                            <div class="inventory-modal-field">
                                <label class="inventory-modal-label">@{{ form.type === 'set_balance' ? 'New Balance *' : 'Quantity *' }}</label>
                                <div class="inventory-quantity-control">
                                    <input v-model="form.quantity" type="number" min="0" step="0.0001" class="inventory-modal-control" required>
                                    <span class="inventory-unit-addon">@{{ selectedUnit || '-' }}</span>
                                </div>
                                <p v-if="fieldError('quantity')" class="mt-1 text-xs text-red-600">@{{ fieldError('quantity') }}</p>
                            </div>

                            <div v-if="requiresUnitCost" class="inventory-modal-field">
                                <label class="inventory-modal-label">Unit Cost *</label>
                                <input v-model="form.unit_cost" type="number" min="0" step="0.0001" class="inventory-modal-control" required>
                                <p v-if="fieldError('unit_cost')" class="mt-1 text-xs text-red-600">@{{ fieldError('unit_cost') }}</p>
                            </div>

                            <div class="inventory-modal-field">
                                <label class="inventory-modal-label">Date *</label>
                                <input v-model="form.occurred_at" type="datetime-local" class="inventory-modal-control" required>
                                <p v-if="fieldError('occurred_at')" class="mt-1 text-xs text-red-600">@{{ fieldError('occurred_at') }}</p>
                            </div>

                            <div class="inventory-modal-field inventory-modal-field-full">
                                <label class="inventory-modal-label">Notes</label>
                                <textarea v-model="form.notes" rows="3" class="inventory-modal-control min-h-24 resize-y"></textarea>
                                <p v-if="fieldError('notes')" class="mt-1 text-xs text-red-600">@{{ fieldError('notes') }}</p>
                            </div>
                        </form>
                    </x-slot>

                    <x-slot:footer>
                        <div class="inventory-modal-actions">
                            <button type="button" class="secondary-button" :disabled="isProcessing" @click="$refs.inventoryModal.close()">Cancel</button>
                            <button type="submit" form="inventory-movement-form" class="primary-button inline-flex min-w-[168px] items-center justify-center gap-2" :disabled="isProcessing">
                                <span :class="isProcessing ? 'inventory-button-spinner' : (isEditing ? 'icon-edit' : 'icon-add')"></span>
                                @{{ isProcessing ? 'Saving...' : (isEditing ? 'Update Inventory' : 'Save Movement') }}
                            </button>
                        </div>
                    </x-slot>
                </x-admin::modal>

                <x-admin::modal ref="materialModal">
                    <x-slot:header>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">Add Material</p>
                    </x-slot>

                    <x-slot:content>
                        <form id="inventory-material-form" class="inventory-modal-grid" @submit.prevent="saveMaterial">
                            <div class="inventory-modal-field inventory-modal-field-full">
                                <label class="inventory-modal-label">Material Name *</label>
                                <input v-model.trim="materialForm.name" type="text" maxlength="255" class="inventory-modal-control" placeholder="Material Name" required>
                                <p v-if="materialFieldError('name')" class="text-xs text-red-600">@{{ materialFieldError('name') }}</p>
                            </div>

                            <div class="inventory-modal-field">
                                <label class="inventory-modal-label">Qty *</label>
                                <input v-model="materialForm.qty" type="number" min="0" step="0.001" class="inventory-modal-control" placeholder="0.002" required>
                                <p v-if="materialFieldError('qty')" class="text-xs text-red-600">@{{ materialFieldError('qty') }}</p>
                            </div>

                            <div class="inventory-modal-field">
                                <label class="inventory-modal-label">Unit *</label>
                                <select v-model="materialForm.unit" class="inventory-modal-control" required>
                                    <option value="">Select Unit</option>
                                    <option v-for="unit in units" :key="unit.name" :value="unit.name">@{{ unit.name }}</option>
                                </select>
                                <p v-if="materialFieldError('unit')" class="text-xs text-red-600">@{{ materialFieldError('unit') }}</p>
                            </div>

                            <div class="inventory-modal-field inventory-modal-field-full">
                                <label class="inventory-modal-label">Vendors</label>
                                <input v-model="vendorSearch" type="search" class="inventory-modal-control" placeholder="Search vendors...">
                                <div class="max-h-44 overflow-y-auto rounded-md border border-gray-300 p-2 dark:border-gray-700" style="scrollbar-width: thin;">
                                    <label v-for="vendor in filteredVendors" :key="vendor.id" class="flex cursor-pointer items-center gap-2 rounded px-2 py-2 text-sm hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">
                                        <input v-model="materialForm.vendor_ids" type="checkbox" :value="Number(vendor.id)" class="peer h-4 w-4 cursor-pointer rounded border-gray-300 text-brandColor">
                                        <span class="truncate">@{{ vendor.name }}</span>
                                    </label>
                                    <p v-if="! filteredVendors.length" class="px-2 py-4 text-center text-sm text-gray-500">No vendors found.</p>
                                </div>
                                <p v-if="materialFieldError('vendor_ids')" class="text-xs text-red-600">@{{ materialFieldError('vendor_ids') }}</p>
                            </div>
                        </form>
                    </x-slot>

                    <x-slot:footer>
                        <div class="inventory-modal-actions">
                            <button type="button" class="secondary-button" :disabled="materialProcessing" @click="$refs.materialModal.close()">Cancel</button>
                            <button type="submit" form="inventory-material-form" class="primary-button inline-flex min-w-[150px] items-center justify-center gap-2" :disabled="materialProcessing">
                                <span :class="materialProcessing ? 'inventory-button-spinner' : 'icon-add'"></span>
                                @{{ materialProcessing ? 'Saving...' : 'Save Material' }}
                            </button>
                        </div>
                    </x-slot>
                </x-admin::modal>
            </div>
        </script>

        <script type="module">
            app.component('v-material-inventory', {
                template: '#material-inventory-template',

                props: {
                    materials: { type: Array, default: () => [] },
                    units: { type: Array, default: () => [] },
                    vendors: { type: Array, default: () => [] },
                    initialStats: { type: Object, required: true },
                    movementTypes: { type: Object, required: true },
                    movementUrl: { type: String, required: true },
                    materialUrl: { type: String, required: true },
                    initialDate: { type: String, required: true },
                },

                data() {
                    return {
                        isEditing: false,
                        isProcessing: false,
                        materialProcessing: false,
                        materialLabel: '',
                        lockedUnit: '',
                        materialOptions: this.materials.map((material) => ({ ...material })),
                        materialPickerOpen: false,
                        materialSearch: '',
                        vendorSearch: '',
                        stats: { ...this.initialStats },
                        errors: {},
                        materialErrors: {},
                        form: this.emptyForm(),
                        materialForm: this.emptyMaterialForm(),
                    };
                },

                computed: {
                    requiresUnitCost() {
                        return ['stock_in', 'set_balance'].includes(this.form.type);
                    },

                    selectedMaterial() {
                        return this.materialOptions.find((material) => Number(material.id) === Number(this.form.material_reference_id)) || null;
                    },

                    selectedUnit() {
                        return this.selectedMaterial?.unit || this.lockedUnit || '';
                    },

                    filteredMaterials() {
                        const term = String(this.materialSearch || '').trim().toLowerCase();

                        return this.materialOptions.filter((material) => {
                            return ! term
                                || String(material.name || '').toLowerCase().includes(term)
                                || String(material.unit || '').toLowerCase().includes(term);
                        });
                    },

                    filteredVendors() {
                        const term = String(this.vendorSearch || '').trim().toLowerCase();
                        const selectedIds = this.materialForm.vendor_ids.map((id) => Number(id));

                        return this.vendors
                            .filter((vendor) => ! term || String(vendor.name || '').toLowerCase().includes(term))
                            .slice()
                            .sort((a, b) => {
                                const aSelected = selectedIds.includes(Number(a.id));
                                const bSelected = selectedIds.includes(Number(b.id));

                                if (aSelected !== bSelected) {
                                    return aSelected ? -1 : 1;
                                }

                                return String(a.name || '').localeCompare(String(b.name || ''));
                            });
                    },
                },

                methods: {
                    emptyForm() {
                        return {
                            material_reference_id: '',
                            type: 'stock_in',
                            quantity: '',
                            unit_cost: '',
                            occurred_at: this.initialDate,
                            notes: '',
                        };
                    },

                    emptyMaterialForm() {
                        return {
                            name: '',
                            qty: '',
                            unit: '',
                            vendor_ids: [],
                        };
                    },

                    fieldError(field) {
                        return this.errors[field]?.[0] || '';
                    },

                    materialFieldError(field) {
                        return this.materialErrors[field]?.[0] || '';
                    },

                    toggleMaterialPicker() {
                        this.materialPickerOpen = ! this.materialPickerOpen;

                        if (this.materialPickerOpen) {
                            this.materialSearch = '';
                            this.$nextTick(() => this.$refs.materialSearch?.focus());
                        }
                    },

                    selectMaterial(material) {
                        this.form.material_reference_id = Number(material.id);
                        this.materialPickerOpen = false;
                        this.materialSearch = '';
                        delete this.errors.material_reference_id;
                    },

                    closeMaterialPicker() {
                        this.materialPickerOpen = false;
                    },

                    openMaterialModal() {
                        this.materialPickerOpen = false;
                        this.materialErrors = {};
                        this.vendorSearch = '';
                        this.materialForm = this.emptyMaterialForm();
                        this.$refs.materialModal.open();
                    },

                    saveMaterial() {
                        this.materialProcessing = true;
                        this.materialErrors = {};

                        this.$axios.post(this.materialUrl, this.materialForm).then((response) => {
                            const saved = response.data.data;
                            const material = {
                                id: Number(saved.id),
                                name: saved.name,
                                unit: saved.unit,
                            };

                            this.materialOptions.push(material);
                            this.materialOptions.sort((a, b) => String(a.name || '').localeCompare(String(b.name || '')));
                            this.selectMaterial(material);
                            this.stats = {
                                ...this.stats,
                                material_count: Number(this.stats.material_count) + 1,
                                out_of_stock_count: Number(this.stats.out_of_stock_count) + 1,
                            };
                            this.materialProcessing = false;
                            this.$refs.materialModal.close();
                            this.$refs.datagrid.get();
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        }).catch((error) => {
                            this.materialProcessing = false;
                            this.materialErrors = error.response?.data?.errors || {};

                            if (! Object.keys(this.materialErrors).length) {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response?.data?.message || 'Unable to create material.' });
                            }
                        });
                    },

                    openMovementModal() {
                        this.isEditing = false;
                        this.materialLabel = '';
                        this.lockedUnit = '';
                        this.materialPickerOpen = false;
                        this.errors = {};
                        this.form = this.emptyForm();
                        this.$refs.inventoryModal.open();
                    },

                    editStock(url) {
                        this.$axios.get(url).then((response) => {
                            const record = response.data.data;
                            this.isEditing = true;
                            this.errors = {};
                            this.materialLabel = `${record.material_name} (${record.unit})`;
                            this.lockedUnit = record.unit || '';
                            this.form = {
                                material_reference_id: record.material_reference_id,
                                type: 'set_balance',
                                quantity: record.on_hand,
                                unit_cost: record.average_unit_cost,
                                occurred_at: this.initialDate,
                                notes: '',
                            };
                            this.$refs.inventoryModal.open();
                        }).catch(() => {
                            this.$emitter.emit('add-flash', { type: 'error', message: 'Unable to load this inventory record.' });
                        });
                    },

                    handleDatagridModalAction(action) {
                        if (action?.index === 'edit_stock' && action.url) {
                            this.editStock(action.url);
                        }
                    },

                    saveMovement() {
                        if (! this.form.material_reference_id) {
                            this.errors = { material_reference_id: ['Please select a material.'] };
                            return;
                        }

                        this.isProcessing = true;
                        this.errors = {};

                        this.$axios.post(this.movementUrl, this.form).then((response) => {
                            this.isProcessing = false;
                            this.stats = response.data.data.totals;
                            this.$refs.inventoryModal.close();
                            this.$refs.datagrid.get();
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        }).catch((error) => {
                            this.isProcessing = false;
                            this.errors = error.response?.data?.errors || {};

                            if (! Object.keys(this.errors).length) {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response?.data?.message || 'Unable to update inventory.' });
                            }
                        });
                    },
                },

                mounted() {
                    this.$emitter.on('datagrid-modal-action', this.handleDatagridModalAction);
                    document.addEventListener('click', this.closeMaterialPicker);
                },

                beforeUnmount() {
                    this.$emitter.off('datagrid-modal-action', this.handleDatagridModalAction);
                    document.removeEventListener('click', this.closeMaterialPicker);
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
