@php
    $organizations = app(\Webkul\Contact\Repositories\OrganizationRepository::class)
        ->whereIn('type', ['customer', 'Customer'])
        ->get(['id', 'name']);

    $persons = app(\Webkul\Contact\Repositories\PersonRepository::class)->all(['id', 'name', 'organization_id']);

    $quotes = app(\Webkul\Quote\Repositories\QuoteRepository::class)->all(['id', 'quote_number', 'subject']);

    $initialItems = isset($quote)
        ? $quote->items->map(fn ($item) => [
            'id'               => null,
            'product_id'       => $item->product_id,
            'item_name'        => $item->item_name ?: $item->name,
            'item_code'        => $item->item_code ?: $item->sku,
            'description'      => $item->description,
            'qty'              => $item->qty ?: $item->quantity,
            'unit'             => $item->unit,
            'unit_price'       => $item->unit_price ?: $item->price,
            'discount_amount'  => $item->discount_amount ?: 0,
            'tax_amount'       => $item->tax_amount ?: 0,
        ])->values()->all()
        : [[
            'id'               => null,
            'product_id'       => null,
            'item_name'        => '',
            'item_code'        => '',
            'description'      => '',
            'qty'              => 1,
            'unit'             => '',
            'unit_price'       => 0,
            'discount_amount'  => 0,
            'tax_amount'       => 0,
        ]];

    $initialForm = [
        'quote_id' => $quote->id ?? request('quote_id'),
        'organization_id' => $quote->organization_id ?? request('organization_id'),
        'person_id' => $quote->person_id ?? null,
        'subject' => $quote->subject ?? '',
        'issue_date' => now()->toDateString(),
        'due_date' => optional($quote?->expired_at)->format('Y-m-d'),
        'status' => 'draft',
        'adjustment_amount' => $quote->adjustment_amount ?? 0,
        'customer_po_reference' => '',
        'notes' => $quote->notes ?? '',
        'terms' => $quote->terms ?? '',
        'items' => $initialItems,
    ];
@endphp

<x-admin::layouts>
    <x-slot:title>
        Create Proforma Invoice
    </x-slot>

    <x-admin::form :action="route('admin.proforma_invoices.store')" method="POST">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="text-xl font-bold dark:text-white">Create Proforma Invoice</div>

                <button type="submit" class="primary-button">Save Proforma</button>
            </div>

            <v-proforma-form
                :initial-form='@json($initialForm)'
                :organizations='@json($organizations)'
                :persons='@json($persons)'
                :quotes='@json($quotes)'
            ></v-proforma-form>
        </div>
    </x-admin::form>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-proforma-form-template">
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="grid gap-4 md:grid-cols-3">
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>Quote</x-admin::form.control-group.label>
                        <select name="quote_id" v-model="form.quote_id" class="custom-select">
                            <option value="">Select Quote</option>
                            <option v-for="q in quotes" :key="q.id" :value="q.id">@{{ q.quote_number ? q.quote_number + ' - ' + q.subject : ('Quote #' + q.id) }}</option>
                        </select>
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">Customer Organization</x-admin::form.control-group.label>
                        <select name="organization_id" v-model="form.organization_id" class="custom-select" required>
                            <option value="">Select Customer</option>
                            <option v-for="org in organizations" :key="org.id" :value="org.id">@{{ org.name }}</option>
                        </select>
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>Contact Person</x-admin::form.control-group.label>
                        <select name="person_id" v-model="form.person_id" class="custom-select">
                            <option value="">Select Person</option>
                            <option v-for="person in filteredPersons" :key="person.id" :value="person.id">@{{ person.name }}</option>
                        </select>
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
                        <x-admin::form.control-group.label>Due Date</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="date" name="due_date" v-model="form.due_date" />
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

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>Customer PO Reference</x-admin::form.control-group.label>
                        <x-admin::form.control-group.control type="text" name="customer_po_reference" v-model="form.customer_po_reference" />
                    </x-admin::form.control-group>
                </div>

                <div class="mt-6">
                    <h3 class="mb-4 text-lg font-semibold dark:text-white">Items</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                                <tr>
                                    <th class="px-3 py-2">Name</th>
                                    <th class="px-3 py-2">Code</th>
                                    <th class="px-3 py-2">Qty</th>
                                    <th class="px-3 py-2">Unit</th>
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
                                    </td>
                                    <td class="px-3 py-2">
                                        <input :name="`items[${index}][item_code]`" v-model="item.item_code" class="custom-input">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.0001" min="0.0001" :name="`items[${index}][qty]`" v-model.number="item.qty" class="custom-input">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input :name="`items[${index}][unit]`" v-model="item.unit" class="custom-input">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.0001" min="0" :name="`items[${index}][unit_price]`" v-model.number="item.unit_price" class="custom-input">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.0001" min="0" :name="`items[${index}][discount_amount]`" v-model.number="item.discount_amount" class="custom-input">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.0001" min="0" :name="`items[${index}][tax_amount]`" v-model.number="item.tax_amount" class="custom-input">
                                    </td>
                                    <td class="px-3 py-2">@{{ formatPrice(lineTotal(item)) }}</td>
                                    <td class="px-3 py-2">
                                        <button type="button" class="text-red-600" @click="removeItem(index)" v-if="form.items.length > 1">Remove</button>
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

                <div class="mt-4 flex justify-end">
                    <div class="w-72 space-y-2 text-sm dark:text-white">
                        <div class="flex justify-between"><span>Subtotal</span><span>@{{ formatPrice(subtotal) }}</span></div>
                        <div class="flex justify-between"><span>Discount</span><span>@{{ formatPrice(discountAmount) }}</span></div>
                        <div class="flex justify-between"><span>Tax</span><span>@{{ formatPrice(taxAmount) }}</span></div>
                        <div class="flex justify-between">
                            <span>Adjustment</span>
                            <input type="number" step="0.0001" min="0" name="adjustment_amount" v-model.number="form.adjustment_amount" class="custom-input w-24">
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-2 dark:border-gray-700 font-bold"><span>Grand Total</span><span>@{{ formatPrice(grandTotal) }}</span></div>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-proforma-form', {
                template: '#v-proforma-form-template',
                props: ['initialForm', 'organizations', 'persons', 'quotes'],
                data() {
                    return { form: this.initialForm };
                },
                computed: {
                    filteredPersons() {
                        if (! this.form.organization_id) {
                            return this.persons;
                        }

                        return this.persons.filter(person => String(person.organization_id) === String(this.form.organization_id));
                    },
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
                },
                methods: {
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
