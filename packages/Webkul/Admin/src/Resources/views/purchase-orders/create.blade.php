<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.purchase-orders.create.title')
    </x-slot>

    {!! view_render_event('admin.purchase_orders.create.form.before') !!}

    <x-admin::form
        :action="route('admin.purchase_orders.store')"
        method="POST"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs name="purchase_orders.create" />

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.purchase-orders.create.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('admin.purchase_orders.create.save_button.before') !!}

                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('admin::app.purchase-orders.create.save-btn')
                    </button>

                    {!! view_render_event('admin.purchase_orders.create.save_button.after') !!}
                </div>
            </div>

            <v-purchase-order-form :next-po-number="'{{ $nextPoNumber }}'"></v-purchase-order-form>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.purchase_orders.create.form.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-purchase-order-form-template"
        >
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                {{-- Top meta info --}}
                <div class="mb-4 grid gap-4 md:grid-cols-3">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            @lang('admin::app.purchase-orders.create.po-number')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="po_number"
                            v-model="formData.po_number"
                            rules="required"
                            :label="trans('admin::app.purchase-orders.create.po-number')"
                        />

                        <x-admin::form.control-group.error control-name="po_number" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('admin::app.purchase-orders.create.job-number')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="job_number"
                            v-model="formData.job_number"
                            :label="trans('admin::app.purchase-orders.create.job-number')"
                        />

                        <x-admin::form.control-group.error control-name="job_number" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('admin::app.purchase-orders.create.sales-tax-percent')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="number"
                            name="sales_tax_percent"
                            step="0.01"
                            v-model="formData.sales_tax_percent"
                            :label="trans('admin::app.purchase-orders.create.sales-tax-percent')"
                        />

                        <x-admin::form.control-group.error control-name="sales_tax_percent" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('admin::app.purchase-orders.create.freight')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="number"
                            name="freight"
                            step="0.01"
                            v-model="formData.freight"
                            :label="trans('admin::app.purchase-orders.create.freight')"
                        />

                        <x-admin::form.control-group.error control-name="freight" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('admin::app.purchase-orders.create.completion-date')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="date"
                            name="completion_date"
                            v-model="formData.completion_date"
                            :label="trans('admin::app.purchase-orders.create.completion-date')"
                        />

                        <x-admin::form.control-group.error control-name="completion_date" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('admin::app.purchase-orders.create.last-delivery-date')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="date"
                            name="last_delivery_date"
                            v-model="formData.last_delivery_date"
                            :label="trans('admin::app.purchase-orders.create.last-delivery-date')"
                        />

                        <x-admin::form.control-group.error control-name="last_delivery_date" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('admin::app.purchase-orders.create.payment-term')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="select"
                            name="payment_term"
                            v-model="formData.payment_term"
                            :label="trans('admin::app.purchase-orders.create.payment-term')"
                        >
                            <option value="">@lang('admin::app.purchase-orders.create.select-payment-term')</option>
                            <option value="net_15">Net 15</option>
                            <option value="net_30">Net 30</option>
                            <option value="net_45">Net 45</option>
                            <option value="net_60">Net 60</option>
                            <option value="due_on_receipt">Due on Receipt</option>
                            <option value="cod">COD (Cash on Delivery)</option>
                            <option value="prepaid">Prepaid</option>
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="payment_term" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('admin::app.purchase-orders.create.shipping-method')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="select"
                            name="shipping_method"
                            v-model="formData.shipping_method"
                            :label="trans('admin::app.purchase-orders.create.shipping-method')"
                        >
                            <option value="">@lang('admin::app.purchase-orders.create.select-shipping-method')</option>
                            <option value="ground">Ground</option>
                            <option value="express">Express</option>
                            <option value="overnight">Overnight</option>
                            <option value="two_day">2-Day</option>
                            <option value="freight">Freight</option>
                            <option value="pickup">Customer Pickup</option>
                            <option value="other">Other</option>
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group.error control-name="shipping_method" />
                    </x-admin::form.control-group>
                </div>

                <!-- Organization & Notes Section -->
                <div class="mb-4 grid gap-4 md:grid-cols-2">
                    {{-- Organization select --}}
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('admin::app.purchase-orders.create.organization')
                        </x-admin::form.control-group.label>

                        <select
                            name="organization_id"
                            v-model="formData.organization_id"
                            class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                        >
                            <option value="">
                                @lang('admin::app.purchase-orders.create.select-organization')
                            </option>

                            @foreach(app(\Webkul\Contact\Repositories\OrganizationRepository::class)->all() as $organization)
                                <option value="{{ $organization->id }}">
                                    {{ $organization->name }}
                                </option>
                            @endforeach
                        </select>

                        <x-admin::form.control-group.error control-name="organization_id" />
                    </x-admin::form.control-group>

                    {{-- Notes --}}
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('admin::app.purchase-orders.create.notes')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="textarea"
                            name="notes"
                            v-model="formData.notes"
                            rows="3"
                            :label="trans('admin::app.purchase-orders.create.notes')"
                            :placeholder="trans('admin::app.purchase-orders.create.notes-placeholder')"
                        />

                        <x-admin::form.control-group.error control-name="notes" />
                    </x-admin::form.control-group>
                </div>

                <h3 class="mb-4 text-lg font-semibold dark:text-white">
                    @lang('admin::app.purchase-orders.create.items')
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-3 font-semibold">@lang('admin::app.purchase-orders.create.item')</th>
                                <th class="px-4 py-3 font-semibold">@lang('admin::app.purchase-orders.create.description')</th>
                                <th class="px-4 py-3 font-semibold">@lang('admin::app.purchase-orders.create.qty')</th>
                                <th class="px-4 py-3 font-semibold">@lang('admin::app.purchase-orders.create.price-each')</th>
                                <th class="px-4 py-3 font-semibold">@lang('admin::app.purchase-orders.create.total')</th>
                                <th class="px-4 py-3 font-semibold">@lang('admin::app.purchase-orders.create.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in formData.items"
                                :key="index"
                                class="border-b border-gray-200 dark:border-gray-700"
                            >
                                <td class="px-4 py-3">
                                    <input
                                        type="text"
                                        :name="`items[${index}][item]`"
                                        v-model="item.item"
                                        class="w-full rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                    />
                                </td>
                                <td class="px-4 py-3">
                                    <input
                                        type="text"
                                        :name="`items[${index}][description]`"
                                        v-model="item.description"
                                        class="w-full rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                    />
                                </td>
                                <td class="px-4 py-3">
                                    <input
                                        type="number"
                                        :name="`items[${index}][quantity]`"
                                        v-model.number="item.quantity"
                                        @input="calculateItemTotal(index)"
                                        min="1"
                                        class="w-20 rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                    />
                                </td>
                                <td class="px-4 py-3">
                                    <input
                                        type="number"
                                        :name="`items[${index}][price]`"
                                        v-model.number="item.price"
                                        @input="calculateItemTotal(index)"
                                        step="0.01"
                                        class="w-24 rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                    />
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                    @{{ formatCurrency(item.total) }}
                                </td>
                                <td class="px-4 py-3">
                                    <button
                                        type="button"
                                        class="text-red-600 hover:text-red-800"
                                        @click="removeItem(index)"
                                        v-if="formData.items.length > 1"
                                    >
                                        <i class="icon-delete text-2xl"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button
                        type="button"
                        class="flex items-center gap-2 text-brandColor hover:underline"
                        @click="addItem"
                    >
                        <i class="icon-add text-md"></i>
                        @lang('admin::app.purchase-orders.create.add-item')
                    </button>
                </div>

                <div class="mt-6 flex justify-end">
                    <div class="w-64 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">@lang('admin::app.purchase-orders.create.subtotal'):</span>
                            <span class="font-medium dark:text-white">@{{ formatCurrency(subTotal) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">@lang('admin::app.purchase-orders.create.tax') (@{{ formData.sales_tax_percent || 0 }}%):</span>
                            <span class="font-medium dark:text-white">@{{ formatCurrency(taxAmount) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">@lang('admin::app.purchase-orders.create.freight'):</span>
                            <span class="font-medium dark:text-white">@{{ formatCurrency(parseFloat(formData.freight) || 0) }}</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-2 dark:border-gray-700">
                            <span class="font-semibold dark:text-white">@lang('admin::app.purchase-orders.create.grand-total'):</span>
                            <span class="font-bold text-brandColor">@{{ formatCurrency(grandTotal) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-purchase-order-form', {
                template: '#v-purchase-order-form-template',

                props: {
                    nextPoNumber: {
                        type: String,
                        default: '00001',
                    },
                },

                data() {
                    return {
                        formData: {
                            po_number: this.nextPoNumber,
                            job_number: '',
                            sales_tax_percent: 0,
                            freight: 0,
                            notes: '',
                            completion_date: '',
                            last_delivery_date: '',
                            payment_term: '',
                            shipping_method: '',
                            organization_id: '',
                            items: [
                                { item: '', description: '', quantity: 1, price: 0, total: 0 }
                            ],
                        },
                    };
                },

                computed: {
                    subTotal() {
                        return this.formData.items.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
                    },

                    taxAmount() {
                        return this.subTotal * ((parseFloat(this.formData.sales_tax_percent) || 0) / 100);
                    },

                    grandTotal() {
                        return this.subTotal + this.taxAmount + (parseFloat(this.formData.freight) || 0);
                    },
                },

                methods: {
                    addItem() {
                        this.formData.items.push({
                            item: '',
                            description: '',
                            quantity: 1,
                            price: 0,
                            total: 0,
                        });
                    },

                    removeItem(index) {
                        this.formData.items.splice(index, 1);
                    },

                    calculateItemTotal(index) {
                        const item = this.formData.items[index];
                        item.total = (parseFloat(item.quantity) || 0) * (parseFloat(item.price) || 0);
                    },

                    formatCurrency(value) {
                        return new Intl.NumberFormat('en-US', {
                            style: 'currency',
                            currency: '{{ config('app.currency') }}',
                        }).format(value);
                    },
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
