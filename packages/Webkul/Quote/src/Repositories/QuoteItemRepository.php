<?php

namespace Webkul\Quote\Repositories;

use Illuminate\Container\Container;
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

        $quoteItem = parent::update($data, $id);

        return $quoteItem;
    }
}
