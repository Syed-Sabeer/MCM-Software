@php
    $chargeManager = app(\Webkul\Core\Support\DocumentChargeManager::class);
    $addressManager = app(\Webkul\Core\Support\DocumentAddressManager::class);
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
    $defaultExpectedReceiveDate = old('expected_receive_date', $formatSafeDate($vendorQuote?->last_delivery_date ?: $vendorQuote?->first_delivery_date ?: $jobOrder?->required_delivery_date));
    $prefillItems = old('items', $vendorQuote?->items?->map(fn ($item) => [
        'requirement_id' => $item->requirement_id,
        'item' => $item->material_name,
        'material_name' => $item->material_name,
        'ordered_quantity' => $item->quantity,
        'received_quantity' => 0,
        'pending_quantity' => $item->quantity,
        'unit' => $item->unit ?: optional($item->requirement)->unit ?: 'PCS',
        'price' => $item->unit_price,
        'vendor_id' => $vendorQuote?->organization_id,
    ])->toArray() ?? $jobOrder?->requirements?->map(fn ($req) => [
        'requirement_id' => $req->id,
        'item' => $req->material_name,
        'material_name' => $req->material_name,
        'ordered_quantity' => $req->balance_qty,
        'received_quantity' => 0,
        'pending_quantity' => $req->balance_qty,
        'unit' => $req->unit ?: 'PCS',
        'price' => 0,
        'vendor_id' => collect((array) $req->vendor_ids)->filter()->map(fn ($id) => (int) $id)->first(),
    ])->toArray() ?? [['item' => '', 'ordered_quantity' => 1, 'unit' => 'PCS', 'price' => 0]]);
    $initialPurchaseOrderCharges = collect(old('charges', $vendorQuote ? $chargeManager->extract($vendorQuote->loadMissing('additionalCharges'), 'vendor_quote') : []))->values()->all();
    $vendorOptions = $vendors->map(fn ($vendor) => [
        'id' => (int) $vendor->id,
        'name' => (string) $vendor->name,
    ])->values();
    $vendorAddressOptions = $vendors->mapWithKeys(fn ($vendor) => [
        $vendor->id => [
            'options' => $addressManager->getOptions($vendor),
            'default_billing_address' => $addressManager->getDefaultAddress($vendor, 'billing'),
            'default_shipping_address' => $addressManager->getDefaultAddress($vendor, 'shipping'),
        ],
    ]);
    $requirementVendorMap = collect($jobOrder?->requirements ?? [])->mapWithKeys(function ($requirement) {
        $ids = collect((array) $requirement->vendor_ids)->filter()->map(fn ($id) => (int) $id)->values()->all();

        return [(int) $requirement->id => $ids];
    });
@endphp
<x-admin::layouts>
    <x-slot:title>Create Vendor Purchase Order</x-slot>

    @include('admin::components.documents.form-styles')

    <x-admin::form :action="route('admin.purchase_orders.store')" method="POST" enctype="multipart/form-data">
        <div class="flex flex-col gap-4">
            <div class="document-form-toolbar flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <div class="text-xl font-bold dark:text-white">Create Vendor Purchase Order</div>
                    <div class="text-sm text-gray-500">{{ $vendorQuote?->vendor_quote_number ?: ($jobOrder?->job_order_number ?: 'Manual') }}</div>
                </div>
                <button class="primary-button">Save Purchase Order</button>
            </div>

            <div class="document-form-panel rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <input type="hidden" name="vendor_quote_id" value="{{ $vendorQuote?->id }}">
                <input type="hidden" name="job_order_id" value="{{ $vendorQuote?->job_order_id ?: $jobOrder?->id }}">
                <input type="hidden" name="expected_receive_date" value="{{ $defaultExpectedReceiveDate }}">
                <input type="hidden" name="billing_address[key]" id="po-billing-key" value="{{ old('billing_address.key', data_get($vendorQuote?->billing_address, 'key')) }}">
                <input type="hidden" name="billing_address[label]" id="po-billing-label" value="{{ old('billing_address.label', data_get($vendorQuote?->billing_address, 'label')) }}">
                <input type="hidden" name="billing_address[type]" id="po-billing-type" value="{{ old('billing_address.type', data_get($vendorQuote?->billing_address, 'type', 'billing')) }}">
                <input type="hidden" name="billing_address[address]" id="po-billing-address" value="{{ old('billing_address.address', data_get($vendorQuote?->billing_address, 'address')) }}">
                <input type="hidden" name="shipping_address[key]" id="po-shipping-key" value="{{ old('shipping_address.key', data_get($vendorQuote?->shipping_address, 'key')) }}">
                <input type="hidden" name="shipping_address[label]" id="po-shipping-label" value="{{ old('shipping_address.label', data_get($vendorQuote?->shipping_address, 'label')) }}">
                <input type="hidden" name="shipping_address[type]" id="po-shipping-type" value="{{ old('shipping_address.type', data_get($vendorQuote?->shipping_address, 'type', 'shipping')) }}">
                <input type="hidden" name="shipping_address[address]" id="po-shipping-address" value="{{ old('shipping_address.address', data_get($vendorQuote?->shipping_address, 'address')) }}">

                <div class="grid gap-4" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
                    <div>
                        <label class="mb-1 block text-sm font-medium">PO #</label>
                        <input type="text" name="po_number" value="{{ old('po_number', $nextPoNumber) }}" class="custom-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Vendor Quote #</label>
                        <input type="text" value="{{ $vendorQuote?->vendor_quote_number ?: '-' }}" class="custom-input" disabled>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Status</label>
                        <select name="status" class="custom-select">
                            @foreach (['draft', 'issued', 'partially_received', 'fully_received', 'closed', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected(old('status', 'draft') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 grid gap-4" style="grid-template-columns: repeat({{ $jobOrder && ! $vendorQuote ? 2 : 3 }}, minmax(0, 1fr));">
                    @if (! ($jobOrder && ! $vendorQuote))
                        <div>
                            <label class="mb-1 block text-sm font-medium">Vendor</label>
                            <select name="organization_id" class="custom-select" id="po-organization-select">
                                <option value="">Select vendor</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" @selected(old('organization_id', $vendorQuote?->organization_id) == $vendor->id)>{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <label class="mb-1 block text-sm font-medium">First Delivery</label>
                        <input type="date" name="completion_date" value="{{ old('completion_date', optional($vendorQuote?->first_delivery_date)->toDateString()) }}" class="custom-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Last Delivery</label>
                        <input type="date" name="last_delivery_date" value="{{ old('last_delivery_date', optional($vendorQuote?->last_delivery_date)->toDateString()) }}" class="custom-input">
                    </div>
                </div>

                <div class="mt-4 grid gap-4" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Payment Term</label>
                        <input type="text" name="payment_term" value="{{ old('payment_term', $vendorQuote?->payment_term) }}" class="custom-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Shipping Method</label>
                        <input type="text" name="shipping_method" value="{{ old('shipping_method', $vendorQuote?->shipping_method) }}" class="custom-input">
                    </div>
                </div>

                @if (! ($jobOrder && ! $vendorQuote))
                    <div class="mt-4 grid gap-4" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
                        <div>
                            <label class="mb-1 block text-sm font-medium">Vendor Address</label>
                            <select class="custom-select" id="po-billing-select"></select>
                            <div class="mt-2 whitespace-pre-line text-xs text-gray-500" id="po-billing-preview">No address selected.</div>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Ship To Address</label>
                            <select class="custom-select" id="po-shipping-select"></select>
                            <div class="mt-2 whitespace-pre-line text-xs text-gray-500" id="po-shipping-preview">No address selected.</div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="document-form-panel rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 flex items-center justify-between">
                    <div>
                        <div class="text-base font-semibold">Vendor PO Items</div>
                        <div class="text-sm text-gray-500">Add material requests for this vendor purchase order.</div>
                    </div>
                    <button type="button" class="secondary-button" onclick="window.addPoRow()">Add Row</button>
                </div>

                <table class="w-full text-sm" id="purchase-order-items-table">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="py-2 text-left">Material</th>
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
                                <td class="py-2"><input type="hidden" name="items[{{ $index }}][requirement_id]" value="{{ $item['requirement_id'] ?? '' }}"><input type="text" name="items[{{ $index }}][item]" value="{{ $item['item'] ?? '' }}" class="custom-input"><input type="hidden" name="items[{{ $index }}][material_name]" value="{{ $item['material_name'] ?? $item['item'] ?? '' }}"></td>
                                <td class="py-2"><input type="number" step="0.0001" min="0.0001" name="items[{{ $index }}][ordered_quantity]" value="{{ $item['ordered_quantity'] ?? 1 }}" class="custom-input item-qty" oninput="window.calculatePoTotals()"></td>
                                <td class="py-2"><input type="text" name="items[{{ $index }}][unit]" value="{{ $item['unit'] ?? 'PCS' }}" class="custom-input"></td>
                                <td class="py-2">
                                    <select name="items[{{ $index }}][vendor_id]" class="custom-select item-vendor">
                                        <option value="">Select vendor</option>
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" @selected((int) old('items.' . $index . '.vendor_id', $item['vendor_id'] ?? 0) === (int) $vendor->id)>{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-2"><input type="number" step="0.01" min="0" name="items[{{ $index }}][price]" value="{{ $item['price'] ?? 0 }}" class="custom-input item-price" oninput="window.calculatePoTotals()"></td>
                                <td class="py-2 text-right align-middle"><span class="item-amount">0.00</span></td>
                                <td class="py-2 text-center"><button type="button" class="inline-flex items-center justify-center rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:text-white dark:hover:bg-gray-800" onclick="window.removePoRow(this)"><i class="icon-delete"></i></button></td>
                                <input type="hidden" name="items[{{ $index }}][received_quantity]" value="0">
                                <input type="hidden" name="items[{{ $index }}][pending_quantity]" value="{{ $item['pending_quantity'] ?? $item['ordered_quantity'] ?? 1 }}">
                                <input type="hidden" name="items[{{ $index }}][expected_receive_date]" value="{{ $defaultExpectedReceiveDate }}">
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4 flex justify-end">
                    <div class="document-form-summary-box is-wide grid gap-4 rounded-lg bg-gray-100 p-5 text-sm dark:bg-gray-950 dark:text-white" style="max-width: 100%;">
                        <div class="flex items-center justify-between gap-x-5"><span>Sub Total</span><input type="hidden" name="sub_total" id="po-subtotal-input" value="0"><p class="text-base font-semibold" id="po-subtotal">0.00</p></div>
                        <div id="po-charges-container" class="grid gap-2"></div>
                        <div><button type="button" class="secondary-button" onclick="window.addPoCharge()">Add Charge</button></div>
                        <div class="flex items-center justify-between gap-x-5"><span>Additional Charges</span><input type="hidden" name="tax_amount" id="po-tax-amount-input" value="0"><input type="hidden" name="freight" id="po-freight-input" value="0"><p class="text-base font-semibold" id="po-charges-total">0.00</p></div>
                        <div class="flex items-center justify-between gap-x-5 border-t border-gray-200 pt-3 text-base font-semibold dark:border-gray-800"><span>Grand Total</span><input type="hidden" name="grand_total" id="po-grand-total-input" value="0"><p id="po-grand-total">0.00</p></div>
                    </div>
                </div>
            </div>

            <div class="document-form-panel rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium dark:text-white">Remarks</label>
                        <textarea name="notes" rows="4" class="custom-input">{{ old('notes', $vendorQuote?->notes) }}</textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium dark:text-white">Terms & Conditions</label>
                        <textarea name="terms" rows="4" class="custom-input">{{ old('terms', $vendorQuote?->terms) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </x-admin::form>

    @pushOnce('scripts')
        <script>
            window.poCharges = @json($initialPurchaseOrderCharges);
            window.poVendorOptions = @json($vendorOptions);
            window.poVendorAddressBook = @json($vendorAddressOptions);
            window.requirementVendorMap = @json($requirementVendorMap);

            window.syncPoAddressField = function (prefix, option) {
                const keyInput = document.getElementById(`po-${prefix}-key`);
                if (! keyInput) return;
                keyInput.value = option?.key || '';
                document.getElementById(`po-${prefix}-label`).value = option?.label || '';
                document.getElementById(`po-${prefix}-type`).value = option?.type || prefix;
                document.getElementById(`po-${prefix}-address`).value = option?.address || '';
                const preview = document.getElementById(`po-${prefix}-preview`);
                if (preview) preview.textContent = option?.address || 'No address selected.';
            };

            window.populatePoAddressOptions = function () {
                const orgSelect = document.getElementById('po-organization-select');
                const billingSelect = document.getElementById('po-billing-select');
                const shippingSelect = document.getElementById('po-shipping-select');

                if (! orgSelect || ! billingSelect || ! shippingSelect) {
                    return;
                }

                const vendorId = orgSelect.value || '';
                const addressMeta = window.poVendorAddressBook?.[vendorId] || { options: [], default_billing_address: null, default_shipping_address: null };
                const optionHtml = ['<option value="">Select address</option>'].concat(
                    (addressMeta.options || []).map((option) => `<option value="${option.key}">${option.label}</option>`)
                ).join('');

                billingSelect.innerHTML = optionHtml;
                shippingSelect.innerHTML = optionHtml;

                const selectedBilling = (addressMeta.options || []).find((option) => String(option.key) === String(document.getElementById('po-billing-key')?.value || '')) || addressMeta.default_billing_address;
                const selectedShipping = (addressMeta.options || []).find((option) => String(option.key) === String(document.getElementById('po-shipping-key')?.value || '')) || addressMeta.default_shipping_address;

                billingSelect.value = selectedBilling?.key || '';
                shippingSelect.value = selectedShipping?.key || '';

                window.syncPoAddressField('billing', selectedBilling);
                window.syncPoAddressField('shipping', selectedShipping);
            };

            window.vendorOptionsHtml = function (requirementId, selectedVendorId) {
                const reqId = Number(requirementId || 0);
                const allowedIds = Array.isArray(window.requirementVendorMap?.[reqId]) ? window.requirementVendorMap[reqId].map((id) => Number(id)) : [];
                const options = allowedIds.length
                    ? window.poVendorOptions.filter((vendor) => allowedIds.includes(Number(vendor.id)))
                    : window.poVendorOptions;

                const selectedIdNum = Number(selectedVendorId || 0);
                let html = '<option value="">Select vendor</option>';

                options.forEach((vendor) => {
                    const selectedAttr = selectedIdNum === Number(vendor.id) ? ' selected' : '';
                    html += `<option value="${vendor.id}"${selectedAttr}>${vendor.name}</option>`;
                });

                return html;
            };

            window.syncRowVendor = function (row) {
                if (!row) return;

                const requirementInput = row.querySelector('input[name*="[requirement_id]"]');
                const vendorSelect = row.querySelector('.item-vendor');

                if (!vendorSelect) return;

                const requirementId = Number(requirementInput?.value || 0);
                const selectedVendorId = Number(vendorSelect.value || 0);
                vendorSelect.innerHTML = window.vendorOptionsHtml(requirementId, selectedVendorId);
            };

            window.isTaxChargeName = function (name) {
                const normalized = String(name || '').trim().toLowerCase();
                return ['tax', 'tariff', 'tarrif', 'duty', 'vat', 'gst'].some((token) => normalized.includes(token));
            };

            window.poChargeAmount = function (charge, subtotal) {
                const value = parseFloat(charge?.value || 0);

                if ((charge?.type || 'value') === 'percentage') {
                    return subtotal * (value / 100);
                }

                return value;
            };

            window.renderPoCharges = function () {
                const container = document.getElementById('po-charges-container');
                if (! container) return;
                container.innerHTML = '';

                window.poCharges.forEach((charge, index) => {
                    const row = document.createElement('div');
                    row.className = 'document-summary-line';
                    row.style.cssText = 'display:grid;grid-template-columns:minmax(180px,1.2fr) 110px 110px 120px 36px;align-items:center;gap:10px;';
                    row.innerHTML = `
                        <div><input type="text" name="charges[${index}][name]" value="${charge.name ?? ''}" class="custom-input" placeholder="Charge name" oninput="window.updatePoCharge(${index}, 'name', this.value)"></div>
                        <div><select name="charges[${index}][type]" class="custom-select" onchange="window.updatePoCharge(${index}, 'type', this.value)"><option value="percentage" ${(charge.type || 'value') === 'percentage' ? 'selected' : ''}>Percentage</option><option value="value" ${(charge.type || 'value') === 'value' ? 'selected' : ''}>Value</option></select></div>
                        <div><input type="number" min="0" step="0.01" name="charges[${index}][value]" value="${charge.value ?? 0}" class="custom-input" oninput="window.updatePoCharge(${index}, 'value', this.value)"></div>
                        <div class="text-right font-medium"><span id="po-charge-amount-${index}">0.00</span></div>
                        <button type="button" class="inline-flex items-center justify-center rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:text-white dark:hover:bg-gray-800" onclick="window.removePoCharge(${index})"><i class="icon-delete"></i></button>
                    `;
                    container.appendChild(row);
                });
            };

            window.addPoCharge = function () {
                window.poCharges.push({ name: '', type: 'percentage', value: 0 });
                window.renderPoCharges();
                window.calculatePoTotals();
            };

            window.updatePoCharge = function (index, field, value) {
                if (! window.poCharges[index]) return;
                window.poCharges[index][field] = value;
                window.calculatePoTotals();
            };

            window.removePoCharge = function (index) {
                window.poCharges.splice(index, 1);
                window.renderPoCharges();
                window.calculatePoTotals();
            };

            window.calculatePoTotals = function () {
                const rows = document.querySelectorAll('#purchase-order-items-table tbody tr');
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

                window.poCharges.forEach((charge, index) => {
                    const amount = window.poChargeAmount(charge, subtotal);
                    const amountNode = document.getElementById(`po-charge-amount-${index}`);
                    if (amountNode) amountNode.textContent = amount.toFixed(2);
                    if (window.isTaxChargeName(charge.name)) taxCharges += amount;
                    else otherCharges += amount;
                });

                const chargeTotal = taxCharges + otherCharges;
                const grandTotal = subtotal + chargeTotal;
                document.getElementById('po-subtotal').textContent = subtotal.toFixed(2);
                document.getElementById('po-subtotal-input').value = subtotal.toFixed(4);
                document.getElementById('po-tax-amount-input').value = taxCharges.toFixed(4);
                document.getElementById('po-freight-input').value = otherCharges.toFixed(4);
                document.getElementById('po-charges-total').textContent = chargeTotal.toFixed(2);
                document.getElementById('po-grand-total').textContent = grandTotal.toFixed(2);
                document.getElementById('po-grand-total-input').value = grandTotal.toFixed(4);
            };
            window.removePoRow = function (button) { const tbody = document.querySelector('#purchase-order-items-table tbody'); const row = button.closest('tr'); if (!tbody || !row) return; if (tbody.children.length <= 1) { row.querySelectorAll('input').forEach((input) => { if (input.type === 'hidden') input.value = ''; else if (input.type === 'number') input.value = input.name.includes('[ordered_quantity]') ? '1' : '0'; else input.value = input.name.includes('[unit]') ? 'PCS' : ''; }); const amount = row.querySelector('.item-amount'); if (amount) amount.textContent = '0.00'; window.calculatePoTotals(); return; } row.remove(); window.calculatePoTotals(); };
            window.addPoRow = function () { const tbody = document.querySelector('#purchase-order-items-table tbody'); const index = tbody.children.length; const expectedDate = document.querySelector('input[name="expected_receive_date"]')?.value || ''; tbody.insertAdjacentHTML('beforeend', `<tr><td class="py-2"><input type="hidden" name="items[${index}][requirement_id]" value=""><input type="text" name="items[${index}][item]" class="custom-input"><input type="hidden" name="items[${index}][material_name]" value=""></td><td class="py-2"><input type="number" step="0.0001" min="0.0001" name="items[${index}][ordered_quantity]" value="1" class="custom-input item-qty" oninput="window.calculatePoTotals()"></td><td class="py-2"><input type="text" name="items[${index}][unit]" value="PCS" class="custom-input"></td><td class="py-2"><select name="items[${index}][vendor_id]" class="custom-select item-vendor">${window.vendorOptionsHtml(0, 0)}</select></td><td class="py-2"><input type="number" step="0.01" min="0" name="items[${index}][price]" value="0" class="custom-input item-price" oninput="window.calculatePoTotals()"></td><td class="py-2 text-right align-middle"><span class="item-amount">0.00</span></td><td class="py-2 text-center"><button type="button" class="inline-flex items-center justify-center rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:text-white dark:hover:bg-gray-800" onclick="window.removePoRow(this)"><i class="icon-delete"></i></button></td><input type="hidden" name="items[${index}][received_quantity]" value="0"><input type="hidden" name="items[${index}][pending_quantity]" value="1"><input type="hidden" name="items[${index}][expected_receive_date]" value="${expectedDate}"></tr>`); window.calculatePoTotals(); };
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('po-organization-select')?.addEventListener('change', window.populatePoAddressOptions);
                document.getElementById('po-billing-select')?.addEventListener('change', function () {
                    const vendorId = document.getElementById('po-organization-select')?.value || '';
                    const options = window.poVendorAddressBook?.[vendorId]?.options || [];
                    window.syncPoAddressField('billing', options.find((option) => String(option.key) === String(this.value)));
                });
                document.getElementById('po-shipping-select')?.addEventListener('change', function () {
                    const vendorId = document.getElementById('po-organization-select')?.value || '';
                    const options = window.poVendorAddressBook?.[vendorId]?.options || [];
                    window.syncPoAddressField('shipping', options.find((option) => String(option.key) === String(this.value)));
                });
                window.populatePoAddressOptions();
                document.querySelectorAll('#purchase-order-items-table tbody tr').forEach((row) => window.syncRowVendor(row));
                window.renderPoCharges();
                window.calculatePoTotals();
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
