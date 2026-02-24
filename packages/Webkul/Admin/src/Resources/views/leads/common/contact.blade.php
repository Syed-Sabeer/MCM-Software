{!! view_render_event('admin.leads.create.contact_person.form_controls.before') !!}

<v-contact-component
    :prefill-organization-id="prefillOrganizationId || null"
    organization-fetch-url="{{ route('admin.contacts.organizations.fetch', ['id' => '__ID__']) }}"
    @organization-selected="handleOrganizationSelected"
></v-contact-component>

{!! view_render_event('admin.leads.create.contact_person.form_controls.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-contact-component-template"
    >
        <div class="flex flex-col gap-3">
            <div id="contacts-container" class="flex flex-col gap-3">
                <div v-for="(contact, index) in contacts" :key="index" class="contact-row flex gap-2 items-start">
                    <!-- Contact Name -->
                    <div class="flex-1 relative max-w-md">
                        <input
                            type="text"
                            v-model="contact.searchQuery"
                            @input="searchPersons(index)"
                            @focus="showSearchResults(index)"
                            placeholder="Search Contact Name"
                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                        />

                        <div
                            v-if="contact.showResults && contact.searchResults.length > 0"
                            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900"
                        >
                            <div
                                v-for="person in contact.searchResults"
                                :key="person.id"
                                @click="selectContact(index, person)"
                                class="cursor-pointer px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800"
                            >
                                <div class="text-sm font-medium dark:text-white">@{{ person.name }}</div>
                                <div v-if="person.organization" class="text-xs text-gray-500">@{{ person.organization.name }}</div>
                            </div>
                        </div>

                        <input
                            v-if="contact.id || contact.name"
                            type="hidden"
                            :name="index === 0 ? 'person[id]' : `person[${index}][id]`"
                            :value="contact.id"
                        />

                        <input
                            v-if="contact.id || contact.name"
                            type="hidden"
                            :name="index === 0 ? 'person[name]' : `person[${index}][name]`"
                            :value="contact.name"
                        />

                        <input
                            v-if="index === 0 && contact.organization_id && (contact.id || contact.name)"
                            type="hidden"
                            name="person[organization_id]"
                            :value="contact.organization_id"
                        />
                    </div>

                    <!-- Organization (auto-filled) -->
                    <div class="flex-1">
                        <input
                            type="text"
                            :value="contact.organization_name"
                            placeholder="Organization"
                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            disabled
                        />
                    </div>

                    <!-- Remove Button -->
                    <button
                        v-if="contacts.length > 1"
                        type="button"
                        @click="removeContact(index)"
                        class="secondary-button mt-1"
                    >
                        &times;
                    </button>
                </div>
            </div>

            <!-- Add New Contact Button -->
            <button
                type="button"
                @click="addContact"
                class="text-xs font-semibold text-brandColor hover:underline w-fit"
            >
                + Add Another Contact
            </button>
        </div>
    </script>

    <script type="module">
        app.component('v-contact-component', {
            template: '#v-contact-component-template',

            props: {
                prefillOrganizationId: { type: [String, Number], default: null },
                organizationFetchUrl: { type: String, default: '' }
            },

            data() {
                return {
                    contacts: [
                        {
                            id: null,
                            name: '',
                            searchQuery: '',
                            organization_name: '',
                            organization_id: null,
                            searchResults: [],
                            showResults: false
                        }
                    ],
                }
            },

            mounted() {
                // Close search results when clicking outside
                document.addEventListener('click', this.handleClickOutside);
            },

            beforeUnmount() {
                document.removeEventListener('click', this.handleClickOutside);
            },

            methods: {
                addContact() {
                    this.contacts.push({
                        id: null,
                        name: '',
                        searchQuery: '',
                        organization_name: '',
                        organization_id: null,
                        searchResults: [],
                        showResults: false
                    });
                },

                removeContact(index) {
                    this.contacts.splice(index, 1);
                },

                showSearchResults(index) {
                    this.contacts[index].showResults = true;
                },

                handleClickOutside(event) {
                    if (!event.target.closest('.contact-row')) {
                        this.contacts.forEach(contact => {
                            contact.showResults = false;
                        });
                    }
                },

                searchPersons(index) {
                    const query = this.contacts[index].searchQuery;

                    if (query.length < 2) {
                        this.contacts[index].searchResults = [];
                        return;
                    }

                    this.$axios.get("{{ route('admin.contacts.persons.search') }}", {
                            params: { query }
                        })
                        .then(response => {
                            const allPersons = response.data.data || response.data || [];

                            const lowerQuery = query.toLowerCase();

                            this.contacts[index].searchResults = allPersons
                                .filter(person =>
                                    person.name && person.name.toLowerCase().includes(lowerQuery)
                                )
                                .slice(0, 10);

                            this.contacts[index].showResults = true;
                        })
                        .catch(error => {
                            console.error('Failed to search persons:', error);
                        });
                },

                selectContact(index, person) {
                    this.contacts[index].id = person.id;
                    this.contacts[index].name = person.name;
                    this.contacts[index].searchQuery = person.name;
                    this.contacts[index].showResults = false;

                    // Auto-populate organization from person if they have one
                    if (person.organization) {
                        this.contacts[index].organization_name = person.organization.name;
                        this.contacts[index].organization_id = person.organization.id;
                    } else if (this.prefillOrganizationId && index === 0) {
                        this.fetchOrganization(this.prefillOrganizationId);
                        return;
                    }

                    this.$emit('organization-selected', {
                        name: this.contacts[index].organization_name,
                        id: this.contacts[index].organization_id,
                    });
                },

                fetchOrganization(organizationId) {
                    const url = this.organizationFetchUrl
                        ? this.organizationFetchUrl.replace('__ID__', organizationId)
                        : `/api/organizations/${organizationId}`;
                    this.$axios.get(url)
                        .then(response => {
                            const data = response.data.data || response.data;
                            if (this.contacts[0] && (data.name || data.id)) {
                                this.contacts[0].organization_name = data.name || '';
                                this.contacts[0].organization_id = data.id || null;

                                this.$emit('organization-selected', {
                                    name: this.contacts[0].organization_name,
                                    id: this.contacts[0].organization_id,
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Failed to fetch organization:', error);
                        });
                },
            }
        });
    </script>
@endPushOnce
