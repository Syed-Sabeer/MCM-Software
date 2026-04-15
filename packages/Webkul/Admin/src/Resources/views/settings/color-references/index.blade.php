<x-admin::layouts>
    <x-slot:title>
        Product Colors
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs name="configuration" />
                <div class="text-xl font-bold dark:text-white">Product Colors</div>
            </div>

            <button type="button" class="primary-button" @click="$refs.colorReferences.openModal()">
                Add Color
            </button>
        </div>

        <v-color-references ref="colorReferences">
            <x-admin::shimmer.datagrid />
        </v-color-references>
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="color-references-template">
            <x-admin::datagrid :src="route('admin.settings.color_references.index')" ref="datagrid">
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
                            <div v-html="record.code"></div>
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
                    <x-admin::modal ref="colorReferencesModal">
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @{{ selectedRecord ? 'Edit Color' : 'Add Color' }}
                            </p>
                        </x-slot>

                        <x-slot:content>
                            <x-admin::form.control-group.control type="hidden" name="id" />

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">Color Name</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    id="color-reference-name"
                                    name="name"
                                    rules="required|max:100"
                                    label="Color Name"
                                    maxlength="100"
                                    placeholder="Color Name"
                                />
                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">Color Code</x-admin::form.control-group.label>
                                <div class="flex items-center gap-3">
                                    <x-admin::form.control-group.control
                                        type="text"
                                        id="color-reference-code"
                                        name="code"
                                        rules="required|max:20"
                                        label="Color Code"
                                        maxlength="20"
                                        placeholder="#000000"
                                    />

                                    <input
                                        type="color"
                                        v-model="colorPicker"
                                        class="h-11 w-14 cursor-pointer rounded border border-gray-300 bg-white p-1 dark:border-gray-700 dark:bg-gray-900"
                                        @input="updateCodeFromPicker"
                                    >
                                </div>
                                <x-admin::form.control-group.error control-name="code" />
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
            app.component('v-color-references', {
                template: '#color-references-template',

                data() {
                    return {
                        isProcessing: false,
                        selectedRecord: false,
                        colorPicker: '#000000',
                    };
                },

                methods: {
                    normalizeColorCode(value) {
                        const trimmed = String(value || '').trim();

                        if (! trimmed) {
                            return '';
                        }

                        return trimmed.startsWith('#') ? trimmed : `#${trimmed}`;
                    },

                    syncCodeInput(value) {
                        const input = document.getElementById('color-reference-code');
                        const normalized = this.normalizeColorCode(value) || '#000000';

                        this.colorPicker = /^#[0-9A-Fa-f]{6}$/.test(normalized) ? normalized : '#000000';

                        if (! input) {
                            return;
                        }

                        input.value = normalized;
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    },

                    syncPickerFromCodeInput() {
                        const input = document.getElementById('color-reference-code');
                        const normalized = this.normalizeColorCode(input ? input.value : '');

                        this.colorPicker = /^#[0-9A-Fa-f]{6}$/.test(normalized) ? normalized : '#000000';
                    },

                    updateCodeFromPicker(event) {
                        this.syncCodeInput(event.target.value || '#000000');
                    },

                    openModal() {
                        this.selectedRecord = false;
                        this.colorPicker = '#000000';
                        this.$refs.modalForm.setValues({ id: '', name: '', code: '#000000' });
                        this.$refs.colorReferencesModal.open();

                        this.$nextTick(() => {
                            this.bindCodeInputEvents();
                        });
                    },

                    editModal(url) {
                        this.$axios.get(url).then((response) => {
                            const data = response.data.data || {};
                            const formData = {
                                id: data.id || '',
                                name: data.name || '',
                                code: this.normalizeColorCode(data.code) || '#000000',
                            };

                            this.colorPicker = formData.code;
                            this.$refs.modalForm.setValues(formData);
                            this.$refs.colorReferencesModal.open();

                            this.$nextTick(() => {
                                this.bindCodeInputEvents();
                            });
                        });
                    },

                    bindCodeInputEvents() {
                        const input = document.getElementById('color-reference-code');

                        if (! input || input.dataset.colorPickerBound === 'true') {
                            this.syncPickerFromCodeInput();
                            return;
                        }

                        input.addEventListener('input', () => this.syncPickerFromCodeInput());
                        input.addEventListener('blur', () => this.syncCodeInput(input.value));
                        input.dataset.colorPickerBound = 'true';

                        this.syncPickerFromCodeInput();
                    },

                    updateOrCreate(params, { resetForm, setErrors }) {
                        this.isProcessing = true;

                        params.code = this.normalizeColorCode(params.code) || '#000000';
                        this.colorPicker = params.code;

                        this.$axios.post(params.id ? `{{ route('admin.settings.color_references.update', '') }}/${params.id}` : "{{ route('admin.settings.color_references.store') }}", {
                            ...params,
                            _method: params.id ? 'put' : 'post',
                        }).then((response) => {
                            this.isProcessing = false;
                            this.$refs.colorReferencesModal.close();
                            this.$refs.datagrid.get();
                            resetForm();
                            this.colorPicker = '#000000';
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
