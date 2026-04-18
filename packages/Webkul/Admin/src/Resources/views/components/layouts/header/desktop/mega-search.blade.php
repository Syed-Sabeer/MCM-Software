<v-mega-search>
    <div class="relative flex w-[550px] max-w-[550px] items-center max-lg:w-[400px] ltr:ml-2.5 rtl:mr-2.5">
        <i class="icon-search absolute top-2 flex items-center text-2xl ltr:left-3 rtl:right-3"></i>

        <input
            type="text"
            class="block w-full rounded-3xl border bg-white px-10 py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
            placeholder="@lang('admin::app.components.layouts.header.mega-search.title')"
        >
    </div>
</v-mega-search>

@pushOnce('scripts')
    <script type="text/x-template" id="v-mega-search-template">
        <div class="relative flex w-[620px] max-w-[620px] items-center max-lg:w-[420px] ltr:ml-2.5 rtl:mr-2.5">
            <i class="icon-search absolute top-2 flex items-center text-2xl ltr:left-3 rtl:right-3"></i>

            <input
                type="text"
                class="peer block w-full rounded-3xl border bg-white px-10 py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                :class="{'border-gray-400': isDropdownOpen}"
                placeholder="@lang('admin::app.components.layouts.header.mega-search.title')"
                v-model="searchTerm"
                @focus="openIfReady"
            >

            <div
                class="absolute top-10 z-10 w-full overflow-hidden rounded-2xl border bg-white shadow-[0px_10px_30px_rgba(15,23,42,0.12)] dark:border-gray-800 dark:bg-gray-900"
                v-if="isDropdownOpen"
            >
                <div class="flex overflow-x-auto border-b bg-gray-50/80 text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300">
                    <button
                        type="button"
                        class="flex items-center gap-2 whitespace-nowrap px-4 py-3 transition hover:bg-white dark:hover:bg-gray-900"
                        :class="{ 'border-b-2 border-brandColor bg-white text-gray-900 dark:bg-gray-900 dark:text-white': activeTab === tab.key }"
                        v-for="tab in tabs"
                        :key="tab.key"
                        @click="activeTab = tab.key"
                    >
                        <span>@{{ tab.title }}</span>
                        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-[11px] font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            @{{ resultCount(tab.key) }}
                        </span>
                    </button>
                </div>

                <div v-if="isLoading" class="grid gap-2 p-4">
                    <div v-for="index in 6" :key="index" class="h-14 animate-pulse rounded-xl bg-gray-100 dark:bg-gray-800"></div>
                </div>

                <template v-else>
                    <div v-if="activeTab === 'all'" class="max-h-[470px] overflow-y-auto">
                        <div v-if="allSections.length" class="divide-y divide-gray-100 dark:divide-gray-800">
                            <section v-for="section in allSections" :key="section.key" class="p-2">
                                <div class="px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">
                                    @{{ section.title }}
                                </div>

                                <a
                                    v-for="item in section.items"
                                    :key="`${section.key}-${item.id}`"
                                    :href="item.url"
                                    class="flex items-start justify-between gap-3 rounded-xl px-3 py-3 transition hover:bg-gray-50 dark:hover:bg-gray-950"
                                >
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="rounded-full bg-brandColor/10 px-2 py-0.5 text-[11px] font-semibold text-brandColor">
                                                @{{ item.badge }}
                                            </span>
                                            <p class="truncate text-sm font-semibold text-gray-800 dark:text-white">@{{ item.title }}</p>
                                        </div>

                                        <p v-if="item.subtitle" class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">@{{ item.subtitle }}</p>
                                        <p v-if="item.meta" class="mt-1 truncate text-xs text-gray-400 dark:text-gray-500">@{{ item.meta }}</p>
                                    </div>

                                    <i class="icon-arrow-right text-xl text-gray-300 dark:text-gray-600"></i>
                                </a>
                            </section>
                        </div>

                        <div v-else class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            No matching records found across the software.
                        </div>
                    </div>

                    <div v-else class="max-h-[470px] overflow-y-auto">
                        <template v-if="displayResults.length">
                            <a
                                v-for="item in displayResults"
                                :key="`${activeTab}-${item.id}`"
                                :href="item.url"
                                class="flex items-start justify-between gap-3 border-b border-gray-100 px-4 py-3 last:border-b-0 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950"
                            >
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-full bg-brandColor/10 px-2 py-0.5 text-[11px] font-semibold text-brandColor">
                                            @{{ item.badge }}
                                        </span>
                                        <p class="truncate text-sm font-semibold text-gray-800 dark:text-white">@{{ item.title }}</p>
                                    </div>

                                    <p v-if="item.subtitle" class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">@{{ item.subtitle }}</p>
                                    <p v-if="item.meta" class="mt-1 truncate text-xs text-gray-400 dark:text-gray-500">@{{ item.meta }}</p>
                                </div>

                                <i class="icon-arrow-right text-xl text-gray-300 dark:text-gray-600"></i>
                            </a>
                        </template>

                        <div v-else class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            No matching results in this section.
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t bg-gray-50/70 px-4 py-3 text-xs dark:border-gray-800 dark:bg-gray-950">
                        <span class="font-medium text-gray-500 dark:text-gray-400">
                            @{{ footerText }}
                        </span>

                        <a
                            v-if="footerUrl"
                            :href="footerUrl"
                            class="font-semibold text-brandColor transition hover:underline"
                        >
                            Open full section
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-mega-search', {
            template: '#v-mega-search-template',

            data() {
                return {
                    activeTab: 'all',
                    isDropdownOpen: false,
                    isLoading: false,
                    searchTerm: '',
                    searchTimeout: null,
                    tabs: [
                        { key: 'all', title: 'All' },
                        { key: 'organizations', title: 'Companies' },
                        { key: 'persons', title: 'Contacts' },
                        { key: 'leads', title: 'Cases' },
                        { key: 'products', title: 'Products' },
                        { key: 'quotes', title: 'Quotes' },
                        { key: 'proforma_invoices', title: 'Proformas' },
                        { key: 'purchase_orders', title: 'POs' },
                        { key: 'settings', title: 'Settings' },
                        { key: 'configurations', title: 'Configuration' },
                    ],
                    searchedResults: this.emptyResults(),
                    sectionTitles: {
                        organizations: 'Companies',
                        persons: 'Contacts',
                        leads: 'Cases',
                        products: 'Products',
                        quotes: 'Quotes',
                        proforma_invoices: 'Proformas',
                        purchase_orders: 'Purchase Orders',
                        settings: 'Settings',
                        configurations: 'Configuration',
                    },
                    exploreUrls: {
                        organizations: '{{ route('admin.contacts.organizations.index') }}',
                        persons: '{{ route('admin.contacts.persons.index') }}',
                        leads: '{{ route('admin.leads.index') }}',
                        products: '{{ route('admin.products.index') }}',
                        quotes: '{{ route('admin.quotes.index') }}',
                        proforma_invoices: '{{ route('admin.proforma_invoices.index') }}',
                        purchase_orders: '{{ route('admin.purchase_orders.index') }}',
                        settings: '{{ route('admin.settings.index') }}',
                        configurations: '{{ route('admin.configuration.index') }}',
                    },
                };
            },

            computed: {
                displayResults() {
                    return this.searchedResults[this.activeTab] || [];
                },

                allSections() {
                    return this.tabs
                        .filter(tab => tab.key !== 'all')
                        .map(tab => ({
                            key: tab.key,
                            title: tab.title,
                            items: this.searchedResults[tab.key] || [],
                        }))
                        .filter(section => section.items.length);
                },

                footerText() {
                    if (this.searchTerm.length < 2) {
                        return 'Type at least 2 characters to search.';
                    }

                    if (this.activeTab === 'all') {
                        return `${this.resultCount('all')} results across the software`;
                    }

                    return `${this.resultCount(this.activeTab)} result(s) in ${this.sectionTitles[this.activeTab] || 'this section'}`;
                },

                footerUrl() {
                    if (this.activeTab === 'all') {
                        return '';
                    }

                    const baseUrl = this.exploreUrls[this.activeTab] || '';

                    if (!baseUrl) {
                        return '';
                    }

                    return `${baseUrl}${baseUrl.includes('?') ? '&' : '?'}search=${encodeURIComponent(this.searchTerm)}`;
                },
            },

            watch: {
                searchTerm(newValue) {
                    clearTimeout(this.searchTimeout);

                    if (newValue.trim().length < 2) {
                        this.searchedResults = this.emptyResults();
                        this.isDropdownOpen = false;
                        this.isLoading = false;

                        return;
                    }

                    this.searchTimeout = setTimeout(() => {
                        this.performSearch();
                    }, 300);
                },
            },

            created() {
                window.addEventListener('click', this.handleFocusOut);
            },

            beforeUnmount() {
                window.removeEventListener('click', this.handleFocusOut);
                clearTimeout(this.searchTimeout);
            },

            methods: {
                emptyResults() {
                    return {
                        all: [],
                        organizations: [],
                        persons: [],
                        leads: [],
                        products: [],
                        quotes: [],
                        proforma_invoices: [],
                        purchase_orders: [],
                        settings: [],
                        configurations: [],
                    };
                },

                resultCount(key) {
                    return (this.searchedResults[key] || []).length;
                },

                openIfReady() {
                    if (this.searchTerm.trim().length >= 2) {
                        this.isDropdownOpen = true;
                    }
                },

                performSearch() {
                    this.isLoading = true;
                    this.isDropdownOpen = true;

                    this.$axios.get('{{ route('admin.search.global') }}', {
                        params: {
                            query: this.searchTerm.trim(),
                        },
                    }).then((response) => {
                        this.searchedResults = {
                            ...this.emptyResults(),
                            ...(response.data.data || {}),
                        };
                    }).catch(() => {
                        this.searchedResults = this.emptyResults();
                    }).finally(() => {
                        this.isLoading = false;
                    });
                },

                handleFocusOut(event) {
                    if (!this.$el.contains(event.target)) {
                        this.isDropdownOpen = false;
                    }
                },
            },
        });
    </script>
@endPushOnce
