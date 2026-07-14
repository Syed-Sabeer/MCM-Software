@extends('admin::customer-portal.layouts.app', ['title' => $product->name, 'subtitle' => 'Product item code, specifications, color variants, and assigned images.'])

@section('content')
    @php
        $coverImageUrl = \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::productImageUrl($product->cover_image);
        $gallery = collect();
        if ($coverImageUrl) {
            $gallery->push(['url' => $coverImageUrl, 'label' => 'Cover image', 'color' => null]);
        }
        foreach ($product->otherImages as $image) {
            $imageUrl = \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::productImageUrl($image->path);

            if ($imageUrl) {
                $gallery->push([
                    'url' => $imageUrl,
                    'label' => $image->original_name ?: 'Product image',
                    'color' => $image->color?->name ?: $image->color?->color_code,
                ]);
            }
        }
        $primaryImage = data_get($gallery->first(), 'url');
    @endphp

    <div class="mb-4 flex justify-end"><a href="{{ route('customer_portal.products.index') }}" class="secondary-button inline-flex items-center gap-2"><span class="icon-left-arrow"></span> Back to products</a></div>

    <div class="grid gap-4 xl:grid-cols-[minmax(360px,0.9fr)_minmax(0,1.3fr)]">
        <section class="portal-card overflow-hidden">
            <div class="flex aspect-[4/3] items-center justify-center bg-slate-50 dark:bg-gray-950">
                @if($primaryImage)<img id="portal-product-primary-image" src="{{ $primaryImage }}" alt="{{ $product->name }}" class="h-full w-full object-contain p-4">@else<span class="icon-image text-7xl text-gray-300 dark:text-gray-700"></span>@endif
            </div>
            @if($gallery->isNotEmpty())
                <div class="grid grid-cols-4 gap-2 border-t border-gray-200 p-3 sm:grid-cols-5 dark:border-gray-800">
                    @foreach($gallery as $index => $image)
                        <button type="button" class="group relative aspect-square overflow-hidden rounded-md border {{ $index === 0 ? 'border-brandColor' : 'border-gray-200 dark:border-gray-700' }} bg-slate-50" onclick="document.getElementById('portal-product-primary-image').src = this.dataset.image" data-image="{{ $image['url'] }}" title="{{ $image['color'] ? $image['label'].' - '.$image['color'] : $image['label'] }}"><img src="{{ $image['url'] }}" alt="{{ $image['label'] }}" class="h-full w-full object-cover">@if($image['color'])<span class="absolute inset-x-0 bottom-0 truncate bg-black/70 px-1 py-0.5 text-[10px] text-white">{{ $image['color'] }}</span>@endif</button>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="portal-card p-5">
            <div class="border-b border-gray-200 pb-4 dark:border-gray-800"><p class="portal-kicker">Product details</p><h2 class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $product->name }}</h2><p class="mt-1 text-sm font-medium text-brandColor">Item Code: {{ $product->sku ?: '-' }}</p></div>
            <table class="mt-3 w-full text-sm"><tbody class="divide-y divide-gray-100 dark:divide-gray-800"><tr><td class="py-3 pr-4 text-gray-500">Product Item Code</td><td class="py-3 font-semibold text-gray-900 dark:text-white">{{ $product->sku ?: '-' }}</td></tr><tr><td class="py-3 pr-4 text-gray-500">Product Name</td><td class="py-3 font-semibold text-gray-900 dark:text-white">{{ $product->name }}</td></tr><tr><td class="py-3 pr-4 text-gray-500">Style</td><td class="py-3 text-gray-800 dark:text-gray-200">{{ $product->style ?: '-' }}</td></tr><tr><td class="py-3 pr-4 text-gray-500">Size</td><td class="py-3 text-gray-800 dark:text-gray-200">{{ $product->size ?: '-' }}</td></tr><tr><td class="py-3 pr-4 text-gray-500">Weight</td><td class="py-3 text-gray-800 dark:text-gray-200">{{ $product->weight ? \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::decimal($product->weight, 2).' '.strtoupper((string) $product->weight_unit) : '-' }}</td></tr></tbody></table>
            @if($product->description)<div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-800"><p class="portal-kicker">Description</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700 dark:text-gray-300">{{ $product->description }}</p></div>@endif
        </section>
    </div>

    <section class="portal-card mt-4 overflow-hidden">
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800"><p class="portal-kicker">Available options</p><h2 class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Color Variants & Images</h2></div>
        <div class="grid gap-3 p-5 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($product->colors as $color)
                @php
                    $colorImages = $product->otherImages->where('color_id', $color->id)
                        ->map(fn ($image) => [
                            'url' => \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::productImageUrl($image->path),
                            'name' => $image->original_name ?: $color->name,
                        ])
                        ->filter(fn ($image) => $image['url']);
                @endphp
                <article class="rounded-md border border-gray-200 p-3 dark:border-gray-700">
                    <div class="flex items-center gap-2"><span class="h-6 w-6 rounded-full border border-gray-300 dark:border-gray-600" style="background-color: {{ $color->color_code ?: '#d1d5db' }}"></span><div><p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $color->name ?: 'Color variant' }}</p><p class="text-xs text-gray-500">{{ $color->color_code ?: 'No color code' }}</p></div></div>
                    @if($colorImages->isNotEmpty())<div class="mt-3 grid grid-cols-4 gap-2">@foreach($colorImages as $image)<a href="{{ $image['url'] }}" target="_blank" class="aspect-square overflow-hidden rounded border border-gray-200 dark:border-gray-700"><img src="{{ $image['url'] }}" alt="{{ $image['name'] }}" class="h-full w-full object-cover"></a>@endforeach</div>@else<p class="mt-3 text-xs text-gray-500">No image assigned specifically to this color.</p>@endif
                </article>
            @empty
                <div class="col-span-full rounded-md border border-dashed border-gray-300 p-8 text-center text-sm text-gray-500 dark:border-gray-700">No color variants defined.</div>
            @endforelse
        </div>
    </section>

    @if($product->keyPoints->isNotEmpty() || $product->additional_info || $product->shipping_info)
        <div class="mt-4 grid gap-4 lg:grid-cols-2">
            @if($product->keyPoints->isNotEmpty() || $product->additional_info)<section class="portal-card p-5"><p class="portal-kicker">Product information</p><h2 class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Additional Details</h2>@if($product->keyPoints->isNotEmpty())<dl class="mt-4 grid gap-3">@foreach($product->keyPoints as $point)<div><dt class="text-sm font-semibold text-gray-900 dark:text-white">{{ $point->key_heading }}</dt><dd class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $point->key_point }}</dd></div>@endforeach</dl>@endif @if($product->additional_info)<div class="prose mt-4 max-w-none text-sm text-gray-700 dark:text-gray-300">{!! $product->additional_info !!}</div>@endif</section>@endif
            @if($product->shipping_info)<section class="portal-card p-5"><p class="portal-kicker">Fulfilment</p><h2 class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Shipping Information</h2><div class="prose mt-4 max-w-none text-sm text-gray-700 dark:text-gray-300">{!! $product->shipping_info !!}</div></section>@endif
        </div>
    @endif
@endsection
