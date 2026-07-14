@php
    $vendorOptions = $vendors->map(fn ($vendor) => ['id' => (int) $vendor->id, 'name' => $vendor->name])->values();
    $unitOptions = $units->map(fn ($unit) => ['name' => $unit->name])->values();
@endphp

<x-admin::layouts>
    <x-slot:title>
        Product Materials
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs name="configuration" />
                <div class="text-xl font-bold dark:text-white">Product Materials</div>
            </div>

            <button type="button" class="primary-button" @click="$refs.materialReferences.openModal()">
                Add Material
            </button>
        </div>

        <v-material-references
            ref="materialReferences"
            :vendors='@json($vendorOptions)'
            :units='@json($unitOptions)'
            initial-edit-url="{{ $editId ? route('admin.settings.material_references.edit', $editId) : '' }}"
        >
            <x-admin::shimmer.datagrid />
        </v-material-references>
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="material-references-template">
            <div>
                <x-admin::datagrid :src="route('admin.settings.material_references.index')" ref="datagrid" />

                <x-admin::form v-slot="{ handleSubmit }" as="div" ref="modalForm">
                    <form @submit="handleSubmit($event, updateOrCreate)">
                        <x-admin::modal ref="materialReferencesModal">
                            <x-slot:header>
                                <p class="text-lg font-bold text-gray-800 dark:text-white">
                                    @{{ selectedRecord ? 'Edit Material' : 'Add Material' }}
                                </p>
                            </x-slot>

                            <x-slot:content>
                                <x-admin::form.control-group.control type="hidden" name="id" />

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label class="required">Material Name</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.control type="text" name="name" rules="required|max:255" label="Material Name" placeholder="Material Name" />
                                    <x-admin::form.control-group.error control-name="name" />
                                </x-admin::form.control-group>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">Qty</x-admin::form.control-group.label>
                                        <x-admin::form.control-group.control type="text" name="qty" inputmode="decimal" rules="required|decimal:0,3|min_value:0" label="Qty" placeholder="0.002" />
                                        <x-admin::form.control-group.error control-name="qty" />
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">Unit</x-admin::form.control-group.label>
                                        <select
                                            name="unit"
                                            class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                            v-model="selectedUnit"
                                        >
                                            <option value="">Select Unit</option>
                                            <option v-for="unit in units" :value="unit.name">@{{ unit.name }}</option>
                                        </select>
                                        <x-admin::form.control-group.error control-name="unit" />
                                    </x-admin::form.control-group>
                                </div>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Vendors</x-admin::form.control-group.label>
                                    <div ref="vendorDropdown">
                                        <button
                                            type="button"
                                            class="w-full rounded border border-gray-300 px-3 py-2 text-left text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                            @click="vendorDropdownOpen = !vendorDropdownOpen"
                                        >
                                            @{{ selectedVendorSummary }}
                                        </button>

                                        <div
                                            v-if="vendorDropdownOpen"
                                            class="mt-2 rounded border border-gray-300 bg-white p-2 dark:border-gray-700 dark:bg-gray-900"
                                        >
                                            <input
                                                type="text"
                                                v-model="vendorSearchTerm"
                                                class="mb-2 w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                                placeholder="Search vendor..."
                                            >

                                            <div class="overflow-y-auto rounded border border-gray-300 p-2 pr-1 dark:border-gray-700" style="max-height: 148px; scrollbar-width: thin;">
                                                <label
                                                    v-for="vendor in filteredVendors"
                                                    :key="vendor.id"
                                                    class="mb-1 flex cursor-pointer items-center gap-2 rounded px-2 py-1 text-sm hover:bg-gray-100 dark:hover:bg-gray-800"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        :value="vendor.id"
                                                        v-model="selectedVendorIds"
                                                        class="h-4 w-4"
                                                    >
                                                    <span>@{{ vendor.name }}</span>
                                                </label>

                                                <p v-if="! filteredVendors.length" class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400">
                                                    No vendors found.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="hidden">
                                        <label
                                            v-for="vendorId in selectedVendorIds"
                                            :key="vendorId"
                                        >
                                            <input
                                                type="hidden"
                                                name="vendor_ids[]"
                                                :value="vendorId"
                                            >
                                        </label>
                                    </div>
                                    <x-admin::form.control-group.error control-name="vendor_ids" />
                                </x-admin::form.control-group>

                            </x-slot>

                            <x-slot:footer>
                                <x-admin::button
                                    button-type="submit"
                                    class="primary-button justify-center"
                                    title="Save"
                                    ::loading="isProcessing"
                                    ::disabled="isProcessing"
                                />
                            </x-slot>
                        </x-admin::modal>
                    </form>
                </x-admin::form>
            </div>
        </script>

        <script type="module">
            app.component('v-material-references', {
                template: '#material-references-template',
                props: {
                    vendors: {
                        type: Array,
                        default: () => [],
                    },

                    units: {
                        type: Array,
                        default: () => [],
                    },

                    initialEditUrl: {
                        type: String,
                        default: '',
                    },
                },

                data() {
                    return {
                        isProcessing: false,
                        selectedRecord: false,
                        selectedVendorIds: [],
                        selectedUnit: '',
                        vendorSearchTerm: '',
                        vendorDropdownOpen: false,
                    };
                },

                computed: {
                    filteredVendors() {
                        const term = String(this.vendorSearchTerm || '').trim().toLowerCase();
                        const selectedIds = this.selectedVendorIds.map((id) => Number(id));

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

                    selectedVendorSummary() {
                        if (! this.selectedVendorIds.length) {
                            return 'Select vendors';
                        }

                        const selectedNames = this.vendors
                            .filter((vendor) => this.selectedVendorIds.includes(Number(vendor.id)))
                            .map((vendor) => vendor.name);

                        if (selectedNames.length <= 2) {
                            return selectedNames.join(', ');
                        }

                        return `${selectedNames[0]}, ${selectedNames[1]} +${selectedNames.length - 2} more`;
                    },
                },

                methods: {
                    formatQtyForInput(value) {
                        if (value === null || value === undefined || value === '') {
                            return '';
                        }

                        const numericValue = Number(value);

                        if (Number.isNaN(numericValue)) {
                            return String(value);
                        }

                        return numericValue
                            .toFixed(3)
                            .replace(/\.?0+$/, '');
                    },

                    syncModalValues(values) {
                        values = {
                            ...values,
                            qty: this.formatQtyForInput(values.qty),
                        };

                        this.$refs.modalForm.setValues(values);
                        this.selectedVendorIds = (values.vendor_ids || []).map((id) => Number(id));
                        this.selectedUnit = values.unit || '';
                    },

                    openModal() {
                        this.selectedRecord = false;
                        this.syncModalValues({ id: '', name: '', qty: '', unit: '', vendor_ids: [] });
                        this.vendorSearchTerm = '';
                        this.vendorDropdownOpen = false;
                        this.$refs.materialReferencesModal.open();
                    },

                    editModal(url) {
                        this.$axios.get(url).then((response) => {
                            this.selectedRecord = true;
                            this.syncModalValues(response.data.data || {});
                            this.$refs.materialReferencesModal.open();
                        });
                    },

                    handleDatagridModalAction(action) {
                        if (! action || action.index !== 'edit' || ! action.url) {
                            return;
                        }

                        this.editModal(action.url);
                    },

                    updateOrCreate(params, { resetForm, setErrors }) {
                        this.isProcessing = true;
                        params.vendor_ids = this.selectedVendorIds;
                        params.unit = this.selectedUnit;

                        this.$axios.post(params.id ? `{{ route('admin.settings.material_references.update', '') }}/${params.id}` : "{{ route('admin.settings.material_references.store') }}", {
                            ...params,
                            _method: params.id ? 'put' : 'post',
                        }).then((response) => {
                            this.isProcessing = false;
                            this.$refs.materialReferencesModal.close();
                            this.$refs.datagrid.get();
                            resetForm();
                            this.selectedVendorIds = [];
                            this.selectedUnit = '';
                            this.vendorSearchTerm = '';
                            this.vendorDropdownOpen = false;
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        }).catch((error) => {
                            this.isProcessing = false;
                            if (error.response?.data?.errors) {
                                setErrors(error.response.data.errors);
                            }
                        });
                    },
                },

                mounted() {
                    this.$emitter.on('datagrid-modal-action', this.handleDatagridModalAction);

                    this.$el.addEventListener('change', (event) => {
                        if (event.target.name === 'unit') {
                            this.selectedUnit = event.target.value || '';
                        }
                    });

                    if (this.initialEditUrl) {
                        this.$nextTick(() => this.editModal(this.initialEditUrl));
                    }
                },

                beforeUnmount() {
                    this.$emitter.off('datagrid-modal-action', this.handleDatagridModalAction);
                },
            });
        </script>
    @endpushOnce
</x-admin::layouts>
