<?php

namespace Webkul\Admin\Http\Controllers\Products;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Product\ProductDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Resources\ProductResource;
use Webkul\Contact\Models\Organization;
use Webkul\Product\Repositories\ProductRepository;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected ProductRepository $productRepository)
    {
        request()->request->add(['entity_type' => 'products']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(ProductDataGrid::class)->process();
        }

        return view('admin::products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $customers = Organization::query()
            ->whereIn('type', ['customer', 'Customer'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin::products.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AttributeForm $request): RedirectResponse
    {
        $this->validateProductFields($request, false, null);

        Event::dispatch('product.create.before');

        $data = $this->prepareProductData($request->all(), null);

        $product = $this->productRepository->create($data);

        Event::dispatch('product.create.after', $product);

        session()->flash('success', trans('admin::app.products.index.create-success'));

        return redirect()->route('admin.products.index');
    }

    /**
     * Show the form for viewing the specified resource.
     */
    public function view(int $id): View
    {
        $product = $this->productRepository
            ->with(['customerOrganization', 'colors', 'otherImages.color', 'consumptions', 'productionSections.items'])
            ->findOrFail($id);

        return view('admin::products.view', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View|JsonResponse
    {
        $product = $this->productRepository
            ->with(['customerOrganization', 'consumptions', 'productionSections.items'])
            ->findOrFail($id);

        $inventories = $product->inventories()
            ->with('location')
            ->get()
            ->map(function ($inventory) {
                return [
                    'id'                    => $inventory->id,
                    'name'                  => $inventory->location->name,
                    'warehouse_id'          => $inventory->warehouse_id,
                    'warehouse_location_id' => $inventory->warehouse_location_id,
                    'in_stock'              => $inventory->in_stock,
                    'allocated'             => $inventory->allocated,
                ];
            });

        $customers = Organization::query()
            ->whereIn('type', ['customer', 'Customer'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin::products.edit', compact('product', 'inventories', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(AttributeForm $request, int $id): JsonResponse|RedirectResponse
    {
        $this->validateProductFields($request, true, $id);

        Event::dispatch('product.update.before', $id);

        $data = $this->prepareProductData($request->all(), $id);

        $product = $this->productRepository->update($data, $id);

        Event::dispatch('product.update.after', $product);

        if (request()->ajax()) {
            return response()->json([
                'message' => trans('admin::app.products.index.update-success'),
            ]);
        }

        session()->flash('success', trans('admin::app.products.index.update-success'));

        return redirect()->route('admin.products.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeInventories(int $id, ?int $warehouseId = null): JsonResponse
    {
        $this->validate(request(), [
            'inventories'                         => 'array',
            'inventories.*.warehouse_location_id' => 'required',
            'inventories.*.warehouse_id'          => 'required',
            'inventories.*.in_stock'              => 'required|integer|min:0',
            'inventories.*.allocated'             => 'required|integer|min:0',
        ]);

        $product = $this->productRepository->findOrFail($id);

        Event::dispatch('product.update.before', $id);

        $this->productRepository->saveInventories(request()->all(), $id, $warehouseId);

        Event::dispatch('product.update.after', $product);

        return new JsonResponse([
            'message' => trans('admin::app.products.index.update-success'),
        ], 200);
    }

    /**
     * Search product results
     */
    public function search(): JsonResource
    {
        $query = trim((string) request('query', ''));
        $organizationId = request('organization_id');

        $products = $this->productRepository
            ->with(['colors', 'otherImages'])
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('sku', 'like', "%{$query}%")
                        ->orWhere('internal_code', 'like', "%{$query}%");
                });
            })
            ->when($organizationId, function ($builder) use ($organizationId) {
                $builder->where(function ($inner) use ($organizationId) {
                    $inner->whereNull('customer_organization_id')
                        ->orWhere('customer_organization_id', $organizationId);
                });
            }, function ($builder) {
                $builder->whereNull('customer_organization_id');
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return ProductResource::collection($products);
    }

    /**
     * Toggle product publish on website.
     */
    public function togglePublish(int $id): JsonResponse
    {
        try {
            $product = $this->productRepository->findOrFail($id);

            if (! \Illuminate\Support\Facades\Schema::hasColumn('products', 'publish_on_website')) {
                return new JsonResponse([
                    'message' => 'Database column "publish_on_website" is missing. Run sql-updates.sql in phpMyAdmin to add it.',
                ], 500);
            }

            $product->publish_on_website = ! $product->publish_on_website;
            $product->save();

            return new JsonResponse([
                'message' => trans('admin::app.products.index.update-success'),
                'publish_on_website' => (bool) $product->publish_on_website,
            ], 200);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Returns product inventories grouped by warehouse.
     */
    public function warehouses(int $id): JsonResponse
    {
        $warehouses = $this->productRepository->getInventoriesGroupedByWarehouse($id);

        return response()->json(array_values($warehouses));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = $this->productRepository->findOrFail($id);

        try {
            Event::dispatch('settings.products.delete.before', $id);

            $product->delete($id);

            Event::dispatch('settings.products.delete.after', $id);

            return new JsonResponse([
                'message' => trans('admin::app.products.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => trans('admin::app.products.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $indices = $massDestroyRequest->input('indices');

        foreach ($indices as $index) {
            Event::dispatch('product.delete.before', $index);

            $this->productRepository->delete($index);

            Event::dispatch('product.delete.after', $index);
        }

        return new JsonResponse([
            'message' => trans('admin::app.products.index.delete-success'),
        ]);
    }

    /**
     * Duplicate the specified product with all its relationships.
     */
    public function duplicate(int $id): JsonResponse
    {
        try {
            $original = $this->productRepository->with([
                'category',
                'otherImages',
                'colors',
                'keyPoints',
                'pricingCharts.types.tiers',
                'consumptions',
                'productionSections.items',
            ])->findOrFail($id);

            Event::dispatch('product.create.before');

            // Prepare base product data with "(Copy)" added to name
            $newData = [
                'name'            => $original->name . ' (Copy)',
                'slug'            => '',  // Will be auto-generated from name
                'sku'             => '',  // Will be auto-generated
                'internal_code'   => $original->internal_code,
                'customer_organization_id' => $original->customer_organization_id,
                'description'     => $original->description,
                'quantity'        => $original->quantity ?? 0,
                'price'           => $original->price ?? 0,
                'category_id'     => $original->category_id,
                'style'           => $original->style,
                'size'            => $original->size,
                'additional_info' => $original->additional_info,
                'shipping_info'   => $original->shipping_info,
                'publish_on_website' => false, // Set to unpublished by default
                'entity_type'     => 'products', // Required for attribute value repository
            ];

            // Copy cover image if exists
            if ($original->cover_image) {
                $newData['cover_image'] = $this->duplicateFile($original->cover_image, 'product-images');
            }

            // Prepare colors data
            $colors = [];
            $colorMapping = []; // Map old color IDs to new array indices
            foreach ($original->colors as $idx => $color) {
                $colors[] = [
                    'name'       => $color->name,
                    'color_code' => $color->color_code,
                ];
                $colorMapping[$color->id] = $idx;
            }
            $newData['colors'] = $colors;

            // Prepare other images data (will be created after colors are saved)
            $otherImagesData = [];
            $otherImageColors = [];
            foreach ($original->otherImages as $idx => $image) {
                if ($image->path) {
                    $newPath = $this->duplicateFile($image->path, 'product-other-images');
                    $otherImagesData[] = [
                        'path'          => $newPath,
                        'original_name' => $image->original_name,
                    ];
                    // If image had a color reference, map it to new color array index
                    if ($image->color_id && isset($colorMapping[$image->color_id])) {
                        $otherImageColors[] = 'new_' . $colorMapping[$image->color_id];
                    } else {
                        $otherImageColors[] = null;
                    }
                }
            }
            $newData['other_images'] = $otherImagesData;
            $newData['other_image_colors'] = $otherImageColors;

            // Prepare key points data
            $keyPoints = [];
            foreach ($original->keyPoints as $kp) {
                $keyPoints[] = [
                    'key_heading' => $kp->key_heading,
                    'key_point'   => $kp->key_point,
                ];
            }
            $newData['key_points'] = $keyPoints;

            // Prepare pricing charts data
            $pricingCharts = [];
            foreach ($original->pricingCharts as $chart) {
                $types = [];
                foreach ($chart->types as $type) {
                    $tiers = [];
                    foreach ($type->tiers as $tier) {
                        $tiers[] = [
                            'quantity' => $tier->quantity,
                            'price'    => $tier->price,
                        ];
                    }
                    $types[] = [
                        'type'  => $type->type,
                        'tiers' => $tiers,
                    ];
                }
                $pricingCharts[] = [
                    'heading' => $chart->heading,
                    'types'   => $types,
                ];
            }
            $newData['pricing_charts'] = $pricingCharts;

            $newData['consumptions'] = $original->consumptions->map(fn ($consumption) => [
                'name' => $consumption->name,
                'qty'  => $consumption->qty,
                'unit' => $consumption->unit,
            ])->toArray();

            $newData['production_sections'] = $original->productionSections->map(fn ($section) => [
                'section_name' => $section->section_name,
                'items' => $section->items->map(fn ($item) => [
                    'name' => $item->name,
                    'qty'  => $item->qty,
                    'unit' => $item->unit,
                ])->toArray(),
            ])->toArray();

            // Generate unique slug and SKU
            $data = $this->prepareProductData($newData, null);

            // Create the duplicate product
            $product = $this->productRepository->create($data);

            Event::dispatch('product.create.after', $product);

            return response()->json([
                'message' => trans('admin::app.products.index.duplicate-success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to duplicate product: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Duplicate a file in storage.
     */
    protected function duplicateFile(string $originalPath, string $directory): string
    {
        if (! Storage::disk('public')->exists($originalPath)) {
            return $originalPath;
        }

        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $newPath = $directory . '/' . $filename;

        Storage::disk('public')->copy($originalPath, $newPath);

        return $newPath;
    }

    /**
     * Validate product-specific ERP catalog fields.
     */
    protected function validateProductFields($request, bool $isUpdate, ?int $productId): void
    {
        $skuRule = ['required', 'string', 'max:255'];

        if ($isUpdate && $productId) {
            $skuRule[] = Rule::unique('products', 'sku')->ignore($productId);
        } else {
            $skuRule[] = Rule::unique('products', 'sku');
        }

        $rules = [
            'sku'                  => $skuRule,
            'internal_code'        => ['nullable', 'string', 'max:255'],
            'name'                 => ['required', 'string', 'max:255'],
            'customer_organization_id' => [
                'nullable',
                Rule::exists('organizations', 'id')->where(fn ($query) => $query->whereIn('type', ['customer', 'Customer'])),
            ],
            'size'                 => ['nullable', 'string', 'max:100'],
            'cost_price'           => ['nullable', 'numeric', 'min:0'],
            'selling_price'        => ['nullable', 'numeric', 'min:0'],
            'colors'               => ['nullable', 'array'],
            'colors.*.name'        => ['nullable', 'string', 'max:100'],
            'colors.*.color_code'  => ['nullable', 'string', 'max:20'],
            'consumptions'         => ['nullable', 'array'],
            'consumptions.*.name'  => ['required', 'string', 'max:255'],
            'consumptions.*.qty'   => ['required', 'numeric'],
            'consumptions.*.unit'  => ['required', 'string', 'max:100'],
            'production_sections'                          => ['nullable', 'array'],
            'production_sections.*.section_name'           => ['required', 'string', 'max:255'],
            'production_sections.*.items'                  => ['required', 'array', 'min:1'],
            'production_sections.*.items.*.name'           => ['required', 'string', 'max:255'],
            'production_sections.*.items.*.qty'            => ['required', 'numeric'],
            'production_sections.*.items.*.unit'           => ['required', 'string', 'max:100'],
        ];

        $request->validate($rules);
    }

    /**
     * Check if a slug is available (AJAX endpoint).
     */
    public function checkSlug(): JsonResponse
    {
        $slug = trim(request()->input('slug', ''));
        $productId = request()->input('product_id');

        if ($slug === '') {
            return new JsonResponse(['available' => false, 'message' => 'Slug is empty']);
        }

        $query = $this->productRepository->where('slug', $slug);

        if ($productId) {
            $query->where('id', '!=', $productId);
        }

        $exists = $query->exists();

        return new JsonResponse([
            'available' => ! $exists,
            'slug'      => $slug,
            'message'   => $exists ? 'Slug already exists' : 'Slug is available',
        ]);
    }

    /**
     * Generate a unique slug based on base slug.
     */
    protected function generateUniqueSlug(string $baseSlug, ?int $productId): string
    {
        if ($baseSlug === '') {
            $baseSlug = 'product';
        }

        $slug = $baseSlug;
        $counter = 1;

        $query = $this->productRepository->where('slug', $slug);
        if ($productId) {
            $query->where('id', '!=', $productId);
        }

        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;

            $query = $this->productRepository->where('slug', $slug);
            if ($productId) {
                $query->where('id', '!=', $productId);
            }
        }

        return $slug;
    }

    /**
     * Generate a unique SKU based on product name.
     */
    protected function generateUniqueSku(string $name, ?int $productId): string
    {
        $baseSku = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', substr($name, 0, 20)));
        if ($baseSku === '') {
            $baseSku = 'PROD';
        }
        $baseSku .= '-' . strtoupper(substr(uniqid(), -6));

        $sku = $baseSku;
        $counter = 1;

        $query = $this->productRepository->where('sku', $sku);
        if ($productId) {
            $query->where('id', '!=', $productId);
        }

        while ($query->exists()) {
            $sku = $baseSku . '-' . $counter;
            $counter++;

            $query = $this->productRepository->where('sku', $sku);
            if ($productId) {
                $query->where('id', '!=', $productId);
            }
        }

        return $sku;
    }

    /**
     * Prepare product data: upload files and build other_images/colors arrays.
     *
     * @param  array  $data
     * @param  int|null  $productId
     * @return array
     */
    protected function prepareProductData(array $data, ?int $productId): array
    {
        $data['sku'] = isset($data['sku']) ? trim((string) $data['sku']) : '';

        $data['internal_code'] = isset($data['internal_code'])
            ? trim((string) $data['internal_code'])
            : '';

        if ($data['internal_code'] === '') {
            $data['internal_code'] = $data['sku'];
        }

        $baseSlug = isset($data['slug']) && trim((string) $data['slug']) !== ''
            ? \Illuminate\Support\Str::slug(trim($data['slug']))
            : \Illuminate\Support\Str::slug($data['name'] ?? '');

        $data['slug'] = $this->generateUniqueSlug($baseSlug, $productId);

        if (empty($data['sku'])) {
            $data['sku'] = $this->generateUniqueSku($data['name'] ?? 'product', $productId);
        }

        $data['cost_price'] = isset($data['cost_price']) && $data['cost_price'] !== ''
            ? $data['cost_price']
            : null;

        $data['selling_price'] = isset($data['selling_price']) && $data['selling_price'] !== ''
            ? $data['selling_price']
            : null;

        if ($data['selling_price'] !== null) {
            // Keep legacy price field aligned with ERP selling price.
            $data['price'] = $data['selling_price'];
        }

        if (request()->hasFile('cover_image')) {
            $path = request()->file('cover_image')->store('product-images', 'public');
            $data['cover_image'] = $path;
        }

        $otherImages = [];
        $otherImageColors = [];
        $inputColors = $data['other_image_colors'] ?? [];
        if (request()->hasFile('other_images')) {
            foreach (request()->file('other_images') as $idx => $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('product-other-images', 'public');
                    $otherImages[] = ['path' => $path, 'original_name' => $file->getClientOriginalName()];
                    $otherImageColors[] = $inputColors[$idx] ?? null;
                }
            }
        }
        $data['other_images'] = $otherImages;
        $data['other_image_colors'] = $otherImageColors;

        $replaceImages = [];
        if (request()->hasFile('replace_images')) {
            foreach (request()->file('replace_images') as $imageId => $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('product-other-images', 'public');
                    $replaceImages[$imageId] = ['path' => $path, 'original_name' => $file->getClientOriginalName()];
                }
            }
        }
        $data['replace_images'] = $replaceImages;

        $colors = [];
        foreach ($data['colors'] ?? [] as $c) {
            if (is_array($c) && (isset($c['name']) || isset($c['color_code']))) {
                $colors[] = [
                    'name'       => $c['name'] ?? '',
                    'color_code' => $c['color_code'] ?? '#000000',
                ];
            }
        }
        $data['colors'] = $colors;

        $keyPoints = [];
        foreach ($data['key_points'] ?? [] as $kp) {
            if (is_array($kp) && (isset($kp['key_heading']) || isset($kp['key_point']))) {
                $keyPoints[] = [
                    'key_heading' => $kp['key_heading'] ?? '',
                    'key_point'   => $kp['key_point'] ?? '',
                ];
            }
        }
        $data['key_points'] = $keyPoints;

        $pricingCharts = [];
        foreach ($data['pricing_charts'] ?? [] as $chart) {
            if (! is_array($chart)) {
                continue;
            }
            $types = [];
            foreach ($chart['types'] ?? [] as $typeData) {
                if (! is_array($typeData)) {
                    continue;
                }
                $tiers = [];
                foreach ($typeData['tiers'] ?? [] as $tier) {
                    if (is_array($tier) && (isset($tier['quantity']) || isset($tier['price']))) {
                        $tiers[] = [
                            'quantity' => $tier['quantity'] ?? 0,
                            'price'    => $tier['price'] ?? 0,
                        ];
                    }
                }
                $types[] = [
                    'type'  => $typeData['type'] ?? '',
                    'tiers' => $tiers,
                ];
            }
            $pricingCharts[] = [
                'heading' => $chart['heading'] ?? '',
                'types'   => $types,
            ];
        }
        $data['pricing_charts'] = $pricingCharts;

        $consumptions = [];
        foreach ($data['consumptions'] ?? [] as $consumption) {
            if (! is_array($consumption)) {
                continue;
            }

            $name = trim((string) ($consumption['name'] ?? ''));
            $unit = trim((string) ($consumption['unit'] ?? ''));
            $qty = $consumption['qty'] ?? null;

            if ($name === '' && ($qty === null || $qty === '') && $unit === '') {
                continue;
            }

            $consumptions[] = [
                'name' => $name,
                'qty'  => $qty,
                'unit' => $unit,
            ];
        }
        $data['consumptions'] = $consumptions;

        $productionSections = [];
        foreach ($data['production_sections'] ?? [] as $section) {
            if (! is_array($section)) {
                continue;
            }

            $sectionName = trim((string) ($section['section_name'] ?? ''));
            $items = [];

            foreach ($section['items'] ?? [] as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $name = trim((string) ($item['name'] ?? ''));
                $unit = trim((string) ($item['unit'] ?? ''));
                $qty = $item['qty'] ?? null;

                if ($name === '' && ($qty === null || $qty === '') && $unit === '') {
                    continue;
                }

                $items[] = [
                    'name' => $name,
                    'qty'  => $qty,
                    'unit' => $unit,
                ];
            }

            if ($sectionName === '' && empty($items)) {
                continue;
            }

            $productionSections[] = [
                'section_name' => $sectionName,
                'items'        => $items,
            ];
        }
        $data['production_sections'] = $productionSections;

        return $data;
    }
}
