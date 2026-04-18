@props([
    'endpoint',
    'inputName',
    'multiple' => true,
    'placeholder' => 'Search...',
    'selectedItems' => [],
])

<v-activity-search-select
    endpoint="{{ $endpoint }}"
    input-name="{{ $inputName }}"
    :multiple='@json($multiple)'
    placeholder="{{ $placeholder }}"
    :selected-items='@json($selectedItems)'
></v-activity-search-select>

@pushOnce('scripts')
    <script type="text/x-template" id="v-activity-search-select-template">
        <div class="relative" ref="lookup">
            <div
                class="relative inline-block w-full"
                @click="toggle"
            >
                <div class="relative flex min-h-[42px] cursor-pointer items-center justify-between rounded border border-gray-200 bg-white px-3 py-2 hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                    <div class="flex min-w-0 flex-1 flex-wrap items-center gap-2">
                    <template v-for="(item, index) in items" :key="`${item.id}-${index}`">
                        <div class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                            <span>@{{ item.name }}</span>

                            <button
                                type="button"
                                class="icon-cross-large text-lg"
                                @click.stop="remove(item)"
                            ></button>

                            <input
                                v-if="multiple"
                                type="hidden"
                                :name="`${inputName}[${index}]`"
                                :value="item.id"
                            >
                        </div>
                    </template>

                    <input
                        v-if="!multiple && selectedItem"
                        type="hidden"
                        :name="inputName"
                        :value="selectedItem.id"
                    >

                        <span
                            v-if="!multiple && !selectedItem && !searchTerm"
                            class="text-sm text-gray-400 dark:text-gray-500"
                        >
                            @{{ placeholder }}
                        </span>

                        <input
                            v-model="searchTerm"
                            type="text"
                            class="min-w-[120px] flex-1 border-0 bg-transparent px-1 py-1 text-sm text-gray-900 outline-none placeholder:text-gray-400 dark:text-white dark:placeholder:text-gray-500"
                            :placeholder="multiple && items.length ? '' : ''"
                            @click.stop
                            @focus="isOpen = true"
                            @input="search"
                            ref="searchInput"
                        >
                    </div>

                    <div class="flex items-center gap-2">
                        <div
                            class="relative"
                            v-if="isSearching"
                        >
                            <x-admin::spinner />
                        </div>

                        <i
                            v-else
                            class="text-2xl text-gray-600"
                            :class="isOpen ? 'icon-up-arrow' : 'icon-down-arrow'"
                        ></i>
                    </div>
                </div>
            </div>

            <div
                v-if="isOpen"
                class="absolute z-[10050] mt-2 max-h-[156px] w-full overflow-y-auto rounded-xl border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-900"
            >
                <template v-if="isSearching">
                    <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                        Searching...
                    </div>
                </template>

                <template v-else-if="results.length">
                    <button
                        v-for="result in results"
                        :key="result.id"
                        type="button"
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800"
                        @click="select(result)"
                    >
                        <span>@{{ result.name }}</span>

                        <span v-if="result.email" class="truncate pl-3 text-xs text-gray-400">@{{ result.email }}</span>
                    </button>
                </template>

                <template v-else>
                    <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                        @{{ searchTerm.length < 2 ? 'Type at least 2 characters to search.' : 'No results found.' }}
                    </div>
                </template>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-activity-search-select', {
            template: '#v-activity-search-select-template',

            props: {
                endpoint: {
                    type: String,
                    required: true,
                },

                inputName: {
                    type: String,
                    required: true,
                },

                multiple: {
                    type: Boolean,
                    default: true,
                },

                placeholder: {
                    type: String,
                    default: 'Search...',
                },

                selectedItems: {
                    type: Array,
                    default: () => [],
                },
            },

            data() {
                return {
                    searchTerm: '',
                    results: [],
                    items: [],
                    isSearching: false,
                    isOpen: false,
                };
            },

            computed: {
                selectedItem() {
                    return this.items[0] ?? null;
                },
            },

            mounted() {
                this.items = Array.isArray(this.selectedItems) ? [...this.selectedItems] : [];

                this._outsideClickListener = (event) => {
                    if (! this.$el.contains(event.target)) {
                        this.isOpen = false;
                    }
                };

                window.addEventListener('click', this._outsideClickListener);
            },

            beforeUnmount() {
                window.removeEventListener('click', this._outsideClickListener);
            },

            methods: {
                toggle() {
                    this.isOpen = ! this.isOpen;

                    if (this.isOpen) {
                        this.search();

                        this.$nextTick(() => this.$refs.searchInput?.focus());
                    }
                },

                search() {
                    this.isSearching = true;
                    this.isOpen = true;

                    this.$axios.get(this.endpoint, {
                        params: {
                            query: this.searchTerm,
                        },
                    }).then((response) => {
                        const selectedIds = this.items.map(item => item.id);

                        this.results = (response.data.data || []).filter(item => ! selectedIds.includes(item.id));
                        this.isSearching = false;
                    }).catch(() => {
                        this.results = [];
                        this.isSearching = false;
                    });
                },

                select(item) {
                    if (this.multiple) {
                        this.items.push(item);
                    } else {
                        this.items = [item];
                    }

                    this.searchTerm = '';
                    this.results = [];
                    this.isOpen = false;
                },

                remove(item) {
                    this.items = this.items.filter(selected => selected.id !== item.id);
                },
            },
        });
    </script>
@endPushOnce
