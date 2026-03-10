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

    $initialItems = $proformaInvoice->items->map(fn ($item) => [
        'id' => $item->id,
        'product_id' => $item->product_id,
        'item_name' => $item->item_name,
        'item_code' => $item->item_code,
        'description' => $item->description,
        'qty' => $item->qty,
        'unit' => $item->unit,
        'unit_price' => $item->unit_price,
        'discount_amount' => $item->discount_amount,
        'tax_amount' => $item->tax_amount,
    ])->values()->all();

    $initialForm = [
        'proforma_number' => $proformaInvoice->proforma_number,
        'quote_id' => $proformaInvoice->quote_id,
        'organization_id' => $proformaInvoice->organization_id,
        'organization_name' => optional($proformaInvoice->organization)->name,
        'person_id' => $proformaInvoice->person_id,
        'sales_owner_id' => $proformaInvoice->sales_owner_id,
        'sales_owner_name' => optional($proformaInvoice->salesOwner)->name,
        'subject' => $proformaInvoice->subject,
        'issue_date' => optional($proformaInvoice->issue_date)->format('Y-m-d'),
        'due_date' => optional($proformaInvoice->due_date)->format('Y-m-d'),
        'status' => $proformaInvoice->status,
        'adjustment_amount' => $proformaInvoice->adjustment_amount,
        'notes' => $proformaInvoice->notes,
        'terms' => $proformaInvoice->terms,
        'billing_address' => $proformaInvoice->billing_address ?: ['address' => ''],
        'shipping_address' => $proformaInvoice->shipping_address ?: ['address' => ''],
        'items' => $initialItems,
    ];
@endphp

<x-admin::layouts>
    <x-slot:title>
        Edit Proforma Invoice
    </x-slot>

    <x-admin::form :action="route('admin.proforma_invoices.update', $proformaInvoice->id)" method="PUT" enctype="multipart/form-data">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col">
                    <div class="text-xl font-bold dark:text-white">Edit Proforma Invoice</div>
                    <div class="text-sm text-gray-500">#{{ $proformaInvoice->proforma_number }}</div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.proforma_invoices.view', $proformaInvoice->id) }}" class="secondary-button">View</a>
                    <button type="submit" class="primary-button">Update Proforma</button>
                </div>
            </div>

            <v-proforma-form
                :initial-form='@json($initialForm)'
                :quotes='@json($quotes)'
            ></v-proforma-form>
        </div>
    </x-admin::form>

    <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-3 text-lg font-semibold dark:text-white">Payment Summary</h3>
            <div class="space-y-2 text-sm dark:text-white">
                <div class="flex justify-between"><span>Grand Total</span><span>{{ core()->formatBasePrice($proformaInvoice->grand_total) }}</span></div>
                <div class="flex justify-between"><span>Received Amount</span><span>{{ core()->formatBasePrice($proformaInvoice->received_amount) }}</span></div>
                <div class="flex justify-between font-bold"><span>Remaining Amount</span><span>{{ core()->formatBasePrice($proformaInvoice->remaining_amount) }}</span></div>
            </div>

            <div class="mt-4">
                <x-admin::form :action="route('admin.proforma_invoices.status', $proformaInvoice->id)" method="POST">
                    <div class="flex items-end gap-2">
                        <x-admin::form.control-group class="!mb-0 flex-1">
                            <x-admin::form.control-group.label>Status</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="select" name="status">
                                @foreach (['draft', 'issued', 'partially_paid', 'fully_paid', 'cancelled', 'ready_for_job_order', 'converted'] as $status)
                                    <option value="{{ $status }}" @selected($proformaInvoice->status === $status)>{{ $status }}</option>
                                @endforeach
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                        <button type="submit" class="secondary-button">Update Status</button>
                    </div>
                </x-admin::form>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-3 text-lg font-semibold dark:text-white">Add Advance Receipt</h3>

            <x-admin::form :action="route('admin.proforma_invoices.receipts.store', $proformaInvoice->id)" method="POST" enctype="multipart/form-data">
                <div class="grid gap-3">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">Payment Date</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="date" name="payment_date" rules="required" value="{{ now()->toDateString() }}" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">Amount</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="number" name="amount" step="0.0001" min="0.0001" rules="required" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Payment Method</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="text" name="payment_method" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Reference No</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="text" name="reference_no" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Notes</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="textarea" name="notes" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Attachment</x-admin::form.control-group.label>
                        <input type="file" name="attachment" class="custom-input">
                    </x-admin::form.control-group>

                    <button type="submit" class="primary-button w-max">Add Receipt</button>
                </div>
            </x-admin::form>
        </div>
    </div>

    <div class="mt-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-3 text-lg font-semibold dark:text-white">Receipt History</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-2 py-2">Receipt #</th>
                        <th class="px-2 py-2">Date</th>
                        <th class="px-2 py-2">Amount</th>
                        <th class="px-2 py-2">Method</th>
                        <th class="px-2 py-2">Reference</th>
                        <th class="px-2 py-2">Received By</th>
                        <th class="px-2 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proformaInvoice->receipts as $receipt)
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-2 py-2">{{ $receipt->receipt_number ?: '-' }}</td>
                            <td class="px-2 py-2">{{ optional($receipt->payment_date)->format('Y-m-d') }}</td>
                            <td class="px-2 py-2">
                                <div>{{ core()->formatBasePrice($receipt->amount) }}</div>
                                @if ($receipt->attachment_path)
                                    <a href="{{ asset('storage/' . $receipt->attachment_path) }}" target="_blank" class="text-xs text-brandColor">Attachment</a>
                                @endif
                            </td>
                            <td class="px-2 py-2">{{ $receipt->payment_method ?: '-' }}</td>
                            <td class="px-2 py-2">{{ $receipt->reference_no ?: '-' }}</td>
                            <td class="px-2 py-2">{{ optional($receipt->receivedBy)->name ?: '-' }}</td>
                            <td class="px-2 py-2">
                                <x-admin::form :action="route('admin.proforma_invoices.receipts.delete', [$proformaInvoice->id, $receipt->id])" method="DELETE">
                                    <button class="text-red-600">Delete</button>
                                </x-admin::form>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="px-2 py-3 text-gray-500" colspan="7">No receipts recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

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
                                    <td class="px-3 py-2">
                                        <input :name="`items[${index}][item_name]`" v-model="item.item_name" class="custom-input">
                                        <input type="hidden" :name="`items[${index}][id]`" v-model="item.id">
                                    </td>
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
                        <x-admin::form.control-group.label>Replace Attachment</x-admin::form.control-group.label>
                        <input type="file" name="attachment" class="custom-input">

                        @if ($proformaInvoice->attachment_path)
                            <a href="{{ asset('storage/' . $proformaInvoice->attachment_path) }}" target="_blank" class="mt-2 inline-flex text-sm text-brandColor">View current attachment</a>
                        @endif
                    </x-admin::form.control-group>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <div class="mb-2 font-semibold">Payment Summary</div>
                        <div class="flex justify-between"><span>Received Amount</span><span>{{ core()->formatBasePrice($proformaInvoice->received_amount) }}</span></div>
                        <div class="mt-2 flex justify-between font-semibold"><span>Remaining Amount</span><span>{{ core()->formatBasePrice($proformaInvoice->remaining_amount) }}</span></div>
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
                            id: null,
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
