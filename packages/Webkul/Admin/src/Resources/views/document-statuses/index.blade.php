@php
    $typeLabels = [
        'proforma_invoice' => 'Proforma Invoice Statuses',
        'job_order'        => 'Job Order Statuses',
        'vendor_quote'     => 'Vendor Quote Statuses',
        'purchase_order'   => 'Purchase Order Statuses',
    ];

    $title = $typeLabels[$type] ?? 'Document Statuses';
@endphp

<x-admin::layouts>
    <x-slot:title>{{ $title }}</x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs name="configuration" />

                <div class="text-xl font-bold dark:text-white">{{ $title }}</div>
            </div>

            <button type="button" class="primary-button" @click="$refs.documentStatuses.openModal()">
                Add Status
            </button>
        </div>

        <v-document-statuses ref="documentStatuses">
            <x-admin::shimmer.datagrid />
        </v-document-statuses>
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="document-statuses-template">
            <div>
                <x-admin::datagrid :src="route('admin.document_statuses.index', $type)" ref="datagrid">
                    <template #body="{ isLoading, available, applied, performAction }">
                        <template v-if="isLoading">
                            <x-admin::shimmer.datagrid.table.body />
                        </template>

                        <template v-else>
                            <div
                                v-for="record in available.records"
                                :key="record.id"
                                class="row grid items-center gap-2.5 border-b px-4 py-4 text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950 max-lg:hidden"
                                :style="`grid-template-columns: repeat(${gridsCount}, minmax(0, 1fr))`"
                            >
                                <p>@{{ record.id }}</p>
                                <p class="font-medium text-gray-800 dark:text-white">@{{ record.name }}</p>
                                <p>@{{ record.updated_at }}</p>

                                <div class="flex justify-end gap-1">
                                    <button
                                        type="button"
                                        class="icon-edit cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800"
                                        title="Edit"
                                        @click="openModal(record)"
                                    ></button>

                                    <button
                                        type="button"
                                        class="icon-delete cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800"
                                        title="Delete"
                                        @click="performAction(record.actions.find(action => action.index === 'delete'))"
                                    ></button>
                                </div>
                            </div>

                            <div
                                v-for="record in available.records"
                                :key="`mobile-${record.id}`"
                                class="hidden border-b px-4 py-4 text-gray-600 dark:border-gray-800 dark:text-gray-300 max-lg:block"
                            >
                                <div class="mb-3 flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-white">@{{ record.name }}</p>
                                    </div>

                                    <div class="flex gap-1">
                                        <button type="button" class="icon-edit rounded-md p-1.5 text-2xl hover:bg-gray-200 dark:hover:bg-gray-800" title="Edit" @click="openModal(record)"></button>
                                        <button type="button" class="icon-delete rounded-md p-1.5 text-2xl hover:bg-gray-200 dark:hover:bg-gray-800" title="Delete" @click="performAction(record.actions.find(action => action.index === 'delete'))"></button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <span class="text-gray-500">ID</span><span>@{{ record.id }}</span>
                                    <span class="text-gray-500">Updated At</span><span>@{{ record.updated_at }}</span>
                                </div>
                            </div>

                            <div v-if="! available.records.length" class="px-4 py-10 text-center text-sm text-gray-500">
                                No statuses found.
                            </div>
                        </template>
                    </template>
                </x-admin::datagrid>

                <x-admin::form v-slot="{ handleSubmit }" as="div" ref="modalForm">
                    <form @submit="handleSubmit($event, saveStatus)">
                        <x-admin::modal ref="statusModal">
                            <x-slot:header>
                                <p class="text-lg font-bold text-gray-800 dark:text-white">
                                    @{{ selectedRecord ? 'Edit Status' : 'Add Status' }}
                                </p>
                            </x-slot>

                            <x-slot:content>
                                <x-admin::form.control-group.control type="hidden" name="id" />

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label class="required">Status Name</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.control
                                        type="text"
                                        id="document-status-name"
                                        name="name"
                                        rules="required|max:255"
                                        label="Status Name"
                                        maxlength="255"
                                        placeholder="Status Name"
                                    />
                                    <x-admin::form.control-group.error control-name="name" />
                                </x-admin::form.control-group>
                            </x-slot>

                            <x-slot:footer>
                                <button type="button" class="secondary-button" :disabled="isProcessing" @click="$refs.statusModal.close()">
                                    Cancel
                                </button>

                                <x-admin::button
                                    button-type="submit"
                                    class="primary-button justify-center"
                                    title="Save Status"
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
            app.component('v-document-statuses', {
                template: '#document-statuses-template',

                data() {
                    return {
                        isProcessing: false,
                        selectedRecord: null,
                    };
                },

                computed: {
                    gridsCount() {
                        const columns = this.$refs.datagrid?.available?.columns?.length || 3;

                        return columns + 1;
                    },
                },

                methods: {
                    openModal(record = null) {
                        this.selectedRecord = record;
                        this.$refs.modalForm.setValues({
                            id: record?.id || '',
                            name: record?.name || '',
                        });
                        this.$refs.statusModal.open();
                    },

                    saveStatus(params, { resetForm, setErrors }) {
                        this.isProcessing = true;

                        const url = params.id
                            ? `{{ route('admin.document_statuses.update', [$type, '__ID__']) }}`.replace('__ID__', params.id)
                            : `{{ route('admin.document_statuses.store', $type) }}`;

                        this.$axios.post(url, {
                            name: params.name,
                            _method: params.id ? 'PUT' : 'POST',
                        }).then((response) => {
                            this.$refs.statusModal.close();
                            this.$refs.datagrid.get();
                            resetForm();
                            this.selectedRecord = null;
                            this.$emitter.emit('add-flash', {
                                type: 'success',
                                message: response.data.message,
                            });
                        }).catch((error) => {
                            if (error.response?.data?.errors) {
                                setErrors(error.response.data.errors);
                            } else {
                                this.$emitter.emit('add-flash', {
                                    type: 'error',
                                    message: error.response?.data?.message || 'Unable to save status.',
                                });
                            }
                        }).finally(() => {
                            this.isProcessing = false;
                        });
                    },
                },
            });
        </script>
    @endpushOnce
</x-admin::layouts>
