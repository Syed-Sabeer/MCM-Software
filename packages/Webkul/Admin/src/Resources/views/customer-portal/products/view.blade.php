@extends('admin::customer-portal.layouts.app', ['title' => $product->name])

@section('content')
    @php($imageUrl = \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::productImageUrl($product->cover_image))

    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-wrap items-start gap-4">
            @if ($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-40 w-40 rounded-lg border border-gray-200 object-cover dark:border-gray-800">
            @endif

            <div class="min-w-[260px] flex-1">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Item Code</p><p class="mt-1 text-sm text-gray-800 dark:text-white">{{ $product->sku ?: '-' }}</p></div>
                    <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Internal Code</p><p class="mt-1 text-sm text-gray-800 dark:text-white">{{ $product->internal_code ?: '-' }}</p></div>
                    <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Size</p><p class="mt-1 text-sm text-gray-800 dark:text-white">{{ $product->size ?: '-' }}</p></div>
                    <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Price</p><p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($product->selling_price ?: $product->price) }}</p></div>
                </div>

                @if ($product->description)
                    <p class="mt-4 text-sm text-gray-700 dark:text-gray-300">{{ $product->description }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="mb-3 text-base font-semibold text-gray-800 dark:text-white">Colors</p>
            <div class="grid gap-2">
                @forelse ($product->colors as $color)
                    <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><span class="inline-block h-4 w-4 rounded-full border border-gray-300" style="background: {{ $color->color_code ?: '#ddd' }}"></span>{{ $color->name }}</p>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-300">No colors listed.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="mb-3 text-base font-semibold text-gray-800 dark:text-white">Material Consumption</p>
            <div class="grid gap-2">
                @forelse ($product->consumptions as $consumption)
                    <p class="rounded border border-gray-100 px-3 py-2 text-sm text-gray-700 dark:border-gray-800 dark:text-gray-300">{{ $consumption->name }} | {{ rtrim(rtrim(number_format((float) $consumption->qty, 3, '.', ''), '0'), '.') }} {{ $consumption->unit }}</p>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-300">No material consumption listed.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
