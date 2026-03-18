<?php

namespace Webkul\Quote\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Webkul\Contact\Models\Person;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\Quote\Contracts\Quote;

class QuoteRepository extends Repository
{
    /**
     * Searchable fields.
     */
    protected $fieldSearchable = [
        'subject',
        'description',
        'person_id',
        'person.name',
        'user_id',
        'user.name',
    ];

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository,
        protected QuoteItemRepository $quoteItemRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return Quote::class;
    }

    /**
     * Create.
     *
     * @return \Webkul\Quote\Contracts\Quote
     */
    public function create(array $data)
    {
        $data = $this->prepareQuoteData($data);

        $quote = parent::create($data);

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $quote->id,
        ]));

        foreach ($data['items'] ?? [] as $itemData) {
            $this->quoteItemRepository->create(array_merge($itemData, [
                'quote_id' => $quote->id,
            ]));
        }

        return $quote;
    }

    /**
     * Update.
     *
     * @param  int  $id
     * @param  array  $attribute
     * @return \Webkul\Quote\Contracts\Quote
     */
    public function update(array $data, $id, $attributes = [])
    {
        $data = $this->prepareQuoteData($data);

        $quote = $this->find($id);

        parent::update($data, $id);

        /**
         * If attributes are provided then only save the provided attributes and return.
         */
        if (! empty($attributes)) {
            $conditions = ['entity_type' => $data['entity_type']];

            if (isset($data['quick_add'])) {
                $conditions['quick_add'] = 1;
            }

            $attributes = $this->attributeRepository->where($conditions)
                ->whereIn('code', $attributes)
                ->get();

            $this->attributeValueRepository->save(array_merge($data, [
                'entity_id' => $quote->id,
            ]), $attributes);

            return $quote;
        }

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $quote->id,
        ]));

        $previousItemIds = $quote->items->pluck('id');

        if (isset($data['items'])) {
            foreach ($data['items'] as $itemId => $itemData) {
                if (Str::contains($itemId, 'item_')) {
                    $this->quoteItemRepository->create(array_merge($itemData, [
                        'quote_id' => $id,
                    ]));
                } else {
                    if (is_numeric($index = $previousItemIds->search($itemId))) {
                        $previousItemIds->forget($index);
                    }

                    $this->quoteItemRepository->update($itemData, $itemId);
                }
            }
        }

        foreach ($previousItemIds as $itemId) {
            $this->quoteItemRepository->delete($itemId);
        }

        return $quote;
    }

    /**
     * Normalize quote payload and recalculate totals server-side.
     */
    protected function prepareQuoteData(array $data): array
    {
        if (empty($data['quote_date'])) {
            $data['quote_date'] = now()->toDateString();
        }

        if (empty($data['status'])) {
            $data['status'] = 'draft';
        }

        if (empty($data['subject'])) {
            $data['subject'] = ! empty($data['quote_number'])
                ? 'Quote ' . $data['quote_number']
                : 'Quote Draft';
        }

        if (! empty($data['person_id']) && empty($data['organization_id'])) {
            $person = Person::find($data['person_id']);
            $data['organization_id'] = $person?->organization_id;
        }

        $items = $data['items'] ?? [];
        $normalizedItems = [];
        $subTotal = 0;
        $discountAmount = 0;
        $taxAmount = 0;

        foreach ($items as $itemKey => $item) {
            if (! is_array($item)) {
                continue;
            }

            $qty = (float) ($item['qty'] ?? $item['quantity'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
            $lineSubTotal = $qty * $unitPrice;

            $lineDiscountPercent = 0;
            $lineDiscountAmount = 0;
            $lineTaxPercent = 0;
            $lineTaxAmount = 0;
            $lineTotal = max($lineSubTotal, 0);
            $name = $item['item_name'] ?? $item['name'] ?? '';
            $code = $item['item_code'] ?? $item['sku'] ?? null;

            $normalizedItems[$itemKey] = array_merge($item, [
                'item_name'       => $name,
                'name'            => $name,
                'item_code'       => $code,
                'sku'             => $code,
                'qty'             => $qty,
                'quantity'        => $qty,
                'unit_price'      => $unitPrice,
                'price'           => $unitPrice,
                'discount_amount' => $lineDiscountAmount,
                'tax_amount'      => $lineTaxAmount,
                'line_subtotal'   => $lineSubTotal,
                'line_total'      => $lineTotal,
                'total'           => $lineTotal,
            ]);

            $subTotal += $lineSubTotal;
            $discountAmount += $lineDiscountAmount;
            $taxAmount += $lineTaxAmount;
        }

        $tariffPercent = (float) ($data['tariff_percent'] ?? 0);
        $freightPercent = (float) ($data['freight_percent'] ?? 0);
        $taxAmount = $subTotal * ($tariffPercent / 100);
        $freightAmount = $subTotal * ($freightPercent / 100);
        $discountAmount = 0;
        $grandTotal = max($subTotal + $taxAmount + $freightAmount, 0);

        $data['items'] = $normalizedItems;
        $data['sub_total'] = $subTotal;
        $data['discount_amount'] = $discountAmount;
        $data['tax_amount'] = $taxAmount;
        $data['adjustment_amount'] = $freightAmount;
        $data['tariff_percent'] = $tariffPercent;
        $data['freight_percent'] = $freightPercent;
        $data['grand_total'] = $grandTotal;

        return $data;
    }

    /**
     * Retrieves customers count based on date.
     *
     * @return number
     */
    public function getQuotesCount($startDate, $endDate)
    {
        return $this
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->count();
    }
}
