<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Webkul\Product\Models\Product;

class ProductApiController extends Controller
{
    /**
     * Get all published products with pagination.
     * Shows: cover image, category name, title (name), style, size
     * Only returns products where publish_on_website = 1
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        
        $products = Product::with('category')
            ->select('id', 'name', 'slug', 'cover_image', 'category_id', 'style', 'size')
            ->where('publish_on_website', 1)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $data = $products->getCollection()->map(function ($product) {
            return [
                'id'           => $product->id,
                'title'        => $product->name,
                'slug'         => $product->slug,
                'cover_image'  => $product->cover_image ? asset('storage/' . $product->cover_image) : null,
                'category'     => $product->category ? $product->category->name : null,
                'style'        => $product->style,
                'size'         => $product->size,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    /**
     * Get product details by ID.
     * Returns all details organized by sections.
     * Only returns product if publish_on_website = 1
     */
    public function show($id): JsonResponse
    {
        $product = Product::with([
            'category',
            'otherImages.color',
            'colors',
            'keyPoints',
            'pricingCharts.types.tiers',
        ])
        ->where('publish_on_website', 1)
        ->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or not published',
            ], 404);
        }

        $data = [
            'success' => true,
            'data'    => [
                'basic_info' => [
                    'id'          => $product->id,
                    'title'       => $product->name,
                    'slug'        => $product->slug,
                    'sku'         => $product->sku,
                    'description' => $product->description,
                    'category'    => $product->category ? $product->category->name : null,
                    'style'       => $product->style,
                    'size'        => $product->size,
                    'quantity'    => $product->quantity,
                    'price'       => $product->price,
                    'publish_on_website' => $product->publish_on_website,
                    'created_at'  => $product->created_at,
                    'updated_at'  => $product->updated_at,
                ],

                'images' => [
                    'cover_image'  => $product->cover_image ? asset('storage/' . $product->cover_image) : null,
                    'other_images' => $product->otherImages->map(function ($img) {
                        return [
                            'id'            => $img->id,
                            'url'           => asset('storage/' . $img->path),
                            'original_name' => $img->original_name,
                            'color'         => $img->color ? [
                                'id'         => $img->color->id,
                                'name'       => $img->color->name,
                                'color_code' => $img->color->color_code,
                            ] : null,
                        ];
                    }),
                ],

                'colors' => $product->colors->map(function ($color) {
                    return [
                        'id'         => $color->id,
                        'name'       => $color->name,
                        'color_code' => $color->color_code,
                    ];
                }),

                'description_info' => [
                    'additional_info' => $product->additional_info,
                    'shipping_info'   => $product->shipping_info,
                ],

                'key_points' => $product->keyPoints->map(function ($kp) {
                    return [
                        'id'          => $kp->id,
                        'key_heading' => $kp->key_heading,
                        'key_point'   => $kp->key_point,
                    ];
                }),

                'pricing_charts' => $product->pricingCharts->map(function ($chart) {
                    return [
                        'id'      => $chart->id,
                        'heading' => $chart->heading,
                        'types'   => $chart->types->map(function ($type) {
                            return [
                                'id'    => $type->id,
                                'type'  => $type->type,
                                'tiers' => $type->tiers->map(function ($tier) {
                                    return [
                                        'id'       => $tier->id,
                                        'quantity' => intval($tier->quantity),
                                        'price'    => floatval($tier->price),
                                    ];
                                }),
                            ];
                        }),
                    ];
                }),
            ],
        ];

        return response()->json($data);
    }
}
