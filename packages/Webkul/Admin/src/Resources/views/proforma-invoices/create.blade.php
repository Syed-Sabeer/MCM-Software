@php
    $quotes = \Webkul\Quote\Models\Quote::query()
        ->with(['items', 'organization', 'person', 'user'])
        ->get()
        ->map(fn ($quoteRow) => [
            'id' => $quoteRow->id,
            'quote_number' => $quoteRow->quote_number,
            'subject' => $quoteRow->subject,
            'organization_id' => $quoteRow->organization_id,
            'organization_name' => optional($quoteRow->organization)->name,
            'person_id' => $quoteRow->person_id,
            'person_name' => optional($quoteRow->person)->name,
            'sales_owner_id' => $quoteRow->user_id,
            'sales_owner_name' => optional($quoteRow->user)->name,
            'due_date' => optional($quoteRow->expired_at)->format('Y-m-d'),
            'adjustment_amount' => $quoteRow->adjustment_amount,
            'notes' => $quoteRow->notes,
            'terms' => $quoteRow->terms,
            'billing_address' => $quoteRow->billing_address ?: ['address' => ''],
            'shipping_address' => $quoteRow->shipping_address ?: ['address' => ''],
            'items' => $quoteRow->items->map(fn ($item) => [
                'id' => null,
                'product_id' => $item->product_id,
                'item_name' => $item->item_name ?: $item->name,
                'item_code' => $item->item_code ?: $item->sku,
                'description' => $item->description,
                'qty' => $item->qty ?: $item->quantity,
                'unit' => $item->unit,
                'unit_price' => $item->unit_price ?: $item->price,
                'discount_amount' => $item->discount_amount ?: 0,
                'tax_amount' => $item->tax_amount ?: 0,
            ])->values()->all(),
        ])
        ->values();

    $selectedQuote = isset($quote)
        ? $quotes->firstWhere('id', $quote->id)
        : $quotes->firstWhere('id', (int) request('quote_id'));

    $initialForm = [
        'proforma_number' => old('proforma_number', $nextProformaNumber ?? \Webkul\Quote\Models\ProformaInvoice::generateNextProformaNumber()),
        'quote_id' => $selectedQuote['id'] ?? request('quote_id'),
        'organization_id' => $selectedQuote['organization_id'] ?? null,
        'organization_name' => $selectedQuote['organization_name'] ?? '',
        'person_id' => $selectedQuote['person_id'] ?? null,
        'sales_owner_id' => $selectedQuote['sales_owner_id'] ?? auth()->id(),
        'sales_owner_name' => $selectedQuote['sales_owner_name'] ?? optional(auth()->user())->name,
        'subject' => $selectedQuote['subject'] ?? '',
        'issue_date' => now()->toDateString(),
        'due_date' => $selectedQuote['due_date'] ?? null,
        'status' => 'draft',
        'adjustment_amount' => $selectedQuote['adjustment_amount'] ?? 0,
        'notes' => $selectedQuote['notes'] ?? '',
        'terms' => $selectedQuote['terms'] ?? '',
        'billing_address' => $selectedQuote['billing_address'] ?? ['address' => ''],
        'shipping_address' => $selectedQuote['shipping_address'] ?? ['address' => ''],
        'items' => $selectedQuote['items'] ?? [[
            'id' => null,
            'product_id' => null,
            'item_name' => '',
            'item_code' => '',
            'description' => '',
            'qty' => 1,
            'unit' => '',
            'unit_price' => 0,
            'discount_amount' => 0,
            'tax_amount' => 0,
        ]],
    ];
@endphp

<x-admin::layouts>
    <x-slot:title>
        Create Proforma Invoice
    </x-slot>

    <x-admin::form :action="route('admin.proforma_invoices.store')" method="POST" enctype="multipart/form-data">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="text-xl font-bold dark:text-white">Create Proforma Invoice</div>

                <button type="submit" class="primary-button">Save Proforma</button>
            </div>

            <v-proforma-form
                :initial-form='@json($initialForm)'
                :quotes='@json($quotes)'
            ></v-proforma-form>
        </div>
    </x-admin::form>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-proforma-form-template">
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <input type="hidden" name="organization_id" :value="form.organization_id">
                <input type="hidden" name="person_id" :value="form.person_id || ''">
                <input type="hidden" name="sales_owner_id" :value="form.sales_owner_id || ''">
                <input type="hidden" name="due_date" :value="form.due_date || ''">
                <input type="hidden" name="billing_address[address]" :value="form.billing_address?.address || ''">
                <input type="hidden" name="shipping_address[address]" :value="form.shipping_address?.address || ''">

                <div class="grid gap-4 md:grid-cols-3">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>Proforma #</x-admin::form.control-group.label>
                        <input type="text" name="proforma_number" v-model="form.proforma_number" class="custom-input">
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">Quote</x-admin::form.control-group.label>
                        <select name="quote_id" v-model="form.quote_id" @change="applyQuoteDetails" class="custom-select" required>
                            <option value="">Select Quote</option>
                            <option v-for="q in quotes" :key="q.id" :value="q.id">@{{ q.quote_number ? q.quote_number + ' - ' + q.subject : ('Quote #' + q.id) }}</option>
                        </select>
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>Customer</x-admin::form.control-group.label>
                        <input type="text" class="custom-input" :value="form.organization_name || ''" disabled>
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>Sales Owner</x-admin::form.control-group.label>
                        <input type="text" class="custom-input" :value="form.sales_owner_name || ''" disabled>
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>Subject</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="text" name="subject" v-model="form.subject" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">Issue Date</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="date" name="issue_date" v-model="form.issue_date" rules="required" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
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
                </div>

                <div class="mt-6">
                    <div class="mb-4 flex flex-col gap-1">
                        <p class="text-base font-semibold text-gray-800 dark:text-white">Proforma Items</p>
                        <p class="text-sm text-gray-600 dark:text-white">Add Product Request for this proforma invoice.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                                <tr>
                                    <th class="px-3 py-2">Name</th>
                                    <th class="px-3 py-2">Code</th>
                                    <th class="px-3 py-2">Qty</th>
                                    <th class="px-3 py-2">Unit Price</th>
                                    <th class="px-3 py-2">Discount</th>
                                    <th class="px-3 py-2">Tax</th>
                                    <th class="px-3 py-2">Total</th>
                                    <th class="px-3 py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in form.items" :key="index" class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-3 py-2"><input :name="`items[${index}][item_name]`" v-model="item.item_name" class="custom-input"></td>
                                    <td class="px-3 py-2"><input :name="`items[${index}][item_code]`" v-model="item.item_code" class="custom-input"></td>
                                    <td class="px-3 py-2"><input type="number" step="0.0001" min="0.0001" :name="`items[${index}][qty]`" v-model.number="item.qty" class="custom-input"></td>
                                    <input type="hidden" :name="`items[${index}][unit]`" :value="item.unit || ''">
                                    <td class="px-3 py-2"><input type="number" step="0.0001" min="0" :name="`items[${index}][unit_price]`" v-model.number="item.unit_price" class="custom-input"></td>
                                    <td class="px-3 py-2"><input type="number" step="0.0001" min="0" :name="`items[${index}][discount_amount]`" v-model.number="item.discount_amount" class="custom-input"></td>
                                    <td class="px-3 py-2"><input type="number" step="0.0001" min="0" :name="`items[${index}][tax_amount]`" v-model.number="item.tax_amount" class="custom-input"></td>
                                    <td class="px-3 py-2">@{{ formatPrice(lineTotal(item)) }}</td>
                                    <td class="px-3 py-2">
                                        <button type="button" @click="removeItem(index)" v-if="form.items.length > 1">
                                            <i class="icon-delete cursor-pointer text-2xl"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="mt-3 text-brandColor" @click="addItem">Add Row</button>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>Notes</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="textarea" name="notes" v-model="form.notes" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>Terms</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="textarea" name="terms" v-model="form.terms" />
                    </x-admin::form.control-group>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>Attachment</x-admin::form.control-group.label>
                        <input type="file" name="attachment" class="custom-input">
                    </x-admin::form.control-group>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <div class="mb-2 font-semibold">Payment Summary</div>
                        <div class="flex justify-between"><span>Received Amount</span><span>@{{ formatPrice(0) }}</span></div>
                        <div class="mt-2 flex justify-between font-semibold"><span>Remaining Amount</span><span>@{{ formatPrice(grandTotal) }}</span></div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <div class="w-72 space-y-2 text-sm dark:text-white">
                        <div class="flex justify-between"><span>Subtotal</span><span>@{{ formatPrice(subtotal) }}</span></div>
                        <div class="flex justify-between"><span>Discount</span><span>@{{ formatPrice(discountAmount) }}</span></div>
                        <div class="flex justify-between"><span>Tax</span><span>@{{ formatPrice(taxAmount) }}</span></div>
                        <input type="hidden" name="adjustment_amount" v-model.number="form.adjustment_amount">
                        <div class="flex justify-between border-t border-gray-200 pt-2 font-bold dark:border-gray-700"><span>Grand Total</span><span>@{{ formatPrice(grandTotal) }}</span></div>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-proforma-form', {
                template: '#v-proforma-form-template',
                props: ['initialForm', 'quotes'],
                data() {
                    return {
                        form: {
                            ...this.initialForm,
                            billing_address: this.initialForm?.billing_address || { address: '' },
                            shipping_address: this.initialForm?.shipping_address || { address: '' },
                        }
                    };
                },
                computed: {
                    subtotal() {
                        return this.form.items.reduce((sum, item) => sum + ((parseFloat(item.qty) || 0) * (parseFloat(item.unit_price) || 0)), 0);
                    },
                    discountAmount() {
                        return this.form.items.reduce((sum, item) => sum + (parseFloat(item.discount_amount) || 0), 0);
                    },
                    taxAmount() {
                        return this.form.items.reduce((sum, item) => sum + (parseFloat(item.tax_amount) || 0), 0);
                    },
                    grandTotal() {
                        return this.subtotal - this.discountAmount + this.taxAmount + (parseFloat(this.form.adjustment_amount) || 0);
                    },
                    selectedQuote() {
                        return this.quotes.find(quote => String(quote.id) === String(this.form.quote_id)) || null;
                    },
                },
                watch: {
                    'form.quote_id'(value, oldValue) {
                        if (! value || String(value) === String(oldValue)) {
                            return;
                        }

                        this.applyQuoteDetails();
                    }
                },
                mounted() {
                    if (this.form.quote_id) {
                        this.applyQuoteDetails();
                    }
                },
                methods: {
                    applyQuoteDetails() {
                        if (! this.selectedQuote) {
                            return;
                        }

                        this.form.organization_id = this.selectedQuote.organization_id || '';
                        this.form.organization_name = this.selectedQuote.organization_name || '';
                        this.form.person_id = this.selectedQuote.person_id || '';
                        this.form.sales_owner_id = this.selectedQuote.sales_owner_id || '';
                        this.form.sales_owner_name = this.selectedQuote.sales_owner_name || '';
                        this.form.subject = this.selectedQuote.subject || '';
                        this.form.due_date = this.selectedQuote.due_date || '';
                        this.form.adjustment_amount = parseFloat(this.selectedQuote.adjustment_amount || 0);
                        this.form.notes = this.selectedQuote.notes || '';
                        this.form.terms = this.selectedQuote.terms || '';
                        this.form.billing_address = this.selectedQuote.billing_address || { address: '' };
                        this.form.shipping_address = this.selectedQuote.shipping_address || { address: '' };
                        this.form.items = (this.selectedQuote.items || []).map(item => ({ ...item }));
                    },
                    addItem() {
                        this.form.items.push({
                            item_name: '',
                            item_code: '',
                            qty: 1,
                            unit: '',
                            unit_price: 0,
                            discount_amount: 0,
                            tax_amount: 0,
                            description: '',
                        });
                    },
                    removeItem(index) {
                        this.form.items.splice(index, 1);
                    },
                    lineTotal(item) {
                        const subtotal = (parseFloat(item.qty) || 0) * (parseFloat(item.unit_price) || 0);
                        return subtotal - (parseFloat(item.discount_amount) || 0) + (parseFloat(item.tax_amount) || 0);
                    },
                    formatPrice(value) {
                        return this.$admin.formatPrice(value || 0);
                    },
                },
            });
        </script>
    @endPushOnce

    @pushOnce('styles')
        <style>
            .custom-input {
                width: 100%;
                border: 1px solid #d1d5db;
                border-radius: 0.375rem;
                padding: 0.45rem 0.6rem;
                font-size: 0.875rem;
                background: transparent;
            }

            .custom-select {
                width: 100%;
                border: 1px solid #d1d5db;
                border-radius: 0.375rem;
                padding: 0.45rem 0.6rem;
                font-size: 0.875rem;
                background: transparent;
            }
        </style>
    @endPushOnce
</x-admin::layouts>
