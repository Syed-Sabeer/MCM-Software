<x-admin::layouts>
    <x-slot:title>
        {{ $product->name }}
    </x-slot>

    <div class="flex flex-col gap-4">
        {{-- Header bar --}}
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs name="products.view" :entity="$product" />
                <div class="text-xl font-bold dark:text-white">
                    {{ $product->name }}
                    @if ($product->slug)
                        <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">/{{ $product->slug }}</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-x-2.5">
                @if (bouncer()->hasPermission('products.edit'))
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="primary-button">
                        @lang('admin::app.products.index.datagrid.edit')
                    </a>
                @endif
                <a href="{{ route('admin.products.index') }}" class="secondary-button">
                    @lang('admin::app.products.index.title')
                </a>
            </div>
                </div>

        {{-- Main content: two column layout --}}
        <div class="flex gap-4 max-xl:flex-wrap">
            {{-- Left column --}}
            <div class="flex flex-1 flex-col gap-4 max-xl:flex-auto">
                {{-- Images Section --}}
                <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Images</p>
                    @if ($product->cover_image)
                        <div class="mb-4">
                            <img
                                src="{{ asset('storage/' . $product->cover_image) }}"
                                alt="{{ $product->name }}"
                                class="max-h-80 w-full rounded-lg border border-gray-200 object-contain dark:border-gray-700"
                            />
                        </div>
                    @else
                        <div class="mb-4 flex h-40 items-center justify-center rounded-lg border-2 border-dashed border-gray-200 text-gray-400 dark:border-gray-700">
                            No cover image
                        </div>
                    @endif
                    @if ($product->otherImages->isNotEmpty())
                        <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Other Images</p>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($product->otherImages as $img)
                                <a href="{{ asset('storage/' . $img->path) }}" target="_blank" class="block">
                                    <img src="{{ asset('storage/' . $img->path) }}" alt="" class="h-16 w-16 rounded-lg border border-gray-200 object-cover hover:opacity-80 dark:border-gray-700" loading="lazy" />
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Description Section --}}
                @if ($product->additional_info || $product->shipping_info)
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Description</p>
                        <div class="flex flex-col gap-4">
                            @if ($product->additional_info)
                                <div>
                                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">@lang('admin::app.products.create.additional-info')</p>
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                        {!! $product->additional_info !!}
                                    </div>
                                </div>
                            @endif
                            @if ($product->shipping_info)
                                <div>
                                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">@lang('admin::app.products.create.shipping-info')</p>
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                        {!! $product->shipping_info !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Key Points Section --}}
                @if ($product->keyPoints->isNotEmpty())
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">@lang('admin::app.products.create.key-points')</p>
                        <div class="flex flex-col gap-3">
                            @foreach ($product->keyPoints as $kp)
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                                    <p class="font-semibold text-gray-800 dark:text-white">{{ $kp->key_heading }}</p>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $kp->key_point }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Pricing Charts Section --}}
                @if ($product->pricingCharts->isNotEmpty())
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">@lang('admin::app.products.create.pricing-charts')</p>
                        <div class="flex flex-col gap-4">
                            @foreach ($product->pricingCharts as $chart)
                                <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-2 dark:border-gray-700 dark:bg-gray-800">
                                        <span class="font-semibold text-gray-800 dark:text-white">{{ $chart->heading }}</span>
                                    </div>
                                    @if ($chart->types->isNotEmpty())
                                        @foreach ($chart->types as $type)
                                            <div class="border-b border-gray-100 dark:border-gray-700">
                                                <div class="bg-gray-25 px-4 py-2 dark:bg-gray-750">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $type->type }}</span>
                                                </div>
                                                @if ($type->tiers->isNotEmpty())
                                                    <table class="w-full text-sm">
                                                        <thead class="bg-gray-50 dark:bg-gray-800">
                                                            <tr>
                                                                <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">@lang('admin::app.products.create.quantity')</th>
                                                                <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400">Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                            @foreach ($type->tiers as $tier)
                                                                <tr>
                                                                    <td class="px-4 py-2 text-gray-800 dark:text-gray-300">{{ intval($tier->quantity) }}</td>
                                                                    <td class="px-4 py-2 text-gray-800 dark:text-gray-300">{{ rtrim(rtrim(number_format($tier->price, 2, '.', ''), '0'), '.') }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">No pricing types</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right column --}}
            <div class="flex w-[360px] max-w-full flex-col gap-4 max-sm:w-full">
                {{-- Basic Info --}}
                <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Basic Info</p>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @if ($product->category)
                                <tr>
                                    <td class="py-2 pr-4 text-gray-500 dark:text-gray-400">@lang('admin::app.products.create.category')</td>
                                    <td class="py-2 font-medium text-gray-800 dark:text-white">{{ $product->category->name }}</td>
                                </tr>
                            @endif
                            @if ($product->style)
                                <tr>
                                    <td class="py-2 pr-4 text-gray-500 dark:text-gray-400">@lang('admin::app.products.create.style')</td>
                                    <td class="py-2 font-medium text-gray-800 dark:text-white">{{ $product->style }}</td>
                                </tr>
                            @endif
                            @if ($product->size)
                                <tr>
                                    <td class="py-2 pr-4 text-gray-500 dark:text-gray-400">@lang('admin::app.products.create.size')</td>
                                    <td class="py-2 font-medium text-gray-800 dark:text-white">{{ $product->size }}</td>
                                </tr>
                            @endif
                            @if (isset($product->publish_on_website))
                                <tr>
                                    <td class="py-2 pr-4 text-gray-500 dark:text-gray-400">Published</td>
                                    <td class="py-2">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $product->publish_on_website ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                            {{ $product->publish_on_website ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    @if (!$product->category && !$product->style && !$product->size && !isset($product->publish_on_website))
                        <p class="text-sm text-gray-500 dark:text-gray-400">No basic info available</p>
                    @endif
                </div>

                {{-- Colors --}}
                <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">@lang('admin::app.products.create.colors')</p>
                    @if ($product->colors->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                            @foreach ($product->colors as $c)
                                <div class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-1.5 dark:border-gray-700">
                                    <span class="h-5 w-5 rounded-full border border-gray-300 dark:border-gray-600" style="background-color: {{ $c->color_code }}"></span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $c->name ?: $c->color_code }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No colors added</p>
                    @endif
                </div>

                {{-- Attributes --}}
                <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Attributes</p>
                    <x-admin::attributes.view
                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere(['entity_type' => 'products'])->sortBy('sort_order')"
                        :entity="$product"
                        :url="route('admin.products.update', $product->id)"
                        :allow-edit="false"
                    />
                </div>
            </div>
        </div>
    </div>    
</x-admin::layouts>
