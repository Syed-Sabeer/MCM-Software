<?php

namespace Webkul\Product\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Contracts\Product;
use Webkul\Product\Models\ProductColor;
use Webkul\Product\Models\ProductKeyPoint;
use Webkul\Product\Models\ProductOtherImage;
use Webkul\Product\Models\ProductPricingChart;
use Webkul\Product\Models\ProductPricingChartTier;

class ProductRepository extends Repository
{
    /**
     * Searchable fields.
     */
    protected $fieldSearchable = [
        'sku',
        'name',
        'description',
    ];

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository,
        protected ProductInventoryRepository $productInventoryRepository,
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
        return Product::class;
    }

    /**
     * Create.
     *
     * @return \Webkul\Product\Contracts\Product
     */
    public function create(array $data)
    {
        $otherImages = $data['other_images'] ?? [];
        $colors = $data['colors'] ?? [];
        $keyPoints = $data['key_points'] ?? [];
        $pricingCharts = $data['pricing_charts'] ?? [];
        $productData = collect($data)->except(['other_images', 'colors', 'key_points', 'pricing_charts'])->only($this->model->getFillable())->toArray();

        $product = parent::create($productData);

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $product->id,
        ]));

        foreach ($otherImages as $sortOrder => $img) {
            if (! empty($img['path'])) {
                ProductOtherImage::create([
                    'product_id'     => $product->id,
                    'path'           => $img['path'],
                    'original_name'  => $img['original_name'] ?? null,
                    'sort_order'     => $sortOrder,
                ]);
            }
        }

        foreach ($colors as $sortOrder => $color) {
            if (! empty($color['name']) || ! empty($color['color_code'])) {
                ProductColor::create([
                    'product_id'  => $product->id,
                    'name'        => $color['name'] ?? '',
                    'color_code' => $color['color_code'] ?? '#000000',
                    'sort_order'  => $sortOrder,
                ]);
            }
        }

        foreach ($keyPoints as $sortOrder => $kp) {
            if (! empty($kp['key_heading']) || ! empty($kp['key_point'])) {
                ProductKeyPoint::create([
                    'product_id'   => $product->id,
                    'key_heading'  => $kp['key_heading'] ?? '',
                    'key_point'    => $kp['key_point'] ?? '',
                    'sort_order'   => $sortOrder,
                ]);
            }
        }

        foreach ($pricingCharts as $chartOrder => $chart) {
            $heading = $chart['heading'] ?? '';
            $type = $chart['type'] ?? '';
            $tiers = $chart['tiers'] ?? [];
            if ($heading !== '' || $type !== '' || ! empty($tiers)) {
                $pc = ProductPricingChart::create([
                    'product_id' => $product->id,
                    'heading'    => $heading,
                    'type'      => $type,
                    'sort_order'=> $chartOrder,
                ]);
                foreach ($tiers as $tierOrder => $tier) {
                    $qty = $tier['quantity'] ?? 0;
                    $price = $tier['price'] ?? 0;
                    if ($qty !== '' || $price !== '' || $qty !== null || $price !== null) {
                        ProductPricingChartTier::create([
                            'product_pricing_chart_id' => $pc->id,
                            'quantity' => $qty,
                            'price'    => $price,
                            'sort_order' => $tierOrder,
                        ]);
                    }
                }
            }
        }

        return $product;
    }

    /**
     * Update.
     *
     * @param  int  $id
     * @param  array  $attribute
     * @return \Webkul\Product\Contracts\Product
     */
    public function update(array $data, $id, $attributes = [])
    {
        $otherImages = $data['other_images'] ?? [];
        $colors = $data['colors'] ?? [];
        $keyPoints = $data['key_points'] ?? [];
        $pricingCharts = $data['pricing_charts'] ?? [];
        $productData = collect($data)->except(['other_images', 'colors', 'key_points', 'pricing_charts'])->only($this->model->getFillable())->toArray();

        $product = parent::update($productData, $id);

        /**
         * If attributes are provided then only save the provided attributes and return.
         */
        if (! empty($attributes)) {
            $conditions = ['entity_type' => $data['entity_type'] ?? 'products'];

            if (isset($data['quick_add'])) {
                $conditions['quick_add'] = 1;
            }

            $attributesCollection = $this->attributeRepository->where($conditions)
                ->whereIn('code', $attributes)
                ->get();

            $this->attributeValueRepository->save(array_merge($data, [
                'entity_id' => $product->id,
            ]), $attributesCollection);

            return $product;
        }

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $product->id,
        ]));

        // Sync colors: replace all
        ProductColor::where('product_id', $id)->delete();
        foreach ($colors as $sortOrder => $color) {
            if (! empty($color['name']) || ! empty($color['color_code'])) {
                ProductColor::create([
                    'product_id'  => $id,
                    'name'        => $color['name'] ?? '',
                    'color_code' => $color['color_code'] ?? '#000000',
                    'sort_order'  => $sortOrder,
                ]);
            }
        }

        // Append new other images
        foreach ($otherImages as $sortOrder => $img) {
            if (! empty($img['path'])) {
                $maxOrder = (int) ProductOtherImage::where('product_id', $id)->max('sort_order');
                ProductOtherImage::create([
                    'product_id'     => $id,
                    'path'           => $img['path'],
                    'original_name'  => $img['original_name'] ?? null,
                    'sort_order'     => $maxOrder + 1 + $sortOrder,
                ]);
            }
        }

        // Sync key points: replace all
        ProductKeyPoint::where('product_id', $id)->delete();
        foreach ($keyPoints as $sortOrder => $kp) {
            if (! empty($kp['key_heading']) || ! empty($kp['key_point'])) {
                ProductKeyPoint::create([
                    'product_id'   => $id,
                    'key_heading'  => $kp['key_heading'] ?? '',
                    'key_point'    => $kp['key_point'] ?? '',
                    'sort_order'   => $sortOrder,
                ]);
            }
        }

        // Sync pricing charts: replace all (charts + tiers)
        $existingCharts = ProductPricingChart::where('product_id', $id)->get();
        foreach ($existingCharts as $ec) {
            ProductPricingChartTier::where('product_pricing_chart_id', $ec->id)->delete();
        }
        ProductPricingChart::where('product_id', $id)->delete();
        foreach ($pricingCharts as $chartOrder => $chart) {
            $heading = $chart['heading'] ?? '';
            $type = $chart['type'] ?? '';
            $tiers = $chart['tiers'] ?? [];
            if ($heading !== '' || $type !== '' || ! empty($tiers)) {
                $pc = ProductPricingChart::create([
                    'product_id' => $id,
                    'heading'    => $heading,
                    'type'      => $type,
                    'sort_order'=> $chartOrder,
                ]);
                foreach ($tiers as $tierOrder => $tier) {
                    $qty = $tier['quantity'] ?? 0;
                    $price = $tier['price'] ?? 0;
                    if ($qty !== '' || $price !== '' || $qty !== null || $price !== null) {
                        ProductPricingChartTier::create([
                            'product_pricing_chart_id' => $pc->id,
                            'quantity' => $qty,
                            'price'    => $price,
                            'sort_order' => $tierOrder,
                        ]);
                    }
                }
            }
        }

        return $product;
    }

    /**
     * Save inventories.
     *
     * @param  int  $id
     * @param  ?int  $warehouseId
     * @return void
     */
    public function saveInventories(array $data, $id, $warehouseId = null)
    {
        $productInventories = $this->productInventoryRepository->where('product_id', $id);

        if ($warehouseId) {
            $productInventories = $productInventories->where('warehouse_id', $warehouseId);
        }

        $previousInventoryIds = $productInventories->pluck('id');

        if (isset($data['inventories'])) {
            foreach ($data['inventories'] as $inventoryId => $inventoryData) {
                if (Str::contains($inventoryId, 'inventory_')) {
                    $this->productInventoryRepository->create(array_merge($inventoryData, [
                        'product_id'   => $id,
                        'warehouse_id' => $warehouseId,
                    ]));
                } else {
                    if (is_numeric($index = $previousInventoryIds->search($inventoryId))) {
                        $previousInventoryIds->forget($index);
                    }

                    $this->productInventoryRepository->update($inventoryData, $inventoryId);
                }
            }
        }

        foreach ($previousInventoryIds as $inventoryId) {
            $this->productInventoryRepository->delete($inventoryId);
        }
    }

    /**
     * Retrieves customers count based on date.
     *
     * @return int
     */
    public function getProductCount($startDate, $endDate)
    {
        return $this
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->count();
    }

    /**
     * Get inventories grouped by warehouse.
     *
     * @param  int  $id
     * @return array
     */
    public function getInventoriesGroupedByWarehouse($id)
    {
        $product = $this->findOrFail($id);

        $warehouses = [];

        foreach ($product->inventories as $inventory) {
            if (! isset($warehouses[$inventory->warehouse_id])) {
                $warehouses[$inventory->warehouse_id] = [
                    'id'        => $inventory->warehouse_id,
                    'name'      => $inventory->warehouse->name,
                    'in_stock'  => $inventory->in_stock,
                    'allocated' => $inventory->allocated,
                    'on_hand'   => $inventory->on_hand,
                ];
            } else {
                $warehouses[$inventory->warehouse_id]['in_stock'] += $inventory->in_stock;
                $warehouses[$inventory->warehouse_id]['allocated'] += $inventory->allocated;
                $warehouses[$inventory->warehouse_id]['on_hand'] += $inventory->on_hand;
            }

            $warehouses[$inventory->warehouse_id]['locations'][] = [
                'id'        => $inventory->warehouse_location_id,
                'name'      => $inventory->location->name,
                'in_stock'  => $inventory->in_stock,
                'allocated' => $inventory->allocated,
                'on_hand'   => $inventory->on_hand,
            ];
        }

        return $warehouses;
    }
}
