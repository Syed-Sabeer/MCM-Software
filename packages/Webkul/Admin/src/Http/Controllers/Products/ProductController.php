<?php

namespace Webkul\Admin\Http\Controllers\Products;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Product\ProductDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Resources\ProductResource;
use Webkul\Contact\Models\Organization;
use Webkul\Product\Models\ColorReference;
use Webkul\Product\Models\MaterialReference;
use Webkul\Product\Models\UnitReference;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Services\UnitConversionService;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ProductRepository $productRepository,
        protected UnitConversionService $unitConversionService
    ) {
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

        $duplicateDraft = null;
        $colorReferences = ColorReference::query()->orderBy('name')->get(['name', 'code']);
        $materialReferences = MaterialReference::with('vendors')->orderBy('name')->get();
        $units = UnitReference::query()->orderBy('name')->get(['name', 'meter_conversion']);
        $vendors = Organization::query()
            ->whereRaw("LOWER(TRIM(type)) IN ('vendor', 'vendors')")
            ->orderBy('name')
            ->get(['id', 'name']);

        if (request()->filled('duplicate_from')) {
            $original = $this->productRepository->with([
                'customerOrganization',
                'otherImages',
                'colors',
                'keyPoints',
                'pricingCharts.types.tiers',
                'consumptions',
                'productionSections.items',
            ])->findOrFail((int) request('duplicate_from'));

            $duplicateDraft = $this->buildDuplicateDraft($original);
        }

        return view('admin::products.create', compact('customers', 'duplicateDraft', 'colorReferences', 'materialReferences', 'units', 'vendors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AttributeForm $request): RedirectResponse
    {
        $original = null;
        $isDuplicate = $request->filled('duplicate_from');

        if ($isDuplicate) {
            $original = $this->productRepository->with(['otherImages.color', 'colors'])
                ->findOrFail((int) $request->input('duplicate_from'));
            $requestedSku = trim((string) $request->input('sku'));

            if ($requestedSku === '' || $this->productRepository->where('sku', $requestedSku)->exists()) {
                $requestedSku = $this->generateDuplicateSku((string) $original->sku);
                $request->merge([
                    'sku'           => $requestedSku,
                    'internal_code' => $requestedSku,
                ]);
            }
        }

        $this->validateProductFields($request, false, null, $isDuplicate);

        Event::dispatch('product.create.before');

        $data = $this->prepareProductData($request->all(), null);

        if ($original) {
            $data = $this->duplicateMediaFromOriginal($original, $data);
        }

        $product = $this->productRepository->create($data);

        Event::dispatch('product.create.after', $product);

        session()->flash('success', trans('admin::app.products.index.create-success'));

        return redirect()->route('admin.products.index');
    }

    /**
     * Create the minimum complete product needed by document line items.
     */
    public function quickStore(): JsonResponse
    {
        $data = request()->validate([
            'name'                     => ['required', 'string', 'max:255'],
            'sku'                      => ['required', 'string', 'max:255', Rule::unique('products', 'sku')],
            'selling_price'            => ['nullable', 'numeric', 'min:0'],
            'customer_organization_id' => [
                'nullable',
                Rule::exists('organizations', 'id')->where(
                    fn ($query) => $query->whereRaw("LOWER(TRIM(type)) = 'customer'")
                ),
            ],
        ]);

        Event::dispatch('product.create.before');

        $payload = $this->prepareProductData([
            'entity_type'             => 'products',
            'quick_add'               => 1,
            'name'                    => trim((string) $data['name']),
            'sku'                     => trim((string) $data['sku']),
            'internal_code'           => trim((string) $data['sku']),
            'customer_organization_id'=> $data['customer_organization_id'] ?? null,
            'selling_price'           => $data['selling_price'] ?? null,
            'quantity'                => 0,
        ], null);

        $product = $this->productRepository->create($payload)->load(['colors', 'otherImages']);

        Event::dispatch('product.create.after', $product);

        return response()->json([
            'data'    => (new ProductResource($product))->resolve(),
            'message' => 'Product created successfully.',
        ], 201);
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

        $colorReferences = ColorReference::query()->orderBy('name')->get(['name', 'code']);
        $materialReferences = MaterialReference::with('vendors')->orderBy('name')->get();
        $units = UnitReference::query()->orderBy('name')->get(['name', 'meter_conversion']);
        $vendors = Organization::query()
            ->whereRaw("LOWER(TRIM(type)) IN ('vendor', 'vendors')")
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin::products.edit', compact('product', 'inventories', 'customers', 'colorReferences', 'materialReferences', 'units', 'vendors'));
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

            $newData = $this->buildDuplicateDraft($original);

            // Copy cover image if exists
            if ($original->cover_image) {
                $newData['cover_image'] = $this->duplicateFile($original->cover_image, 'product-images');
            }

            // Prepare colors data
            $colorMapping = [];
            foreach ($original->colors as $idx => $color) {
                $colorMapping[$color->id] = $idx;
            }

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
                'material_reference_id' => $consumption->material_reference_id,
                'name' => $consumption->name,
                'qty'  => $consumption->qty,
                'unit' => $consumption->unit,
                'vendor_ids' => $consumption->vendor_ids ?? [],
                'color_name' => $consumption->color_name,
                'color_code' => $consumption->color_code,
            ])->toArray();

            $newData['production_sections'] = $original->productionSections->map(fn ($section) => [
                'section_name' => $section->section_name,
                'items' => $section->items->map(fn ($item) => [
                    'name' => $item->name,
                    'qty'  => $item->qty,
                    'unit' => $item->unit,
                ])->toArray(),
            ])->toArray();

            if (! empty($newData['sku']) && $this->productRepository->where('sku', $newData['sku'])->exists()) {
                return response()->json([
                    'message' => 'Item Code already exists. Please choose a unique item code.',
                ], 422);
            }

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
    protected function validateProductFields($request, bool $isUpdate, ?int $productId, bool $isDuplicate = false): void
    {
        $this->normalizeDynamicProductInputs($request);

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
            'customer_organization_id' => $isDuplicate
                ? ['nullable', Rule::exists('organizations', 'id')]
                : [
                    'nullable',
                    Rule::exists('organizations', 'id')->where(fn ($query) => $query->whereIn('type', ['customer', 'Customer'])),
                ],
            'size'                 => ['nullable', 'string', 'max:100'],
            'weight'               => $isDuplicate ? ['nullable', 'numeric'] : ['nullable', 'numeric', 'min:0'],
            'weight_unit'          => $isDuplicate ? ['nullable', 'string', 'max:100'] : ['nullable', 'in:gsm,oz'],
            'cost_price'           => $isDuplicate ? ['nullable', 'numeric'] : ['nullable', 'numeric', 'min:0'],
            'selling_price'        => $isDuplicate ? ['nullable', 'numeric'] : ['nullable', 'numeric', 'min:0'],
            'colors'               => ['nullable', 'array'],
            'colors.*.name'        => ['nullable', 'string', 'max:100'],
            'colors.*.color_code'  => ['nullable', 'string', 'max:20'],
            'colors.*.cost_price'  => $isDuplicate ? ['nullable', 'numeric'] : ['nullable', 'numeric', 'min:0'],
            'colors.*.selling_price' => $isDuplicate ? ['nullable', 'numeric'] : ['nullable', 'numeric', 'min:0'],
            'consumptions'         => ['nullable', 'array'],
            'consumptions.*.material_reference_id' => ['nullable', 'integer', 'exists:material_references,id'],
            'consumptions.*.name'  => ['required', 'string', 'max:255'],
            'consumptions.*.qty'   => ['required', 'numeric'],
            'consumptions.*.unit'  => $isDuplicate
                ? ['required', 'string', 'max:100']
                : ['required', 'string', 'max:100', Rule::exists('unit_references', 'name')],
            'consumptions.*.vendor_ids' => ['nullable', 'array'],
            'consumptions.*.vendor_ids.*' => $isDuplicate
                ? ['integer']
                : ['integer', Rule::exists('organizations', 'id')->where(fn ($query) => $query->whereIn('type', ['vendor', 'Vendor']))],
            'consumptions.*.color_name' => ['nullable', 'string', 'max:100'],
            'consumptions.*.color_code' => ['nullable', 'string', 'max:20'],
            'production_sections'                          => ['nullable', 'array'],
            'production_sections.*.section_name'           => ['required', 'string', 'max:255'],
            'production_sections.*.items'                  => ['required', 'array', 'min:1'],
            'production_sections.*.items.*.name'           => ['required', 'string', 'max:255'],
            'production_sections.*.items.*.qty'            => ['required', 'numeric'],
            'production_sections.*.items.*.unit'           => ['required', 'string', 'max:100'],
        ];

        $messages = [
            'sku.required'                                 => 'Item Code is required.',
            'name.required'                                => 'Product Name is required.',
            'consumptions.*.name.required'                 => 'Material name is required.',
            'consumptions.*.qty.required'                  => 'Material qty is required.',
            'consumptions.*.unit.required'                 => 'Material unit is required.',
            'production_sections.*.section_name.required'  => 'Production section name is required.',
            'production_sections.*.items.*.name.required'  => 'Production row name is required.',
            'production_sections.*.items.*.qty.required'   => 'Production row qty is required.',
            'production_sections.*.items.*.unit.required'  => 'Production row unit is required.',
        ];

        $request->validate($rules, $messages);
    }

    /**
     * Remove blank dynamic rows before validation so optional UI rows do not block saving.
     */
    protected function normalizeDynamicProductInputs($request): void
    {
        $consumptions = [];
        $requestedConsumptions = (array) $request->input('consumptions', []);
        $materialReferences = MaterialReference::query()
            ->whereIn('id', collect($requestedConsumptions)->pluck('material_reference_id')->filter()->map(fn ($id) => (int) $id))
            ->get(['id', 'name', 'unit'])
            ->keyBy('id');

        foreach ($requestedConsumptions as $index => $consumption) {
            if (! is_array($consumption)) {
                continue;
            }

            $name = trim((string) ($consumption['name'] ?? ''));
            $qty = $consumption['qty'] ?? null;
            $unit = trim((string) ($consumption['unit'] ?? ''));
            $materialReferenceId = $consumption['material_reference_id'] ?? null;
            $vendorIds = collect($consumption['vendor_ids'] ?? [])
                ->filter(fn ($id) => $id !== null && $id !== '')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
            $colorName = trim((string) ($consumption['color_name'] ?? ''));
            $colorCode = trim((string) ($consumption['color_code'] ?? ''));

            if ($materialReferenceId && ($materialReference = $materialReferences->get((int) $materialReferenceId))) {
                $targetUnit = (string) $materialReference->unit;

                if ($unit !== '' && strcasecmp($unit, $targetUnit) !== 0 && $qty !== null && $qty !== '') {
                    $convertedQty = $this->unitConversionService->convert((float) $qty, $unit, $targetUnit);

                    if ($convertedQty === null) {
                        throw ValidationException::withMessages([
                            "consumptions.$index.unit" => "{$unit} cannot be converted to the material stock unit {$targetUnit}.",
                        ]);
                    }

                    $qty = round($convertedQty, 4);
                }

                $name = (string) $materialReference->name;
                $unit = $targetUnit;
            } elseif ($canonicalUnit = $this->unitConversionService->canonicalName($unit)) {
                $unit = $canonicalUnit;
            }

            if ($name === '' && ($qty === null || $qty === '') && $unit === '') {
                continue;
            }

            $consumptions[] = [
                'material_reference_id' => $materialReferenceId ?: null,
                'name' => $name,
                'qty'  => $qty,
                'unit' => $unit,
                'vendor_ids' => $vendorIds,
                'color_name' => $colorName,
                'color_code' => $colorCode,
            ];
        }

        $sections = [];

        foreach ((array) $request->input('production_sections', []) as $section) {
            if (! is_array($section)) {
                continue;
            }

            $sectionName = trim((string) ($section['section_name'] ?? ''));
            $items = [];

            foreach ((array) ($section['items'] ?? []) as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $name = trim((string) ($item['name'] ?? ''));
                $qty = $item['qty'] ?? null;
                $unit = trim((string) ($item['unit'] ?? ''));

                if ($canonicalUnit = $this->unitConversionService->canonicalName($unit)) {
                    $unit = $canonicalUnit;
                }

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

            $sections[] = [
                'section_name' => $sectionName,
                'items'        => $items,
            ];
        }

        $request->merge([
            'consumptions'        => $consumptions,
            'production_sections' => $sections,
        ]);
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

        $data['weight'] = isset($data['weight']) && $data['weight'] !== ''
            ? number_format((float) $data['weight'], 2, '.', '')
            : null;

        $data['weight_unit'] = isset($data['weight_unit']) && $data['weight_unit'] !== ''
            ? strtolower((string) $data['weight_unit'])
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
                    'name'          => $c['name'] ?? '',
                    'color_code'    => $c['color_code'] ?? '#000000',
                    'cost_price'    => isset($c['cost_price']) && $c['cost_price'] !== '' ? $c['cost_price'] : null,
                    'selling_price' => isset($c['selling_price']) && $c['selling_price'] !== '' ? $c['selling_price'] : null,
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
            $materialReferenceId = $consumption['material_reference_id'] ?? null;
            $vendorIds = collect($consumption['vendor_ids'] ?? [])
                ->filter(fn ($id) => $id !== null && $id !== '')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
            $colorName = trim((string) ($consumption['color_name'] ?? ''));
            $colorCode = trim((string) ($consumption['color_code'] ?? ''));

            if ($name === '' && ($qty === null || $qty === '') && $unit === '') {
                continue;
            }

            $consumptions[] = [
                'material_reference_id' => $materialReferenceId ?: null,
                'name' => $name,
                'qty'  => $qty,
                'unit' => $unit,
                'vendor_ids' => $vendorIds,
                'color_name' => $colorName !== '' ? $colorName : null,
                'color_code' => $colorCode !== '' ? strtoupper($colorCode) : null,
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

    /**
     * Build a duplicate-ready payload from an existing product.
     */
    protected function buildDuplicateDraft($original): array
    {
        $duplicateSku = $this->generateDuplicateSku((string) $original->sku);

        return [
            'name'                     => $original->name,
            'slug'                     => '',
            'sku'                      => $duplicateSku,
            'internal_code'            => $duplicateSku,
            'customer_organization_id' => $original->customer_organization_id,
            'description'              => $original->description,
            'quantity'                 => $original->quantity ?? 0,
            'price'                    => $original->price ?? 0,
            'cost_price'               => $original->cost_price,
            'selling_price'            => $original->selling_price ?? $original->price,
            'category_id'              => $original->category_id,
            'style'                    => $original->style,
            'size'                     => $original->size,
            'weight'                   => $original->weight,
            'weight_unit'              => $original->weight_unit,
            'additional_info'          => $original->additional_info,
            'shipping_info'            => $original->shipping_info,
            'publish_on_website'       => false,
            'entity_type'              => 'products',
            'colors' => $original->colors->map(fn ($color) => [
                'name'          => $color->name,
                'color_code'    => $color->color_code,
                'cost_price'    => $color->cost_price,
                'selling_price' => $color->selling_price,
            ])->toArray(),
            'key_points' => $original->keyPoints->map(fn ($kp) => [
                'key_heading' => $kp->key_heading,
                'key_point'   => $kp->key_point,
            ])->toArray(),
            'pricing_charts' => $original->pricingCharts->map(function ($chart) {
                return [
                    'heading' => $chart->heading,
                    'types'   => $chart->types->map(fn ($type) => [
                        'type'  => $type->type,
                        'tiers' => $type->tiers->map(fn ($tier) => [
                            'quantity' => $tier->quantity,
                            'price'    => $tier->price,
                        ])->toArray(),
                    ])->toArray(),
                ];
            })->toArray(),
            'consumptions' => $original->consumptions->map(fn ($consumption) => [
                'material_reference_id' => $consumption->material_reference_id,
                'name' => $consumption->name,
                'qty'  => $consumption->qty,
                'unit' => $consumption->unit,
                'vendor_ids' => $consumption->vendor_ids ?? [],
                'color_name' => $consumption->color_name,
                'color_code' => $consumption->color_code,
            ])->toArray(),
            'production_sections' => $original->productionSections->map(fn ($section) => [
                'section_name' => $section->section_name,
                'items' => $section->items->map(fn ($item) => [
                    'name' => $item->name,
                    'qty'  => $item->qty,
                    'unit' => $item->unit,
                ])->toArray(),
            ])->toArray(),
        ];
    }

    /**
     * Generate a readable, unique item code for a duplicated product.
     */
    protected function generateDuplicateSku(string $originalSku): string
    {
        $baseSku = trim($originalSku) !== '' ? trim($originalSku).'-COPY' : 'PRODUCT-COPY';
        $sku = $baseSku;
        $counter = 2;

        while ($this->productRepository->where('sku', $sku)->exists()) {
            $sku = $baseSku.'-'.$counter;
            $counter++;
        }

        return $sku;
    }

    /**
     * Copy cover/other images from the original product into a duplicate payload.
     */
    protected function duplicateMediaFromOriginal($original, array $data): array
    {
        if (empty($data['cover_image']) && $original->cover_image) {
            $data['cover_image'] = $this->duplicateFile($original->cover_image, 'product-images');
        }

        $existingOtherImages = $data['other_images'] ?? [];
        $existingOtherImageColors = $data['other_image_colors'] ?? [];

        foreach ($original->otherImages as $image) {
            if (! $image->path) {
                continue;
            }

            $existingOtherImages[] = [
                'path'          => $this->duplicateFile($image->path, 'product-other-images'),
                'original_name' => $image->original_name,
            ];

            $existingOtherImageColors[] = $image->color
                ? 'new_' . $image->color->sort_order
                : null;
        }

        $data['other_images'] = $existingOtherImages;
        $data['other_image_colors'] = $existingOtherImageColors;

        return $data;
    }
}
