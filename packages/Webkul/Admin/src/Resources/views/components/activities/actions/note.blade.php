@props([
    'entity'            => null,
    'entityControlName' => null,
])

<!-- Note Button -->
<div>
    {!! view_render_event('admin.components.activities.actions.note.create_btn.before') !!}

    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg border border-transparent bg-orange-200 font-medium text-orange-800 transition-all hover:border-orange-400"
        onclick="window.dispatchEvent(new Event('open-note-activity'))"
    >
        <span class="icon-note text-2xl dark:!text-orange-800"></span>

        @lang('admin::app.components.activities.actions.note.btn')
    </button>

    {!! view_render_event('admin.components.activities.actions.note.create_btn.after') !!}

    {!! view_render_event('admin.components.activities.actions.note.before') !!}

    <!-- Note Action Vue Component -->
    <v-note-activity
        ref="noteActionComponent"
        :entity="{{ json_encode($entity) }}"
        entity-control-name="{{ $entityControlName }}"
    ></v-note-activity>

    {!! view_render_event('admin.components.activities.actions.note.after') !!}
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-note-activity-template">
        <Teleport to="body">
            {!! view_render_event('admin.components.activities.actions.note.form_controls.before') !!}

            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form ref="noteFormElement" @submit="handleSubmit($event, save)">
                    {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.before') !!}

                    <x-admin::modal
                        ref="noteActivityModal"
                        position="bottom-right"
                    >
                        <x-slot:header>
                            {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.header.title.before') !!}

                            <h3 class="text-base font-semibold dark:text-white">
                                @{{ editingActivityId ? 'Edit Comment' : 'Add Comment' }}
                            </h3>

                            {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.header.title.after') !!}
                        </x-slot>

                        <x-slot:content>
                            {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.header.content.controls.before') !!}

                            <!-- Activity Type -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="type"
                                value="note"
                            />

                            <!-- Id -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                ::name="entityControlName"
                                ::value="entity.id"
                            />

                            <!-- Comment -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.components.activities.actions.note.comment')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="textarea"
                                    name="comment"
                                    rules="required"
                                    :label="trans('admin::app.components.activities.actions.note.comment')"
                                />

                                <x-admin::form.control-group.error control-name="comment" />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.header.content.controls.after') !!}
                        </x-slot>

                        <x-slot:footer>
                            {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.header.footer.save_button.before') !!}

                            <x-admin::button
                                class="primary-button"
                                ::title="editingActivityId ? 'Save Changes' : 'Save Comment'"
                                ::loading="isStoring"
                                ::disabled="isStoring"
                            />

                            {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.header.footer.save_button.after') !!}
                        </x-slot>
                    </x-admin::modal>

                    {!! view_render_event('admin.components.activities.actions.note.form_controls.modal.after') !!}
                </form>
            </x-admin::form>

            {!! view_render_event('admin.components.activities.actions.note.form_controls.after') !!}
        </Teleport>
    </script>

    <script type="module">
        app.component('v-note-activity', {
            template: '#v-note-activity-template',

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
                    editingActivityId: null,
                }
            },

            methods: {
                openModal(activity = null) {
                    this.editingActivityId = activity?.id || null;

                    if (this.$refs.noteActivityModal && typeof this.$refs.noteActivityModal.open === 'function') {
                        this.$refs.noteActivityModal.open();
                    } else {
                        this.$nextTick(() => {
                            if (this.$refs.noteActivityModal && typeof this.$refs.noteActivityModal.open === 'function') {
                                this.$refs.noteActivityModal.open();
                            }
                        });
                    }

                    this.$nextTick(() => {
                        const commentInput = this.$refs.noteFormElement?.querySelector('[name="comment"]');

                        if (commentInput) {
                            commentInput.value = activity?.comment || '';
                            commentInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });
                },

                save(params, { setErrors } = {}) {
                    const isEditing = !! this.editingActivityId;

                    const requestUrl = isEditing
                        ? "{{ route('admin.activities.update', '__id__') }}".replace('__id__', String(this.editingActivityId))
                        : "{{ route('admin.activities.store') }}";

                    const payload = isEditing
                        ? {
                            ...params,
                            _method: 'PUT',
                        }
                        : params;

                    this.isStoring = true;

                    this.$axios.post(requestUrl, payload)
                        .then (response => {
                            this.isStoring = false;

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            if (isEditing) {
                                this.$emitter.emit('on-activity-updated', response.data.data);
                            } else {
                                this.$emitter.emit('on-activity-added', response.data.data);
                            }

                            this.editingActivityId = null;

                            this.$refs.noteActivityModal.close();
                        })
                        .catch (error => {
                            this.isStoring = false;

                            if (error.response?.status == 422) {
                                if (typeof setErrors === 'function') {
                                    setErrors(error.response.data.errors || {});
                                }

                                this.$emitter.emit('add-flash', {
                                    type: 'error',
                                    message: Object.values(error.response?.data?.errors || {}).flat()[0] || error.response?.data?.message || 'Validation failed',
                                });
                            } else {
                                this.$emitter.emit('add-flash', {
                                    type: 'error',
                                    message: error.response?.data?.message || 'Failed to save comment',
                                });

                                this.$refs.noteActivityModal.close();
                            }
                        });
                },
            },

            mounted() {
                this._openNoteListener = (event) => this.openModal(event?.detail?.activity || null);
                window.addEventListener('open-note-activity', this._openNoteListener);
            },

            beforeUnmount() {
                window.removeEventListener('open-note-activity', this._openNoteListener);
            },
        });
    </script>
@endPushOnce
