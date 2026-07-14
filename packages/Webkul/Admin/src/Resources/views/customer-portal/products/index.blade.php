@extends('admin::customer-portal.layouts.app', ['title' => 'Products', 'subtitle' => 'Products assigned to your company.'])

@section('content')
    @include('admin::customer-portal.partials.list-toolbar', ['route' => 'customer_portal.products.index', 'placeholder' => 'Search product name, item code, or description'])

    <section class="portal-card overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-800">
            <div><p class="portal-kicker">Customer catalog</p><p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $records->total() }} product{{ $records->total() === 1 ? '' : 's' }}</p></div>
            <span class="portal-icon-box"><i class="icon-product text-xl"></i></span>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-left">
                <thead class="border-b border-gray-200 bg-slate-50 text-xs font-semibold uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300">
                    <tr><th class="w-20 px-4 py-3">Image</th><th class="px-4 py-3">Item Code</th><th class="px-4 py-3">Product Name</th><th class="px-4 py-3">Color Variants</th><th class="px-4 py-3">Size</th><th class="px-4 py-3 text-right">Price</th><th class="w-12 px-4 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    @forelse($records as $product)
                        @php($imageUrl = \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::productImageUrl($product->cover_image ?: $product->otherImages->first()?->path))
                        <tr class="portal-row">
                            <td class="px-4 py-3"><a href="{{ route('customer_portal.products.view', $product->id) }}" class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-md border border-gray-200 bg-slate-50 dark:border-gray-700 dark:bg-gray-950">@if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover">@else<span class="icon-image text-2xl text-gray-300"></span>@endif</a></td>
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $product->sku ?: '-' }}</td>
                            <td class="px-4 py-3"><a href="{{ route('customer_portal.products.view', $product->id) }}" class="font-semibold text-brandColor hover:underline">{{ $product->name }}</a><p class="mt-0.5 max-w-[320px] truncate text-xs text-gray-500">{{ $product->description ?: 'Customer product' }}</p></td>
                            <td class="px-4 py-3"><div class="flex max-w-[260px] flex-wrap gap-1.5">@forelse($product->colors->take(5) as $color)<span class="inline-flex items-center gap-1.5 rounded border border-gray-200 px-2 py-1 text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300"><span class="h-3.5 w-3.5 rounded-full border border-gray-300 dark:border-gray-600" style="background-color: {{ $color->color_code ?: '#d1d5db' }}"></span>{{ $color->name ?: $color->color_code }}</span>@empty<span class="text-xs text-gray-500">No variants</span>@endforelse @if($product->colors->count() > 5)<span class="px-1 py-1 text-xs font-semibold text-brandColor">+{{ $product->colors->count() - 5 }}</span>@endif</div></td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $product->size ?: '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($product->selling_price ?: $product->price) }}</td>
                            <td class="px-4 py-3"><a href="{{ route('customer_portal.products.view', $product->id) }}" class="inline-flex rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-brandColor dark:hover:bg-gray-800" aria-label="View {{ $product->name }}"><span class="icon-right-arrow text-xl"></span></a></td>
                        </tr>
                    @empty
                        @include('admin::customer-portal.partials.table-empty', ['colspan' => 7, 'message' => 'No products match your search.'])
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-gray-200 md:hidden dark:divide-gray-800">
            @forelse($records as $product)
                @php($imageUrl = \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::productImageUrl($product->cover_image ?: $product->otherImages->first()?->path))
                <a href="{{ route('customer_portal.products.view', $product->id) }}" class="portal-row flex gap-3 p-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-md border border-gray-200 bg-slate-50 dark:border-gray-700 dark:bg-gray-950">@if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover">@else<span class="icon-image text-2xl text-gray-300"></span>@endif</div>
                    <div class="min-w-0 flex-1"><div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="truncate font-semibold text-gray-900 dark:text-white">{{ $product->name }}</p><p class="mt-0.5 text-xs font-medium text-brandColor">{{ $product->sku ?: 'No item code' }}</p></div><span class="icon-right-arrow text-xl text-gray-400"></span></div><div class="mt-2 flex items-center justify-between gap-3"><div class="flex -space-x-1">@foreach($product->colors->take(5) as $color)<span title="{{ $color->name ?: $color->color_code }}" class="h-5 w-5 rounded-full border-2 border-white dark:border-gray-900" style="background-color: {{ $color->color_code ?: '#d1d5db' }}"></span>@endforeach</div><p class="text-sm font-semibold text-gray-900 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($product->selling_price ?: $product->price) }}</p></div></div>
                </a>
            @empty
                <div class="p-8 text-center text-sm text-gray-500">No products match your search.</div>
            @endforelse
        </div>
    </section>

    <div class="mt-4">{{ $records->links() }}</div>
@endsection
