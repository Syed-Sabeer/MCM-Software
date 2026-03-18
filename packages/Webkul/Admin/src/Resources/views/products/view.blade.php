<x-admin::layouts>
    <x-slot:title>
        {{ $product->name }}
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs name="products.view" :entity="$product" />
                <div class="text-xl font-bold dark:text-white">{{ $product->name }}</div>
            </div>

            <div class="flex items-center gap-2">
                @if (bouncer()->hasPermission('products.edit'))
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="primary-button">Edit</a>
                @endif

                <a href="{{ route('admin.products.index') }}" class="secondary-button">Back</a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Basic Info</p>

                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr>
                            <td class="py-2 pr-4 text-gray-500 dark:text-gray-400">Product Name</td>
                            <td class="py-2 font-medium text-gray-800 dark:text-white">{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 text-gray-500 dark:text-gray-400">Item Code</td>
                            <td class="py-2 font-medium text-gray-800 dark:text-white">{{ $product->sku }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 text-gray-500 dark:text-gray-400">Internal Code</td>
                            <td class="py-2 font-medium text-gray-800 dark:text-white">{{ $product->internal_code ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 text-gray-500 dark:text-gray-400">Customer</td>
                            <td class="py-2 font-medium text-gray-800 dark:text-white">
                                @if ($product->customer_organization_id && $product->customerOrganization)
                                    <a class="text-brandColor" href="{{ route('admin.contacts.organizations.view', $product->customer_organization_id) }}">{{ $product->customerOrganization->name }}</a>
                                @else
                                    Global Product
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 text-gray-500 dark:text-gray-400">Size</td>
                            <td class="py-2 font-medium text-gray-800 dark:text-white">{{ $product->size ?: '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Material Consumption</p>

                @if ($product->consumptions->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">No consumption rows defined.</p>
                @else
                    <div class="flex flex-col gap-2">
                        @foreach ($product->consumptions as $consumption)
                            <div class="grid grid-cols-3 gap-2 rounded border border-gray-200 p-2 text-sm dark:border-gray-700">
                                <div class="font-medium text-gray-800 dark:text-white">{{ $consumption->name }}</div>
                                <div class="text-gray-700 dark:text-gray-300">{{ rtrim(rtrim((string) $consumption->qty, '0'), '.') }}</div>
                                <div class="text-gray-700 dark:text-gray-300">{{ $consumption->unit }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Color Variants</p>

            @if ($product->colors->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No color variants defined.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach ($product->colors as $color)
                        <div class="inline-flex items-center gap-2 rounded border border-gray-200 px-3 py-1.5 text-sm dark:border-gray-700">
                            <span
                                class="h-4 w-4 rounded-full border border-gray-300 dark:border-gray-600"
                                style="background-color: {{ $color->color_code ?: '#000000' }};"
                            ></span>
                            <span class="text-gray-800 dark:text-gray-300">{{ $color->name ?: ($color->color_code ?: 'Color') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Production Sections</p>

            @if ($product->productionSections->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No production sections defined.</p>
            @else
                <div class="flex flex-col gap-3">
                    @foreach ($product->productionSections as $section)
                        <div class="rounded border border-gray-200 p-3 dark:border-gray-700">
                            <div class="mb-2 text-sm font-semibold text-gray-800 dark:text-white">{{ $section->section_name }}</div>

                            @if ($section->items->isEmpty())
                                <p class="text-xs text-gray-500 dark:text-gray-400">No rows.</p>
                            @else
                                <div class="flex flex-col gap-1">
                                    @foreach ($section->items as $item)
                                        <div class="grid grid-cols-3 gap-2 text-sm">
                                            <div class="text-gray-800 dark:text-gray-300">{{ $item->name }}</div>
                                            <div class="text-gray-700 dark:text-gray-400">{{ rtrim(rtrim((string) $item->qty, '0'), '.') }}</div>
                                            <div class="text-gray-700 dark:text-gray-400">{{ $item->unit }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-admin::layouts>
