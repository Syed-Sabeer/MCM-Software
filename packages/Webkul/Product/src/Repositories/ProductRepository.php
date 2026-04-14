<?php

namespace Webkul\Product\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Contracts\Product;
use Webkul\Product\Models\ProductColor;
use Webkul\Product\Models\ProductConsumption;
use Webkul\Product\Models\ProductKeyPoint;
use Webkul\Product\Models\ProductOtherImage;
use Webkul\Product\Models\ProductPricingChart;
use Webkul\Product\Models\ProductPricingChartTier;
use Webkul\Product\Models\ProductPricingChartType;
use Webkul\Product\Models\ProductProductionSection;
use Webkul\Product\Models\ProductProductionSectionItem;

class ProductRepository extends Repository
{
    /**
     * Searchable fields.
     */
    protected $fieldSearchable = [
        'sku',
        'internal_code',
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
        $otherImageColors = $data['other_image_colors'] ?? [];
        $colors = $data['colors'] ?? [];
        $keyPoints = $data['key_points'] ?? [];
        $pricingCharts = $data['pricing_charts'] ?? [];
        $consumptions = $data['consumptions'] ?? [];
        $productionSections = $data['production_sections'] ?? [];
        $productData = collect($data)
            ->except(['other_images', 'other_image_colors', 'colors', 'key_points', 'pricing_charts', 'consumptions', 'production_sections'])
            ->only($this->model->getFillable())
            ->toArray();

        if (isset($productData['customer_organization_id']) && $productData['customer_organization_id'] === '') {
            $productData['customer_organization_id'] = null;
        }

        $product = parent::create($productData);

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $product->id,
        ]));

        $colorIndexToId = [];
        foreach ($colors as $sortOrder => $color) {
            if (! empty($color['name']) || ! empty($color['color_code'])) {
                $pc = ProductColor::create([
                    'product_id'  => $product->id,
                    'name'        => $color['name'] ?? '',
                    'color_code' => $color['color_code'] ?? '#000000',
                    'cost_price'  => isset($color['cost_price']) && $color['cost_price'] !== '' ? $color['cost_price'] : null,
                    'selling_price' => isset($color['selling_price']) && $color['selling_price'] !== '' ? $color['selling_price'] : null,
                    'sort_order'  => $sortOrder,
                ]);
                $colorIndexToId[$sortOrder] = $pc->id;
            }
        }

        foreach ($otherImages as $sortOrder => $img) {
            if (! empty($img['path'])) {
                $colorId = null;
                $colorRef = $otherImageColors[$sortOrder] ?? null;
                if ($colorRef && strpos($colorRef, 'new_') === 0) {
                    $colorIndex = (int) str_replace('new_', '', $colorRef);
                    $colorId = $colorIndexToId[$colorIndex] ?? null;
                }
                ProductOtherImage::create([
                    'product_id'     => $product->id,
                    'path'           => $img['path'],
                    'original_name'  => $img['original_name'] ?? null,
                    'sort_order'     => $sortOrder,
                    'color_id'       => $colorId,
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
            $types = $chart['types'] ?? [];
            
            // Support legacy structure (type + tiers directly on chart)
            $legacyType = $chart['type'] ?? '';
            $legacyTiers = $chart['tiers'] ?? [];
            
            if ($heading !== '' || ! empty($types) || $legacyType !== '' || ! empty($legacyTiers)) {
                $pc = ProductPricingChart::create([
                    'product_id' => $product->id,
                    'heading'    => $heading,
                    'type'       => $legacyType,
                    'sort_order' => $chartOrder,
                ]);
                
                // New structure: types with tiers
                foreach ($types as $typeOrder => $typeData) {
                    $typeName = $typeData['type'] ?? '';
                    $tiers = $typeData['tiers'] ?? [];
                    if ($typeName !== '' || ! empty($tiers)) {
                        $pct = ProductPricingChartType::create([
                            'product_pricing_chart_id' => $pc->id,
                            'type'       => $typeName,
                            'sort_order' => $typeOrder,
                        ]);
                        foreach ($tiers as $tierOrder => $tier) {
                            $qty = $tier['quantity'] ?? 0;
                            $price = $tier['price'] ?? 0;
                            if ($qty !== '' || $price !== '' || $qty !== null || $price !== null) {
                                ProductPricingChartTier::create([
                                    'product_pricing_chart_id'      => $pc->id,
                                    'product_pricing_chart_type_id' => $pct->id,
                                    'quantity'   => $qty,
                                    'price'      => $price,
                                    'sort_order' => $tierOrder,
                                ]);
                            }
                        }
                    }
                }
                
                // Legacy structure: tiers directly on chart
                foreach ($legacyTiers as $tierOrder => $tier) {
                    $qty = $tier['quantity'] ?? 0;
                    $price = $tier['price'] ?? 0;
                    if ($qty !== '' || $price !== '' || $qty !== null || $price !== null) {
                        ProductPricingChartTier::create([
                            'product_pricing_chart_id' => $pc->id,
                            'quantity'   => $qty,
                            'price'      => $price,
                            'sort_order' => $tierOrder,
                        ]);
                    }
                }
            }
        }

        $this->syncConsumptions($product->id, $consumptions);
        $this->syncProductionSections($product->id, $productionSections);

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
        $otherImageColors = $data['other_image_colors'] ?? [];
        $existingImageIds = $data['existing_image_ids'] ?? [];
        $existingImageColors = $data['existing_image_colors'] ?? [];
        $deleteImageIds = $data['delete_image_ids'] ?? '';
        $replaceImages = $data['replace_images'] ?? [];
        $colors = $data['colors'] ?? [];
        $keyPoints = $data['key_points'] ?? [];
        $pricingCharts = $data['pricing_charts'] ?? [];
        $consumptions = $data['consumptions'] ?? [];
        $productionSections = $data['production_sections'] ?? [];
        $productData = collect($data)
            ->except([
                'other_images',
                'other_image_colors',
                'existing_image_ids',
                'existing_image_colors',
                'delete_image_ids',
                'replace_images',
                'colors',
                'key_points',
                'pricing_charts',
                'consumptions',
                'production_sections',
            ])
            ->only($this->model->getFillable())
            ->toArray();

        if (isset($productData['customer_organization_id']) && $productData['customer_organization_id'] === '') {
            $productData['customer_organization_id'] = null;
        }

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

        // Delete removed images
        if (! empty($deleteImageIds)) {
            $idsToDelete = array_filter(explode(',', $deleteImageIds));
            foreach ($idsToDelete as $imageIdToDelete) {
                $imageToDelete = ProductOtherImage::where('id', $imageIdToDelete)->where('product_id', $id)->first();
                if ($imageToDelete) {
                    if ($imageToDelete->path && \Illuminate\Support\Facades\Storage::disk('public')->exists($imageToDelete->path)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($imageToDelete->path);
                    }
                    $imageToDelete->delete();
                }
            }
        }

        // Sync colors: replace all and build index-to-id map
        ProductColor::where('product_id', $id)->delete();
        $colorIndexToId = [];
        foreach ($colors as $sortOrder => $color) {
            if (! empty($color['name']) || ! empty($color['color_code'])) {
                $pc = ProductColor::create([
                    'product_id'  => $id,
                    'name'        => $color['name'] ?? '',
                    'color_code' => $color['color_code'] ?? '#000000',
                    'cost_price'  => isset($color['cost_price']) && $color['cost_price'] !== '' ? $color['cost_price'] : null,
                    'selling_price' => isset($color['selling_price']) && $color['selling_price'] !== '' ? $color['selling_price'] : null,
                    'sort_order'  => $sortOrder,
                ]);
                $colorIndexToId[$sortOrder] = $pc->id;
            }
        }

        // Replace images
        foreach ($replaceImages as $imageId => $imgData) {
            $existingImage = ProductOtherImage::where('id', $imageId)->where('product_id', $id)->first();
            if ($existingImage && ! empty($imgData['path'])) {
                if ($existingImage->path && \Illuminate\Support\Facades\Storage::disk('public')->exists($existingImage->path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($existingImage->path);
                }
                $existingImage->update([
                    'path' => $imgData['path'],
                    'original_name' => $imgData['original_name'] ?? null,
                ]);
            }
        }

        // Update existing image color associations
        foreach ($existingImageIds as $idx => $imageId) {
            $colorRef = $existingImageColors[$idx] ?? null;
            $colorId = null;
            if ($colorRef && strpos($colorRef, 'new_') === 0) {
                $colorIndex = (int) str_replace('new_', '', $colorRef);
                $colorId = $colorIndexToId[$colorIndex] ?? null;
            }
            ProductOtherImage::where('id', $imageId)->update(['color_id' => $colorId]);
        }

        // Append new other images
        foreach ($otherImages as $sortOrder => $img) {
            if (! empty($img['path'])) {
                $colorId = null;
                $colorRef = $otherImageColors[$sortOrder] ?? null;
                if ($colorRef && strpos($colorRef, 'new_') === 0) {
                    $colorIndex = (int) str_replace('new_', '', $colorRef);
                    $colorId = $colorIndexToId[$colorIndex] ?? null;
                }
                $maxOrder = (int) ProductOtherImage::where('product_id', $id)->max('sort_order');
                ProductOtherImage::create([
                    'product_id'     => $id,
                    'path'           => $img['path'],
                    'original_name'  => $img['original_name'] ?? null,
                    'sort_order'     => $maxOrder + 1 + $sortOrder,
                    'color_id'       => $colorId,
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

        // Sync pricing charts: replace all (charts + types + tiers)
        $existingCharts = ProductPricingChart::where('product_id', $id)->get();
        foreach ($existingCharts as $ec) {
            ProductPricingChartTier::where('product_pricing_chart_id', $ec->id)->delete();
            ProductPricingChartType::where('product_pricing_chart_id', $ec->id)->delete();
        }
        ProductPricingChart::where('product_id', $id)->delete();
        
        foreach ($pricingCharts as $chartOrder => $chart) {
            $heading = $chart['heading'] ?? '';
            $types = $chart['types'] ?? [];
            
            // Support legacy structure
            $legacyType = $chart['type'] ?? '';
            $legacyTiers = $chart['tiers'] ?? [];
            
            if ($heading !== '' || ! empty($types) || $legacyType !== '' || ! empty($legacyTiers)) {
                $pc = ProductPricingChart::create([
                    'product_id' => $id,
                    'heading'    => $heading,
                    'type'       => $legacyType,
                    'sort_order' => $chartOrder,
                ]);
                
                // New structure: types with tiers
                foreach ($types as $typeOrder => $typeData) {
                    $typeName = $typeData['type'] ?? '';
                    $tiers = $typeData['tiers'] ?? [];
                    if ($typeName !== '' || ! empty($tiers)) {
                        $pct = ProductPricingChartType::create([
                            'product_pricing_chart_id' => $pc->id,
                            'type'       => $typeName,
                            'sort_order' => $typeOrder,
                        ]);
                        foreach ($tiers as $tierOrder => $tier) {
                            $qty = $tier['quantity'] ?? 0;
                            $price = $tier['price'] ?? 0;
                            if ($qty !== '' || $price !== '' || $qty !== null || $price !== null) {
                                ProductPricingChartTier::create([
                                    'product_pricing_chart_id'      => $pc->id,
                                    'product_pricing_chart_type_id' => $pct->id,
                                    'quantity'   => $qty,
                                    'price'      => $price,
                                    'sort_order' => $tierOrder,
                                ]);
                            }
                        }
                    }
                }
                
                // Legacy structure: tiers directly on chart
                foreach ($legacyTiers as $tierOrder => $tier) {
                    $qty = $tier['quantity'] ?? 0;
                    $price = $tier['price'] ?? 0;
                    if ($qty !== '' || $price !== '' || $qty !== null || $price !== null) {
                        ProductPricingChartTier::create([
                            'product_pricing_chart_id' => $pc->id,
                            'quantity'   => $qty,
                            'price'      => $price,
                            'sort_order' => $tierOrder,
                        ]);
                    }
                }
            }
        }

        $this->syncConsumptions($id, $consumptions);
        $this->syncProductionSections($id, $productionSections);

        return $product;
    }

    /**
     * Replace product material consumptions with supplied rows.
     */
    protected function syncConsumptions(int $productId, array $consumptions): void
    {
        ProductConsumption::where('product_id', $productId)->delete();

        foreach ($consumptions as $sortOrder => $consumption) {
            if (! is_array($consumption)) {
                continue;
            }

            $name = trim((string) ($consumption['name'] ?? ''));
            $unit = trim((string) ($consumption['unit'] ?? ''));
            $qty = $consumption['qty'] ?? null;

            if ($name === '' || $unit === '' || $qty === null || $qty === '') {
                continue;
            }

            ProductConsumption::create([
                'product_id'  => $productId,
                'name'        => $name,
                'qty'         => $qty,
                'unit'        => $unit,
                'sort_order'  => $sortOrder,
            ]);
        }
    }

    /**
     * Replace product production sections and section items.
     */
    protected function syncProductionSections(int $productId, array $productionSections): void
    {
        $existingSections = ProductProductionSection::where('product_id', $productId)->get(['id']);

        foreach ($existingSections as $existingSection) {
            ProductProductionSectionItem::where('product_production_section_id', $existingSection->id)->delete();
        }

        ProductProductionSection::where('product_id', $productId)->delete();

        foreach ($productionSections as $sectionOrder => $section) {
            if (! is_array($section)) {
                continue;
            }

            $sectionName = trim((string) ($section['section_name'] ?? ''));
            $items = $section['items'] ?? [];

            if ($sectionName === '') {
                continue;
            }

            $savedSection = ProductProductionSection::create([
                'product_id'    => $productId,
                'section_name'  => $sectionName,
                'sort_order'    => $sectionOrder,
            ]);

            foreach ($items as $itemOrder => $item) {
                if (! is_array($item)) {
                    continue;
                }

                $name = trim((string) ($item['name'] ?? ''));
                $unit = trim((string) ($item['unit'] ?? ''));
                $qty = $item['qty'] ?? null;

                if ($name === '' || $unit === '' || $qty === null || $qty === '') {
                    continue;
                }

                ProductProductionSectionItem::create([
                    'product_production_section_id' => $savedSection->id,
                    'name'                          => $name,
                    'qty'                           => $qty,
                    'unit'                          => $unit,
                    'sort_order'                    => $itemOrder,
                ]);
            }
        }
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
