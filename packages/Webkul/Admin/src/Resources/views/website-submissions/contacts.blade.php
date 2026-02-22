<x-admin::layouts>
    <!-- Title of the page. -->
    <x-slot:title>
        @lang('admin::app.website-submissions.contacts')
    </x-slot>

    <!-- Breadcrumbs -->
    <x-admin::breadcrumbs name="website-submissions.contacts" />

    <!-- Heading of the page -->
    <div class="mb-7 flex flex-wrap items-center justify-between">
        <p class="py-3 text-xl font-bold text-gray-800 dark:text-white">
            @lang('admin::app.website-submissions.contacts')
        </p>
    </div>

    <!-- Vue Component for Contacts List -->
    <v-website-contacts></v-website-contacts>

    @pushOnce('scripts')
    <script type="text/x-template" id="v-website-contacts-template">
        <div class="flex flex-col gap-4">
            <!-- Loading State -->
            <template v-if="isLoading">
                <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <div class="shimmer h-64 w-full rounded"></div>
                </div>
            </template>

            <!-- Contacts Table -->
            <template v-else>
                <div class="overflow-x-auto box-shadow rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <table class="w-full min-w-[600px] table-auto border-collapse text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-800 dark:text-white">Name</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-800 dark:text-white">Email</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-800 dark:text-white">Phone</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-800 dark:text-white">Service</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-800 dark:text-white">Message</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-800 dark:text-white">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="contact in contacts" :key="contact.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-300">@{{ contact.name }}</td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-300">
                                    <a :href="'mailto:' + contact.email" class="text-blue-600 hover:underline dark:text-blue-400">@{{ contact.email }}</a>
                                </td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-300">
                                    <a :href="'tel:' + contact.phone" class="text-blue-600 hover:underline dark:text-blue-400">@{{ contact.phone }}</a>
                                </td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-300">@{{ contact.service }}</td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-300 truncate max-w-xs">@{{ contact.message }}</td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-300">@{{ formatDate(contact.created_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="pagination" class="flex items-center justify-between py-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Showing @{{ pagination.from }} to @{{ pagination.to }} of @{{ pagination.total }} results
                    </p>
                    <div class="flex gap-2">
                        <button
                            v-for="link in pagination.links"
                            :key="link.url"
                            @click="link.url && goToPage(new URL(link.url).searchParams.get('page'))"
                            :disabled="!link.url"
                            :class="[
                                'px-3 py-1 rounded border transition-all',
                                link.active
                                    ? 'bg-blue-600 text-white border-blue-600'
                                    : 'bg-white text-gray-800 border-gray-200 hover:border-gray-400 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700'
                            ]"
                        >
                            @{{ link.label.replace('&laquo; ', '').replace(' &raquo;', '') }}
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!isLoading && contacts.length === 0" class="text-center py-12 text-gray-500 dark:text-gray-400">
                    No contacts found.
                </div>

                <!-- Error State -->
                <div v-if="error" class="rounded border border-red-200 bg-red-50 p-4 text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
                    @{{ error }}
                </div>
            </template>
        </div>
    </script>

    <script type="module">
        app.component('v-website-contacts', {
            template: '#v-website-contacts-template',

            data() {
                return {
                    contacts: [],
                    pagination: null,
                    isLoading: true,
                    error: null,
                    currentPage: 1,
                };
            },

            created() {
                this.fetchContacts(1);
            },

            methods: {
                fetchContacts(page) {
                    this.isLoading = true;
                    this.error = null;

                    this.$axios.get('{{ route("admin.website_submissions.api.contacts") }}', {
                        params: { page, per_page: 15 }
                    })
                    .then((response) => {
                        if (response.data.success) {
                            this.contacts = response.data.data.data || [];
                            this.pagination = response.data.data;
                            this.currentPage = page;
                        } else {
                            this.error = response.data.message || 'Failed to load contacts';
                        }
                    })
                    .catch((error) => {
                        this.error = error.message || 'Error loading contacts';
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
                },

                goToPage(page) {
                    if (page && page !== this.currentPage) {
                        this.fetchContacts(page);
                    }
                },

                formatDate(dateString) {
                    return new Date(dateString).toLocaleDateString();
                },
            },
        });
    </script>
    @endPushOnce
</x-admin::layouts>
