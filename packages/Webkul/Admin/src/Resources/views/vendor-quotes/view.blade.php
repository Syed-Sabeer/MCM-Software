<x-admin::layouts>
    <x-slot:title>{{ $vendorQuote->vendor_quote_number }}</x-slot>

    @php
        $chargeManager = app(\Webkul\Core\Support\DocumentChargeManager::class);
        $formatQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
        $formatChargeLabel = fn (array $charge) => ($charge['name'] ?? 'Charge')
            . (($charge['type'] ?? 'value') === 'percentage' ? ' (' . rtrim(rtrim(number_format((float) ($charge['value'] ?? 0), 2, '.', ''), '0'), '.') . '%)' : '');
        $companyName = core()->getConfigData('general.general.company_info.company_name');
        $companyAddress = core()->getConfigData('general.general.company_info.address');
        $companyPhone = core()->getConfigData('general.general.company_info.telephone');
        $companyCell = core()->getConfigData('general.general.company_info.cell');
        $companyEmail = core()->getConfigData('general.general.company_info.email');

        $vendor = $vendorQuote->organization;
        $createdPurchaseOrder = $vendorQuote->purchaseOrders->first();
        $charges = $chargeManager->extract($vendorQuote->loadMissing('additionalCharges'), 'vendor_quote');
        $selectedBillingAddress = trim((string) data_get($vendorQuote->billing_address, 'address'));
        $selectedShippingAddress = trim((string) data_get($vendorQuote->shipping_address, 'address'));
        $addressLines = fn ($value) => collect(preg_split("/\r\n|\n|\r/", (string) $value))->map(fn ($line) => trim((string) $line))->filter()->values()->all();
        $vendorLines = array_filter([
            $vendor?->name,
            ...($selectedBillingAddress !== '' ? $addressLines($selectedBillingAddress) : [
                $vendor?->billing_street ?: $vendor?->shipping_street,
                trim(implode(', ', array_filter([
                    $vendor?->billing_city ?: $vendor?->shipping_city,
                    $vendor?->billing_state ?: $vendor?->shipping_state,
                    $vendor?->billing_postcode ?: $vendor?->shipping_postcode,
                    $vendor?->billing_country ?: $vendor?->shipping_country,
                ]))),
            ]),
            $vendor?->phone ? 'Phone: ' . $vendor->phone : null,
            $vendor?->phone ? 'Cell: ' . $vendor->phone : null,
            data_get($vendor, 'email') ? 'Email: ' . data_get($vendor, 'email') : null,
        ]);

        $shipToLines = array_filter([
            $vendor?->name,
            ...($selectedShippingAddress !== '' ? $addressLines($selectedShippingAddress) : []),
        ]);
    @endphp

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xl font-bold dark:text-white">Vendor Quote {{ $vendorQuote->vendor_quote_number }}</div>
                    <div class="text-sm text-gray-500">Status: {{ ucfirst($vendorQuote->status) }}</div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.vendor_quotes.print', $vendorQuote->id) }}" class="secondary-button" data-loading-link data-loading-text="Opening...">Print</a>
                    @if ($createdPurchaseOrder)
                        <a href="{{ route('admin.purchase_orders.view', $createdPurchaseOrder->id) }}" class="secondary-button" data-loading-link data-loading-text="Opening...">PO Created</a>
                    @else
                        <a href="{{ route('admin.purchase_orders.create', ['vendor_quote_id' => $vendorQuote->id]) }}" class="secondary-button" data-loading-link data-loading-text="Opening...">Create Vendor PO</a>
                    @endif
                    <a href="{{ route('admin.vendor_quotes.edit', $vendorQuote->id) }}" class="primary-button" data-loading-link data-loading-text="Opening...">Edit</a>
                </div>
            </div>

            <div class="mt-4 grid gap-4 text-sm dark:text-white md:grid-cols-4">
                <div>
                    <strong>Vendor:</strong>
                    @if ($vendorQuote->organization_id && $vendorQuote->organization)
                        <a class="text-brandColor" href="{{ route('admin.contacts.organizations.view', $vendorQuote->organization_id) }}">{{ $vendorQuote->organization->name }}</a>
                    @else
                        -
                    @endif
                </div>
                <div>
                    <strong>Job Order:</strong>
                    @if ($vendorQuote->job_order_id && $vendorQuote->jobOrder)
                        <a class="text-brandColor" href="{{ route('admin.job_orders.view', $vendorQuote->job_order_id) }}">{{ $vendorQuote->jobOrder->job_order_number }}</a>
                    @else
                        -
                    @endif
                </div>
                <div><strong>Issue Date:</strong> {{ optional($vendorQuote->issue_date)->format('Y-m-d') ?: '-' }}</div>
                <div><strong>Payment Term:</strong> {{ $vendorQuote->payment_term ?: '-' }}</div>
                <div><strong>Shipping Method:</strong> {{ $vendorQuote->shipping_method ?: '-' }}</div>
                <div><strong>First Delivery:</strong> {{ optional($vendorQuote->first_delivery_date)->format('Y-m-d') ?: '-' }}</div>
                <div><strong>Last Delivery:</strong> {{ optional($vendorQuote->last_delivery_date)->format('Y-m-d') ?: '-' }}</div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Vendor</div>
                <div class="space-y-1 text-sm">
                    @if ($vendorQuote->organization_id && $vendorQuote->organization)
                        <div><a class="text-brandColor" href="{{ route('admin.contacts.organizations.view', $vendorQuote->organization_id) }}">{{ $vendorQuote->organization->name }}</a></div>
                    @endif

                    @forelse ($vendorLines as $line)
                        @continue($vendor?->name && $line === $vendor->name)
                        <div>{{ $line }}</div>
                    @empty
                        <div>-</div>
                    @endforelse
                </div>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Ship To</div>
                <div class="space-y-1 text-sm">
                    @forelse ($shipToLines as $line)
                        @continue($vendor?->name && $line === $vendor->name)
                        <div>{{ $line }}</div>
                    @empty
                        <div>{{ $companyName ?: 'Our Company' }}</div>
                        @if ($companyAddress)<div>{{ $companyAddress }}</div>@endif
                        @if ($companyPhone)<div>Phone: {{ $companyPhone }}</div>@endif
                        @if ($companyCell)<div>Cell: {{ $companyCell }}</div>@endif
                        @if ($companyEmail)<div>Email: {{ $companyEmail }}</div>@endif
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <table class="w-full text-sm dark:text-white">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="py-2 text-left">Material</th>
                        <th class="py-2 text-left">Color</th>
                        <th class="py-2 text-left">Qty</th>
                        <th class="py-2 text-left">Unit</th>
                        <th class="py-2 text-left">Price</th>
                        <th class="py-2 text-left">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vendorQuote->items as $item)
                        @php $unit = $item->unit ?: optional($item->requirement)->unit ?: 'PCS'; @endphp
                        <tr class="border-b dark:border-gray-800">
                            <td class="py-2">
                                @if ($item->product_id)
                                    <a class="text-brandColor" href="{{ route('admin.products.view', $item->product_id) }}">{{ $item->material_name }}</a>
                                @else
                                    {{ $item->material_name }}
                                @endif
                            </td>
                            <td class="py-2">{{ $item->color ?: optional($item->requirement)->color_name ?: optional($item->requirement)->color_code ?: '-' }}</td>
                            <td class="py-2">{{ $formatQty($item->quantity) }}</td>
                            <td class="py-2">{{ $unit }}</td>
                            <td class="py-2">{{ core()->formatBasePrice($item->unit_price, 2) }}</td>
                            <td class="py-2">{{ core()->formatBasePrice($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Totals</div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span>Sub Total</span><span>{{ number_format((float) $vendorQuote->subtotal, 2) }}</span></div>
                    @foreach ($charges as $charge)
                        <div class="flex justify-between"><span>{{ $formatChargeLabel($charge) }}</span><span>{{ number_format((float) $charge['amount'], 2) }}</span></div>
                    @endforeach
                    <div class="flex justify-between border-t pt-2 text-base font-bold"><span>Grand Total</span><span>{{ number_format((float) $vendorQuote->grand_total, 2) }}</span></div>
                </div>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Notes</div>
                <div class="text-sm whitespace-pre-line">{{ $vendorQuote->notes ?: '-' }}</div>
                <div class="mb-3 mt-4 text-base font-semibold">Terms & Conditions</div>
                <div class="text-sm whitespace-pre-line">{{ $vendorQuote->terms ?: '-' }}</div>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-loading-link]').forEach((link) => {
                    link.addEventListener('click', function () {
                        if (this.dataset.loading === '1') {
                            return;
                        }

                        this.dataset.loading = '1';
                        this.classList.add('opacity-70', 'pointer-events-none');
                        this.innerHTML = `<span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent align-[-2px]"></span><span class="ml-2">${this.dataset.loadingText || 'Opening...'}</span>`;
                    });
                });
            });
        </script>
    @endPushOnce
</x-admin::layouts>
