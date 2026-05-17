<x-admin::layouts>
    <x-slot:title>
        Material Units
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs name="configuration" />
                <div class="text-xl font-bold dark:text-white">Material Units</div>
            </div>

            <button type="button" class="primary-button" @click="$refs.unitReferences.openModal()">
                Add Unit
            </button>
        </div>

        <v-unit-references ref="unitReferences">
            <x-admin::shimmer.datagrid />
        </v-unit-references>
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="unit-references-template">
            <x-admin::datagrid :src="route('admin.settings.units.index')" ref="datagrid">
                <template #body="{ isLoading, available, applied, performAction }">
                    <template v-if="isLoading">
                        <x-admin::shimmer.datagrid.table.body />
                    </template>

                    <template v-else>
                        <div
                            v-for="record in available.records"
                            class="row grid items-center gap-2.5 border-b px-4 py-4 text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950 max-lg:hidden"
                            :style="`grid-template-columns: repeat(${gridsCount}, minmax(0, 1fr))`"
                        >
                            <div
                                v-if="available.massActions.length"
                                class="flex select-none items-center gap-16"
                            >
                                <input
                                    type="checkbox"
                                    :name="`mass_action_select_record_${record.id}`"
                                    :id="`mass_action_select_record_${record.id}`"
                                    :value="record.id"
                                    class="peer hidden"
                                    v-model="applied.massActions.indices"
                                >

                                <label
                                    class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-600 peer-checked:text-brandColor dark:text-gray-300"
                                    :for="`mass_action_select_record_${record.id}`"
                                ></label>
                            </div>

                            <p>@{{ record.id }}</p>
                            <p>@{{ record.name }}</p>
                            <p>@{{ record.meter_conversion }}</p>
                            <p>@{{ record.created_at }}</p>
                            <div
                                v-if="available.actions.length"
                                class="flex justify-end gap-1"
                            >
                                <a @click="selectedRecord = true; editModal(record.actions.find(action => action.index === 'edit')?.url)">
                                    <span :class="record.actions.find(action => action.index === 'edit')?.icon" class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800"></span>
                                </a>
                                <a @click="performAction(record.actions.find(action => action.index === 'delete'))">
                                    <span :class="record.actions.find(action => action.index === 'delete')?.icon" class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800"></span>
                                </a>
                            </div>
                        </div>
                    </template>
                </template>
            </x-admin::datagrid>

            <x-admin::form v-slot="{ handleSubmit }" as="div" ref="modalForm">
                <form @submit="handleSubmit($event, updateOrCreate)">
                    <x-admin::modal ref="unitReferencesModal">
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @{{ selectedRecord ? 'Edit Unit' : 'Add Unit' }}
                            </p>
                        </x-slot>

                        <x-slot:content>
                            <x-admin::form.control-group.control type="hidden" name="id" />

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">Unit Name</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    id="unit-reference-name"
                                    name="name"
                                    rules="required|max:100"
                                    label="Unit Name"
                                    maxlength="100"
                                    placeholder="Unit Name"
                                />
                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Conversion To Meter</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="number"
                                    id="unit-reference-meter-conversion"
                                    name="meter_conversion"
                                    rules="decimal|min_value:0.00000001"
                                    label="Conversion To Meter"
                                    step="0.00000001"
                                    min="0"
                                    placeholder="Example: 0.0254 for Inch"
                                />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">
                                    Leave blank for non-length units like PCS, CONES, BOX, etc.
                                </p>
                                <x-admin::form.control-group.error control-name="meter_conversion" />
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
        </script>

        <script type="module">
            app.component('v-unit-references', {
                template: '#unit-references-template',

                data() {
                    return {
                        isProcessing: false,
                        selectedRecord: false,
                    };
                },

                methods: {
                    openModal() {
                        this.selectedRecord = false;
                        this.$refs.modalForm.setValues({ id: '', name: '', meter_conversion: '' });
                        this.$refs.unitReferencesModal.open();
                    },

                    editModal(url) {
                        this.$axios.get(url).then((response) => {
                            this.selectedRecord = true;
                            this.$refs.modalForm.setValues(response.data.data || {});
                            this.$refs.unitReferencesModal.open();
                        });
                    },

                    updateOrCreate(params, { resetForm, setErrors }) {
                        this.isProcessing = true;

                        this.$axios.post(params.id ? `{{ route('admin.settings.units.update', '') }}/${params.id}` : "{{ route('admin.settings.units.store') }}", {
                            ...params,
                            _method: params.id ? 'put' : 'post',
                        }).then((response) => {
                            this.isProcessing = false;
                            this.$refs.unitReferencesModal.close();
                            this.$refs.datagrid.get();
                            resetForm();
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        }).catch((error) => {
                            this.isProcessing = false;
                            if (error.response?.data?.errors) {
                                setErrors(error.response.data.errors);
                            }
                        });
                    },
                },

                computed: {
                    gridsCount() {
                        let count = this.$refs.datagrid.available.columns.length;

                        if (this.$refs.datagrid.available.actions.length) {
                            ++count;
                        }

                        if (this.$refs.datagrid.available.massActions.length) {
                            ++count;
                        }

                        return count;
                    },
                },
            });
        </script>
    @endpushOnce
</x-admin::layouts>
