<?php

namespace Webkul\Quote\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Schema;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Repositories\ProductRepository;

class QuoteItemRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected ProductRepository $productRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Quote\Contracts\QuoteItem';
    }

    /**
     * @return mixed
     */
    public function create(array $data)
    {
        $data = $this->prepareItemData($data);

        $quoteItem = parent::create($data);

        return $quoteItem;
    }

    /**
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Quote\Contracts\QuoteItem
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        $data = $this->prepareItemData($data);

        $quoteItem = parent::update($data, $id);

        return $quoteItem;
    }

    /**
     * Normalize quote item payload and only keep columns that exist in the current schema.
     */
    protected function prepareItemData(array $data): array
    {
        if (! empty($data['product_id'])) {
            $product = $this->productRepository->findOrFail($data['product_id']);

            $data = array_merge($data, [
                'sku'       => $data['sku'] ?? $product->sku,
                'item_code' => $data['item_code'] ?? $product->sku,
                'name'      => $data['name'] ?? $product->name,
                'item_name' => $data['item_name'] ?? $product->name,
            ]);
        } else {
            $data = array_merge($data, [
                'sku'       => $data['sku'] ?? ($data['item_code'] ?? null),
                'item_code' => $data['item_code'] ?? ($data['sku'] ?? null),
                'name'      => $data['name'] ?? ($data['item_name'] ?? 'Manual Item'),
                'item_name' => $data['item_name'] ?? ($data['name'] ?? 'Manual Item'),
            ]);
        }

        $quantity = $data['quantity'] ?? $data['qty'] ?? 0;
        $unitPrice = $data['price'] ?? $data['unit_price'] ?? 0;
        $lineTotal = $data['total'] ?? $data['line_total'] ?? 0;
        $lineSubtotal = $data['line_subtotal'] ?? $lineTotal;

        $data['quantity'] = $quantity;
        $data['price'] = $unitPrice;
        $data['total'] = $lineTotal;

        if (Schema::hasColumn('quote_items', 'qty')) {
            $data['qty'] = $quantity;
        } else {
            unset($data['qty']);
        }

        if (Schema::hasColumn('quote_items', 'unit_price')) {
            $data['unit_price'] = $unitPrice;
        } else {
            unset($data['unit_price']);
        }

        if (Schema::hasColumn('quote_items', 'line_subtotal')) {
            $data['line_subtotal'] = $lineSubtotal;
        } else {
            unset($data['line_subtotal']);
        }

        if (Schema::hasColumn('quote_items', 'line_total')) {
            $data['line_total'] = $lineTotal;
        } else {
            unset($data['line_total']);
        }

        foreach (['item_code', 'item_name', 'unit', 'sort_order', 'color_variant_id', 'color_variant_name', 'preview_image'] as $column) {
            if (! Schema::hasColumn('quote_items', $column)) {
                unset($data[$column]);
            }
        }

        // Normalize color variant fields: convert empty strings to null and cast ids to int
        if (array_key_exists('color_variant_id', $data)) {
            if ($data['color_variant_id'] === '' || $data['color_variant_id'] === null) {
                $data['color_variant_id'] = null;
            } else {
                $data['color_variant_id'] = (int) $data['color_variant_id'];
            }
        }

        if (array_key_exists('color_variant_name', $data)) {
            if ($data['color_variant_name'] === '') {
                $data['color_variant_name'] = null;
            }
        }

        return $data;
    }
}
