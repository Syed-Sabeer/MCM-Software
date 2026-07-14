@extends('admin::customer-portal.layouts.app', ['title' => 'Products'])

@section('content')
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300">
                    <tr><th class="px-4 py-3">Image</th><th class="px-4 py-3">Item Code</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Size</th><th class="px-4 py-3">Selling Price</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    @forelse ($records as $product)
                        @php($imageUrl = \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::productImageUrl($product->cover_image))
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-950">
                            <td class="px-4 py-3">
                                @if ($imageUrl)
                                    <img class="h-12 w-12 rounded object-cover" src="{{ $imageUrl }}" alt="{{ $product->name }}">
                                @else
                                    <span class="text-gray-500 dark:text-gray-300">No image</span>
                                @endif
                            </td>
                            <td class="px-4 py-3"><a class="font-medium text-brandColor" href="{{ route('customer_portal.products.view', $product->id) }}">{{ $product->sku ?: $product->internal_code ?: '-' }}</a></td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $product->size ?: '-' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($product->selling_price ?: $product->price) }}</td>
                        </tr>
                    @empty
                        @include('admin::customer-portal.partials.table-empty', ['colspan' => 5])
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($records, 'links'))
        <div class="mt-4">{{ $records->links() }}</div>
    @endif
@endsection
