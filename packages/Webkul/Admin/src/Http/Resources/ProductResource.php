<?php

namespace Webkul\Admin\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $coverImageUrl = $this->cover_image ? secure_url('public/storage/'.$this->cover_image) : null;

        $colors = $this->relationLoaded('colors')
            ? $this->colors->map(fn ($color) => [
                'id'            => $color->id,
                'name'          => $color->name,
                'color_code'    => $color->color_code,
                'cost_price'    => $color->cost_price,
                'selling_price' => $color->selling_price,
            ])->values()
            : [];

        $colorImageMap = [];

        if ($this->relationLoaded('otherImages')) {
            foreach ($this->otherImages as $image) {
                if (! $image->color_id || empty($image->path)) {
                    continue;
                }

                $colorImageMap[(string) $image->color_id] = secure_url('public/storage/'.$image->path);
            }
        }

        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'description'     => $this->description,
            'sku'             => $this->sku,
            'internal_code'   => $this->internal_code,
            'price'           => $this->selling_price ?? $this->price,
            'selling_price'   => $this->selling_price ?? $this->price,
            'cover_image_url' => $coverImageUrl,
            'colors'          => $colors,
            'color_images'    => $colorImageMap,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
