@php
    use Webkul\Product\Models\Product;

    $customerOrganizations = app(\Webkul\Contact\Repositories\OrganizationRepository::class)
        ->whereIn('type', ['customer', 'Customer'])
        ->orderBy('name')
        ->get(['id', 'name'])
        ->values();

    $oldItems = old('items');

    $rawItems = $oldItems
        ? collect($oldItems)->map(function ($item, $key) {
            return array_merge($item, [
                'id' => is_numeric($key) ? (int) $key : null,
            ]);
        })->values()
        : $quote->items->map(function ($item) {
            return [
                'id'                 => $item->id,
                'product_id'         => $item->product_id,
                'name'               => $item->name ?: $item->item_name,
                'item_name'          => $item->item_name ?: $item->name,
                'item_code'          => $item->item_code ?: $item->sku,
                'quantity'           => $item->quantity ?: $item->qty,
                'price'              => $item->price ?: $item->unit_price,
                'discount_amount'    => $item->discount_amount,
                'tax_amount'         => $item->tax_amount,
                'preview_image'      => $item->preview_image,
                'color_variant_id'   => $item->color_variant_id,
                'color_variant_name' => $item->color_variant_name,
            ];
        })->values();

    $productIds = $rawItems->pluck('product_id')
        ->filter()
        ->map(fn ($id) => (int) $id)
        ->unique()
        ->values();

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

    $quoteItems = $rawItems->map(function ($item) use ($productPayload) {
        $productMeta = $productPayload->get((int) ($item['product_id'] ?? 0), [
            'cover_image_url' => null,
            'colors'          => [],
            'color_images'    => [],
        ]);

        $selectedColorId = (string) ($item['color_variant_id'] ?? '');
        $previewImage = $item['preview_image'] ?? null;

        if (! $previewImage && $selectedColorId && isset($productMeta['color_images'][$selectedColorId])) {
            $previewImage = $productMeta['color_images'][$selectedColorId];
        }

        if (! $previewImage) {
            $previewImage = $productMeta['cover_image_url'] ?? null;
        }

        return [
            'id'                  => $item['id'] ?? null,
            'product_id'          => $item['product_id'] ?? null,
            'name'                => $item['name'] ?? ($item['item_name'] ?? ''),
            'item_name'           => $item['item_name'] ?? ($item['name'] ?? ''),
            'item_code'           => $item['item_code'] ?? '',
            'quantity'            => (float) ($item['quantity'] ?? $item['qty'] ?? 1),
            'price'               => (float) ($item['price'] ?? $item['unit_price'] ?? 0),
            'discount_amount'     => (float) ($item['discount_amount'] ?? 0),
            'tax_amount'          => (float) ($item['tax_amount'] ?? 0),
            'available_colors'    => $productMeta['colors'] ?? [],
            'color_images'        => $productMeta['color_images'] ?? [],
            'selected_color_id'   => $selectedColorId,
            'selected_color_name' => $item['color_variant_name'] ?? '',
            'cover_image_url'     => $productMeta['cover_image_url'] ?? '',
            'preview_image'       => $previewImage,
        ];
    })->values();

    $selectedCustomerId = old('organization_id', $quote->organization_id);
@endphp

<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.quotes.edit.title')
    </x-slot>

    @include('admin::components.documents.form-styles')

    <x-admin::form
        :action="route('admin.quotes.update', $quote->id).'?'.http_build_query(array_merge(
            request()->route()->parameters(),
            request()->all()
        ))"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="document-form-toolbar flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs name="quotes.edit" :entity="$quote" />

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.quotes.edit.title')
                    </div>
                </div>

                <button type="submit" class="primary-button">
                    @lang('admin::app.quotes.edit.save-btn')
                </button>
            </div>

            <v-quote
                :errors="errors"
                :customers='@json($customerOrganizations)'
                :initial-products='@json($quoteItems)'
                initial-customer-id="{{ $selectedCustomerId }}"
            >
                <x-admin::shimmer.quotes />
            </v-quote>
        </div>
    </x-admin::form>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-quote-template">
            <div class="document-form-panel box-shadow flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="grid gap-4 md:grid-cols-2">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">Quote #</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="text" name="quote_number" value="{{ old('quote_number', $quote->quote_number) }}" rules="required" />
                        <x-admin::form.control-group.error control-name="quote_number" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">Customer</x-admin::form.control-group.label>
                        <div class="relative" ref="customerLookup">
                            <div class="relative inline-block w-full" @click="toggleCustomerLookup">
                                <div class="relative flex cursor-pointer items-center justify-between rounded border border-gray-200 p-2 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:text-gray-300">
                                    <span class="overflow-hidden text-ellipsis" :title="customerName">
                                        @{{ customerName !== '' ? customerName : 'Click to add' }}
                                    </span>

                                    <div class="flex items-center gap-2">
                                        <i
                                            v-if="customerName"
                                            class="icon-cross-large cursor-pointer text-xl text-gray-600"
                                            @click.stop="clearCustomer"
                                        ></i>

                                        <i class="text-2xl text-gray-600" :class="showCustomerLookup ? 'icon-up-arrow' : 'icon-down-arrow'"></i>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="showCustomerLookup"
                                class="absolute top-full z-10 mt-1 flex w-full origin-top transform flex-col gap-2 rounded-lg border border-gray-200 bg-white p-2 shadow-lg transition-transform dark:border-gray-900 dark:bg-gray-800"
                            >
                                <div class="relative flex items-center">
                                    <input
                                        type="text"
                                        v-model="customerSearchTerm"
                                        class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                        placeholder="Search"
                                        ref="customerSearchInput"
                                    />
                                </div>

                                <ul class="max-h-40 divide-y divide-gray-100 overflow-y-auto">
                                    <li
                                        v-for="customer in filteredCustomers"
                                        :key="customer.id"
                                        class="cursor-pointer px-4 py-2 text-gray-800 transition-colors hover:bg-blue-100 dark:text-white dark:hover:bg-gray-900"
                                        @click="selectCustomer(customer)"
                                    >
                                        @{{ customer.name }}
                                    </li>

                                    <li v-if="! filteredCustomers.length" class="px-4 py-2 text-gray-500">
                                        No results
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" name="organization_id" :value="selectedOrganizationId">
                        <x-admin::form.control-group.error control-name="organization_id" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Sales Owner</x-admin::form.control-group.label>
                        <input
                            type="text"
                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            value="{{ optional($quote->user)->name }}"
                            disabled
                        />
                        <input type="hidden" name="user_id" value="{{ old('user_id', $quote->user_id) }}">
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">Quote Date</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="date" name="quote_date" value="{{ old('quote_date', $quote->quote_date?->format('Y-m-d')) }}" rules="required" />
                        <x-admin::form.control-group.error control-name="quote_date" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Status</x-admin::form.control-group.label>
                        <select
                            name="status"
                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                        >
                            @foreach (['draft', 'sent', 'approved', 'rejected', 'expired', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $quote->status ?: 'approved') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <x-admin::form.control-group.error control-name="status" />
                    </x-admin::form.control-group>
                </div>

                <input type="hidden" name="subject" value="{{ old('subject', $quote->subject ?: ('Quote ' . $quote->quote_number)) }}">

                <div class="document-form-items mt-2 flex flex-col gap-4" id="quote-items">
                    <div class="flex flex-col gap-1">
                        <p class="text-base font-semibold text-gray-800 dark:text-white">@lang('admin::app.quotes.create.quote-items')</p>
                        <p class="text-sm text-gray-600 dark:text-white">@lang('admin::app.quotes.create.quote-item-info')</p>
                    </div>

                    <v-quote-item-list :errors="errors" :organization-id="selectedOrganizationId" :initial-products="initialProducts"></v-quote-item-list>
                </div>
            </div>
        </script>

        <script type="text/x-template" id="v-quote-item-list-template">
            <div class="flex flex-col gap-4">
                <div class="block w-full overflow-visible">
                    <x-admin::table>
                        <x-admin::table.thead>
                            <x-admin::table.thead.tr>
                                <x-admin::table.th>@lang('admin::app.quotes.create.product-name')</x-admin::table.th>
                                <x-admin::table.th class="text-center">Image</x-admin::table.th>
                                <x-admin::table.th class="text-center">Color Variant</x-admin::table.th>
                                <x-admin::table.th class="text-center">@lang('admin::app.quotes.create.quantity')</x-admin::table.th>
                                <x-admin::table.th class="text-center">@lang('admin::app.quotes.create.price')</x-admin::table.th>
                                <x-admin::table.th class="text-center">@lang('admin::app.quotes.create.amount')</x-admin::table.th>
                                <x-admin::table.th class="text-center">@lang('admin::app.quotes.create.discount')</x-admin::table.th>
                                <x-admin::table.th class="text-center">@lang('admin::app.quotes.create.tax')</x-admin::table.th>
                                <x-admin::table.th class="text-center">@lang('admin::app.quotes.create.total')</x-admin::table.th>
                                <x-admin::table.th v-if="products.length > 1" class="!px-2 ltr:text-right rtl:text-left">@lang('admin::app.quotes.create.action')</x-admin::table.th>
                            </x-admin::table.thead.tr>
                        </x-admin::table.thead>

                        <x-admin::table.tbody>
                            <template v-for='(product, index) in products' :key="index">
                                <v-quote-item :product="product" :index="index" :errors="errors" :organization-id="organizationId" @onRemoveProduct="removeProduct($event)"></v-quote-item>
                            </template>
                        </x-admin::table.tbody>
                    </x-admin::table>
                </div>

                <span class="text-md flex max-w-max cursor-pointer items-center gap-2 text-brandColor" @click="addProduct">@lang('admin::app.quotes.create.add-item')</span>

                <div class="flex justify-end">
                    <div class="document-form-summary-box grid w-[348px] gap-4 rounded-lg bg-gray-100 p-4 text-sm dark:bg-gray-950 dark:text-white">
                        <div class="flex w-full justify-between gap-x-5">
                            @lang('admin::app.quotes.create.sub-total', ['symbol' => core()->currencySymbol(config('app.currency'))])
                            <input type="hidden" name="sub_total" class="control" :value="subTotal" readonly>
                            <p>@{{ subTotal }}</p>
                        </div>

                        <div class="flex w-full justify-between gap-x-5">
                            @lang('admin::app.quotes.create.total-discount', ['symbol' => core()->currencySymbol(config('app.currency'))])
                            <input type="hidden" name="discount_amount" :value="discountAmount">
                            <p>@{{ discountAmount }}</p>
                        </div>

                        <div class="flex w-full justify-between gap-x-5">
                            @lang('admin::app.quotes.create.total-tax', ['symbol' => core()->currencySymbol(config('app.currency'))])
                            <input type="hidden" name="tax_amount" :value="taxAmount">
                            <p>@{{ taxAmount }}</p>
                        </div>

                        <input type="hidden" name="adjustment_amount" value="0">

                        <div class="flex w-full justify-between gap-x-5">
                            @lang('admin::app.quotes.create.grand-total', ['symbol' => core()->currencySymbol(config('app.currency'))])
                            <input type="hidden" name="grand_total" :value="grandTotal">
                            <p>@{{ grandTotal }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </script>

        <script type="text/x-template" id="v-quote-item-template">
            <x-admin::table.thead.tr>
                <x-admin::table.td class="!px-2 align-top overflow-visible">
                    <div class="relative min-w-[380px]">
                        <v-quote-product-lookup
                            :src="src"
                            :organization-id="organizationId"
                            :selected-product="product"
                            @on-selected="(product) => addProduct(product)"
                        ></v-quote-product-lookup>
                        <input type="hidden" :name="`${inputName}[product_id]`" :value="product.product_id || ''">
                    </div>
                </x-admin::table.td>

                <x-admin::table.td class="!px-2">
                    <input type="hidden" :name="`${inputName}[preview_image]`" :value="product.preview_image || ''">
                    <img v-if="product.preview_image" :src="product.preview_image" class="mx-auto h-12 w-12 rounded object-cover border border-gray-200" alt="preview">
                    <span v-else class="text-xs text-gray-500">No image</span>
                </x-admin::table.td>

                <x-admin::table.td class="!px-2">
                    <x-admin::form.control-group class="!mb-0">
                        <select class="w-full rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-700 dark:bg-gray-800" v-model="product.selected_color_id" @change="onColorChange($event.target.value)">
                            <option value="">No Color</option>
                            <option v-for="color in (product.available_colors || [])" :key="color.id" :value="String(color.id)">@{{ color.name }}</option>
                        </select>
                        <input type="hidden" :name="`${inputName}[color_variant_id]`" :value="product.selected_color_id || ''">
                        <input type="hidden" :name="`${inputName}[color_variant_name]`" :value="product.selected_color_name || ''">
                    </x-admin::form.control-group>
                </x-admin::table.td>

                <input type="hidden" :name="`${inputName}[item_name]`" :value="product.name || ''">
                <input type="hidden" :name="`${inputName}[item_code]`" :value="product.item_code || ''">

                <x-admin::table.td class="!px-2 ltr:text-right rtl:text-left">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.control type="inline" ::name="`${inputName}[quantity]`" ::value="product.quantity" rules="required|decimal:4" ::errors="errors" :label="trans('admin::app.quotes.create.quantity')" :placeholder="trans('admin::app.quotes.create.quantity')" @on-change="(event) => product.quantity = event.value" position="center" />
                    </x-admin::form.control-group>
                </x-admin::table.td>

                <x-admin::table.td class="!px-2 ltr:text-right rtl:text-left">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.control type="inline" ::name="`${inputName}[price]`" ::value="product.price" rules="required|decimal:4" ::errors="errors" :label="trans('admin::app.quotes.create.price')" :placeholder="trans('admin::app.quotes.create.price')" @on-change="(event) => product.price = event.value" position="center" ::value-label="$admin.formatPrice(product.price)" />
                    </x-admin::form.control-group>
                </x-admin::table.td>

                <x-admin::table.td class="!px-2 ltr:text-right rtl:text-left">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.control type="inline" ::name="`${inputName}[total]`" ::value="product.price * product.quantity" rules="required|decimal:4" ::errors="errors" :label="trans('admin::app.quotes.create.total')" :placeholder="trans('admin::app.quotes.create.total')" :allowEdit="false" position="center" ::value-label="$admin.formatPrice(product.price * product.quantity)" />
                    </x-admin::form.control-group>
                </x-admin::table.td>

                <x-admin::table.td class="!px-2 ltr:text-right rtl:text-left">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.control type="inline" ::name="`${inputName}[discount_amount]`" ::value="product.discount_amount" rules="required|decimal:4" ::errors="errors" :label="trans('admin::app.quotes.create.discount-amount')" :placeholder="trans('admin::app.quotes.create.discount-amount')" @on-change="(event) => product.discount_amount = event.value" position="center" ::value-label="$admin.formatPrice(product.discount_amount)" />
                    </x-admin::form.control-group>
                </x-admin::table.td>

                <x-admin::table.td class="!px-2 ltr:text-right rtl:text-left">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.control type="inline" ::name="`${inputName}[tax_amount]`" ::value="product.tax_amount" rules="required|decimal:4" ::errors="errors" :label="trans('admin::app.quotes.create.tax-amount')" :placeholder="trans('admin::app.quotes.create.tax-amount')" @on-change="(event) => product.tax_amount = event.value" position="center" ::value-label="$admin.formatPrice(product.tax_amount)" />
                    </x-admin::form.control-group>
                </x-admin::table.td>

                <x-admin::table.td class="!px-2 ltr:text-right rtl:text-left">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.control type="inline" ::name="`${inputName}[final_total]`" ::errors="errors" ::value="parseFloat(product.price * product.quantity) + parseFloat(product.tax_amount) - parseFloat(product.discount_amount)" :allowEdit="false" position="center" ::value-label="$admin.formatPrice(parseFloat(product.price * product.quantity) + parseFloat(product.tax_amount) - parseFloat(product.discount_amount))" />
                    </x-admin::form.control-group>
                </x-admin::table.td>

                <x-admin::table.td v-if="$parent.products.length > 1" class="!px-2 ltr:text-right rtl:text-left">
                    <x-admin::form.control-group class="!mb-0">
                        <i @click="removeProduct" class="icon-delete cursor-pointer text-2xl"></i>
                    </x-admin::form.control-group>
                </x-admin::table.td>
            </x-admin::table.thead.tr>
        </script>

        <script type="module">
            app.component('v-quote', {
                template: '#v-quote-template',
                props: ['errors', 'customers', 'initialProducts', 'initialCustomerId'],
                data() {
                    return {
                        customerName: '',
                        customerSearchTerm: '',
                        showCustomerLookup: false,
                        selectedOrganizationId: this.initialCustomerId || '',
                    }
                },
                computed: {
                    filteredCustomers() {
                        const query = (this.customerSearchTerm || '').toLowerCase().trim();

                        if (! query) {
                            return this.customers;
                        }

                        return this.customers.filter((customer) => (customer.name || '').toLowerCase().includes(query));
                    }
                },
                methods: {
                    toggleCustomerLookup() {
                        this.showCustomerLookup = ! this.showCustomerLookup;

                        if (this.showCustomerLookup) {
                            this.$nextTick(() => this.$refs.customerSearchInput.focus());
                        }
                    },
                    selectCustomer(customer) {
                        this.customerName = customer?.name || '';
                        this.selectedOrganizationId = customer?.id ? String(customer.id) : '';
                        this.customerSearchTerm = '';
                        this.showCustomerLookup = false;
                    },
                    clearCustomer() {
                        this.customerName = '';
                        this.selectedOrganizationId = '';
                        this.customerSearchTerm = '';
                    },
                    handleFocusOut(event) {
                        const lookup = this.$refs.customerLookup;

                        if (lookup && ! lookup.contains(event.target)) {
                            this.showCustomerLookup = false;
                        }
                    },
                },
                mounted() {
                    window.addEventListener('click', this.handleFocusOut);

                    if (this.selectedOrganizationId) {
                        const selected = this.customers.find(customer => String(customer.id) === String(this.selectedOrganizationId));
                        this.customerName = selected ? selected.name : '';
                    }
                },
                beforeUnmount() {
                    window.removeEventListener('click', this.handleFocusOut);
                },
            });

            app.component('v-quote-item-list', {
                template: '#v-quote-item-list-template',
                props: ['errors', 'organizationId', 'initialProducts'],
                data() {
                    return {
                        adjustmentAmount: 0,
                        products: this.initialProducts?.length
                            ? this.initialProducts
                            : [{ id: null, product_id: null, name: '', item_code: '', quantity: 1, price: 0, discount_amount: 0, tax_amount: 0, available_colors: [], color_images: {}, selected_color_id: '', selected_color_name: '', preview_image: '' }],
                    }
                },
                watch: {
                    organizationId(newValue, oldValue) {
                        if (newValue !== oldValue && ! oldValue) {
                            return;
                        }

                        this.products = [{ id: null, product_id: null, name: '', item_code: '', quantity: 1, price: 0, discount_amount: 0, tax_amount: 0, available_colors: [], color_images: {}, selected_color_id: '', selected_color_name: '', preview_image: '' }];
                    }
                },
                computed: {
                    subTotal() {
                        let total = 0;
                        this.products.forEach(product => total += parseFloat(product.price * product.quantity));
                        return total;
                    },
                    discountAmount() {
                        let total = 0;
                        this.products.forEach(product => total += parseFloat(product.discount_amount));
                        return total;
                    },
                    taxAmount() {
                        let total = 0;
                        this.products.forEach(product => total += parseFloat(product.tax_amount));
                        return total;
                    },
                    grandTotal() {
                        let total = 0;
                        this.products.forEach(product => {
                            total += parseFloat(product.price * product.quantity) + parseFloat(product.tax_amount) - parseFloat(product.discount_amount);
                        });
                        return total;
                    },
                },
                methods: {
                    normalizeColorImages(images) {
                        if (! images || typeof images !== 'object') {
                            return {};
                        }

                        return Object.keys(images).reduce((carry, key) => {
                            carry[String(key)] = images[key];

                            return carry;
                        }, {});
                    },
                    addProduct() {
                        this.products.push({ id: null, product_id: null, name: '', item_code: '', quantity: 1, price: 0, discount_amount: 0, tax_amount: 0, available_colors: [], color_images: {}, selected_color_id: '', selected_color_name: '', preview_image: '' });
                    },
                    removeProduct(product) {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                if (this.products.length === 1) {
                                    this.products = [{ id: null, product_id: null, name: '', item_code: '', quantity: 1, price: 0, discount_amount: 0, tax_amount: 0, available_colors: [], color_images: {}, selected_color_id: '', selected_color_name: '', preview_image: '' }];
                                } else {
                                    const index = this.products.indexOf(product);
                                    if (index !== -1) {
                                        this.products.splice(index, 1);
                                    }
                                }
                            },
                        });
                    },
                },
            });

            app.component('v-quote-product-lookup', {
                props: ['src', 'organizationId', 'selectedProduct'],
                emits: ['on-selected'],
                data() {
                    return {
                        showPopup: false,
                        searchTerm: '',
                        results: [],
                        isSearching: false,
                        cancelToken: null,
                    };
                },
                computed: {
                    selectedLabel() {
                        const itemCode = this.selectedProduct?.item_code || '';
                        const name = this.selectedProduct?.name || '';

                        if (itemCode && name) {
                            return `${itemCode} - ${name}`;
                        }

                        return itemCode || name || '';
                    },
                },
                methods: {
                    toggle() {
                        this.showPopup = ! this.showPopup;

                        if (this.showPopup) {
                            this.$nextTick(() => this.$refs.searchInput.focus());
                        }
                    },
                    clearSelection() {
                        this.searchTerm = '';
                        this.results = [];

                        this.$emit('on-selected', {
                            id: null,
                            name: '',
                            sku: '',
                            internal_code: '',
                            selling_price: 0,
                            colors: [],
                            color_images: {},
                            cover_image_url: '',
                        });
                    },
                    search() {
                        if (! this.organizationId || this.searchTerm.trim().length < 1) {
                            this.results = [];
                            return;
                        }

                        if (this.cancelToken) {
                            this.cancelToken.cancel('Operation canceled due to new request.');
                        }

                        this.cancelToken = this.$axios.CancelToken.source();
                        this.isSearching = true;

                        this.$axios.get(this.src, {
                            params: {
                                organization_id: this.organizationId,
                                query: this.searchTerm.trim(),
                            },
                            cancelToken: this.cancelToken.token,
                        }).then((response) => {
                            const items = response?.data?.data || response?.data || [];
                            this.results = Array.isArray(items) ? items : [];
                        }).catch((error) => {
                            if (! this.$axios.isCancel(error)) {
                                this.results = [];
                            }
                        }).finally(() => {
                            this.isSearching = false;
                        });
                    },
                    selectProduct(product) {
                        this.showPopup = false;
                        this.searchTerm = '';
                        this.results = [];
                        this.$emit('on-selected', product);
                    },
                    handleFocusOut(event) {
                        if (this.$refs.lookup && ! this.$refs.lookup.contains(event.target)) {
                            this.showPopup = false;
                        }
                    },
                    resultLabel(product) {
                        const itemCode = product?.sku || product?.internal_code || '';
                        const name = product?.name || '';

                        if (itemCode && name) {
                            return `${itemCode} - ${name}`;
                        }

                        return itemCode || name || '';
                    },
                },
                watch: {
                    organizationId() {
                        this.showPopup = false;
                        this.searchTerm = '';
                        this.results = [];
                    },
                    searchTerm() {
                        this.search();
                    },
                },
                mounted() {
                    window.addEventListener('click', this.handleFocusOut);
                },
                beforeUnmount() {
                    window.removeEventListener('click', this.handleFocusOut);
                },
                template: `
                    <div class="relative" ref="lookup">
                        <div class="relative inline-block w-full" @click="toggle">
                            <div class="relative flex min-h-[42px] cursor-pointer items-center justify-between rounded border border-gray-200 p-2 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:text-gray-300">
                                <span class="block max-w-[300px] overflow-hidden text-ellipsis whitespace-nowrap" :title="selectedLabel">
                                    @{{ selectedLabel !== '' ? selectedLabel : 'Search by Product Item Code' }}
                                </span>

                                <div class="flex items-center gap-2">
                                    <i
                                        v-if="selectedLabel"
                                        class="icon-cross-large cursor-pointer text-xl text-gray-600"
                                        @click.stop="clearSelection"
                                    ></i>

                                    <i class="text-2xl text-gray-600" :class="showPopup ? 'icon-up-arrow' : 'icon-down-arrow'"></i>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="showPopup"
                            class="absolute top-full z-50 mt-1 flex w-full min-w-[380px] origin-top transform flex-col gap-2 rounded-lg border border-gray-200 bg-white p-2 shadow-lg transition-transform dark:border-gray-900 dark:bg-gray-800"
                        >
                            <div class="relative flex items-center">
                                <input
                                    type="text"
                                    v-model="searchTerm"
                                    class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                    placeholder="Search by Product Item Code"
                                    ref="searchInput"
                                    @click.stop
                                />
                            </div>

                            <ul class="max-h-56 divide-y divide-gray-100 overflow-y-auto rounded">
                                <li
                                    v-for="item in results"
                                    :key="item.id"
                                    class="cursor-pointer px-4 py-2 text-gray-800 transition-colors hover:bg-blue-100 dark:text-white dark:hover:bg-gray-900"
                                    @click="selectProduct(item)"
                                >
                                    <div class="font-medium">@{{ resultLabel(item) }}</div>
                                    <div v-if="item.name && (item.sku || item.internal_code)" class="text-xs text-gray-500">@{{ item.name }}</div>
                                </li>

                                <li v-if="isSearching" class="px-4 py-2 text-gray-500">
                                    Searching...
                                </li>

                                <li v-else-if="searchTerm.trim().length > 0 && ! results.length" class="px-4 py-2 text-gray-500">
                                    No results
                                </li>

                                <li v-else class="px-4 py-2 text-gray-500">
                                    Type product item code to search
                                </li>
                            </ul>
                        </div>
                    </div>
                `,
            });

            app.component('v-quote-item', {
                template: '#v-quote-item-template',
                props: ['index', 'product', 'errors', 'organizationId'],
                computed: {
                    inputName() {
                        if (this.product.id) {
                            return 'items[' + this.product.id + ']';
                        }
                        return 'items[item_' + this.index + ']';
                    },
                    src() {
                        return "{{ route('admin.products.search') }}";
                    },
                },
                methods: {
                    normalizeColorImages(images) {
                        if (! images || typeof images !== 'object') {
                            return {};
                        }

                        return Object.keys(images).reduce((carry, key) => {
                            carry[String(key)] = images[key];

                            return carry;
                        }, {});
                    },
                    resolvePreviewImage(colorId) {
                        const normalizedColorId = colorId ? String(colorId) : '';
                        const imageMap = this.normalizeColorImages(this.product.color_images || {});

                        if (normalizedColorId && imageMap[normalizedColorId]) {
                            return imageMap[normalizedColorId];
                        }

                        return this.product.cover_image_url || '';
                    },
                    addProduct(result) {
                        this.product.product_id = result.id ?? null;
                        this.product.name = result.name ?? '';
                        this.product.item_code = result.sku ?? result.internal_code ?? '';
                        this.product.price = result.selling_price ?? result.price ?? 0;
                        this.product.quantity = 1;
                        this.product.discount_amount = 0;
                        this.product.tax_amount = 0;
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
                    removeProduct() {
                        this.$emit('onRemoveProduct', this.product);
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>





