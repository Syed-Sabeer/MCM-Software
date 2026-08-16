<x-admin::layouts>
    <x-slot:title>{{ $material->name }} Inventory</x-slot>

    @php
        $formatQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 4, '.', ','), '0'), '.');
        $typeLabels = array_merge(
            \Webkul\PurchaseOrder\Services\MaterialInventoryService::MANUAL_TYPES,
            [
                'job_order_issue' => 'Job Order Issue',
                'job_order_adjustment' => 'Job Order Adjustment',
                'goods_receipt' => 'Goods Receipt',
                'goods_receipt_adjustment' => 'Goods Receipt Adjustment',
            ]
        );
        $referenceUrl = function ($transaction) {
            if ($transaction->reference_type === 'job_order' && $transaction->reference_id) {
                return route('admin.job_orders.view', $transaction->reference_id);
            }

            if ($transaction->reference_type === 'goods_receipt' && $transaction->reference_id) {
                return route('admin.goods_receipts.view', $transaction->reference_id);
            }

            return null;
        };
    @endphp

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between gap-4 rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.inventory.index') }}" class="inline-flex rounded-md p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Back to inventory"><span class="icon-left-arrow text-xl"></span></a>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $material->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $material->unit }}</p>
                </div>
            </div>

            <button type="button" class="primary-button inline-flex items-center gap-2" onclick="window.openInventoryMovementModal()"><span class="icon-add"></span> Record Movement</button>
        </div>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4" aria-label="Material inventory summary">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"><p class="text-xs font-medium uppercase text-gray-500">On Hand</p><p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $formatQty($inventory->on_hand) }} <span class="text-sm font-normal text-gray-500">{{ $material->unit }}</span></p></div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"><p class="text-xs font-medium uppercase text-gray-500">Average Cost</p><p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ core()->formatBasePrice($inventory->average_unit_cost, 2) }}</p></div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"><p class="text-xs font-medium uppercase text-gray-500">Stock Value</p><p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ core()->formatBasePrice((float) $inventory->on_hand * (float) $inventory->average_unit_cost, 2) }}</p></div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <form method="POST" action="{{ route('admin.inventory.settings.update', $material->id) }}">
                    @csrf
                    @method('PUT')
                    <label class="text-xs font-medium uppercase text-gray-500">Reorder Level</label>
                    <div class="mt-2 flex gap-2"><input type="number" name="reorder_level" value="{{ old('reorder_level', $inventory->reorder_level) }}" min="0" step="0.0001" class="custom-input" required><button class="secondary-button" data-loading-submit data-loading-text="Saving...">Save</button></div>
                    @error('reorder_level')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </form>
            </div>
        </section>

        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-800"><h2 class="font-semibold text-gray-900 dark:text-white">Movement History</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-gray-500 dark:bg-gray-950 dark:text-gray-300"><tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Movement</th><th class="px-4 py-3">Reference</th><th class="px-4 py-3 text-right">Quantity</th><th class="px-4 py-3 text-right">Unit Cost</th><th class="px-4 py-3 text-right">Balance</th><th class="px-4 py-3">Notes</th></tr></thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($transactions as $transaction)
                            @php $url = $referenceUrl($transaction); @endphp
                            <tr>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $transaction->occurred_at->format('d M Y, H:i') }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $typeLabels[$transaction->type] ?? \Illuminate\Support\Str::headline($transaction->type) }}</td>
                                <td class="px-4 py-3">@if($url)<a href="{{ $url }}" class="text-brandColor">{{ str_replace('_', ' ', ucfirst($transaction->reference_type)) }} #{{ $transaction->reference_id }}</a>@else<span class="text-gray-500">Manual</span>@endif</td>
                                <td class="px-4 py-3 text-right font-semibold {{ (float) $transaction->quantity >= 0 ? 'text-green-700' : 'text-red-700' }}">{{ (float) $transaction->quantity > 0 ? '+' : '' }}{{ $formatQty($transaction->quantity) }}</td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">{{ core()->formatBasePrice($transaction->unit_cost, 2) }}</td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">{{ $formatQty($transaction->balance_after) }}</td>
                                <td class="max-w-xs px-4 py-3 text-gray-600 dark:text-gray-300">{{ $transaction->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">No movements recorded for this material.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())<div class="border-t border-gray-200 px-4 py-3 dark:border-gray-800">{{ $transactions->links() }}</div>@endif
        </section>
    </div>

    @include('admin::inventory.partials.movement-modal', ['lockedMaterial' => $material])
</x-admin::layouts>
