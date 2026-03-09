@php
    $customerOrganizations = app(\Webkul\Contact\Repositories\OrganizationRepository::class)
        ->whereIn('type', ['customer', 'Customer'])
        ->get(['id', 'name']);

    $users = app(\Webkul\User\Repositories\UserRepository::class)->all(['id', 'name']);

    $quoteItems = $quote->items->map(function ($item) {
        return [
            'id'              => $item->id,
            'product_id'      => $item->product_id,
            'name'            => $item->name,
            'item_name'       => $item->item_name ?: $item->name,
            'unit'            => $item->unit,
            'quantity'        => $item->quantity ?: $item->qty,
            'price'           => $item->price ?: $item->unit_price,
            'discount_amount' => $item->discount_amount,
            'tax_amount'      => $item->tax_amount,
        ];
    })->values();
@endphp

<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.quotes.edit.title')
    </x-slot>

    <x-admin::form
        :action="route('admin.quotes.update', $quote->id) . '?' . http_build_query(array_merge(
            request()->route()->parameters(),
            request()->all()
        ))"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs name="quotes.edit" :entity="$quote" />
                    <div class="text-xl font-bold dark:text-white">@lang('admin::app.quotes.edit.title')</div>
                </div>

                <button type="submit" class="primary-button">@lang('admin::app.quotes.edit.save-btn')</button>
            </div>

            <v-quote :errors="errors">
                <x-admin::shimmer.quotes />
            </v-quote>
        </div>
    </x-admin::form>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-quote-template">
            <div class="box-shadow flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="grid gap-4 md:grid-cols-2">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">Quote #</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="text" name="quote_number" value="{{ old('quote_number', $quote->quote_number) }}" rules="required" />
                        <x-admin::form.control-group.error control-name="quote_number" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">Customer</x-admin::form.control-group.label>
                        <select name="organization_id" class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm text-gray-800 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300" required>
                            <option value="">Select Customer</option>
                            @foreach ($customerOrganizations as $organization)
                                <option value="{{ $organization->id }}" @selected((string) old('organization_id', $quote->organization_id) === (string) $organization->id)>{{ $organization->name }}</option>
                            @endforeach
                        </select>
                        <x-admin::form.control-group.error control-name="organization_id" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">Sales Owner</x-admin::form.control-group.label>
                        <select name="user_id" class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm text-gray-800 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300" required>
                            <option value="">Select Sales Owner</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected((string) old('user_id', $quote->user_id) === (string) $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <x-admin::form.control-group.error control-name="user_id" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">Quote Date</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="date" name="quote_date" value="{{ old('quote_date', optional($quote->quote_date)->format('Y-m-d')) }}" rules="required" />
                        <x-admin::form.control-group.error control-name="quote_date" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Status</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="select" name="status">
                            @foreach (['draft', 'sent', 'approved', 'rejected', 'expired', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $quote->status ?: 'draft') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </x-admin::form.control-group.control>
                    </x-admin::form.control-group>
                </div>

                <input type="hidden" name="subject" value="{{ old('subject', $quote->subject ?: ('Quote ' . $quote->quote_number)) }}">

                <div class="mt-2 flex flex-col gap-4" id="quote-items">
                    <div class="flex flex-col gap-1">
                        <p class="text-base font-semibold text-gray-800 dark:text-white">@lang('admin::app.quotes.create.quote-items')</p>
                        <p class="text-sm text-gray-600 dark:text-white">@lang('admin::app.quotes.create.quote-item-info')</p>
                    </div>

                    <v-quote-item-list :errors="errors"></v-quote-item-list>
                </div>
            </div>
        </script>

        <script type="text/x-template" id="v-quote-item-list-template">
            <div class="flex flex-col gap-4">
                <div class="block w-full">
                    <x-admin::table>
                        <x-admin::table.thead>
                            <x-admin::table.thead.tr>
                                <x-admin::table.th>@lang('admin::app.quotes.create.product-name')</x-admin::table.th>
                                <x-admin::table.th class="text-center">Item Name</x-admin::table.th>
                                <x-admin::table.th class="text-center">Unit</x-admin::table.th>
                                <x-admin::table.th class="text-center">@lang('admin::app.quotes.create.quantity')</x-admin::table.th>
                                <x-admin::table.th class="text-center">@lang('admin::app.quotes.create.price') ({{ core()->currencySymbol(config('app.currency')) }})</x-admin::table.th>
                                <x-admin::table.th class="text-center">@lang('admin::app.quotes.create.amount') ({{ core()->currencySymbol(config('app.currency')) }})</x-admin::table.th>
                                <x-admin::table.th class="text-center">@lang('admin::app.quotes.create.discount') ({{ core()->currencySymbol(config('app.currency')) }})</x-admin::table.th>
                                <x-admin::table.th class="text-center">@lang('admin::app.quotes.create.tax') ({{ core()->currencySymbol(config('app.currency')) }})</x-admin::table.th>
                                <x-admin::table.th class="text-center">@lang('admin::app.quotes.create.total') ({{ core()->currencySymbol(config('app.currency')) }})</x-admin::table.th>
                                <x-admin::table.th v-if="products.length > 1" class="!px-2 ltr:text-right rtl:text-left">@lang('admin::app.quotes.create.action')</x-admin::table.th>
                            </x-admin::table.thead.tr>
                        </x-admin::table.thead>

                        <x-admin::table.tbody>
                            <template v-for='(product, index) in products' :key="index">
                                <v-quote-item :product="product" :index="index" :errors="errors" @onRemoveProduct="removeProduct($event)"></v-quote-item>
                            </template>
                        </x-admin::table.tbody>
                    </x-admin::table>
                </div>

                <span class="text-md flex max-w-max cursor-pointer items-center gap-2 text-brandColor" @click="addProduct">@lang('admin::app.quotes.create.add-item')</span>

                <div class="flex justify-end">
                    <div class="grid w-[348px] gap-4 rounded-lg bg-gray-100 p-4 text-sm dark:bg-gray-950 dark:text-white">
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

                        <div class="flex w-full justify-between gap-x-5">
                            @lang('admin::app.quotes.create.total-adjustment', ['symbol' => core()->currencySymbol(config('app.currency'))])
                            <x-admin::form.control-group.control type="inline" ::name="`adjustment_amount`" ::value="adjustmentAmount" rules="required|decimal:4" ::errors="errors" :label="trans('admin::app.quotes.create.adjustment-amount')" :placeholder="trans('admin::app.quotes.create.adjustment-amount')" @on-change="(event) => adjustmentAmount = event.value" />
                        </div>

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
                <x-admin::table.td>
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::lookup
                            ::src="src"
                            ::name="`${inputName}[product_id]`"
                            ::params="params"
                            ::value="{ id: product.product_id, name: product.name }"
                            @on-selected="(product) => addProduct(product)"
                            :placeholder="trans('admin::app.quotes.edit.search-products')"
                        />
                    </x-admin::form.control-group>
                </x-admin::table.td>

                <x-admin::table.td class="!px-2">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.control type="inline" ::name="`${inputName}[item_name]`" ::value="product.item_name ?? product.name" ::errors="errors" label="Item Name" placeholder="Item Name" @on-change="(event) => product.item_name = event.value" />
                    </x-admin::form.control-group>
                </x-admin::table.td>

                <x-admin::table.td class="!px-2">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.control type="inline" ::name="`${inputName}[unit]`" ::value="product.unit" ::errors="errors" label="Unit" placeholder="Unit" @on-change="(event) => product.unit = event.value" />
                    </x-admin::form.control-group>
                </x-admin::table.td>

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

                <x-admin::table.td v-if="$parent.products.length > 1" class="!p-2 !px-2 ltr:text-right rtl:text-left">
                    <x-admin::form.control-group class="!mb-0">
                        <i @click="removeProduct" class="icon-delete cursor-pointer text-2xl"></i>
                    </x-admin::form.control-group>
                </x-admin::table.td>
            </x-admin::table.thead.tr>
        </script>

        <script type="module">
            app.component('v-quote', {
                template: '#v-quote-template',
                props: ['errors'],
            });

            app.component('v-quote-item-list', {
                template: '#v-quote-item-list-template',
                props: ['errors'],
                data() {
                    return {
                        adjustmentAmount: {{ (float) ($quote->adjustment_amount ?? 0) }},
                        products: @json($quoteItems),
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
                            total += parseFloat(product.price * product.quantity) + parseFloat(product.tax_amount) - parseFloat(product.discount_amount) + parseFloat(this.adjustmentAmount);
                        });
                        return total;
                    },
                },
                methods: {
                    addProduct() {
                        this.products.push({ id: null, product_id: null, name: '', item_name: '', unit: '', quantity: 1, total: 0, price: 0, discount_amount: 0, tax_amount: 0 });
                    },
                    removeProduct(product) {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                if (this.products.length === 1) {
                                    this.products = [{ id: null, product_id: null, name: '', item_name: '', unit: '', quantity: 1, total: 0, price: 0, discount_amount: 0, tax_amount: 0 }];
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

            app.component('v-quote-item', {
                template: '#v-quote-item-template',
                props: ['index', 'product', 'errors'],
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
                    params() {
                        return { params: { query: this.product.name } };
                    },
                },
                methods: {
                    addProduct(result) {
                        this.product.product_id = result.id;
                        this.product.name = result.name;
                        this.product.item_name = result.name;
                        this.product.price = result.price ?? 0;
                        this.product.quantity = result.quantity ?? 1;
                        this.product.unit = this.product.unit ?? '';
                        this.product.discount_amount = 0;
                        this.product.tax_amount = 0;
                    },
                    removeProduct() {
                        this.$emit('onRemoveProduct', this.product);
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
