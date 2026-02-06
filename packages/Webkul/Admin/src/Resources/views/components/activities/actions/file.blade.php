@props([
    'entity'            => null,
    'entityControlName' => null,
])

<!-- File Button -->
<div>
    {!! view_render_event('admin.components.activities.actions.file.create_btn.before') !!}

    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg border border-transparent bg-cyan-200 font-medium text-cyan-900 transition-all hover:border-cyan-400"
        onclick="window.dispatchEvent(new Event('open-file-activity'))"
    >
        <span class="icon-file text-2xl dark:!text-cyan-900"></span>

        @lang('admin::app.components.activities.actions.file.btn')
    </button>

    {!! view_render_event('admin.components.activities.actions.file.create_btn.after') !!}

    {!! view_render_event('admin.components.activities.actions.file.before') !!}

    <!-- File Action Vue Component -->
    <v-file-activity
        ref="fileActionComponent"
        :entity="{{ json_encode($entity) }}"
        entity-control-name="{{ $entityControlName }}"
    ></v-file-activity>

    {!! view_render_event('admin.components.activities.actions.file.after') !!}
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-file-activity-template">
        <Teleport to="body">
            {!! view_render_event('admin.components.activities.actions.file.form_controls.before') !!}

            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, save)">
                    {!! view_render_event('admin.components.activities.actions.file.form_controls.modal.before') !!}

                    <x-admin::modal
                        ref="fileActivityModal"
                        position="bottom-right"
                    >
                        <x-slot:header>
                            {!! view_render_event('admin.components.activities.actions.file.form_controls.modal.header.title.before') !!}

                            <h3 class="text-base font-semibold dark:text-white">
                                @lang('admin::app.components.activities.actions.file.title')
                            </h3>

                            {!! view_render_event('admin.components.activities.actions.file.form_controls.modal.header.title.after') !!}
                        </x-slot>

                        <x-slot:content>
                            {!! view_render_event('admin.components.activities.actions.file.form_controls.modal.content.controls.before') !!}

                            <!-- Activity Type -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="type"
                                value="file"
                            />

                            <!-- Id -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                ::name="entityControlName"
                                ::value="entity.id"
                            />

                            <!-- Entity Type -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="entity_type"
                                ::value="entityType"
                            />

                            <!-- Entity ID -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="entity_id"
                                ::value="entityId"
                            />

                            <!-- Title -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.components.activities.actions.file.title-control')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="title"
                                />
                            </x-admin::form.control-group>

                            <!-- Description -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.components.activities.actions.file.description')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    name="comment"
                                />
                            </x-admin::form.control-group>

                            <!-- File Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.components.activities.actions.file.name')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="name"
                                />
                            </x-admin::form.control-group>

                            <!-- File -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.components.activities.actions.file.file')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="file"
                                    id="file"
                                    name="file"
                                    rules="required"
                                    :label="trans('admin::app.components.activities.actions.file.file')"
                                />

                                <x-admin::form.control-group.error control-name="file" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.components.activities.actions.file.form_controls.modal.content.controls.after') !!}
                        </x-slot>

                        <x-slot:footer>
                            {!! view_render_event('admin.components.activities.actions.file.form_controls.modal.footer.save_buton.before') !!}

                            <x-admin::button
                                class="primary-button"
                                :title="trans('admin::app.components.activities.actions.file.save-btn')"
                                ::loading="isStoring"
                                ::disabled="isStoring"
                            />

                            {!! view_render_event('admin.components.activities.actions.file.form_controls.modal.footer.save_buton.after') !!}
                        </x-slot>
                    </x-admin::modal>

                    {!! view_render_event('admin.components.activities.actions.file.form_controls.modal.after') !!}
                </form>
            </x-admin::form>

            {!! view_render_event('admin.components.activities.actions.file.form_controls.after') !!}
        </Teleport>
    </script>

    <script type="module">
        app.component('v-file-activity', {
            template: '#v-file-activity-template',

            props: {
                entity: {
                    type: Object,
                    required: true,
                    default: () => {}
                },

                entityControlName: {
                    type: String,
                    required: true,
                    default: ''
                }
            },

            data: function () {
                return {
                    isStoring: false,
                }
            },

            computed: {
                entityType() {
                    // Determine entity type based on the entity object
                    if (this.entity && this.entity.billing_street !== undefined) {
                        return 'organizations';
                    }
                    return 'persons';
                },

                entityId() {
                    return this.entity?.id || null;
                },
            },

            methods: {
                openModal(type) {
                    if (this.$refs.fileActivityModal && typeof this.$refs.fileActivityModal.open === 'function') {
                        this.$refs.fileActivityModal.open();
                    } else {
                        this.$nextTick(() => {
                            if (this.$refs.fileActivityModal && typeof this.$refs.fileActivityModal.open === 'function') {
                                this.$refs.fileActivityModal.open();
                            }
                        });
                    }
                },

                save(params, { setErrors }) {
                    this.isStoring = true;

                    // Explicitly add entity type and id to the params
                    const payload = {
                        ...params,
                        entity_type: this.entityType,
                        entity_id: this.entityId,
                    };

                    this.$axios.post("{{ route('admin.activities.store') }}", payload, {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            }
                        })
                        .then (response => {
                            this.isStoring = false;

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            this.$emitter.emit('on-activity-added', response.data.data);

                            this.$refs.fileActivityModal.close();
                        })
                        .catch (error => {
                            this.isStoring = false;

                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);
                            } else {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                this.$refs.fileActivityModal.close();
                            }
                        });
                },
            },
            mounted() {
                this._openFileListener = () => this.openModal();
                window.addEventListener('open-file-activity', this._openFileListener);
            },

            beforeUnmount() {
                window.removeEventListener('open-file-activity', this._openFileListener);
            },
        });
    </script>
@endPushOnce
