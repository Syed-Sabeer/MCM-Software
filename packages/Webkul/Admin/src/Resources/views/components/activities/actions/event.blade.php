@props([
    'entity'            => null,
    'entityControlName' => null,
])

@php
    $defaultAssignee = auth()->guard('user')->check()
        ? [
            'id'    => auth()->guard('user')->user()->id,
            'name'  => auth()->guard('user')->user()->name,
            'email' => auth()->guard('user')->user()->email,
        ]
        : null;
@endphp

<div>
    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg border border-transparent bg-blue-100 font-medium text-blue-800 transition-all hover:border-blue-300"
        onclick="window.dispatchEvent(new Event('open-event-activity'))"
    >
        <span class="icon-calendar text-2xl"></span>
        Event
    </button>

    <v-event-activity
        :entity='@json($entity)'
        entity-control-name="{{ $entityControlName }}"
    ></v-event-activity>
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-event-activity-template">
        <Teleport to="body">
            <x-admin::modal ref="eventModal" position="bottom-right" size="medium">
                <x-slot:header>
                    <h3 class="text-base font-semibold dark:text-white">New Event</h3>
                </x-slot>

                <x-slot:content>
                    <form ref="eventForm" class="grid gap-4 rounded-xl bg-blue-50/70 p-2 dark:bg-blue-950/20" @submit.prevent="save">
                        <input type="hidden" name="type" value="meeting">

                        <input
                            v-if="shouldSendEntityBinding"
                            type="hidden"
                            :name="entityControlName"
                            :value="entity?.id || ''"
                        >

                        <input type="hidden" name="schedule_from" :value="scheduleFrom">
                        <input type="hidden" name="schedule_to" :value="scheduleTo">
                        <input type="hidden" name="organization_id" :value="selectedOrganizationId">
                        <input type="hidden" name="user_id" :value="primaryAssignedUserId">

                        <x-admin::form.control-group style="margin-bottom: 0 !important;">
                            <x-admin::form.control-group.label class="required">Subject</x-admin::form.control-group.label>
                            <input v-model="form.title" type="text" name="title" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" maxlength="200">
                        </x-admin::form.control-group>

                        <x-admin::form.control-group style="margin-bottom: 0 !important;">
                            <x-admin::form.control-group.label>Description</x-admin::form.control-group.label>
                            <textarea v-model="form.comment" name="comment" rows="4" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
                        </x-admin::form.control-group>

                        <div class="grid grid-cols-4 gap-4">
                            <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                <x-admin::form.control-group.label class="required">Start Date</x-admin::form.control-group.label>
                                <input v-model="form.start_date" type="date" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            </x-admin::form.control-group>

                            <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                <x-admin::form.control-group.label class="required">Start Time</x-admin::form.control-group.label>
                                <input v-model="form.start_time" type="time" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            </x-admin::form.control-group>

                            <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                <x-admin::form.control-group.label class="required">End Date</x-admin::form.control-group.label>
                                <input v-model="form.end_date" type="date" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            </x-admin::form.control-group>

                            <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                <x-admin::form.control-group.label class="required">End Time</x-admin::form.control-group.label>
                                <input v-model="form.end_time" type="time" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            </x-admin::form.control-group>
                        </div>

                        <x-admin::form.control-group style="margin-bottom: 0 !important;">
                            <x-admin::form.control-group.label>Name</x-admin::form.control-group.label>
                            <div class="relative" ref="contactsLookup">
                                <div class="rounded-xl border border-blue-200 bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                    <div class="mb-2 flex flex-wrap gap-2" v-if="contacts.selected.length">
                                        <span
                                            v-for="contact in contacts.selected"
                                            :key="`event-contact-${contact.id}`"
                                            class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900/50 dark:text-blue-200"
                                        >
                                            @{{ contact.name }}
                                            <button type="button" class="icon-cross-large text-sm" @click.stop="removeContact(contact.id)"></button>
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input
                                            v-model="contacts.search"
                                            type="text"
                                            class="w-full border-0 bg-transparent px-1 py-1 text-sm text-gray-900 outline-none placeholder:text-gray-400 dark:text-white"
                                            placeholder="Search contacts"
                                            @focus="openLookup('contacts')"
                                            @input="searchContacts"
                                        >

                                        <button type="button" class="text-xl text-gray-500" @click="toggleLookup('contacts')">
                                            <span :class="contacts.open ? 'icon-up-arrow' : 'icon-down-arrow'"></span>
                                        </button>
                                    </div>

                                    <div v-if="contacts.open" class="mt-3 max-h-[156px] overflow-y-auto rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-950">
                                        <div v-if="contacts.loading" class="px-3 py-2 text-sm text-gray-500">Searching...</div>

                                        <template v-else-if="contacts.results.length">
                                            <button
                                                v-for="person in contacts.results"
                                                :key="`event-contact-option-${person.id}`"
                                                type="button"
                                                class="flex w-full items-start justify-between rounded-lg px-3 py-2 text-left hover:bg-blue-50 dark:hover:bg-gray-900"
                                                @click="selectContact(person)"
                                            >
                                                <span class="min-w-0">
                                                    <span class="block text-sm font-medium text-gray-800 dark:text-white">@{{ person.name }}</span>
                                                    <span class="block text-xs text-gray-500">@{{ person.organization_name || person.email || 'Contact' }}</span>
                                                </span>
                                            </button>
                                        </template>

                                        <div v-else class="px-3 py-2 text-sm text-gray-500">
                                            @{{ contacts.search.length < 1 ? 'Start typing to search contacts.' : 'No contacts found.' }}
                                        </div>
                                    </div>
                                </div>

                                <input
                                    v-for="(contact, index) in contacts.selected"
                                    :key="`event-contact-input-${contact.id}`"
                                    type="hidden"
                                    :name="`participants[persons][${index}]`"
                                    :value="contact.id"
                                >
                            </div>
                        </x-admin::form.control-group>

                        <div class="grid grid-cols-2 gap-4">
                        <x-admin::form.control-group style="margin-bottom: 0 !important;">
                            <x-admin::form.control-group.label class="required">Related To</x-admin::form.control-group.label>
                            <div class="relative" ref="organizationLookup">
                                <div class="rounded-xl border border-blue-200 bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                    <div v-if="organization.selected" class="mb-2">
                                        <span class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900/50 dark:text-blue-200">
                                            @{{ organization.selected.name }}
                                            <button type="button" class="icon-cross-large text-sm" @click.stop="clearOrganization"></button>
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input
                                            v-model="organization.search"
                                            type="text"
                                            class="w-full border-0 bg-transparent px-1 py-1 text-sm text-gray-900 outline-none placeholder:text-gray-400 dark:text-white"
                                            placeholder="Search company"
                                            @focus="openLookup('organization')"
                                            @input="searchOrganizations"
                                        >

                                        <button type="button" class="text-xl text-gray-500" @click="toggleLookup('organization')">
                                            <span :class="organization.open ? 'icon-up-arrow' : 'icon-down-arrow'"></span>
                                        </button>
                                    </div>

                                    <div v-if="organization.open" class="mt-3 max-h-[156px] overflow-y-auto rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-950">
                                        <div v-if="organization.loading" class="px-3 py-2 text-sm text-gray-500">Searching...</div>

                                        <template v-else-if="organization.results.length">
                                            <button
                                                v-for="company in organization.results"
                                                :key="`event-company-option-${company.id}`"
                                                type="button"
                                                class="flex w-full items-start justify-between rounded-lg px-3 py-2 text-left hover:bg-blue-50 dark:hover:bg-gray-900"
                                                @click="selectOrganization(company)"
                                            >
                                                <span class="block text-sm font-medium text-gray-800 dark:text-white">@{{ company.name }}</span>
                                            </button>
                                        </template>

                                        <div v-else class="px-3 py-2 text-sm text-gray-500">
                                            @{{ organization.search.length < 1 ? 'Start typing to search companies.' : 'No companies found.' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Assigned To</x-admin::form.control-group.label>
                            <div class="relative" ref="assigneesLookup">
                                <div class="rounded-xl border border-blue-200 bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                    <div class="mb-2 flex flex-wrap gap-2" v-if="assignees.selected.length">
                                        <span
                                            v-for="assignee in assignees.selected"
                                            :key="`event-assignee-${assignee.id}`"
                                            class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900/50 dark:text-blue-200"
                                        >
                                            @{{ assignee.name }}
                                            <button type="button" class="icon-cross-large text-sm" @click.stop="removeAssignee(assignee.id)"></button>
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input
                                            v-model="assignees.search"
                                            type="text"
                                            class="w-full border-0 bg-transparent px-1 py-1 text-sm text-gray-900 outline-none placeholder:text-gray-400 dark:text-white"
                                            placeholder="Search employees"
                                            @focus="openLookup('assignees')"
                                            @input="searchAssignees"
                                        >

                                        <button type="button" class="text-xl text-gray-500" @click="toggleLookup('assignees')">
                                            <span :class="assignees.open ? 'icon-up-arrow' : 'icon-down-arrow'"></span>
                                        </button>
                                    </div>

                                    <div v-if="assignees.open" class="mt-3 max-h-[156px] overflow-y-auto rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-950">
                                        <div v-if="assignees.loading" class="px-3 py-2 text-sm text-gray-500">Searching...</div>

                                        <template v-else-if="assignees.results.length">
                                            <button
                                                v-for="user in assignees.results"
                                                :key="`event-assignee-option-${user.id}`"
                                                type="button"
                                                class="flex w-full items-start justify-between rounded-lg px-3 py-2 text-left hover:bg-blue-50 dark:hover:bg-gray-900"
                                                @click="selectAssignee(user)"
                                            >
                                                <span class="min-w-0">
                                                    <span class="block text-sm font-medium text-gray-800 dark:text-white">@{{ user.name }}</span>
                                                    <span class="block text-xs text-gray-500">@{{ user.email || 'Employee' }}</span>
                                                </span>
                                            </button>
                                        </template>

                                        <div v-else class="px-3 py-2 text-sm text-gray-500">
                                            @{{ assignees.search.length < 1 ? 'Start typing to search employees.' : 'No employees found.' }}
                                        </div>
                                    </div>
                                </div>

                                <input
                                    v-for="(assignee, index) in assignees.selected"
                                    :key="`event-assignee-input-${assignee.id}`"
                                    type="hidden"
                                    :name="`participants[users][${index}]`"
                                    :value="assignee.id"
                                >
                            </div>
                        </x-admin::form.control-group>
                        </div>
                    </form>
                </x-slot>

                <x-slot:footer>
                    <button type="button" class="primary-button" @click="save" :disabled="isSaving">
                        Save Event
                    </button>
                </x-slot>
            </x-admin::modal>
        </Teleport>
    </script>

    <script type="module">
        app.component('v-event-activity', {
            template: '#v-event-activity-template',

            props: ['entity', 'entityControlName'],

            data() {
                return {
                    isSaving: false,
                    searchRequestTimeout: null,
                    defaultAssignee: @json($defaultAssignee),
                    form: {
                        title: '',
                        comment: '',
                        start_date: '',
                        start_time: '',
                        end_date: '',
                        end_time: '',
                    },
                    contacts: {
                        open: false,
                        search: '',
                        loading: false,
                        results: [],
                        selected: [],
                    },
                    organization: {
                        open: false,
                        search: '',
                        loading: false,
                        results: [],
                        selected: null,
                    },
                    assignees: {
                        open: false,
                        search: '',
                        loading: false,
                        results: [],
                        selected: [],
                    },
                };
            },

            computed: {
                shouldSendEntityBinding() {
                    return this.entityControlName && this.entityControlName !== 'organization_id';
                },

                scheduleFrom() {
                    return this.form.start_date && this.form.start_time
                        ? `${this.form.start_date} ${this.form.start_time}:00`
                        : '';
                },

                scheduleTo() {
                    return this.form.end_date && this.form.end_time
                        ? `${this.form.end_date} ${this.form.end_time}:00`
                        : '';
                },

                selectedOrganizationId() {
                    return this.organization.selected?.id || '';
                },

                primaryAssignedUserId() {
                    return this.assignees.selected[0]?.id || '';
                },
            },

            methods: {
                openModal() {
                    this.$refs.eventModal.open();
                },

                resetForm() {
                    this.form = {
                        title: '',
                        comment: '',
                        start_date: '',
                        start_time: '',
                        end_date: '',
                        end_time: '',
                    };

                    this.contacts = {
                        open: false,
                        search: '',
                        loading: false,
                        results: [],
                        selected: [],
                    };

                    this.organization = {
                        open: false,
                        search: '',
                        loading: false,
                        results: [],
                        selected: this.entityControlName === 'organization_id' && this.entity
                            ? { id: this.entity.id, name: this.entity.name }
                            : (this.entity?.organization
                                ? { id: this.entity.organization.id, name: this.entity.organization.name }
                                : null),
                    };

                    this.assignees = {
                        open: false,
                        search: '',
                        loading: false,
                        results: [],
                        selected: this.defaultAssignee ? [{ ...this.defaultAssignee }] : [],
                    };
                },

                toggleLookup(type) {
                    this[type].open = !this[type].open;

                    if (this[type].open) {
                        this.dispatchSearch(type);
                    }
                },

                openLookup(type) {
                    this[type].open = true;
                    this.dispatchSearch(type);
                },

                dispatchSearch(type) {
                    if (type === 'contacts') {
                        this.searchContacts();
                    }

                    if (type === 'organization') {
                        this.searchOrganizations();
                    }

                    if (type === 'assignees') {
                        this.searchAssignees();
                    }
                },

                runSearch(callback) {
                    clearTimeout(this.searchRequestTimeout);
                    this.searchRequestTimeout = setTimeout(callback, 200);
                },

                searchContacts() {
                    this.runSearch(() => {
                        this.contacts.loading = true;

                        this.$axios.get('{{ route('admin.activities.search_persons') }}', {
                            params: {
                                query: this.contacts.search,
                            },
                        }).then((response) => {
                            const selectedIds = this.contacts.selected.map(item => Number(item.id));

                            this.contacts.results = (response.data.data || []).filter(item => !selectedIds.includes(Number(item.id)));
                            this.contacts.loading = false;
                        }).catch(() => {
                            this.contacts.results = [];
                            this.contacts.loading = false;
                        });
                    });
                },

                searchOrganizations() {
                    this.runSearch(() => {
                        this.organization.loading = true;

                        this.$axios.get('{{ route('admin.activities.search_organizations') }}', {
                            params: {
                                query: this.organization.search,
                            },
                        }).then((response) => {
                            const selectedId = Number(this.organization.selected?.id || 0);

                            this.organization.results = (response.data.data || []).filter(item => Number(item.id) !== selectedId);
                            this.organization.loading = false;
                        }).catch(() => {
                            this.organization.results = [];
                            this.organization.loading = false;
                        });
                    });
                },

                searchAssignees() {
                    this.runSearch(() => {
                        this.assignees.loading = true;

                        this.$axios.get('{{ route('admin.activities.search_employee_users') }}', {
                            params: {
                                query: this.assignees.search,
                            },
                        }).then((response) => {
                            const selectedIds = this.assignees.selected.map(item => Number(item.id));

                            this.assignees.results = (response.data.data || []).filter(item => !selectedIds.includes(Number(item.id)));
                            this.assignees.loading = false;
                        }).catch(() => {
                            this.assignees.results = [];
                            this.assignees.loading = false;
                        });
                    });
                },

                selectContact(person) {
                    if (this.contacts.selected.some(item => Number(item.id) === Number(person.id))) {
                        return;
                    }

                    this.contacts.selected.push(person);
                    this.contacts.search = '';
                    this.contacts.results = this.contacts.results.filter(item => Number(item.id) !== Number(person.id));
                },

                removeContact(contactId) {
                    this.contacts.selected = this.contacts.selected.filter(item => Number(item.id) !== Number(contactId));
                },

                selectOrganization(company) {
                    this.organization.selected = company;
                    this.organization.search = '';
                    this.organization.results = [];
                    this.organization.open = false;
                },

                clearOrganization() {
                    this.organization.selected = null;
                },

                selectAssignee(user) {
                    if (this.assignees.selected.some(item => Number(item.id) === Number(user.id))) {
                        return;
                    }

                    this.assignees.selected.push(user);
                    this.assignees.search = '';
                    this.assignees.results = this.assignees.results.filter(item => Number(item.id) !== Number(user.id));
                },

                removeAssignee(userId) {
                    this.assignees.selected = this.assignees.selected.filter(item => Number(item.id) !== Number(userId));
                },

                save() {
                    const payload = new FormData(this.$refs.eventForm);

                    this.isSaving = true;

                    this.$axios.post('{{ route('admin.activities.store') }}', payload)
                        .then((response) => {
                            this.isSaving = false;
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            this.$emitter.emit('on-activity-added', response.data.data);
                            this.$refs.eventModal.close();
                            this.resetForm();
                        })
                        .catch((error) => {
                            this.isSaving = false;
                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: error.response?.data?.message || Object.values(error.response?.data?.errors || {}).flat()[0] || 'Failed to create event',
                            });
                        });
                },

                handleOutsideClick(event) {
                    if (!this.$refs.contactsLookup?.contains(event.target)) {
                        this.contacts.open = false;
                    }

                    if (!this.$refs.organizationLookup?.contains(event.target)) {
                        this.organization.open = false;
                    }

                    if (!this.$refs.assigneesLookup?.contains(event.target)) {
                        this.assignees.open = false;
                    }
                },
            },

            mounted() {
                this.resetForm();
                this._openEventListener = () => this.openModal();
                window.addEventListener('open-event-activity', this._openEventListener);
                window.addEventListener('click', this.handleOutsideClick);
            },

            beforeUnmount() {
                window.removeEventListener('open-event-activity', this._openEventListener);
                window.removeEventListener('click', this.handleOutsideClick);
            },
        });
    </script>
@endPushOnce
