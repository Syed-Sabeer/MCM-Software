@php
    $chargeManager = app(\Webkul\Core\Support\DocumentChargeManager::class);
    $companyName = core()->getConfigData('general.general.company_info.company_name');
    $companyAddress = core()->getConfigData('general.general.company_info.address');
    $companyPhone = core()->getConfigData('general.general.company_info.telephone');
    $companyCell = core()->getConfigData('general.general.company_info.cell');
    $companyEmail = core()->getConfigData('general.general.company_info.email');

    $prefillItems = old('items', $vendorQuote->items->map(fn ($item) => [
        'id' => $item->id,
        'requirement_id' => $item->requirement_id,
        'material_name' => $item->material_name,
        'quantity' => $item->quantity,
        'unit' => $item->unit ?: optional($item->requirement)->unit ?: 'PCS',
        'unit_price' => $item->unit_price,
    ])->toArray());
    $initialVendorQuoteCharges = collect(old('charges', $chargeManager->extract($vendorQuote->loadMissing('additionalCharges'), 'vendor_quote')))->values()->all();
@endphp
<x-admin::layouts>
    <x-slot:title>Edit Vendor Quote</x-slot>

    @include('admin::components.documents.form-styles')

    <x-admin::form :action="route('admin.vendor_quotes.update', $vendorQuote->id)" method="PUT" enctype="multipart/form-data">
        <div class="flex flex-col gap-4">
            <div class="document-form-toolbar flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <div class="text-xl font-bold dark:text-white">Edit Vendor Quote</div>
                    <div class="text-sm text-gray-500">{{ $vendorQuote->jobOrder?->job_order_number ? 'Job Order: ' . $vendorQuote->jobOrder->job_order_number : 'Procurement RFQ' }}</div>
                </div>
                <button class="primary-button">Save Vendor Quote</button>
            </div>

            <div class="document-form-panel rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <input type="hidden" name="job_order_id" value="{{ $vendorQuote->job_order_id }}">

                <div class="grid gap-4" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
                    <div><label class="mb-1 block text-sm font-medium">Vendor Quote #</label><input type="text" name="vendor_quote_number" value="{{ old('vendor_quote_number', $vendorQuote->vendor_quote_number) }}" class="custom-input"></div>
                    <div><label class="mb-1 block text-sm font-medium">Status</label><select name="status" class="custom-select">@foreach (['draft', 'requested', 'received', 'selected', 'rejected', 'cancelled'] as $status)<option value="{{ $status }}" @selected(old('status', $vendorQuote->status) === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
                    <div><label class="mb-1 block text-sm font-medium">Issue Date</label><input type="date" name="issue_date" value="{{ old('issue_date', optional($vendorQuote->issue_date)->toDateString()) }}" class="custom-input"></div>
                </div>

                <div class="mt-4 grid gap-4" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Vendor</label>
                        <select name="organization_id" class="custom-select" id="vendor-select">
                            <option value="">Select vendor</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}" @selected(old('organization_id', $vendorQuote->organization_id) == $vendor->id)>{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="mb-1 block text-sm font-medium">Payment Term</label><input type="text" name="payment_term" value="{{ old('payment_term', $vendorQuote->payment_term) }}" class="custom-input"></div>
                    <div><label class="mb-1 block text-sm font-medium">Shipping Method</label><input type="text" name="shipping_method" value="{{ old('shipping_method', $vendorQuote->shipping_method) }}" class="custom-input"></div>
                </div>

                <div class="mt-4 grid gap-4" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
                    <div><label class="mb-1 block text-sm font-medium">First Delivery</label><input type="date" name="first_delivery_date" value="{{ old('first_delivery_date', optional($vendorQuote->first_delivery_date)->toDateString()) }}" class="custom-input"></div>
                    <div><label class="mb-1 block text-sm font-medium">Last Delivery</label><input type="date" name="last_delivery_date" value="{{ old('last_delivery_date', optional($vendorQuote->last_delivery_date)->toDateString()) }}" class="custom-input"></div>
                </div>
            </div>

            <div class="document-form-panel rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 flex items-center justify-between">
                    <div>
                        <div class="text-base font-semibold">Vendor Quote Items</div>
                        <div class="text-sm text-gray-500">Add material requests for this vendor quote.</div>
                    </div>
                    <button type="button" class="secondary-button" onclick="window.addVendorQuoteRow()">Add Row</button>
                </div>

                <table class="w-full text-sm" id="vendor-quote-items-table">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="py-2 text-left">Material</th>
                            <th class="py-2 text-left">Qty</th>
                            <th class="py-2 text-left">Unit</th>
                            <th class="py-2 text-left">Price</th>
                            <th class="py-2 text-left">Amount</th>
                            <th class="py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prefillItems as $index => $item)
                            <tr>
                                <td class="py-2"><input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] ?? '' }}"><input type="hidden" name="items[{{ $index }}][requirement_id]" value="{{ $item['requirement_id'] ?? '' }}"><input type="text" name="items[{{ $index }}][material_name]" value="{{ $item['material_name'] ?? '' }}" class="custom-input"></td>
                                <td class="py-2"><input type="number" step="0.0001" min="0.0001" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" class="custom-input item-qty" oninput="window.calculateVendorQuoteTotals()"></td>
                                <td class="py-2"><input type="text" name="items[{{ $index }}][unit]" value="{{ $item['unit'] ?? 'PCS' }}" class="custom-input"></td>
                                <td class="py-2"><input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? 0 }}" class="custom-input item-price" oninput="window.calculateVendorQuoteTotals()"></td>
                                <td class="py-2 text-right align-middle"><span class="item-amount">0.00</span></td>
                                <td class="py-2 text-center"><button type="button" class="inline-flex items-center justify-center rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:text-white dark:hover:bg-gray-800" onclick="window.removeVendorQuoteRow(this)"><i class="icon-delete"></i></button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4 flex justify-end">
                    <div class="document-form-summary-box is-wide grid gap-4 rounded-lg bg-gray-100 p-5 text-sm dark:bg-gray-950 dark:text-white" style="max-width: 100%;">
                        <div class="flex items-center justify-between gap-x-5"><span>Sub Total</span><input type="hidden" name="subtotal" id="vendor-quote-subtotal-input" value="{{ old('subtotal', $vendorQuote->subtotal ?: 0) }}"><p class="text-base font-semibold" id="vendor-quote-subtotal">0.00</p></div>
                        <div id="vendor-quote-charges-container" class="grid gap-2"></div>
                        <div><button type="button" class="secondary-button" onclick="window.addVendorQuoteCharge()">Add Charge</button></div>
                        <div class="flex items-center justify-between gap-x-5"><span>Additional Charges</span><input type="hidden" name="sales_tax_amount" id="vendor-quote-sales-tax-amount-input" value="{{ old('sales_tax_amount', $vendorQuote->sales_tax_amount ?: 0) }}"><input type="hidden" name="freight" id="vendor-quote-freight-input" value="{{ old('freight', $vendorQuote->freight ?: 0) }}"><p class="text-base font-semibold" id="vendor-quote-charges-total">0.00</p></div>
                        <div class="flex items-center justify-between gap-x-5 border-t border-gray-200 pt-3 text-base font-semibold dark:border-gray-800"><span>Grand Total</span><input type="hidden" name="grand_total" id="vendor-quote-grand-total-input" value="{{ old('grand_total', $vendorQuote->grand_total ?: 0) }}"><p id="vendor-quote-grand-total">0.00</p></div>
                    </div>
                </div>
            </div>

            <div class="document-form-panel rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 text-base font-semibold dark:text-white">Remarks & Terms</div>

                <div class="grid gap-4" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
                    <div>
                        <label class="mb-1 block text-sm font-medium dark:text-white">Remarks</label>
                        <textarea name="notes" rows="4" class="custom-input">{{ old('notes', $vendorQuote->notes) }}</textarea>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium dark:text-white">Terms & Conditions</label>
                        <textarea name="terms" rows="4" class="custom-input">{{ old('terms', $vendorQuote->terms) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </x-admin::form>

    @push('scripts')
        <script>
            window.vendorQuoteCharges = @json($initialVendorQuoteCharges);

            window.isTaxChargeName = function (name) {
                const normalized = String(name || '').trim().toLowerCase();
                return ['tax', 'tariff', 'tarrif', 'duty', 'vat', 'gst'].some((token) => normalized.includes(token));
            };

            window.vendorQuoteChargeAmount = function (charge, subtotal) {
                const value = parseFloat(charge?.value || 0);

                if ((charge?.type || 'value') === 'percentage') {
                    return subtotal * (value / 100);
                }

                return value;
            };

            window.renderVendorQuoteCharges = function () {
                const container = document.getElementById('vendor-quote-charges-container');
                if (! container) return;
                container.innerHTML = '';

                window.vendorQuoteCharges.forEach((charge, index) => {
                    const row = document.createElement('div');
                    row.className = 'document-summary-line';
                    row.style.cssText = 'display:grid;grid-template-columns:minmax(180px,1.2fr) 110px 110px 120px 36px;align-items:center;gap:10px;';
                    row.innerHTML = `
                        <div><input type="text" name="charges[${index}][name]" value="${charge.name ?? ''}" class="custom-input" placeholder="Charge name" oninput="window.updateVendorQuoteCharge(${index}, 'name', this.value)"></div>
                        <div><select name="charges[${index}][type]" class="custom-select" onchange="window.updateVendorQuoteCharge(${index}, 'type', this.value)"><option value="percentage" ${(charge.type || 'value') === 'percentage' ? 'selected' : ''}>Percentage</option><option value="value" ${(charge.type || 'value') === 'value' ? 'selected' : ''}>Value</option></select></div>
                        <div><input type="number" min="0" step="0.01" name="charges[${index}][value]" value="${charge.value ?? 0}" class="custom-input" oninput="window.updateVendorQuoteCharge(${index}, 'value', this.value)"></div>
                        <div class="text-right font-medium"><span id="vendor-quote-charge-amount-${index}">0.00</span></div>
                        <button type="button" class="inline-flex items-center justify-center rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:text-white dark:hover:bg-gray-800" onclick="window.removeVendorQuoteCharge(${index})"><i class="icon-delete"></i></button>
                    `;
                    container.appendChild(row);
                });
            };

            window.addVendorQuoteCharge = function () {
                window.vendorQuoteCharges.push({ name: '', type: 'percentage', value: 0 });
                window.renderVendorQuoteCharges();
                window.calculateVendorQuoteTotals();
            };

            window.updateVendorQuoteCharge = function (index, field, value) {
                if (! window.vendorQuoteCharges[index]) return;
                window.vendorQuoteCharges[index][field] = value;
                window.calculateVendorQuoteTotals();
            };

            window.removeVendorQuoteCharge = function (index) {
                window.vendorQuoteCharges.splice(index, 1);
                window.renderVendorQuoteCharges();
                window.calculateVendorQuoteTotals();
            };

            window.calculateVendorQuoteTotals = function () {
                const rows = document.querySelectorAll('#vendor-quote-items-table tbody tr');
                let subtotal = 0;
                rows.forEach((row) => {
                    const qty = parseFloat(row.querySelector('.item-qty')?.value || 0);
                    const price = parseFloat(row.querySelector('.item-price')?.value || 0);
                    const amount = qty * price;
                    subtotal += amount;
                    const amountNode = row.querySelector('.item-amount');
                    if (amountNode) amountNode.textContent = amount.toFixed(2);
                });

                let taxCharges = 0;
                let otherCharges = 0;

                window.vendorQuoteCharges.forEach((charge, index) => {
                    const amount = window.vendorQuoteChargeAmount(charge, subtotal);
                    const amountNode = document.getElementById(`vendor-quote-charge-amount-${index}`);
                    if (amountNode) amountNode.textContent = amount.toFixed(2);
                    if (window.isTaxChargeName(charge.name)) taxCharges += amount;
                    else otherCharges += amount;
                });

                const chargeTotal = taxCharges + otherCharges;
                const grandTotal = subtotal + chargeTotal;
                document.getElementById('vendor-quote-subtotal').textContent = subtotal.toFixed(2);
                document.getElementById('vendor-quote-subtotal-input').value = subtotal.toFixed(4);
                document.getElementById('vendor-quote-sales-tax-amount-input').value = taxCharges.toFixed(4);
                document.getElementById('vendor-quote-freight-input').value = otherCharges.toFixed(4);
                document.getElementById('vendor-quote-charges-total').textContent = chargeTotal.toFixed(2);
                document.getElementById('vendor-quote-grand-total').textContent = grandTotal.toFixed(2);
                document.getElementById('vendor-quote-grand-total-input').value = grandTotal.toFixed(4);
            };
            window.removeVendorQuoteRow = function (button) { const tbody = document.querySelector('#vendor-quote-items-table tbody'); const row = button.closest('tr'); if (!tbody || !row) return; if (tbody.children.length <= 1) { row.querySelectorAll('input').forEach((input) => { if (input.type === 'hidden') input.value = ''; else if (input.type === 'number') input.value = input.name.includes('[quantity]') ? '1' : '0'; else input.value = input.name.includes('[unit]') ? 'PCS' : ''; }); const amount = row.querySelector('.item-amount'); if (amount) amount.textContent = '0.00'; window.calculateVendorQuoteTotals(); return; } row.remove(); window.calculateVendorQuoteTotals(); };
            window.addVendorQuoteRow = function () { const tbody = document.querySelector('#vendor-quote-items-table tbody'); const index = tbody.children.length; tbody.insertAdjacentHTML('beforeend', `<tr><td class="py-2"><input type="hidden" name="items[${index}][id]" value=""><input type="hidden" name="items[${index}][requirement_id]" value=""><input type="text" name="items[${index}][material_name]" class="custom-input"></td><td class="py-2"><input type="number" step="0.0001" min="0.0001" name="items[${index}][quantity]" value="1" class="custom-input item-qty" oninput="window.calculateVendorQuoteTotals()"></td><td class="py-2"><input type="text" name="items[${index}][unit]" value="PCS" class="custom-input"></td><td class="py-2"><input type="number" step="0.01" min="0" name="items[${index}][unit_price]" value="0" class="custom-input item-price" oninput="window.calculateVendorQuoteTotals()"></td><td class="py-2 text-right align-middle"><span class="item-amount">0.00</span></td><td class="py-2 text-center"><button type="button" class="inline-flex items-center justify-center rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:text-white dark:hover:bg-gray-800" onclick="window.removeVendorQuoteRow(this)"><i class="icon-delete"></i></button></td></tr>`); window.calculateVendorQuoteTotals(); };
            document.addEventListener('DOMContentLoaded', () => {
                window.renderVendorQuoteCharges();
                window.calculateVendorQuoteTotals();
            });
        </script>
    @endpush

    @pushOnce('styles')
        <style>
            .custom-input { width: 100%; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.45rem 0.6rem; font-size: 0.875rem; background: transparent; }
            .custom-select { width: 100%; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.45rem 0.6rem; font-size: 0.875rem; background: transparent; }
        </style>
    @endPushOnce
</x-admin::layouts>
