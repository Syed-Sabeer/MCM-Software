@php
    $vendorOptions = $vendors->map(fn ($vendor) => ['id' => (int) $vendor->id, 'name' => $vendor->name])->values();
    $colorOptions = $colorReferences->map(fn ($color) => ['name' => $color->name, 'code' => $color->code])->values();
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
            :colors='@json($colorOptions)'
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
                                        <x-admin::form.control-group.control type="number" name="qty" rules="required|numeric|min:0" label="Qty" placeholder="Qty" />
                                        <x-admin::form.control-group.error control-name="qty" />
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">Unit</x-admin::form.control-group.label>
                                        <x-admin::form.control-group.control type="text" name="unit" rules="required|max:100" label="Unit" placeholder="Unit" />
                                        <x-admin::form.control-group.error control-name="unit" />
                                    </x-admin::form.control-group>
                                </div>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Vendors</x-admin::form.control-group.label>
                                    <select name="vendor_ids[]" multiple class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                        <option v-for="vendor in vendors" :value="vendor.id" :selected="selectedVendorIds.includes(vendor.id)">
                                            @{{ vendor.name }}
                                        </option>
                                    </select>
                                    <x-admin::form.control-group.error control-name="vendor_ids" />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Color</x-admin::form.control-group.label>
                                    <datalist id="material-reference-color-options">
                                        <option v-for="color in colors" :value="color.name">@{{ color.code }}</option>
                                    </datalist>

                                    <div class="flex items-center gap-3">
                                        <input type="text" name="color_name" list="material-reference-color-options" v-model="colorName" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="Color Name" @change="applyColorReference" @blur="applyColorReference">
                                        <input type="text" name="color_code" id="material-reference-color-code" v-model="colorCode" class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="#000000" @blur="normalizeColorCodeField">
                                        <input type="color" :value="colorPickerValue" class="h-11 w-14 cursor-pointer rounded border border-gray-300 bg-white p-1 dark:border-gray-700 dark:bg-gray-900" @input="updateColorFromPicker">
                                    </div>
                                    <x-admin::form.control-group.error control-name="color_name" />
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
                props: ['vendors', 'colors'],

                data() {
                    return {
                        isProcessing: false,
                        selectedRecord: false,
                        selectedVendorIds: [],
                        colorName: '',
                        colorCode: '',
                    };
                },

                computed: {
                    colorPickerValue() {
                        const normalized = this.normalizeColorCode(this.colorCode);

                        return /^#[0-9A-Fa-f]{6}$/.test(normalized) ? normalized : '#000000';
                    },
                },

                methods: {
                    normalizeColorCode(value) {
                        const trimmed = String(value || '').trim();

                        if (! trimmed) {
                            return '';
                        }

                        return trimmed.startsWith('#') ? trimmed.toUpperCase() : `#${trimmed.toUpperCase()}`;
                    },

                    normalizeColorCodeField() {
                        this.colorCode = this.normalizeColorCode(this.colorCode);
                    },

                    applyColorReference() {
                        const match = this.colors.find((color) => String(color.name || '').toLowerCase() === String(this.colorName || '').trim().toLowerCase());

                        if (match?.code) {
                            this.colorCode = this.normalizeColorCode(match.code);
                        }
                    },

                    updateColorFromPicker(event) {
                        this.colorCode = event.target.value || '#000000';
                    },

                    syncModalValues(values) {
                        this.$refs.modalForm.setValues(values);
                        this.selectedVendorIds = (values.vendor_ids || []).map((id) => Number(id));
                        this.colorName = values.color_name || '';
                        this.colorCode = this.normalizeColorCode(values.color_code || '');
                    },

                    openModal() {
                        this.selectedRecord = false;
                        this.syncModalValues({ id: '', name: '', qty: '', unit: '', vendor_ids: [], color_name: '', color_code: '' });
                        this.$refs.materialReferencesModal.open();
                    },

                    editModal(url) {
                        this.$axios.get(url).then((response) => {
                            this.selectedRecord = true;
                            this.syncModalValues(response.data.data || {});
                            this.$refs.materialReferencesModal.open();
                        });
                    },

                    updateOrCreate(params, { resetForm, setErrors }) {
                        this.isProcessing = true;
                        params.vendor_ids = this.selectedVendorIds;
                        params.color_name = this.colorName;
                        params.color_code = this.normalizeColorCode(this.colorCode);

                        this.$axios.post(params.id ? `{{ route('admin.settings.material_references.update', '') }}/${params.id}` : "{{ route('admin.settings.material_references.store') }}", {
                            ...params,
                            _method: params.id ? 'put' : 'post',
                        }).then((response) => {
                            this.isProcessing = false;
                            this.$refs.materialReferencesModal.close();
                            this.$refs.datagrid.get();
                            resetForm();
                            this.selectedVendorIds = [];
                            this.colorName = '';
                            this.colorCode = '';
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
                    this.$el.addEventListener('change', (event) => {
                        if (event.target.name === 'vendor_ids[]') {
                            this.selectedVendorIds = Array.from(event.target.selectedOptions).map((option) => Number(option.value));
                        }
                    });
                },
            });
        </script>
    @endpushOnce
</x-admin::layouts>
