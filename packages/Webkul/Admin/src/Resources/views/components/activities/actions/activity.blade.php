@props([
    'entity'            => null,
    'entityControlName' => null,
])

<div>
    <button
        class="flex h-[74px] w-[84px] flex-col items-center justify-center gap-1 rounded-lg border border-transparent bg-cyan-100 font-medium text-cyan-800 transition-all hover:border-cyan-300"
        onclick="window.dispatchEvent(new Event('open-log-call-activity'))"
    >
        <span class="icon-call text-2xl"></span>
        Log a Call
    </button>

    <v-log-call-activity
        :entity='@json($entity)'
        entity-control-name="{{ $entityControlName }}"
    ></v-log-call-activity>
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-log-call-activity-template">
        <Teleport to="body">
            <x-admin::modal ref="callModal" position="bottom-right">
                <x-slot:header>
                    <h3 class="text-base font-semibold dark:text-white">Log a Call</h3>
                </x-slot>

                <x-slot:content>
                    <form ref="callForm" class="grid gap-4 rounded-xl bg-cyan-50/60 p-2 dark:bg-cyan-950/20" @submit.prevent="save">
                        <input type="hidden" name="type" value="call">

                        <input
                            v-if="entityControlName"
                            type="hidden"
                            :name="entityControlName"
                            :value="entity?.id || ''"
                        >

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">Subject</x-admin::form.control-group.label>
                            <input v-model="form.title" type="text" name="title" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" maxlength="200">
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Comment</x-admin::form.control-group.label>
                            <textarea v-model="form.comment" name="comment" rows="4" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>Name</x-admin::form.control-group.label>

                            <div class="relative" ref="contactsLookup">
                                <div class="rounded-xl border border-cyan-200 bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                                    <div class="mb-2 flex flex-wrap gap-2" v-if="contacts.selected.length">
                                        <span
                                            v-for="contact in contacts.selected"
                                            :key="`call-contact-${contact.id}`"
                                            class="inline-flex items-center gap-2 rounded-full bg-cyan-100 px-3 py-1 text-xs font-medium text-cyan-800 dark:bg-cyan-900/50 dark:text-cyan-200"
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
                                                :key="`call-contact-option-${person.id}`"
                                                type="button"
                                                class="flex w-full items-start justify-between rounded-lg px-3 py-2 text-left hover:bg-cyan-50 dark:hover:bg-gray-900"
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
                                    :key="`call-contact-input-${contact.id}`"
                                    type="hidden"
                                    :name="`participants[persons][${index}]`"
                                    :value="contact.id"
                                >
                            </div>
                        </x-admin::form.control-group>
                    </form>
                </x-slot>

                <x-slot:footer>
                    <button type="button" class="primary-button" @click="save" :disabled="isSaving">
                        Save Call
                    </button>
                </x-slot>
            </x-admin::modal>
        </Teleport>
    </script>

    <script type="module">
        app.component('v-log-call-activity', {
            template: '#v-log-call-activity-template',

            props: ['entity', 'entityControlName'],

            data() {
                return {
                    isSaving: false,
                    searchRequestTimeout: null,
                    form: {
                        title: '',
                        comment: '',
                    },
                    contacts: {
                        open: false,
                        search: '',
                        loading: false,
                        results: [],
                        selected: [],
                    },
                };
            },

            methods: {
                openModal() {
                    this.$refs.callModal.open();
                },

                resetForm() {
                    this.form = {
                        title: '',
                        comment: '',
                    };

                    this.contacts = {
                        open: false,
                        search: '',
                        loading: false,
                        results: [],
                        selected: [],
                    };
                },

                toggleLookup(type) {
                    this[type].open = !this[type].open;

                    if (this[type].open && type === 'contacts') {
                        this.searchContacts();
                    }
                },

                openLookup(type) {
                    this[type].open = true;

                    if (type === 'contacts') {
                        this.searchContacts();
                    }
                },

                searchContacts() {
                    clearTimeout(this.searchRequestTimeout);

                    this.searchRequestTimeout = setTimeout(() => {
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
                    }, 200);
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

                save() {
                    const payload = new FormData(this.$refs.callForm);

                    this.isSaving = true;

                    this.$axios.post('{{ route('admin.activities.store') }}', payload)
                        .then((response) => {
                            this.isSaving = false;
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            this.$emitter.emit('on-activity-added', response.data.data);
                            this.$refs.callModal.close();
                            this.resetForm();
                        })
                        .catch((error) => {
                            this.isSaving = false;
                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: error.response?.data?.message || Object.values(error.response?.data?.errors || {}).flat()[0] || 'Failed to log call',
                            });
                        });
                },

                handleOutsideClick(event) {
                    if (!this.$refs.contactsLookup?.contains(event.target)) {
                        this.contacts.open = false;
                    }
                },
            },

            mounted() {
                this._openCallListener = () => this.openModal();
                window.addEventListener('open-log-call-activity', this._openCallListener);
                window.addEventListener('click', this.handleOutsideClick);
            },

            beforeUnmount() {
                window.removeEventListener('open-log-call-activity', this._openCallListener);
                window.removeEventListener('click', this.handleOutsideClick);
            },
        });
    </script>
@endPushOnce
