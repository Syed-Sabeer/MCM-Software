<x-admin::layouts>
    <x-slot:title>Quote {{ $quote->quote_number }}</x-slot>

    @php
        $chargeManager = app(\Webkul\Core\Support\DocumentChargeManager::class);
        $organization = $quote->organization;
        $salesPerson = $quote->user;
        $selectedBillingAddress = trim((string) data_get($quote->billing_address, 'address'));
        $selectedShippingAddress = trim((string) data_get($quote->shipping_address, 'address'));
        $addressLines = fn ($value) => collect(preg_split("/\r\n|\n|\r/", (string) $value))->map(fn ($line) => trim((string) $line))->filter()->values()->all();

        $billTo = array_filter([
            $organization?->name,
            ...($selectedBillingAddress !== '' ? $addressLines($selectedBillingAddress) : [
                $organization?->billing_street ?: $organization?->shipping_street,
                trim(implode(', ', array_filter([
                    $organization?->billing_city ?: $organization?->shipping_city,
                    $organization?->billing_state ?: $organization?->shipping_state,
                    $organization?->billing_postcode ?: $organization?->shipping_postcode,
                    $organization?->billing_country ?: $organization?->shipping_country,
                ]))),
            ]),
            $organization?->phone ? 'Phone: ' . $organization->phone : null,
            data_get($organization, 'email') ? 'Email: ' . data_get($organization, 'email') : null,
        ]);

        $shipTo = array_filter([
            $organization?->name,
            ...($selectedShippingAddress !== '' ? $addressLines($selectedShippingAddress) : [
                $organization?->shipping_street ?: $organization?->billing_street,
                trim(implode(', ', array_filter([
                    $organization?->shipping_city ?: $organization?->billing_city,
                    $organization?->shipping_state ?: $organization?->billing_state,
                    $organization?->shipping_postcode ?: $organization?->billing_postcode,
                    $organization?->shipping_country ?: $organization?->billing_country,
                ]))),
            ]),
            $organization?->phone ? 'Phone: ' . $organization->phone : null,
        ]);

        $formatQty = fn ($value) => number_format((float) $value, 0, '.', '');
        $formatAmount = fn ($value) => number_format((float) $value, 3, '.', ',');
        $formatChargeLabel = fn (array $charge) => ($charge['name'] ?? 'Charge')
            . (($charge['type'] ?? 'value') === 'percentage' ? ' (' . rtrim(rtrim(number_format((float) ($charge['value'] ?? 0), 2, '.', ''), '0'), '.') . '%)' : '');

        $itemsTotal = (float) ($quote->sub_total ?: 0);
        $charges = $chargeManager->extract($quote->loadMissing('additionalCharges'), 'quote');
        $chargesTotal = collect($charges)->sum('amount');
        $grandTotal = (float) ($quote->grand_total ?: 0);
    @endphp

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold dark:text-white">Quote {{ $quote->quote_number }}</h1>
                    <p class="text-sm text-gray-500">Commercial quote for
                        @if ($quote->organization_id && $quote->organization)
                            <a class="text-brandColor" href="{{ route('admin.contacts.organizations.view', $quote->organization_id) }}">{{ $quote->organization->name }}</a>
                        @else
                            -
                        @endif
                    </p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.quotes.print', $quote->id) }}" class="secondary-button">Print</a>
                    <a href="{{ route('admin.proforma_invoices.create', ['quote_id' => $quote->id]) }}" class="secondary-button">Create Proforma</a>
                    <a href="{{ route('admin.quotes.edit', $quote->id) }}" class="primary-button">Edit</a>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Document Summary</div>
                <div class="grid gap-3 text-sm md:grid-cols-2">
                    <div><strong>Quote #:</strong> {{ $quote->quote_number ?: '-' }}</div>
                    <div><strong>Status:</strong> {{ ucfirst((string) ($quote->status ?: 'draft')) }}</div>
                    <div><strong>Quote Date:</strong> {{ optional($quote->quote_date)->format('Y-m-d') ?: '-' }}</div>
                    <div><strong>Expiry Date:</strong> {{ optional($quote->expired_at)->format('Y-m-d') ?: '-' }}</div>
                    <div><strong>Sales Owner:</strong> {{ $salesPerson?->name ?: '-' }}</div>
                    <div><strong>Payment Term:</strong> {{ $quote->payment_term ?: '-' }}</div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Commercial Details</div>
                <div class="grid gap-3 text-sm md:grid-cols-2">
                    <div><strong>Shipping Method:</strong> {{ $quote->shipping_method ?: '-' }}</div>
                    <div><strong>Production Time:</strong> {{ $quote->production_time ?: '-' }}</div>
                    <div><strong>Transit Time:</strong> {{ $quote->transit_time ?: '-' }}</div>
                    <div><strong>ETD:</strong> {{ optional($quote->etd)->format('Y-m-d') ?: '-' }}</div>
                    <div><strong>ETA:</strong> {{ optional($quote->eta)->format('Y-m-d') ?: '-' }}</div>
                    <div><strong>Customer Contact:</strong> {{ optional($quote->person)->name ?: '-' }}</div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Bill To</div>
                <div class="space-y-1 text-sm">
                    @if ($organization?->id)
                        <div><a class="text-brandColor" href="{{ route('admin.contacts.organizations.view', $organization->id) }}">{{ $organization->name }}</a></div>
                    @endif

                    @foreach ($billTo as $line)
                        @continue($organization?->name && $line === $organization->name)
                        <div>{{ $line }}</div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Ship To</div>
                <div class="space-y-1 text-sm">
                    @if ($organization?->id)
                        <div><a class="text-brandColor" href="{{ route('admin.contacts.organizations.view', $organization->id) }}">{{ $organization->name }}</a></div>
                    @endif

                    @foreach ($shipTo as $line)
                        @continue($organization?->name && $line === $organization->name)
                        <div>{{ $line }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-3 text-base font-semibold dark:text-white">Quote Items</div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm dark:text-white">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="py-2 text-left">Image</th>
                            <th class="py-2 text-left">Item Code</th>
                            <th class="py-2 text-left">Description</th>
                            <th class="py-2 text-left">Color</th>
                            <th class="py-2 text-right">Qty</th>
                            <th class="py-2 text-right">Rate</th>
                            <th class="py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quote->items as $item)
                            @php
                                $qty = (float) ($item->quantity ?: $item->qty ?: 0);
                                $rate = (float) ($item->price ?: $item->unit_price ?: 0);
                                $amount = $qty * $rate;
                            @endphp

                            <tr class="border-b dark:border-gray-800">
                                <td class="py-2">
                                    @if ($item->preview_image)
                                        <img src="{{ $item->preview_image }}" alt="preview" class="h-12 w-12 rounded border border-gray-200 object-cover dark:border-gray-700">
                                    @else
                                        <span class="text-xs text-gray-500">No image</span>
                                    @endif
                                </td>
                                <td class="py-2 font-medium">
                                    @if ($item->product_id)
                                        <a class="text-brandColor" href="{{ route('admin.products.view', $item->product_id) }}">{{ $item->item_code ?: $item->sku ?: '-' }}</a>
                                    @else
                                        {{ $item->item_code ?: $item->sku ?: '-' }}
                                    @endif
                                </td>
                                <td class="py-2">{{ $item->item_name ?: $item->name ?: '-' }}</td>
                                <td class="py-2">{{ $item->color_variant_name ?: '-' }}</td>
                                <td class="py-2 text-right">{{ $formatQty($qty) }}</td>
                                <td class="py-2 text-right">{{ $formatAmount($rate) }}</td>
                                <td class="py-2 text-right font-medium">{{ $formatAmount($amount) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 text-center text-gray-500">No line items available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Totals</div>
                <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Items Total</span><span>{{ $formatAmount($itemsTotal) }}</span></div>
                        @foreach ($charges as $charge)
                            <div class="flex justify-between"><span>{{ $formatChargeLabel($charge) }}</span><span>{{ $formatAmount($charge['amount']) }}</span></div>
                        @endforeach
                        <div class="flex justify-between"><span>Additional Charges</span><span>{{ $formatAmount($chargesTotal) }}</span></div>
                        <div class="flex justify-between border-t pt-2 text-base font-bold"><span>Grand Total</span><span>{{ $formatAmount($grandTotal) }}</span></div>
                    </div>
                </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Linked Proformas</div>
                <div class="space-y-2 text-sm">
                    @forelse ($quote->proformaInvoices as $proforma)
                        <div class="rounded border border-gray-200 p-3 dark:border-gray-700">
                            <a class="font-semibold text-brandColor" href="{{ route('admin.proforma_invoices.view', $proforma->id) }}">{{ $proforma->proforma_number ?: ('PI-' . $proforma->id) }}</a>
                            <div class="text-gray-500">Status: {{ ucfirst((string) ($proforma->status ?: 'draft')) }}</div>
                        </div>
                    @empty
                        <div class="text-gray-500">No proformas linked yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Remarks</div>
                <div class="text-sm whitespace-pre-line">{{ $quote->notes ?: $quote->description ?: '-' }}</div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Terms & Conditions</div>
                <div class="text-sm whitespace-pre-line">{{ $quote->terms ?: '-' }}</div>
            </div>
        </div>
    </div>
</x-admin::layouts>
