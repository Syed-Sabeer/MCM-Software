@php
    use Webkul\Product\Models\Product;

    $quoteModels = \Webkul\Quote\Models\Quote::query()
        ->with(['items', 'organization', 'person', 'user'])
        ->get();

    $productIds = $quoteModels->pluck('items')->flatten()->pluck('product_id')->filter()->map(fn ($id) => (int) $id)->unique()->values();

    $productPayload = Product::query()
        ->with(['colors', 'otherImages'])
        ->whereIn('id', $productIds)
        ->get()
        ->mapWithKeys(function ($product) {
            $coverImageUrl = $product->cover_image ? secure_url('public/storage/'.$product->cover_image) : null;
            $colorImages = [];

            foreach ($product->otherImages as $image) {
                if (! $image->color_id || empty($image->path)) {
                    continue;
                }

                $colorImages[(string) $image->color_id] = secure_url('public/storage/'.$image->path);
            }

            return [
                $product->id => [
                    'cover_image_url' => $coverImageUrl,
                    'colors'          => $product->colors->map(fn ($color) => [
                        'id'         => $color->id,
                        'name'       => $color->name,
                        'color_code' => $color->color_code,
                    ])->values()->toArray(),
                    'color_images'    => $colorImages,
                ],
            ];
        });

    $quotes = $quoteModels->map(function ($quoteRow) use ($productPayload) {
        return [
            'id' => $quoteRow->id,
            'quote_number' => $quoteRow->quote_number,
            'quote_number_display' => 'Q' . ltrim((string) $quoteRow->quote_number, 'Q'),
            'organization_id' => $quoteRow->organization_id,
            'organization_name' => optional($quoteRow->organization)->name,
            'person_id' => $quoteRow->person_id,
            'sales_owner_id' => $quoteRow->user_id,
            'sales_owner_name' => optional($quoteRow->user)->name,
            'adjustment_amount' => (float) ($quoteRow->adjustment_amount ?? 0),
            'notes' => $quoteRow->notes,
            'terms' => $quoteRow->terms,
            'billing_address' => $quoteRow->billing_address ?: ['address' => ''],
            'shipping_address' => $quoteRow->shipping_address ?: ['address' => ''],
            'items' => $quoteRow->items->map(function ($item) use ($productPayload) {
                $productMeta = $productPayload->get((int) ($item->product_id ?? 0), [
                    'cover_image_url' => null,
                    'colors'          => [],
                    'color_images'    => [],
                ]);

                $selectedColorId = (string) ($item->color_variant_id ?? '');
                $previewImage = $item->preview_image;

                if (! $previewImage && $selectedColorId && isset($productMeta['color_images'][$selectedColorId])) {
                    $previewImage = $productMeta['color_images'][$selectedColorId];
                }

                if (! $previewImage) {
                    $previewImage = $productMeta['cover_image_url'] ?? null;
                }

                return [
                    'id'                  => null,
                    'product_id'          => $item->product_id,
                    'name'                => $item->item_name ?: $item->name,
                    'item_name'           => $item->item_name ?: $item->name,
                    'item_code'           => $item->item_code ?: $item->sku,
                    'quantity'            => (int) round((float) ($item->qty ?: $item->quantity ?: 1)),
                    'price'               => number_format((float) ($item->unit_price ?: $item->price ?: 0), 2, '.', ''),
                    'discount_amount'     => number_format((float) ($item->discount_amount ?: 0), 2, '.', ''),
                    'tax_amount'          => number_format((float) ($item->tax_amount ?: 0), 2, '.', ''),
                    'available_colors'    => $productMeta['colors'] ?? [],
                    'color_images'        => $productMeta['color_images'] ?? [],
                    'selected_color_id'   => $selectedColorId,
                    'selected_color_name' => $item->color_variant_name ?? '',
                    'cover_image_url'     => $productMeta['cover_image_url'] ?? '',
                    'preview_image'       => $previewImage,
                ];
            })->values()->all(),
        ];
    })->values();

    $selectedQuote = isset($quote)
        ? $quotes->firstWhere('id', $quote->id)
        : $quotes->firstWhere('id', (int) request('quote_id'));

    $initialForm = [
        'proforma_number' => old('proforma_number', $nextProformaNumber ?? \Webkul\Quote\Models\ProformaInvoice::generateNextProformaNumber()),
        'quote_id' => $selectedQuote['id'] ?? request('quote_id'),
        'quote_number_display' => $selectedQuote['quote_number_display'] ?? '',
        'organization_id' => $selectedQuote['organization_id'] ?? null,
        'organization_name' => $selectedQuote['organization_name'] ?? '',
        'person_id' => $selectedQuote['person_id'] ?? null,
        'sales_owner_id' => $selectedQuote['sales_owner_id'] ?? auth()->id(),
        'sales_owner_name' => $selectedQuote['sales_owner_name'] ?? optional(auth()->user())->name,
        'issue_date' => now()->toDateString(),
        'status' => 'draft',
        'adjustment_amount' => $selectedQuote['adjustment_amount'] ?? 0,
        'notes' => $selectedQuote['notes'] ?? '',
        'terms' => $selectedQuote['terms'] ?? '',
        'payment_term' => '',
        'billing_address' => $selectedQuote['billing_address'] ?? ['address' => ''],
        'shipping_address' => $selectedQuote['shipping_address'] ?? ['address' => ''],
        'items' => $selectedQuote['items'] ?? [],
    ];
@endphp

<x-admin::layouts>
    

    @include('admin::components.documents.form-styles')

    <x-admin::form :action="route('admin.proforma_invoices.store')" method="POST">
        <div class="flex flex-col gap-4">
            <div class="document-form-toolbar flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="text-xl font-bold dark:text-white">Create Proforma Invoice</div>
                <button type="submit" class="primary-button">Save Proforma</button>
            </div>

            <v-proforma :initial-form='@json($initialForm)' :quotes='@json($quotes)' :errors="errors">
                <x-admin::shimmer.quotes />
            </v-proforma>
        </div>
    </x-admin::form>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-proforma-template">
            <div class="document-form-panel box-shadow flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <input type="hidden" name="organization_id" :value="form.organization_id || ''">
                <input type="hidden" name="person_id" :value="form.person_id || ''">
                <input type="hidden" name="sales_owner_id" :value="form.sales_owner_id || ''">
                <input type="hidden" name="adjustment_amount" :value="form.adjustment_amount || 0">
                <input type="hidden" name="billing_address[address]" :value="form.billing_address?.address || ''">
                <input type="hidden" name="shipping_address[address]" :value="form.shipping_address?.address || ''">

                <div class="grid gap-4 md:grid-cols-2">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Proforma #</x-admin::form.control-group.label>
                        <input type="text" name="proforma_number" v-model="form.proforma_number" class="custom-input">
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">Quote #</x-admin::form.control-group.label>
                        <select name="quote_id" v-model="form.quote_id" class="custom-select" required @change="applyQuoteDetails">
                            <option value="">Select Quote</option>
                            <option v-for="quote in quotes" :key="quote.id" :value="quote.id">@{{ quote.quote_number_display }}</option>
                        </select>
                    </x-admin::form.control-group>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Customer</x-admin::form.control-group.label>
                        <input type="text" class="custom-input" :value="form.organization_name || ''" disabled>
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Sales Owner</x-admin::form.control-group.label>
                        <input type="text" class="custom-input" :value="form.sales_owner_name || ''" disabled>
                    </x-admin::form.control-group>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">Issue Date</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="date" name="issue_date" v-model="form.issue_date" rules="required" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Status</x-admin::form.control-group.label>
                        <select name="status" v-model="form.status" class="custom-select">
                            <option value="draft">draft</option>
                            <option value="issued">issued</option>
                            <option value="partially_paid">partially_paid</option>
                            <option value="fully_paid">fully_paid</option>
                            <option value="cancelled">cancelled</option>
                            <option value="ready_for_job_order">ready_for_job_order</option>
                            <option value="converted">converted</option>
                        </select>
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Payment Term</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="text" name="payment_term" v-model="form.payment_term" />
                    </x-admin::form.control-group>
                </div>

                <div class="document-form-items mt-2 flex flex-col gap-4">
                    <div class="flex flex-col gap-1">
                        <p class="text-base font-semibold text-gray-800 dark:text-white">Proforma Invoice Items</p>
                        <p class="text-sm text-gray-600 dark:text-white">Add Product Request for this proforma invoice.</p>
                    </div>

                    <v-proforma-item-list :errors="errors" :organization-id="form.organization_id" :initial-products="form.items"></v-proforma-item-list>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Notes</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="textarea" name="notes" v-model="form.notes" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Terms</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="textarea" name="terms" v-model="form.terms" />
                    </x-admin::form.control-group>
                </div>
            </div>
        </script>

        <script type="text/x-template" id="v-proforma-item-list-template">
            <div class="flex flex-col gap-4">
                <div class="block w-full overflow-visible">
                    <x-admin::table>
                        <x-admin::table.thead>
                            <x-admin::table.thead.tr>
                                <x-admin::table.th>Product Name</x-admin::table.th>
                                <x-admin::table.th class="text-center">Image</x-admin::table.th>
                                <x-admin::table.th class="text-center">Color Variant</x-admin::table.th>
                                <x-admin::table.th class="text-center">Quantity</x-admin::table.th>
                                <x-admin::table.th class="text-center">Price</x-admin::table.th>
                                <x-admin::table.th class="text-center">Amount</x-admin::table.th>
                                <x-admin::table.th class="text-center">Discount</x-admin::table.th>
                                <x-admin::table.th class="text-center">Tax</x-admin::table.th>
                                <x-admin::table.th class="text-center">Total</x-admin::table.th>
                                <x-admin::table.th class="text-center">Action</x-admin::table.th>
                            </x-admin::table.thead.tr>
                        </x-admin::table.thead>
                        <x-admin::table.tbody>
                            <template v-for="(product, index) in products" :key="index">
                                <v-proforma-item :product="product" :index="index" :organization-id="organizationId" @onRemoveProduct="removeProduct($event)"></v-proforma-item>
                            </template>
                        </x-admin::table.tbody>
                    </x-admin::table>
                </div>

                <span class="text-md flex max-w-max cursor-pointer items-center gap-2 text-brandColor" @click="addProduct">+ Add Item</span>

                <div class="flex justify-end">
                    <div class="document-form-summary-box grid w-[348px] gap-4 rounded-lg bg-gray-100 p-4 text-sm dark:bg-gray-950 dark:text-white">
                        <div class="flex w-full justify-between gap-x-5"><span>Sub Total</span><p>@{{ formatPrice(subTotal) }}</p></div>
                        <div class="flex w-full justify-between gap-x-5"><span>Total Discount</span><p>@{{ formatPrice(discountAmount) }}</p></div>
                        <div class="flex w-full justify-between gap-x-5"><span>Total Tax</span><p>@{{ formatPrice(taxAmount) }}</p></div>
                        <div class="flex w-full justify-between gap-x-5"><span>Grand Total</span><p>@{{ formatPrice(grandTotal) }}</p></div>
                    </div>
                </div>
            </div>
        </script>

        <script type="text/x-template" id="v-proforma-item-template">
            <x-admin::table.thead.tr>
                <x-admin::table.td class="!px-2 align-top overflow-visible">
                    <div class="relative min-w-[380px]">
                        <v-proforma-product-lookup :src="src" :organization-id="organizationId" :selected-product="product" @on-selected="(product) => addProduct(product)"></v-proforma-product-lookup>
                        <input type="hidden" :name="`${inputName}[product_id]`" :value="product.product_id || ''">
                    </div>
                </x-admin::table.td>
                <x-admin::table.td class="!px-2 text-center">
                    <input type="hidden" :name="`${inputName}[preview_image]`" :value="product.preview_image || ''">
                    <img v-if="product.preview_image" :key="product.preview_image" :src="product.preview_image" class="mx-auto h-12 w-12 rounded object-cover border border-gray-200" alt="preview">
                    <span v-else class="text-xs text-gray-500">No image</span>
                </x-admin::table.td>
                <x-admin::table.td class="!px-2">
                    <select class="w-full rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-700 dark:bg-gray-800" v-model="product.selected_color_id" @change="onColorChange($event.target.value)">
                        <option value="">No Color</option>
                        <option v-for="color in (product.available_colors || [])" :key="color.id" :value="String(color.id)">@{{ color.name }}</option>
                    </select>
                    <input type="hidden" :name="`${inputName}[color_variant_id]`" :value="product.selected_color_id || ''">
                    <input type="hidden" :name="`${inputName}[color_variant_name]`" :value="product.selected_color_name || ''">
                </x-admin::table.td>
                <input type="hidden" :name="`${inputName}[item_name]`" :value="product.name || ''">
                <input type="hidden" :name="`${inputName}[item_code]`" :value="product.item_code || ''">
                <input type="hidden" :name="`${inputName}[unit]`" value="">
                <x-admin::table.td class="!px-2 text-center"><input type="number" min="1" step="1" :name="`${inputName}[qty]`" v-model.number="product.quantity" class="custom-input text-center"></x-admin::table.td>
                <x-admin::table.td class="!px-2 text-center"><input type="number" min="0" step="0.01" :name="`${inputName}[unit_price]`" v-model="product.price" class="custom-input text-center"></x-admin::table.td>
                <x-admin::table.td class="!px-2 text-center">@{{ formatPrice(amount) }}</x-admin::table.td>
                <x-admin::table.td class="!px-2 text-center"><input type="number" min="0" step="0.01" :name="`${inputName}[discount_amount]`" v-model="product.discount_amount" class="custom-input text-center"></x-admin::table.td>
                <x-admin::table.td class="!px-2 text-center"><input type="number" min="0" step="0.01" :name="`${inputName}[tax_amount]`" v-model="product.tax_amount" class="custom-input text-center"></x-admin::table.td>
                <x-admin::table.td class="!px-2 text-center">@{{ formatPrice(total) }}</x-admin::table.td>
                <x-admin::table.td class="!px-2 text-center"><i @click="removeProduct" class="icon-delete cursor-pointer text-2xl"></i></x-admin::table.td>
            </x-admin::table.thead.tr>
        </script>

        <script type="module">
            app.component('v-proforma', {
                template: '#v-proforma-template',
                props: ['initialForm', 'quotes', 'errors'],
                data() { return { form: this.initialForm }; },
                methods: {
                    applyQuoteDetails() {
                        const selected = this.quotes.find(q => String(q.id) === String(this.form.quote_id));
                        if (! selected) return;
                        this.form.quote_number_display = selected.quote_number_display || '';
                        this.form.organization_id = selected.organization_id || '';
                        this.form.organization_name = selected.organization_name || '';
                        this.form.person_id = selected.person_id || '';
                        this.form.sales_owner_id = selected.sales_owner_id || '';
                        this.form.sales_owner_name = selected.sales_owner_name || '';
                        this.form.adjustment_amount = selected.adjustment_amount || 0;
                        this.form.notes = selected.notes || '';
                        this.form.terms = selected.terms || '';
                        this.form.billing_address = selected.billing_address || { address: '' };
                        this.form.shipping_address = selected.shipping_address || { address: '' };
                        this.form.items = (selected.items || []).map(item => ({ ...item }));
                    },
                },
            });

            app.component('v-proforma-item-list', {
                template: '#v-proforma-item-list-template',
                props: ['errors', 'organizationId', 'initialProducts'],
                data() {
                    return {
                        products: this.initialProducts?.length ? this.initialProducts : [{ id: null, product_id: null, name: '', item_code: '', quantity: 1, price: '0.00', discount_amount: '0.00', tax_amount: '0.00', available_colors: [], color_images: {}, selected_color_id: '', selected_color_name: '', preview_image: '', cover_image_url: '' }],
                    }
                },
                watch: {
                    initialProducts: {
                        handler(value) {
                            this.products = value?.length ? value.map(item => ({ ...item })) : [{ id: null, product_id: null, name: '', item_code: '', quantity: 1, price: '0.00', discount_amount: '0.00', tax_amount: '0.00', available_colors: [], color_images: {}, selected_color_id: '', selected_color_name: '', preview_image: '', cover_image_url: '' }];
                        },
                        deep: true,
                    }
                },
                computed: {
                    subTotal() { return this.products.reduce((t,p) => t + ((parseInt(p.quantity) || 0) * (parseFloat(p.price) || 0)), 0); },
                    discountAmount() { return this.products.reduce((t,p) => t + (parseFloat(p.discount_amount) || 0), 0); },
                    taxAmount() { return this.products.reduce((t,p) => t + (parseFloat(p.tax_amount) || 0), 0); },
                    grandTotal() { return this.subTotal + this.taxAmount - this.discountAmount; },
                },
                methods: {
                    addProduct() { this.products.push({ id: null, product_id: null, name: '', item_code: '', quantity: 1, price: '0.00', discount_amount: '0.00', tax_amount: '0.00', available_colors: [], color_images: {}, selected_color_id: '', selected_color_name: '', preview_image: '', cover_image_url: '' }); },
                    removeProduct(product) {
                        if (this.products.length === 1) {
                            this.products = [{ id: null, product_id: null, name: '', item_code: '', quantity: 1, price: '0.00', discount_amount: '0.00', tax_amount: '0.00', available_colors: [], color_images: {}, selected_color_id: '', selected_color_name: '', preview_image: '', cover_image_url: '' }];
                            return;
                        }
                        const index = this.products.indexOf(product);
                        if (index !== -1) this.products.splice(index, 1);
                    },
                    formatPrice(value) { return this.$admin.formatPrice(value || 0); },
                },
            });

            app.component('v-proforma-product-lookup', {
                props: ['src', 'organizationId', 'selectedProduct'],
                emits: ['on-selected'],
                data() { return { showPopup: false, searchTerm: '', results: [], isSearching: false, cancelToken: null }; },
                computed: {
                    selectedLabel() {
                        const itemCode = this.selectedProduct?.item_code || '';
                        return itemCode || '';
                    },
                },
                methods: {
                    toggle() { this.showPopup = ! this.showPopup; if (this.showPopup) this.$nextTick(() => this.$refs.searchInput.focus()); },
                    clearSelection() { this.searchTerm=''; this.results=[]; this.$emit('on-selected', { id: null, name: '', sku: '', selling_price: '0.00', colors: [], color_images: {}, cover_image_url: '' }); },
                    search() {
                        if (! this.organizationId || this.searchTerm.trim().length < 1) { this.results = []; return; }
                        if (this.cancelToken) this.cancelToken.cancel('canceled');
                        this.cancelToken = this.$axios.CancelToken.source();
                        this.isSearching = true;
                        this.$axios.get(this.src, { params: { organization_id: this.organizationId, query: this.searchTerm.trim() }, cancelToken: this.cancelToken.token }).then((response) => {
                            const items = response?.data?.data || response?.data || [];
                            this.results = Array.isArray(items) ? items : [];
                        }).catch((error) => { if (! this.$axios.isCancel(error)) this.results = []; }).finally(() => { this.isSearching = false; });
                    },
                    selectProduct(product) { this.showPopup = false; this.searchTerm = ''; this.results = []; this.$emit('on-selected', product); },
                    handleFocusOut(event) { if (this.$refs.lookup && ! this.$refs.lookup.contains(event.target)) this.showPopup = false; },
                    resultLabel(product) { return product?.sku || product?.internal_code || ''; },
                },
                watch: { organizationId() { this.showPopup=false; this.searchTerm=''; this.results=[]; }, searchTerm() { this.search(); } },
                mounted() { window.addEventListener('click', this.handleFocusOut); },
                beforeUnmount() { window.removeEventListener('click', this.handleFocusOut); },
                template: `
                    <div class="relative" ref="lookup">
                        <div class="relative inline-block w-full" @click="toggle">
                            <div class="relative flex min-h-[42px] cursor-pointer items-center justify-between rounded border border-gray-200 p-2 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:text-gray-300">
                                <span class="block max-w-[300px] overflow-hidden text-ellipsis whitespace-nowrap" :title="selectedLabel">@{{ selectedLabel !== '' ? selectedLabel : 'Search by Product Item Code' }}</span>
                                <div class="flex items-center gap-2"><i v-if="selectedLabel" class="icon-cross-large cursor-pointer text-xl text-gray-600" @click.stop="clearSelection"></i><i class="text-2xl text-gray-600" :class="showPopup ? 'icon-up-arrow' : 'icon-down-arrow'"></i></div>
                            </div>
                        </div>
                        <div v-if="showPopup" class="absolute top-full z-50 mt-1 flex w-full min-w-[380px] origin-top transform flex-col gap-2 rounded-lg border border-gray-200 bg-white p-2 shadow-lg transition-transform dark:border-gray-900 dark:bg-gray-800">
                            <div class="relative flex items-center"><input type="text" v-model="searchTerm" class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300" placeholder="Search by Product Item Code" ref="searchInput" @click.stop /></div>
                            <ul class="max-h-56 divide-y divide-gray-100 overflow-y-auto rounded">
                                <li v-for="item in results" :key="item.id" class="cursor-pointer px-4 py-2 text-gray-800 transition-colors hover:bg-blue-100 dark:text-white dark:hover:bg-gray-900" @click="selectProduct(item)"><div class="font-medium">@{{ resultLabel(item) }}</div><div v-if="item.name" class="text-xs text-gray-500">@{{ item.name }}</div></li>
                                <li v-if="isSearching" class="px-4 py-2 text-gray-500">Searching...</li>
                                <li v-else-if="searchTerm.trim().length > 0 && ! results.length" class="px-4 py-2 text-gray-500">No results</li>
                                <li v-else class="px-4 py-2 text-gray-500">Type product item code to search</li>
                            </ul>
                        </div>
                    </div>`
            });

            app.component('v-proforma-item', {
                template: '#v-proforma-item-template',
                props: ['index', 'product', 'organizationId'],
                computed: {
                    inputName() { return `items[${this.index}]`; },
                    src() { return "{{ route('admin.products.search') }}"; },
                    amount() { return (parseInt(this.product.quantity) || 0) * (parseFloat(this.product.price) || 0); },
                    total() { return this.amount + (parseFloat(this.product.tax_amount) || 0) - (parseFloat(this.product.discount_amount) || 0); },
                },
                methods: {
                    normalizeColorImages(images) { if (! images || typeof images !== 'object') return {}; return Object.keys(images).reduce((c,k)=>{c[String(k)] = images[k]; return c;}, {}); },
                    resolvePreviewImage(colorId) { const normalizedColorId = colorId ? String(colorId) : ''; const imageMap = this.normalizeColorImages(this.product.color_images || {}); if (normalizedColorId && imageMap[normalizedColorId]) return imageMap[normalizedColorId]; return this.product.cover_image_url || ''; },
                    addProduct(result) {
                        this.product.product_id = result.id ?? null;
                        this.product.name = result.name ?? '';
                        this.product.item_code = result.sku ?? result.internal_code ?? '';
                        this.product.price = Number(result.selling_price ?? result.price ?? 0).toFixed(2);
                        this.product.quantity = 1;
                        this.product.discount_amount = '0.00';
                        this.product.tax_amount = '0.00';
                        this.product.available_colors = Array.isArray(result.colors) ? result.colors : [];
                        this.product.color_images = this.normalizeColorImages(result.color_images || {});
                        this.product.cover_image_url = result.cover_image_url || '';
                        this.product.selected_color_id = '';
                        this.product.selected_color_name = '';
                        this.product.preview_image = result.cover_image_url || '';
                    },
                    onColorChange(selectedValue) {
                        this.product.selected_color_id = selectedValue ? String(selectedValue) : '';
                        const selected = (this.product.available_colors || []).find(color => String(color.id) === String(this.product.selected_color_id));
                        this.product.selected_color_name = selected ? selected.name : '';
                        this.product.preview_image = this.resolvePreviewImage(this.product.selected_color_id);
                    },
                    removeProduct() { this.$emit('onRemoveProduct', this.product); },
                    formatPrice(value) { return this.$admin.formatPrice(value || 0); },
                },
            });
        </script>
    @endPushOnce

    @pushOnce('styles')
        <style>
            .custom-input { width: 100%; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.45rem 0.6rem; font-size: 0.875rem; background: transparent; }
            .custom-select { width: 100%; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.45rem 0.6rem; font-size: 0.875rem; background: transparent; }
        </style>
    @endPushOnce
</x-admin::layouts>



