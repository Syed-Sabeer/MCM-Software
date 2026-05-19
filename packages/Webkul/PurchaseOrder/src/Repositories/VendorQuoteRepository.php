<?php

namespace Webkul\PurchaseOrder\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Webkul\Contact\Models\Organization;
use Webkul\Core\Support\DocumentAddressManager;
use Webkul\Core\Eloquent\Repository;
use Webkul\Core\Support\DocumentChargeManager;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\PurchaseOrder\Models\VendorQuote;

class VendorQuoteRepository extends Repository
{
    public function __construct(
        protected VendorQuoteItemRepository $vendorQuoteItemRepository,
        protected DocumentAddressManager $documentAddressManager,
        protected DocumentChargeManager $documentChargeManager,
        Container $container
    ) {
        parent::__construct($container);
    }

    public function model(): string
    {
        return VendorQuote::class;
    }

    public function createFromJobOrder(JobOrder $jobOrder, array $overrides = []): VendorQuote
    {
        $payload = array_merge([
            'job_order_id' => $jobOrder->id,
            'organization_id' => $jobOrder->organization_id,
            'issue_date' => now()->toDateString(),
            'status' => 'draft',
            'created_by' => auth()->id(),
            'first_delivery_date' => $jobOrder->required_delivery_date?->toDateString(),
            'last_delivery_date' => $jobOrder->required_delivery_date?->toDateString(),
            'items' => $jobOrder->requirements->map(fn ($requirement) => [
                'requirement_id' => $requirement->id,
                'material_name' => $requirement->material_name,
                'quantity' => $requirement->balance_qty,
                'unit' => $requirement->unit,
                'unit_price' => 0,
            ])->toArray(),
        ], $overrides);

        return $this->create($payload);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data = $this->calculateTotals($data);
            $quote = parent::create($data);
            $this->syncItems($quote, $data['items'] ?? []);
            $this->documentChargeManager->sync($quote, $data['charges'] ?? []);
            return $quote->fresh(['items', 'organization', 'jobOrder']);
        });
    }

    public function update(array $data, $id, $attribute = 'id')
    {
        return DB::transaction(function () use ($data, $id, $attribute) {
            $data = $this->calculateTotals($data);
            $quote = parent::update($data, $id, $attribute);
            $this->syncItems($quote, $data['items'] ?? []);
            $this->documentChargeManager->sync($quote, $data['charges'] ?? []);
            return $quote->fresh(['items', 'organization', 'jobOrder']);
        });
    }

    protected function calculateTotals(array $data): array
    {
        $subtotal = 0;
        $items = $data['items'] ?? [];
        $organization = ! empty($data['organization_id'])
            ? Organization::find($data['organization_id'])
            : null;

        foreach ($items as $index => $item) {
            $materialName = trim((string) ($item['item'] ?? $item['material_name'] ?? ''));
            $quantity = (float) ($item['ordered_quantity'] ?? $item['quantity'] ?? 0);
            $unitPrice = (float) ($item['price'] ?? $item['unit_price'] ?? 0);
            $lineTotal = $quantity * $unitPrice;

            $items[$index]['material_name'] = $materialName;
            $items[$index]['item'] = $materialName;
            $items[$index]['ordered_quantity'] = $quantity;
            $items[$index]['quantity'] = $quantity;
            $items[$index]['price'] = $unitPrice;
            $items[$index]['unit_price'] = $unitPrice;
            $items[$index]['total'] = $lineTotal;

            $subtotal += $lineTotal;
        }

        $charges = $this->documentChargeManager->normalize($data['charges'] ?? [], $subtotal);
        $chargeSummary = $this->documentChargeManager->summarize('vendor_quote', $charges);
        $grandTotal = $subtotal + ($chargeSummary['charge_total'] ?? 0);

        $data['items'] = $items;
        $data['billing_address'] = $this->documentAddressManager->normalize($organization, $data['billing_address'] ?? null, 'billing');
        $data['shipping_address'] = $this->documentAddressManager->normalize($organization, $data['shipping_address'] ?? null, 'shipping');
        $data['charges'] = $charges;
        $data['subtotal'] = $subtotal;
        $data['sales_tax_percent'] = $chargeSummary['sales_tax_percent'] ?? 0;
        $data['sales_tax_amount'] = $chargeSummary['sales_tax_amount'] ?? 0;
        $data['freight'] = $chargeSummary['freight'] ?? 0;
        $data['grand_total'] = $grandTotal;

        return $data;
    }

    protected function syncItems(VendorQuote $vendorQuote, array $items): void
    {
        $existingIds = $vendorQuote->items()->pluck('id')->toArray();
        $savedIds = [];

        foreach ($items as $index => $item) {
            $materialName = trim((string) ($item['item'] ?? $item['material_name'] ?? ''));
            $quantity = (float) ($item['ordered_quantity'] ?? $item['quantity'] ?? 0);
            $unitPrice = (float) ($item['price'] ?? $item['unit_price'] ?? 0);
            $payload = [
                'vendor_quote_id' => $vendorQuote->id,
                'requirement_id' => $item['requirement_id'] ?? null,
                'vendor_id' => $item['vendor_id'] ?? null,
                'material_name' => $materialName,
                'color' => $item['color'] ?? null,
                'description' => $item['description'] ?? null,
                'quantity' => $quantity,
                'unit' => $item['unit'] ?? null,
                'unit_price' => $unitPrice,
                'total' => $quantity * $unitPrice,
                'vendor_lead_time' => null,
                'expected_receive_date' => $item['expected_receive_date'] ?? null,
                'sort_order' => $index,
            ];

            if (! empty($item['id'])) {
                $this->vendorQuoteItemRepository->update($payload, $item['id']);
                $savedIds[] = (int) $item['id'];
            } else {
                $savedIds[] = $this->vendorQuoteItemRepository->create($payload)->id;
            }
        }

        foreach (array_diff($existingIds, $savedIds) as $deleteId) {
            $this->vendorQuoteItemRepository->delete($deleteId);
        }
    }
}
