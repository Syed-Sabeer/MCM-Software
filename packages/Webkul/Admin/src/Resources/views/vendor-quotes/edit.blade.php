@php
    $chargeManager = app(\Webkul\Core\Support\DocumentChargeManager::class);
    $addressManager = app(\Webkul\Core\Support\DocumentAddressManager::class);
    $companyName = core()->getConfigData('general.general.company_info.company_name');
    $companyAddress = core()->getConfigData('general.general.company_info.address');
    $companyPhone = core()->getConfigData('general.general.company_info.telephone');
    $companyCell = core()->getConfigData('general.general.company_info.cell');
    $companyEmail = core()->getConfigData('general.general.company_info.email');

    $formatSafeDate = function ($value) {
        if (blank($value)) {
            return null;
        }

        try {
            $date = $value instanceof \Carbon\CarbonInterface ? $value : \Carbon\Carbon::parse($value);

            return $date->year > 1 ? $date->toDateString() : null;
        } catch (\Throwable $e) {
            return null;
        }
    };
    $defaultExpectedReceiveDate = old('expected_receive_date', $formatSafeDate($vendorQuote->last_delivery_date ?: $vendorQuote->first_delivery_date ?: $vendorQuote->jobOrder?->required_delivery_date));
    $prefillItems = old('items', $vendorQuote->items->map(fn ($item) => [
        'id' => $item->id,
        'requirement_id' => $item->requirement_id,
        'item' => $item->material_name,
        'material_name' => $item->material_name,
        'color' => $item->color ?: optional($item->requirement)->color_name ?: optional($item->requirement)->color_code ?: '-',
        'ordered_quantity' => $item->quantity,
        'unit' => $item->unit ?: optional($item->requirement)->unit ?: 'PCS',
        'price' => $item->unit_price,
        'vendor_id' => $item->vendor_id ?: collect((array) optional($item->requirement)->vendor_ids)->filter()->map(fn ($id) => (int) $id)->first() ?: $vendorQuote->organization_id,
        'expected_receive_date' => $formatSafeDate($item->expected_receive_date) ?: $defaultExpectedReceiveDate,
    ])->toArray());
    $initialVendorQuoteCharges = collect(old('charges', $chargeManager->extract($vendorQuote->loadMissing('additionalCharges'), 'vendor_quote')))->values()->all();
    $vendorAddressOptions = $vendors->mapWithKeys(fn ($vendor) => [
        $vendor->id => [
            'options' => $addressManager->getOptions($vendor),
            'default_billing_address' => $addressManager->getDefaultAddress($vendor, 'billing'),
            'default_shipping_address' => $addressManager->getDefaultAddress($vendor, 'shipping'),
        ],
    ]);
    $vendorOptions = $vendors->map(fn ($vendor) => [
        'id' => (int) $vendor->id,
        'name' => (string) $vendor->name,
    ])->values();
    $requirementVendorMap = collect($vendorQuote->jobOrder?->requirements ?? [])->mapWithKeys(function ($requirement) {
        $ids = collect((array) $requirement->vendor_ids)->filter()->map(fn ($id) => (int) $id)->values()->all();

        return [(int) $requirement->id => $ids];
    });

    foreach ($vendorQuote->items as $item) {
        if (! $item->requirement_id || ! $item->vendor_id) {
            continue;
        }

        $ids = collect($requirementVendorMap->get((int) $item->requirement_id, []))
            ->push((int) $item->vendor_id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $requirementVendorMap->put((int) $item->requirement_id, $ids);
    }
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
                <button type="submit" class="primary-button" data-loading-submit data-loading-text="Saving...">Save Vendor Quote</button>
            </div>

            <div class="document-form-panel rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <input type="hidden" name="job_order_id" value="{{ $vendorQuote->job_order_id }}">
                <input type="hidden" name="billing_address[key]" id="vendor-quote-billing-key" value="{{ old('billing_address.key', data_get($vendorQuote->billing_address, 'key')) }}">
                <input type="hidden" name="billing_address[label]" id="vendor-quote-billing-label" value="{{ old('billing_address.label', data_get($vendorQuote->billing_address, 'label')) }}">
                <input type="hidden" name="billing_address[type]" id="vendor-quote-billing-type" value="{{ old('billing_address.type', data_get($vendorQuote->billing_address, 'type', 'billing')) }}">
                <input type="hidden" name="billing_address[address]" id="vendor-quote-billing-address" value="{{ old('billing_address.address', data_get($vendorQuote->billing_address, 'address')) }}">
                <input type="hidden" name="shipping_address[key]" id="vendor-quote-shipping-key" value="{{ old('shipping_address.key', data_get($vendorQuote->shipping_address, 'key')) }}">
                <input type="hidden" name="shipping_address[label]" id="vendor-quote-shipping-label" value="{{ old('shipping_address.label', data_get($vendorQuote->shipping_address, 'label')) }}">
                <input type="hidden" name="shipping_address[type]" id="vendor-quote-shipping-type" value="{{ old('shipping_address.type', data_get($vendorQuote->shipping_address, 'type', 'shipping')) }}">
                <input type="hidden" name="shipping_address[address]" id="vendor-quote-shipping-address" value="{{ old('shipping_address.address', data_get($vendorQuote->shipping_address, 'address')) }}">

                <div class="grid gap-4" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
                    <div><label class="mb-1 block text-sm font-medium">Vendor Quote #</label><input type="text" name="vendor_quote_number" value="{{ old('vendor_quote_number', $vendorQuote->vendor_quote_number) }}" class="custom-input"></div>
                    <div><label class="mb-1 block text-sm font-medium">Status</label><select name="status" class="custom-select">@foreach (['draft', 'requested', 'received', 'selected', 'rejected', 'cancelled'] as $status)<option value="{{ $status }}" @selected(old('status', $vendorQuote->status) === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
                    <div><label class="mb-1 block text-sm font-medium">Issue Date</label><input type="date" name="issue_date" value="{{ old('issue_date', optional($vendorQuote->issue_date)->toDateString()) }}" class="custom-input"></div>
                </div>

                <div class="mt-4 grid gap-4" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
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
                            <th class="py-2 text-left">Color</th>
                            <th class="py-2 text-left">Ordered Qty</th>
                            <th class="py-2 text-left">Unit</th>
                            <th class="py-2 text-left">Vendor</th>
                            <th class="py-2 text-left">Price</th>
                            <th class="py-2 text-left">Amount</th>
                            <th class="py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prefillItems as $index => $item)
                            <tr>
                                <td class="py-2">
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] ?? '' }}">
                                    <input type="hidden" name="items[{{ $index }}][requirement_id]" value="{{ $item['requirement_id'] ?? '' }}">
                                    <input type="text" name="items[{{ $index }}][item]" value="{{ $item['item'] ?? $item['material_name'] ?? '' }}" class="custom-input">
                                    <input type="hidden" name="items[{{ $index }}][material_name]" value="{{ $item['material_name'] ?? $item['item'] ?? '' }}">
                                </td>
                                <td class="py-2"><input type="text" name="items[{{ $index }}][color]" value="{{ $item['color'] ?? '-' }}" class="custom-input"></td>
                                <td class="py-2"><input type="number" step="0.0001" min="0.0001" name="items[{{ $index }}][ordered_quantity]" value="{{ $item['ordered_quantity'] ?? $item['quantity'] ?? 1 }}" class="custom-input item-qty" oninput="window.calculateVendorQuoteTotals()"></td>
                                <td class="py-2"><input type="text" name="items[{{ $index }}][unit]" value="{{ $item['unit'] ?? 'PCS' }}" class="custom-input"></td>
                                <td class="py-2">
                                    <select name="items[{{ $index }}][vendor_id]" class="custom-select item-vendor">
                                        <option value="">Select vendor</option>
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" @selected((int) old('items.' . $index . '.vendor_id', $item['vendor_id'] ?? 0) === (int) $vendor->id)>{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-2"><input type="number" step="0.01" min="0" name="items[{{ $index }}][price]" value="{{ $item['price'] ?? $item['unit_price'] ?? 0 }}" class="custom-input item-price" oninput="window.calculateVendorQuoteTotals()"></td>
                                <td class="py-2 text-right align-middle"><span class="item-amount">0.00</span></td>
                                <td class="py-2 text-center"><button type="button" class="inline-flex items-center justify-center rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:text-white dark:hover:bg-gray-800" onclick="window.removeVendorQuoteRow(this)"><i class="icon-delete"></i></button></td>
                                <input type="hidden" name="items[{{ $index }}][expected_receive_date]" value="{{ $item['expected_receive_date'] ?? $defaultExpectedReceiveDate }}">
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
            window.vendorQuoteAddressBook = @json($vendorAddressOptions);
            window.vendorQuoteVendorOptions = @json($vendorOptions);
            window.requirementVendorMap = @json($requirementVendorMap);

            window.vendorQuoteVendorOptionsHtml = function (requirementId, selectedVendorId) {
                const reqId = Number(requirementId || 0);
                const allowedIds = Array.isArray(window.requirementVendorMap?.[reqId]) ? window.requirementVendorMap[reqId].map((id) => Number(id)) : [];
                const options = allowedIds.length
                    ? window.vendorQuoteVendorOptions.filter((vendor) => allowedIds.includes(Number(vendor.id)))
                    : window.vendorQuoteVendorOptions;

                const selectedIdNum = Number(selectedVendorId || 0);
                let html = '<option value="">Select vendor</option>';

                options.forEach((vendor) => {
                    const selectedAttr = selectedIdNum === Number(vendor.id) ? ' selected' : '';
                    html += `<option value="${vendor.id}"${selectedAttr}>${vendor.name}</option>`;
                });

                return html;
            };

            window.syncVendorQuoteRowVendor = function (row) {
                if (! row) return;

                const requirementInput = row.querySelector('input[name*="[requirement_id]"]');
                const vendorSelect = row.querySelector('.item-vendor');

                if (! vendorSelect) return;

                const requirementId = Number(requirementInput?.value || 0);
                const selectedVendorId = Number(vendorSelect.value || 0);
                vendorSelect.innerHTML = window.vendorQuoteVendorOptionsHtml(requirementId, selectedVendorId);
            };

            window.syncVendorQuoteAddressField = function (prefix, option) {
                document.getElementById(`vendor-quote-${prefix}-key`).value = option?.key || '';
                document.getElementById(`vendor-quote-${prefix}-label`).value = option?.label || '';
                document.getElementById(`vendor-quote-${prefix}-type`).value = option?.type || prefix;
                document.getElementById(`vendor-quote-${prefix}-address`).value = option?.address || '';
                const preview = document.getElementById(`vendor-quote-${prefix}-preview`);
                if (preview) preview.textContent = option?.address || 'No address selected.';
            };

            window.populateVendorQuoteAddresses = function () {
                const vendorId = document.getElementById('vendor-select')?.value || '';
                const addressMeta = window.vendorQuoteAddressBook?.[vendorId] || { options: [], default_billing_address: null, default_shipping_address: null };
                const billingSelect = document.getElementById('vendor-quote-billing-select');
                const shippingSelect = document.getElementById('vendor-quote-shipping-select');
                const billingKey = document.getElementById('vendor-quote-billing-key')?.value || '';
                const shippingKey = document.getElementById('vendor-quote-shipping-key')?.value || '';

                if (! billingSelect || ! shippingSelect) return;

                const optionHtml = ['<option value="">Select address</option>'].concat(
                    (addressMeta.options || []).map((option) => `<option value="${option.key}">${option.label}</option>`)
                ).join('');

                billingSelect.innerHTML = optionHtml;
                shippingSelect.innerHTML = optionHtml;

                const selectedBilling = (addressMeta.options || []).find((option) => String(option.key) === String(billingKey)) || addressMeta.default_billing_address;
                const selectedShipping = (addressMeta.options || []).find((option) => String(option.key) === String(shippingKey)) || addressMeta.default_shipping_address;

                billingSelect.value = selectedBilling?.key || '';
                shippingSelect.value = selectedShipping?.key || '';

                window.syncVendorQuoteAddressField('billing', selectedBilling);
                window.syncVendorQuoteAddressField('shipping', selectedShipping);
            };

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
            window.removeVendorQuoteRow = function (button) {
                const tbody = document.querySelector('#vendor-quote-items-table tbody');
                const row = button.closest('tr');
                if (! tbody || ! row) return;

                if (tbody.children.length <= 1) {
                    row.querySelectorAll('input').forEach((input) => {
                        if (input.type === 'hidden') input.value = '';
                        else if (input.type === 'number') input.value = input.name.includes('[ordered_quantity]') ? '1' : '0';
                        else input.value = input.name.includes('[unit]') ? 'PCS' : '';
                    });

                    const vendor = row.querySelector('.item-vendor');
                    if (vendor) vendor.value = '';

                    const amount = row.querySelector('.item-amount');
                    if (amount) amount.textContent = '0.00';

                    window.calculateVendorQuoteTotals();

                    return;
                }

                row.remove();
                window.calculateVendorQuoteTotals();
            };

            window.addVendorQuoteRow = function () {
                const tbody = document.querySelector('#vendor-quote-items-table tbody');
                const index = tbody.children.length;
                const expectedDate = document.querySelector('input[name="items[0][expected_receive_date]"]')?.value || '';

                tbody.insertAdjacentHTML('beforeend', `<tr><td class="py-2"><input type="hidden" name="items[${index}][id]" value=""><input type="hidden" name="items[${index}][requirement_id]" value=""><input type="text" name="items[${index}][item]" class="custom-input"><input type="hidden" name="items[${index}][material_name]" value=""></td><td class="py-2"><input type="text" name="items[${index}][color]" value="-" class="custom-input"></td><td class="py-2"><input type="number" step="0.0001" min="0.0001" name="items[${index}][ordered_quantity]" value="1" class="custom-input item-qty" oninput="window.calculateVendorQuoteTotals()"></td><td class="py-2"><input type="text" name="items[${index}][unit]" value="PCS" class="custom-input"></td><td class="py-2"><select name="items[${index}][vendor_id]" class="custom-select item-vendor">${window.vendorQuoteVendorOptionsHtml(0, 0)}</select></td><td class="py-2"><input type="number" step="0.01" min="0" name="items[${index}][price]" value="0" class="custom-input item-price" oninput="window.calculateVendorQuoteTotals()"></td><td class="py-2 text-right align-middle"><span class="item-amount">0.00</span></td><td class="py-2 text-center"><button type="button" class="inline-flex items-center justify-center rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:text-white dark:hover:bg-gray-800" onclick="window.removeVendorQuoteRow(this)"><i class="icon-delete"></i></button></td><input type="hidden" name="items[${index}][expected_receive_date]" value="${expectedDate}"></tr>`);

                window.calculateVendorQuoteTotals();
            };
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('#vendor-quote-items-table tbody tr').forEach((row) => window.syncVendorQuoteRowVendor(row));
                document.getElementById('vendor-select')?.addEventListener('change', window.populateVendorQuoteAddresses);
                document.getElementById('vendor-quote-billing-select')?.addEventListener('change', function () {
                    const vendorId = document.getElementById('vendor-select')?.value || '';
                    const options = window.vendorQuoteAddressBook?.[vendorId]?.options || [];
                    window.syncVendorQuoteAddressField('billing', options.find((option) => String(option.key) === String(this.value)));
                });
                document.getElementById('vendor-quote-shipping-select')?.addEventListener('change', function () {
                    const vendorId = document.getElementById('vendor-select')?.value || '';
                    const options = window.vendorQuoteAddressBook?.[vendorId]?.options || [];
                    window.syncVendorQuoteAddressField('shipping', options.find((option) => String(option.key) === String(this.value)));
                });
                window.populateVendorQuoteAddresses();
                window.renderVendorQuoteCharges();
                window.calculateVendorQuoteTotals();

                document.querySelector('form')?.addEventListener('submit', function () {
                    document.querySelectorAll('[data-loading-submit]').forEach((button) => {
                        if (button.dataset.loading === '1') {
                            return;
                        }

                        button.dataset.loading = '1';
                        button.disabled = true;
                        button.classList.add('opacity-70', 'cursor-not-allowed');
                        button.innerHTML = `<span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent align-[-2px]"></span><span class="ml-2">${button.dataset.loadingText || 'Saving...'}</span>`;
                    });
                });
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
