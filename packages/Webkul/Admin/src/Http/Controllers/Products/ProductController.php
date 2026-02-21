<?php

namespace Webkul\Admin\Http\Controllers\Products;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\DataGrids\Product\ProductDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Resources\ProductResource;
use Webkul\Product\Models\ProductCategory;
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
        $categories = ProductCategory::orderBy('name')->get();

        return view('admin::products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AttributeForm $request): RedirectResponse
    {
        $this->validateProductFields($request, false);

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
        $product = $this->productRepository->with(['category', 'otherImages', 'colors', 'keyPoints', 'pricingCharts.tiers'])->findOrFail($id);

        return view('admin::products.view', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View|JsonResponse
    {
        $product = $this->productRepository->with(['category', 'otherImages', 'colors', 'keyPoints', 'pricingCharts.tiers'])->findOrFail($id);

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

        $categories = ProductCategory::orderBy('name')->get();

        return view('admin::products.edit', compact('product', 'inventories', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(AttributeForm $request, int $id): JsonResponse|RedirectResponse
    {
        $this->validateProductFields($request, true);

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
        $products = $this->productRepository
            ->pushCriteria(app(RequestCriteria::class))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return ProductResource::collection($products);
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
     * Validate product-specific fields (name, category, style, size, images, colors).
     */
    protected function validateProductFields($request, bool $isUpdate): void
    {
        $rules = [
            'name'           => ['required', 'string', 'max:255'],
            'category_id'    => ['nullable', 'exists:product_categories,id'],
            'style'          => ['nullable', 'string', 'max:255'],
            'size'           => ['nullable', 'string', 'max:100'],
            'cover_image'    => [$isUpdate ? 'nullable' : 'nullable', 'image', 'max:5120'],
            'other_images'   => ['nullable', 'array'],
            'other_images.*' => ['nullable', 'image', 'max:5120'],
            'additional_info' => ['nullable', 'string'],
            'shipping_info'  => ['nullable', 'string'],
            'colors'         => ['nullable', 'array'],
            'colors.*.name'  => ['nullable', 'string', 'max:100'],
            'colors.*.color_code' => ['nullable', 'string', 'max:20'],
            'key_points'     => ['nullable', 'array'],
            'key_points.*.key_heading' => ['nullable', 'string', 'max:255'],
            'key_points.*.key_point'   => ['nullable', 'string'],
            'pricing_charts' => ['nullable', 'array'],
            'pricing_charts.*.heading' => ['nullable', 'string', 'max:255'],
            'pricing_charts.*.type'    => ['nullable', 'string', 'max:100'],
            'pricing_charts.*.tiers'   => ['nullable', 'array'],
            'pricing_charts.*.tiers.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'pricing_charts.*.tiers.*.price'    => ['nullable', 'numeric', 'min:0'],
        ];

        $request->validate($rules);
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
        if ($data['name'] ?? null) {
            $data['name'] = $data['name'];
        }

        if (request()->hasFile('cover_image')) {
            $path = request()->file('cover_image')->store('product-images', 'public');
            $data['cover_image'] = $path;
        }

        $otherImages = [];
        if (request()->hasFile('other_images')) {
            foreach (request()->file('other_images') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('product-other-images', 'public');
                    $otherImages[] = ['path' => $path, 'original_name' => $file->getClientOriginalName()];
                }
            }
        }
        $data['other_images'] = $otherImages;

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
            $tiers = [];
            foreach ($chart['tiers'] ?? [] as $tier) {
                if (is_array($tier) && (isset($tier['quantity']) || isset($tier['price']))) {
                    $tiers[] = [
                        'quantity' => $tier['quantity'] ?? 0,
                        'price'    => $tier['price'] ?? 0,
                    ];
                }
            }
            $pricingCharts[] = [
                'heading' => $chart['heading'] ?? '',
                'type'    => $chart['type'] ?? '',
                'tiers'   => $tiers,
            ];
        }
        $data['pricing_charts'] = $pricingCharts;

        return $data;
    }
}
