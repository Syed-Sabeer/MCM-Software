@props([
    'entity'            => null,
    'entityControlName' => null,
])

<!-- Task Button -->
<div>
    {!! view_render_event('admin.components.activities.actions.task.create_btn.before') !!}

    <button
        style="background-color: #e9d5ff; color: #581c87;"
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg border border-transparent font-medium transition-all hover:border-fuchsia-400"
        onmouseover="this.style.borderColor = '#d8b4fe'"
        onmouseout="this.style.borderColor = 'transparent'"
        onclick="window.dispatchEvent(new Event('open-task-activity'))"
    >
        <span class="icon-tick text-2xl" style="color: #581c87;"></span>

        @lang('admin::app.components.activities.actions.task.btn')
    </button>

    {!! view_render_event('admin.components.activities.actions.task.create_btn.after') !!}

    {!! view_render_event('admin.components.activities.actions.task.before') !!}

    <!-- Task Action Vue Component -->
    <v-task
        ref="taskComponent"
        :entity="{{ json_encode($entity) }}"
        entity-control-name="{{ $entityControlName }}"
    ></v-task>

    {!! view_render_event('admin.components.activities.actions.task.after') !!}
</div>


@pushOnce('scripts')
    <script type="text/x-template" id="v-task-template">
        <Teleport to="body">
            {!! view_render_event('admin.components.activities.actions.task.form_controls.before') !!}

            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, save)">
                    {!! view_render_event('admin.components.activities.actions.task.form_controls.modal.before') !!}

                    <x-admin::modal
                        ref="taskModal"
                        position="bottom-right"
                    >
                        <x-slot:header>
                            <h3 class="text-base font-semibold dark:text-white">
                                New Task
                            </h3>
                        </x-slot>

                        <x-slot:content>
                            {!! view_render_event('admin.components.activities.actions.task.form_controls.modal.content.controls.before') !!}

                            <!-- Activity Type (hidden, set to 'task') -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="type"
                                value="task"
                            />

                            <!-- Entity ID -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                ::name="entityControlName"
                                ::value="entity.id"
                            />

                            <!-- Participants -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    Participants
                                </x-admin::form.control-group.label>

                                <x-admin::activities.actions.activity.participants />
                            </x-admin::form.control-group>

                            <!-- Subject -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    Subject
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="title"
                                    rules="required|max:200"
                                    label="Subject"
                                />

                                <x-admin::form.control-group.error control-name="title" />
                            </x-admin::form.control-group>

                            <!-- Due Date -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    Due Date
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="datetime"
                                    name="schedule_from"
                                    label="Due Date"
                                />

                                <x-admin::form.control-group.error control-name="schedule_from" />
                            </x-admin::form.control-group>

                            <!-- Related To -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    Related To
                                </x-admin::form.control-group.label>

                                <input
                                    type="text"
                                    class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-all focus:border-brandColor focus:ring-1 focus:ring-brandColor dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder-gray-500"
                                    :value="entityName"
                                    disabled
                                />

                                <input type="hidden" v-bind:name="entityControlName" v-bind:value="entity.id">
                            </x-admin::form.control-group>

                            <!-- Assigned To -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    Assigned To
                                </x-admin::form.control-group.label>

                                <input
                                    type="text"
                                    class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-all focus:border-brandColor focus:ring-1 focus:ring-brandColor dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder-gray-500"
                                    :value="currentUserName"
                                    disabled
                                />

                                <input type="hidden" name="user_id" v-bind:value="currentUserId">
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.components.activities.actions.task.form_controls.modal.content.controls.after') !!}
                        </x-slot:content>

                        <x-slot:footer>
                            {!! view_render_event('admin.components.activities.actions.task.form_controls.modal.footer.before') !!}

                            <button
                                type="submit"
                                class="primary-button"
                                :disabled="isStoring"
                            >
                                @lang('admin::app.components.activities.actions.task.save-btn')
                            </button>

                            {!! view_render_event('admin.components.activities.actions.task.form_controls.modal.footer.after') !!}
                        </x-slot:footer>
                    </x-admin::modal>

                    {!! view_render_event('admin.components.activities.actions.task.form_controls.modal.after') !!}
                </form>
            </x-admin::form>

            {!! view_render_event('admin.components.activities.actions.task.form_controls.after') !!}
        </Teleport>
    </script>

    <script type="module">
        app.component('v-task', {
            template: '#v-task-template',

            props: ['entity', 'entityControlName'],

            data() {
                return {
                    currentUserName: '{{ optional(auth()->user())->name }}',
                    currentUserId: {{ auth()->id() }},
                    relatedToName: '',
                    isStoring: false,
                };
            },

            computed: {
                entityName() {
                    if (typeof this.entity === 'object' && this.entity !== null) {
                        return this.entity.name || this.entity.title || '';
                    }
                    return '';
                },
            },

            methods: {
                openModal() {
                    if (this.$refs.taskModal && typeof this.$refs.taskModal.open === 'function') {
                        this.$refs.taskModal.open();
                    }
                },

                save(params) {
                    this.isStoring = true;

                    this.$axios.post('{{ route("admin.activities.store") }}', params)
                        .then(response => {
                            this.isStoring = false;

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            this.$emitter.emit('on-activity-added', response.data.data);

                            this.$refs.taskModal.close();
                        })
                        .catch(error => {
                            this.isStoring = false;

                            console.error('Task creation error:', error.response?.data);

                            if (error.response?.status === 422) {
                                const errors = error.response?.data?.errors;
                                const errorMessages = Object.values(errors || {}).flat();
                                const message = errorMessages.length > 0 ? errorMessages[0] : 'Validation failed';

                                this.$emitter.emit('add-flash', {
                                    type: 'error',
                                    message: message
                                });
                            } else {
                                this.$emitter.emit('add-flash', {
                                    type: 'error',
                                    message: error.response?.data?.message || 'Failed to create task'
                                });
                            }
                        });
                },
            },

            mounted() {
                this._openTaskListener = () => this.openModal();
                window.addEventListener('open-task-activity', this._openTaskListener);
            },

            beforeUnmount() {
                window.removeEventListener('open-task-activity', this._openTaskListener);
            },
        });
    </script>
@endPushOnce
