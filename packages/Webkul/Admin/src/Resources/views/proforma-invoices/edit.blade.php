@php
    use Webkul\Product\Models\Product;
    $chargeManager = app(\Webkul\Core\Support\DocumentChargeManager::class);
    $addressManager = app(\Webkul\Core\Support\DocumentAddressManager::class);

    $quoteModels = \Webkul\Quote\Models\Quote::query()
        ->with(['items', 'organization', 'person', 'user', 'additionalCharges'])
        ->get();

    $productIds = collect($initialItems ?? [])->pluck('product_id')->filter()->map(fn ($id) => (int) $id)->unique();
    $quoteProductIds = $quoteModels->pluck('items')->flatten()->pluck('product_id')->filter()->map(fn ($id) => (int) $id)->unique();
    $productIds = $productIds->merge($quoteProductIds)->unique()->values();

    $productPayload = Product::query()
        ->with(['colors', 'otherImages'])
        ->whereIn('id', $productIds)
        ->get()
        ->mapWithKeys(function ($product) {
            $coverImageUrl = $product->cover_image ? secure_url('public/storage/'.$product->cover_image) : null;
            $colorImages = [];

            foreach ($product->otherImages as $image) {
                if (! $image->color_id || empty($image->path)) {
                    continue;
                }

                $colorImages[(string) $image->color_id] = secure_url('public/storage/'.$image->path);
            }

            return [
                $product->id => [
                    'cover_image_url' => $coverImageUrl,
                    'colors'          => $product->colors->map(fn ($color) => [
                        'id'         => $color->id,
                        'name'       => $color->name,
                        'color_code' => $color->color_code,
                    ])->values()->toArray(),
                    'color_images'    => $colorImages,
                ],
            ];
        });

    $quotes = $quoteModels->map(function ($quoteRow) use ($productPayload, $chargeManager, $addressManager) {
        return [
            'id' => $quoteRow->id,
            'quote_number' => $quoteRow->quote_number,
            'quote_number_display' => 'Q' . ltrim((string) $quoteRow->quote_number, 'Q'),
            'quote_date' => optional($quoteRow->quote_date)->format('Y-m-d'),
            'organization_id' => $quoteRow->organization_id,
            'organization_name' => optional($quoteRow->organization)->name,
            'person_id' => $quoteRow->person_id,
            'sales_owner_id' => $quoteRow->user_id,
            'sales_owner_name' => optional($quoteRow->user)->name,
            'shipping_method' => $quoteRow->shipping_method,
            'production_time' => $quoteRow->production_time,
            'transit_time' => $quoteRow->transit_time,
            'etd' => optional($quoteRow->etd)->format('Y-m-d'),
            'eta' => optional($quoteRow->eta)->format('Y-m-d'),
            'charges' => $chargeManager->extract($quoteRow, 'quote'),
            'notes' => $quoteRow->notes,
            'terms' => $quoteRow->terms,
            'payment_term' => $quoteRow->payment_term ?? '',
            'billing_address' => $quoteRow->billing_address ?: ['address' => ''],
            'shipping_address' => $quoteRow->shipping_address ?: ['address' => ''],
            'address_options' => $addressManager->getOptions($quoteRow->organization),
            'default_billing_address' => $addressManager->getDefaultAddress($quoteRow->organization, 'billing'),
            'default_shipping_address' => $addressManager->getDefaultAddress($quoteRow->organization, 'shipping'),
            'items' => $quoteRow->items->map(function ($item) use ($productPayload) {
                $productMeta = $productPayload->get((int) ($item->product_id ?? 0), [
                    'cover_image_url' => null,
                    'colors'          => [],
                    'color_images'    => [],
                ]);
                $selectedColorId = (string) ($item->color_variant_id ?? '');
                $previewImage = $item->preview_image;
                if (! $previewImage && $selectedColorId && isset($productMeta['color_images'][$selectedColorId])) {
                    $previewImage = $productMeta['color_images'][$selectedColorId];
                }
                if (! $previewImage) {
                    $previewImage = $productMeta['cover_image_url'] ?? null;
                }
                return [
                    'id'                  => null,
                    'product_id'          => $item->product_id,
                    'name'                => $item->item_name ?: $item->name,
                    'item_name'           => $item->item_name ?: $item->name,
                    'item_code'           => $item->item_code ?: $item->sku,
                    'quantity'            => (int) round((float) ($item->qty ?: $item->quantity ?: 1)),
                    'price'               => number_format((float) ($item->unit_price ?: $item->price ?: 0), 2, '.', ''),
                    'discount_amount'     => number_format((float) ($item->discount_amount ?: 0), 2, '.', ''),
                    'tax_amount'          => number_format((float) ($item->tax_amount ?: 0), 2, '.', ''),
                    'available_colors'    => $productMeta['colors'] ?? [],
                    'color_images'        => $productMeta['color_images'] ?? [],
                    'selected_color_id'   => $selectedColorId,
                    'selected_color_name' => $item->color_variant_name ?? '',
                    'cover_image_url'     => $productMeta['cover_image_url'] ?? '',
                    'preview_image'       => $previewImage,
                ];
            })->values()->all(),
        ];
    })->values();

    $rawItems = $proformaInvoice->items->map(function ($item) use ($productPayload) {
        $productMeta = $productPayload->get((int) ($item->product_id ?? 0), [
            'cover_image_url' => null,
            'colors'          => [],
            'color_images'    => [],
        ]);
        $selectedColorId = (string) ($item->color_variant_id ?? '');
        $previewImage = $item->preview_image;
        if (! $previewImage && $selectedColorId && isset($productMeta['color_images'][$selectedColorId])) {
            $previewImage = $productMeta['color_images'][$selectedColorId];
        }
        if (! $previewImage) {
            $previewImage = $productMeta['cover_image_url'] ?? null;
        }
        return [
            'id'                  => $item->id,
            'product_id'          => $item->product_id,
            'name'                => $item->item_name,
            'item_name'           => $item->item_name,
            'item_code'           => $item->item_code,
            'quantity'            => (int) round((float) ($item->qty ?: 1)),
            'price'               => number_format((float) ($item->unit_price ?: 0), 2, '.', ''),
            'discount_amount'     => number_format((float) ($item->discount_amount ?: 0), 2, '.', ''),
            'tax_amount'          => number_format((float) ($item->tax_amount ?: 0), 2, '.', ''),
            'available_colors'    => $productMeta['colors'] ?? [],
            'color_images'        => $productMeta['color_images'] ?? [],
            'selected_color_id'   => $selectedColorId,
            'selected_color_name' => $item->color_variant_name ?? '',
            'cover_image_url'     => $productMeta['cover_image_url'] ?? '',
            'preview_image'       => $previewImage,
        ];
    })->values();

    $initialForm = [
        'proforma_number' => $proformaInvoice->proforma_number,
        'quote_id' => $proformaInvoice->quote_id,
        'quote_number_display' => $proformaInvoice->quote_id ? ('Q' . ltrim((string) optional($proformaInvoice->quote)->quote_number, 'Q')) : '',
        'organization_id' => $proformaInvoice->organization_id,
        'organization_name' => optional($proformaInvoice->organization)->name,
        'person_id' => $proformaInvoice->person_id,
        'sales_owner_id' => $proformaInvoice->sales_owner_id,
        'sales_owner_name' => optional($proformaInvoice->salesOwner)->name,
        'issue_date' => optional($proformaInvoice->issue_date)->format('Y-m-d'),
        'status' => $proformaInvoice->status,
        'shipping_method' => old('shipping_method', optional($proformaInvoice->quote)->shipping_method),
        'production_time' => old('production_time', optional($proformaInvoice->quote)->production_time),
        'transit_time' => old('transit_time', optional($proformaInvoice->quote)->transit_time),
        'etd' => old('etd', optional(optional($proformaInvoice->quote)->etd)->format('Y-m-d')),
        'eta' => old('eta', optional(optional($proformaInvoice->quote)->eta)->format('Y-m-d')),
        'charges' => old('charges', $chargeManager->extract($proformaInvoice->loadMissing('additionalCharges'), 'proforma')),
        'notes' => $proformaInvoice->notes,
        'terms' => $proformaInvoice->terms,
        'payment_term' => $proformaInvoice->payment_term,
        'billing_address' => $proformaInvoice->billing_address ?: ['address' => ''],
        'shipping_address' => $proformaInvoice->shipping_address ?: ['address' => ''],
        'items' => $rawItems->all(),
    ];
@endphp

<x-admin::layouts>

    <x-slot:title>
        Edit Proforma Invoice
    </x-slot>

    @include('admin::components.documents.form-styles')

    <style>
        .quote-create-form-panel .document-form-row-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }

        .quote-create-form-panel .document-form-row-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .quote-create-form-panel .document-form-row-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        }

        .quote-create-form-panel .document-form-row-6 {
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
        }

        .quote-create-form-panel .quote-meta-block {
            margin-top: 16px;
        }
    </style>

    <x-admin::form :action="route('admin.proforma_invoices.update', $proformaInvoice->id)" method="PUT">
        <div class="flex flex-col gap-4">
            <div class="document-form-toolbar flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="text-xl font-bold dark:text-white">Edit Proforma Invoice</div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.proforma_invoices.print', $proformaInvoice->id) }}" class="secondary-button">Print</a>
                    <a href="{{ route('admin.proforma_invoices.view', $proformaInvoice->id) }}" class="secondary-button">View</a>
                    <button type="submit" class="primary-button">Update Proforma</button>
                </div>
            </div>

            <v-proforma :initial-form='@json($initialForm)' :quotes='@json($quotes)' :errors="errors">
                <x-admin::shimmer.quotes />
            </v-proforma>
        </div>
    </x-admin::form>

    <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div class="document-form-panel rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-3 text-lg font-semibold dark:text-white">Payment Summary</h3>
            <div class="space-y-2 text-sm dark:text-white">
                <div class="flex justify-between"><span>Grand Total</span><span>{{ core()->formatBasePrice($proformaInvoice->grand_total) }}</span></div>
                <div class="flex justify-between"><span>Received Amount</span><span>{{ core()->formatBasePrice($proformaInvoice->received_amount) }}</span></div>
                <div class="flex justify-between font-bold"><span>Remaining Amount</span><span>{{ core()->formatBasePrice($proformaInvoice->remaining_amount) }}</span></div>
            </div>
        </div>

        <div class="document-form-panel rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-3 text-lg font-semibold dark:text-white">Add Advance Receipt</h3>
            <x-admin::form :action="route('admin.proforma_invoices.receipts.store', $proformaInvoice->id)" method="POST">
                <div class="grid gap-3">
                    <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label class="required">Payment Date</x-admin::form.control-group.label><input type="date" name="payment_date" value="{{ now()->toDateString() }}" class="custom-input" required></x-admin::form.control-group>
                    <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label class="required">Amount</x-admin::form.control-group.label><x-admin::form.control-group.control type="number" name="amount" step="0.0001" min="0.0001" rules="required" /></x-admin::form.control-group>
                    <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Payment Method</x-admin::form.control-group.label><x-admin::form.control-group.control type="text" name="payment_method" /></x-admin::form.control-group>
                    <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Reference No</x-admin::form.control-group.label><x-admin::form.control-group.control type="text" name="reference_no" /></x-admin::form.control-group>
                    <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Notes</x-admin::form.control-group.label><x-admin::form.control-group.control type="textarea" name="notes" /></x-admin::form.control-group>
                    <button type="submit" class="primary-button w-max">Add Receipt</button>
                </div>
            </x-admin::form>
        </div>
    </div>

    <div class="mt-4 document-form-panel rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-3 text-lg font-semibold dark:text-white">Receipt History</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-200 dark:border-gray-700"><tr><th class="px-2 py-2">Receipt #</th><th class="px-2 py-2">Date</th><th class="px-2 py-2">Amount</th><th class="px-2 py-2">Method</th><th class="px-2 py-2">Reference</th><th class="px-2 py-2">Received By</th><th class="px-2 py-2">Action</th></tr></thead>
                <tbody>
                    @forelse ($proformaInvoice->receipts as $receipt)
                        <tr class="border-b border-gray-200 dark:border-gray-700"><td class="px-2 py-2">{{ $receipt->receipt_number ?: '-' }}</td><td class="px-2 py-2">{{ optional($receipt->payment_date)->format('Y-m-d') }}</td><td class="px-2 py-2">{{ core()->formatBasePrice($receipt->amount) }}</td><td class="px-2 py-2">{{ $receipt->payment_method ?: '-' }}</td><td class="px-2 py-2">{{ $receipt->reference_no ?: '-' }}</td><td class="px-2 py-2">{{ optional($receipt->receivedBy)->name ?: '-' }}</td><td class="px-2 py-2"><x-admin::form :action="route('admin.proforma_invoices.receipts.delete', [$proformaInvoice->id, $receipt->id])" method="DELETE"><button class="text-red-600">Delete</button></x-admin::form></td></tr>
                    @empty
                        <tr><td class="px-2 py-3 text-gray-500" colspan="7">No receipts recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('admin::partials.document-status-picker', [
        'renderControl' => false,
    ])

    @pushOnce('scripts')
        <script type="text/x-template" id="v-proforma-template">
            <div class="document-form-panel quote-create-form-panel box-shadow flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <input type="hidden" name="organization_id" :value="form.organization_id || ''">
                <input type="hidden" name="person_id" :value="form.person_id || ''">
                <input type="hidden" name="sales_owner_id" :value="form.sales_owner_id || ''">
                <input type="hidden" name="billing_address[key]" :value="form.billing_address?.key || ''">
                <input type="hidden" name="billing_address[label]" :value="form.billing_address?.label || ''">
                <input type="hidden" name="billing_address[type]" :value="form.billing_address?.type || ''">
                <input type="hidden" name="billing_address[address]" :value="form.billing_address?.address || ''">
                <input type="hidden" name="shipping_address[key]" :value="form.shipping_address?.key || ''">
                <input type="hidden" name="shipping_address[label]" :value="form.shipping_address?.label || ''">
                <input type="hidden" name="shipping_address[type]" :value="form.shipping_address?.type || ''">
                <input type="hidden" name="shipping_address[address]" :value="form.shipping_address?.address || ''">

                <div class="mt-4 space-y-4">
                    <div class="document-form-row-4 quote-meta-block" style="display: grid !important; grid-template-columns: repeat(4, minmax(0, 1fr)) !important; gap: 16px;">
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label>Proforma #</x-admin::form.control-group.label>
                            <input type="text" name="proforma_number" v-model="form.proforma_number" class="custom-input">
                        </x-admin::form.control-group>

                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label class="required">Issue Date</x-admin::form.control-group.label>
                            <input type="date" name="issue_date" v-model="form.issue_date" class="custom-input" required>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group class="!mb-0">
                            @include('admin::partials.document-status-picker', [
                                'type' => 'proforma_invoice',
                                'name' => 'status',
                                'selected' => $formState['status'] ?? $proformaInvoice->status,
                                'selectAttributes' => 'v-model="form.status"',
                                'includeManager' => false,
                            ])
                        </x-admin::form.control-group>

                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Quote #</x-admin::form.control-group.label><input type="text" class="custom-input" :value="form.quote_number_display || ''" disabled></x-admin::form.control-group>
                    </div>

                    <div class="document-form-row-2 quote-meta-block" style="display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 16px; margin-top: 16px;">
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label>Sales Owner</x-admin::form.control-group.label>
                            <input type="text" class="custom-input" :value="form.sales_owner_name || ''" disabled>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label class="required">Customer</x-admin::form.control-group.label>
                            <input type="text" class="custom-input" :value="form.organization_name || ''" disabled>
                        </x-admin::form.control-group>
                    </div>

                    <div class="document-form-row-6 quote-meta-block" style="display: grid !important; grid-template-columns: repeat(6, minmax(0, 1fr)) !important; gap: 16px; margin-top: 16px;">
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Payment Term</x-admin::form.control-group.label><x-admin::form.control-group.control type="text" name="payment_term" v-model="form.payment_term" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Shipping Method</x-admin::form.control-group.label><x-admin::form.control-group.control type="text" name="shipping_method" v-model="form.shipping_method" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Production Time</x-admin::form.control-group.label><x-admin::form.control-group.control type="text" name="production_time" v-model="form.production_time" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Transit Time</x-admin::form.control-group.label><x-admin::form.control-group.control type="text" name="transit_time" v-model="form.transit_time" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>ETD</x-admin::form.control-group.label><input type="date" name="etd" v-model="form.etd" class="custom-input"></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>ETA</x-admin::form.control-group.label><input type="date" name="eta" v-model="form.eta" class="custom-input"></x-admin::form.control-group>
                    </div>

                    <div class="document-form-row-2 quote-meta-block" style="display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 16px; margin-top: 16px;">
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label><b>Bill To Address</b></x-admin::form.control-group.label>
                            <select class="custom-select" v-model="form.billing_address.key" @change="applyAddressSelection('billing', form.billing_address.key)">
                                <option value="">Select billing address</option>
                                <option v-for="option in addressOptions" :key="`billing-${option.key}`" :value="option.key">@{{ option.label }}</option>
                            </select>
                            <div class="mt-2 whitespace-pre-line text-xs text-gray-500 dark:text-gray-400">@{{ form.billing_address?.address || 'No billing address available.' }}</div>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label><b>Ship To Address</b></x-admin::form.control-group.label>
                            <select class="custom-select" v-model="form.shipping_address.key" @change="applyAddressSelection('shipping', form.shipping_address.key)">
                                <option value="">Select shipping address</option>
                                <option v-for="option in addressOptions" :key="`shipping-${option.key}`" :value="option.key">@{{ option.label }}</option>
                            </select>
                            <div class="mt-2 whitespace-pre-line text-xs text-gray-500 dark:text-gray-400">@{{ form.shipping_address?.address || 'No shipping address available.' }}</div>
                        </x-admin::form.control-group>
                    </div>
                </div>

                <div class="document-form-items mt-2 flex flex-col gap-4">
                    <div class="flex flex-col gap-1"><p class="text-base font-semibold text-gray-800 dark:text-white">Proforma Invoice Items</p><p class="text-sm text-gray-600 dark:text-white">Add Product Request for this proforma invoice.</p></div>
                    <v-proforma-item-list :errors="errors" :organization-id="form.organization_id" :initial-products="form.items" :initial-charges="form.charges || []"></v-proforma-item-list>
                </div>

                <div class="document-form-section grid gap-4 md:grid-cols-2">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Remarks</x-admin::form.control-group.label>
                        <textarea
                            name="notes"
                            rows="4"
                            v-model="form.notes"
                            class="w-full rounded border border-gray-200 px-3 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                        ></textarea>
                        <x-admin::form.control-group.error control-name="notes" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label>Terms &amp; Conditions</x-admin::form.control-group.label>
                        <textarea
                            name="terms"
                            rows="4"
                            v-model="form.terms"
                            class="w-full rounded border border-gray-200 px-3 py-2 text-sm font-normal text-gray-800 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                        ></textarea>
                        <x-admin::form.control-group.error control-name="terms" />
                    </x-admin::form.control-group>
                </div>
            </div>
        </script>

        <script type="text/x-template" id="v-proforma-item-list-template">
            <div class="flex flex-col gap-4">
                <div class="block w-full overflow-visible">
                    <x-admin::table>
                        <x-admin::table.thead>
                            <x-admin::table.thead.tr>
                                <x-admin::table.th>Product Name</x-admin::table.th>
                                <x-admin::table.th class="text-center">Image</x-admin::table.th>
                                <x-admin::table.th class="text-center">Color Variant</x-admin::table.th>
                                <x-admin::table.th class="text-center">Quantity</x-admin::table.th>
                                <x-admin::table.th class="text-center">Price</x-admin::table.th>
                                <x-admin::table.th class="text-center">Amount</x-admin::table.th>
                                <x-admin::table.th class="text-center">Total</x-admin::table.th>
                                <x-admin::table.th v-if="products.length > 1" class="!px-2 ltr:text-right rtl:text-left">Action</x-admin::table.th>
                            </x-admin::table.thead.tr>
                        </x-admin::table.thead>

                        <x-admin::table.tbody>
                            <template v-for="(product, index) in products" :key="index">
                                <v-proforma-item :product="product" :index="index" :errors="errors" :organization-id="organizationId" @onRemoveProduct="removeProduct($event)"></v-proforma-item>
                            </template>
                        </x-admin::table.tbody>
                    </x-admin::table>
                </div>

                <span class="text-md flex max-w-max cursor-pointer items-center gap-2 text-brandColor" @click="addProduct">+ Add Item</span>

                <div class="flex justify-end">
                    <div class="document-form-summary-box is-wide grid gap-4 rounded-lg bg-gray-100 p-5 text-sm dark:bg-gray-950 dark:text-white" style="max-width: 100%;">
                        <div class="flex w-full items-center justify-between gap-x-5">
                            <span>Sub Total ($)</span>
                            <input type="hidden" name="subtotal" :value="subTotal">
                            <p class="text-base font-semibold">@{{ formatPrice(subTotal) }}</p>
                        </div>

                        <template v-for="(charge, chargeIndex) in charges" :key="`charge-${chargeIndex}`">
                            <div class="document-summary-line" style="display: grid; grid-template-columns: minmax(180px, 1.2fr) 110px 110px 120px 36px; align-items: center; gap: 10px;">
                                <div>
                                    <input type="text" v-model="charge.name" class="w-full rounded border border-gray-200 px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800" placeholder="Charge name">
                                    <input type="hidden" :name="`charges[${chargeIndex}][name]`" :value="charge.name">
                                </div>
                                <div>
                                    <select v-model="charge.type" class="w-full rounded border border-gray-200 px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
                                        <option value="percentage">Percentage</option>
                                        <option value="value">Value</option>
                                    </select>
                                    <input type="hidden" :name="`charges[${chargeIndex}][type]`" :value="charge.type">
                                </div>
                                <div>
                                    <input type="number" min="0" step="0.01" v-model="charge.value" class="w-full rounded border border-gray-200 px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800" placeholder="0.00">
                                    <input type="hidden" :name="`charges[${chargeIndex}][value]`" :value="charge.value || 0">
                                </div>
                                <div class="text-right font-medium">@{{ formatPrice(chargeAmount(charge)) }}</div>
                                <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-md text-xl hover:bg-gray-200 dark:hover:bg-gray-800" @click="removeCharge(chargeIndex)">
                                    <i class="icon-delete"></i>
                                </button>
                            </div>
                        </template>

                        <div>
                            <button type="button" class="secondary-button" @click="addCharge">Add Charge</button>
                        </div>

                        <div class="flex w-full items-center justify-between gap-x-5">
                            <span>Additional Charges ($)</span>
                            <input type="hidden" name="tax_amount" :value="taxChargeTotal">
                            <input type="hidden" name="adjustment_amount" :value="otherChargeTotal">
                            <p class="text-base font-semibold">@{{ formatPrice(chargesTotal) }}</p>
                        </div>

                        <input type="hidden" name="discount_amount" value="0">

                        <div class="flex w-full items-center justify-between gap-x-5 border-t border-gray-200 pt-3 text-base font-semibold dark:border-gray-800">
                            <span>Grand Total ($)</span>
                            <input type="hidden" name="grand_total" :value="grandTotal">
                            <p>@{{ formatPrice(grandTotal) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </script>

        <script type="text/x-template" id="v-proforma-item-template">
            <x-admin::table.thead.tr>
                <x-admin::table.td class="!px-2 align-top overflow-visible">
                    <div class="relative min-w-[380px]">
                        <v-proforma-product-lookup :src="src" :organization-id="organizationId" :selected-product="product" @on-selected="(product) => addProduct(product)"></v-proforma-product-lookup>
                        <input type="hidden" :name="`${inputName}[product_id]`" :value="product.product_id || ''">
                        <input type="text" :name="`${inputName}[item_name]`" v-model="product.name" class="custom-input mt-2" placeholder="Product name">
                    </div>
                </x-admin::table.td>

                <x-admin::table.td class="!px-2 text-center">
                    <input type="hidden" :name="`${inputName}[preview_image]`" :value="product.preview_image || ''">
                    <img v-if="product.preview_image" :key="product.preview_image" :src="product.preview_image" class="mx-auto h-12 w-12 rounded object-cover border border-gray-200" alt="preview">
                    <span v-else class="text-xs text-gray-500">No image</span>
                </x-admin::table.td>

                <x-admin::table.td class="!px-2">
                    <select class="w-full rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-700 dark:bg-gray-800" v-model="product.selected_color_id" @change="onColorChange($event.target.value)">
                        <option value="">No Color</option>
                        <option v-for="color in (product.available_colors || [])" :key="color.id" :value="String(color.id)">@{{ color.name }}</option>
                    </select>
                    <input type="hidden" :name="`${inputName}[color_variant_id]`" :value="product.selected_color_id || ''">
                    <input type="hidden" :name="`${inputName}[color_variant_name]`" :value="product.selected_color_name || ''">
                </x-admin::table.td>

                <input type="hidden" :name="`${inputName}[item_code]`" :value="product.item_code || ''">
                <input type="hidden" :name="`${inputName}[unit]`" value="">

                <x-admin::table.td class="!px-2 text-center">
                    <input type="number" min="1" step="1" :name="`${inputName}[qty]`" v-model.number="product.quantity" class="custom-input text-center">
                </x-admin::table.td>

                <x-admin::table.td class="!px-2 text-center">
                    <input type="number" min="0" step="0.01" :name="`${inputName}[unit_price]`" v-model="product.price" class="custom-input text-center">
                </x-admin::table.td>

                <x-admin::table.td class="!px-2 text-center">@{{ formatPrice(amount) }}</x-admin::table.td>

                <x-admin::table.td class="!px-2 text-center">@{{ formatPrice(total) }}</x-admin::table.td>

                <x-admin::table.td class="!px-2 text-center">
                    <i @click="removeProduct" class="icon-delete cursor-pointer text-2xl"></i>
                </x-admin::table.td>
            </x-admin::table.thead.tr>
        </script>

        <script type="module">
            app.component('v-proforma', {
                template: '#v-proforma-template',
                props: ['initialForm', 'quotes', 'errors'],
                data() { return { form: this.initialForm }; },
                computed: {
                    selectedQuote() {
                        return this.quotes.find(q => String(q.id) === String(this.form.quote_id)) || null;
                    },
                    addressOptions() {
                        return this.selectedQuote?.address_options || [];
                    },
                },
                methods: {
                    applyQuoteDetails() {
                        const selected = this.quotes.find(q => String(q.id) === String(this.form.quote_id));
                        if (! selected) return;
                        this.form.quote_number_display = selected.quote_number_display || '';
                        this.form.organization_id = selected.organization_id || '';
                        this.form.organization_name = selected.organization_name || '';
                        this.form.person_id = selected.person_id || '';
                        this.form.sales_owner_id = selected.sales_owner_id || '';
                        this.form.sales_owner_name = selected.sales_owner_name || '';
                        this.form.payment_term = selected.payment_term || '';
                        this.form.shipping_method = selected.shipping_method || '';
                        this.form.production_time = selected.production_time || '';
                        this.form.transit_time = selected.transit_time || '';
                        this.form.etd = selected.etd || '';
                        this.form.eta = selected.eta || '';
                        this.form.charges = (selected.charges || []).map(charge => ({ ...charge }));
                        this.form.notes = selected.notes || '';
                        this.form.terms = selected.terms || '';
                        this.form.billing_address = selected.billing_address || selected.default_billing_address || { address: '' };
                        this.form.shipping_address = selected.shipping_address || selected.default_shipping_address || { address: '' };
                        this.form.items = (selected.items || []).map(item => ({ ...item }));
                    },
                    applyAddressSelection(kind, key) {
                        const option = this.addressOptions.find(address => String(address.key) === String(key));

                        this.form[kind === 'billing' ? 'billing_address' : 'shipping_address'] = option
                            ? { ...option }
                            : { key: '', label: '', type: kind, address: '' };
                    },
                },
            });

            app.component('v-proforma-item-list', {
                template: '#v-proforma-item-list-template',
                props: ['errors', 'organizationId', 'initialProducts', 'initialCharges'],
                data() {
                    return {
                        charges: this.initialCharges?.length ? this.initialCharges.map(charge => ({ ...charge })) : [],
                        products: this.initialProducts?.length
                            ? this.initialProducts.map(item => ({ ...item }))
                            : [{ id: null, product_id: null, name: '', item_code: '', quantity: 1, price: '0.00', discount_amount: '0.00', tax_amount: '0.00', available_colors: [], color_images: {}, selected_color_id: '', selected_color_name: '', preview_image: '', cover_image_url: '' }],
                    }
                },
                watch: {
                    initialProducts: {
                        handler(value) {
                            this.products = value?.length
                                ? value.map(item => ({ ...item }))
                                : [{ id: null, product_id: null, name: '', item_code: '', quantity: 1, price: '0.00', discount_amount: '0.00', tax_amount: '0.00', available_colors: [], color_images: {}, selected_color_id: '', selected_color_name: '', preview_image: '', cover_image_url: '' }];
                        },
                        deep: true,
                    },
                    initialCharges(value) {
                        this.charges = value?.length ? value.map(charge => ({ ...charge })) : [];
                    },
                },
                computed: {
                    subTotal() {
                        return this.products.reduce((t, p) => t + ((parseFloat(p.quantity) || 0) * (parseFloat(p.price) || 0)), 0);
                    },
                    chargesTotal() {
                        return this.charges.reduce((total, charge) => total + this.chargeAmount(charge), 0);
                    },
                    taxChargeTotal() {
                        return this.charges
                            .filter(charge => this.isTaxCharge(charge.name))
                            .reduce((total, charge) => total + this.chargeAmount(charge), 0);
                    },
                    otherChargeTotal() {
                        return this.charges
                            .filter(charge => ! this.isTaxCharge(charge.name))
                            .reduce((total, charge) => total + this.chargeAmount(charge), 0);
                    },
                    grandTotal() {
                        return this.subTotal + this.chargesTotal;
                    },
                },
                methods: {
                    blankCharge() {
                        return { name: '', type: 'percentage', value: 0 };
                    },
                    addCharge() {
                        this.charges.push(this.blankCharge());
                    },
                    removeCharge(index) {
                        this.charges.splice(index, 1);
                    },
                    chargeAmount(charge) {
                        const value = parseFloat(charge?.value || 0);

                        if ((charge?.type || 'value') === 'percentage') {
                            return this.subTotal * (value / 100);
                        }

                        return value;
                    },
                    isTaxCharge(name) {
                        const normalized = String(name || '').trim().toLowerCase();
                        return ['tax', 'tariff', 'tarrif', 'duty', 'vat', 'gst'].some(token => normalized.includes(token));
                    },
                    addProduct() {
                        this.products.push({ id: null, product_id: null, name: '', item_code: '', quantity: 1, price: '0.00', discount_amount: '0.00', tax_amount: '0.00', available_colors: [], color_images: {}, selected_color_id: '', selected_color_name: '', preview_image: '', cover_image_url: '' });
                    },
                    removeProduct(product) {
                        if (this.products.length === 1) {
                            this.products = [{ id: null, product_id: null, name: '', item_code: '', quantity: 1, price: '0.00', discount_amount: '0.00', tax_amount: '0.00', available_colors: [], color_images: {}, selected_color_id: '', selected_color_name: '', preview_image: '', cover_image_url: '' }];
                            return;
                        }

                        const index = this.products.indexOf(product);

                        if (index !== -1) {
                            this.products.splice(index, 1);
                        }
                    },
                    formatPrice(value) {
                        return this.$admin.formatPrice(value || 0);
                    },
                },
            });

            app.component('v-proforma-product-lookup', {
                props: ['src', 'organizationId', 'selectedProduct'],
                emits: ['on-selected'],
                data() { return { showPopup: false, searchTerm: '', results: [], isSearching: false, cancelToken: null }; },
                computed: {
                    selectedLabel() {
                        const itemCode = this.selectedProduct?.item_code || '';
                        return itemCode || '';
                    },
                },
                methods: {
                    toggle() { this.showPopup = ! this.showPopup; if (this.showPopup) this.$nextTick(() => this.$refs.searchInput.focus()); },
                    clearSelection() { this.searchTerm=''; this.results=[]; this.$emit('on-selected', { id: null, name: '', sku: '', selling_price: '0.00', colors: [], color_images: {}, cover_image_url: '' }); },
                    search() {
                        if (! this.organizationId || this.searchTerm.trim().length < 1) { this.results = []; return; }
                        if (this.cancelToken) this.cancelToken.cancel('canceled');
                        this.cancelToken = this.$axios.CancelToken.source();
                        this.isSearching = true;
                        this.$axios.get(this.src, { params: { organization_id: this.organizationId, query: this.searchTerm.trim() }, cancelToken: this.cancelToken.token }).then((response) => {
                            const items = response?.data?.data || response?.data || [];
                            this.results = Array.isArray(items) ? items : [];
                        }).catch((error) => { if (! this.$axios.isCancel(error)) this.results = []; }).finally(() => { this.isSearching = false; });
                    },
                    selectProduct(product) { this.showPopup = false; this.searchTerm = ''; this.results = []; this.$emit('on-selected', product); },
                    handleFocusOut(event) { if (this.$refs.lookup && ! this.$refs.lookup.contains(event.target)) this.showPopup = false; },
                    resultLabel(product) { return product?.sku || product?.internal_code || ''; },
                },
                watch: { organizationId() { this.showPopup=false; this.searchTerm=''; this.results=[]; }, searchTerm() { this.search(); } },
                mounted() { window.addEventListener('click', this.handleFocusOut); },
                beforeUnmount() { window.removeEventListener('click', this.handleFocusOut); },
                template: `
                    <div class="relative" ref="lookup">
                        <div class="relative inline-block w-full" @click="toggle">
                            <div class="relative flex min-h-[42px] cursor-pointer items-center justify-between rounded border border-gray-200 p-2 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:text-gray-300">
                                <span class="block max-w-[300px] overflow-hidden text-ellipsis whitespace-nowrap" :title="selectedLabel">@{{ selectedLabel !== '' ? selectedLabel : 'Search by Product Item Code' }}</span>
                                <div class="flex items-center gap-2"><i v-if="selectedLabel" class="icon-cross-large cursor-pointer text-xl text-gray-600" @click.stop="clearSelection"></i><i class="text-2xl text-gray-600" :class="showPopup ? 'icon-up-arrow' : 'icon-down-arrow'"></i></div>
                            </div>
                        </div>
                        <div v-if="showPopup" class="absolute top-full z-50 mt-1 flex w-full min-w-[380px] origin-top transform flex-col gap-2 rounded-lg border border-gray-200 bg-white p-2 shadow-lg transition-transform dark:border-gray-900 dark:bg-gray-800">
                            <div class="relative flex items-center"><input type="text" v-model="searchTerm" class="w-full rounded border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300" placeholder="Search by Product Item Code" ref="searchInput" @click.stop /></div>
                            <ul class="max-h-56 divide-y divide-gray-100 overflow-y-auto rounded">
                                <li v-for="item in results" :key="item.id" class="cursor-pointer px-4 py-2 text-gray-800 transition-colors hover:bg-blue-100 dark:text-white dark:hover:bg-gray-900" @click="selectProduct(item)"><div class="font-medium">@{{ resultLabel(item) }}</div><div v-if="item.name" class="text-xs text-gray-500">@{{ item.name }}</div></li>
                                <li v-if="isSearching" class="px-4 py-2 text-gray-500">Searching...</li>
                                <li v-else-if="searchTerm.trim().length > 0 && ! results.length" class="px-4 py-2 text-gray-500">No results</li>
                                <li v-else class="px-4 py-2 text-gray-500">Type product item code to search</li>
                            </ul>
                        </div>
                    </div>`
            });

            app.component('v-proforma-item', {
                template: '#v-proforma-item-template',
                props: ['index', 'product', 'organizationId', 'errors'],
                computed: {
                    inputName() { return `items[${this.index}]`; },
                    src() { return "{{ route('admin.products.search') }}"; },
                    amount() { return (parseInt(this.product.quantity) || 0) * (parseFloat(this.product.price) || 0); },
                    total() { return this.amount + (parseFloat(this.product.tax_amount) || 0) - (parseFloat(this.product.discount_amount) || 0); },
                },
                methods: {
                    normalizeColorImages(images) { if (! images || typeof images !== 'object') return {}; return Object.keys(images).reduce((c,k)=>{c[String(k)] = images[k]; return c;}, {}); },
                    resolvePreviewImage(colorId) { const normalizedColorId = colorId ? String(colorId) : ''; const imageMap = this.normalizeColorImages(this.product.color_images || {}); if (normalizedColorId && imageMap[normalizedColorId]) return imageMap[normalizedColorId]; return this.product.cover_image_url || ''; },
                    addProduct(result) {
                        this.product.product_id = result.id ?? null;
                        this.product.name = result.name ?? '';
                        this.product.item_code = result.sku ?? result.internal_code ?? '';
                        this.product.price = Number(result.selling_price ?? result.price ?? 0).toFixed(2);
                        this.product.quantity = 1;
                        this.product.discount_amount = '0.00';
                        this.product.tax_amount = '0.00';
                        this.product.available_colors = Array.isArray(result.colors) ? result.colors : [];
                        this.product.color_images = this.normalizeColorImages(result.color_images || {});
                        this.product.cover_image_url = result.cover_image_url || '';
                        this.product.selected_color_id = '';
                        this.product.selected_color_name = '';
                        this.product.preview_image = result.cover_image_url || '';
                    },
                    onColorChange(selectedValue) {
                        this.product.selected_color_id = selectedValue ? String(selectedValue) : '';
                        const selected = (this.product.available_colors || []).find(color => String(color.id) === String(this.product.selected_color_id));
                        this.product.selected_color_name = selected ? selected.name : '';
                        this.product.price = Number(selected && selected.selling_price !== null && selected.selling_price !== undefined
                            ? selected.selling_price
                            : (this.product.price ?? 0)).toFixed(2);
                        this.product.preview_image = this.resolvePreviewImage(this.product.selected_color_id);
                    },
                    removeProduct() { this.$emit('onRemoveProduct', this.product); },
                    formatPrice(value) { return this.$admin.formatPrice(value || 0); },
                },
            });
        </script>
    @endPushOnce

    @pushOnce('styles')
        <style>.custom-input { width: 100%; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.45rem 0.6rem; font-size: 0.875rem; background: transparent; } .custom-select { width: 100%; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.45rem 0.6rem; font-size: 0.875rem; background: transparent; }</style>
    @endPushOnce
</x-admin::layouts>
