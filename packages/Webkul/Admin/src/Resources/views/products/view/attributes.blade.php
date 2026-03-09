{!! view_render_event('admin.products.view.attributes.before', ['product' => $product]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800 dark:text-white">
    <x-admin::accordion  class="select-none !border-none">
        <x-slot:header class="!p-0">
            <h4 class="font-semibold dark:text-white">
                @lang('admin::app.products.view.attributes.about-product')
            </h4>
        </x-slot>

        <x-slot:content class="mt-4 !px-0 !pb-0">
            {!! view_render_event('admin.products.view.attributes.view.before', ['product' => $product]) !!}

            @if ($product->otherImages->isNotEmpty() || $product->colors->isNotEmpty() || $product->additional_info || $product->shipping_info || $product->keyPoints->isNotEmpty() || $product->pricingCharts->isNotEmpty())
                <div class="mb-4 flex flex-col gap-3 text-sm">
                    @if ($product->keyPoints->isNotEmpty())
                        <div>
                            <p class="mb-1 font-semibold dark:text-white">@lang('admin::app.products.create.key-points')</p>
                            <ul class="list-inside list-disc space-y-1 text-gray-600 dark:text-gray-400">
                                @foreach ($product->keyPoints as $kp)
                                    <li><strong>{{ $kp->key_heading }}</strong>: {{ $kp->key_point }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if ($product->pricingCharts->isNotEmpty())
                        <div>
                            <p class="mb-1 font-semibold dark:text-white">@lang('admin::app.products.create.pricing-charts')</p>
                            <div class="flex flex-col gap-3">
                                @foreach ($product->pricingCharts as $chart)
                                    <div class="rounded border border-gray-200 p-2 dark:border-gray-700">
                                        <p class="mb-1 font-medium dark:text-white">{{ $chart->heading }} @if($chart->type)<span class="text-gray-500">({{ $chart->type }})</span>@endif</p>
                                        @if ($chart->tiers->isNotEmpty())
                                            <table class="w-full max-w-xs text-left text-xs">
                                                <thead><tr class="border-b dark:border-gray-700"><th class="py-1 pr-2">@lang('admin::app.products.create.quantity')</th><th>Price</th></tr></thead>
                                                <tbody>
                                                    @foreach ($chart->tiers as $tier)
                                                        <tr class="border-b dark:border-gray-700"><td class="py-1 pr-2">{{ $tier->quantity }}</td><td>{{ $tier->price }}</td></tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if ($product->otherImages->isNotEmpty())
                        <div>
                            <p class="mb-1 font-semibold dark:text-white">@lang('admin::app.products.create.other-images')</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($product->otherImages as $img)
                                    <a href="{{ secure_url('public/storage/' . $img->path) }}" target="_blank" class="text-brandColor hover:underline">{{ $img->original_name ?: 'Image' }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if ($product->colors->isNotEmpty())
                        <div>
                            <p class="mb-1 font-semibold dark:text-white">@lang('admin::app.products.create.colors')</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($product->colors as $c)
                                    <span class="inline-flex items-center gap-1 rounded border border-gray-200 px-2 py-0.5 dark:border-gray-700">
                                        <span class="inline-block h-4 w-4 rounded border border-gray-300 dark:border-gray-600" style="background-color: {{ $c->color_code }}"></span>
                                        {{ $c->name ?: $c->color_code }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if ($product->additional_info)
                        <div>
                            <p class="mb-1 font-semibold dark:text-white">@lang('admin::app.products.create.additional-info')</p>
                            <div class="prose max-w-none text-gray-600 dark:text-gray-400">{!! $product->additional_info !!}</div>
                        </div>
                    @endif
                    @if ($product->shipping_info)
                        <div>
                            <p class="mb-1 font-semibold dark:text-white">@lang('admin::app.products.create.shipping-info')</p>
                            <div class="prose max-w-none text-gray-600 dark:text-gray-400">{!! $product->shipping_info !!}</div>
                        </div>
                    @endif
                </div>
            @endif
    
            <!-- Attributes Listing -->
            <div>
                <!-- Default Attributes --> 
                <x-admin::attributes.view
                    :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'products',
                        ['code', 'IN', ['SKU', 'price', 'quantity', 'status']]
                    ])->sortBy('sort_order')"
                    :entity="$product"
                    :url="route('admin.products.update', $product->id)"   
                    :allow-edit="true"
                />
        
                <!-- Custom Attributes --> 
                <x-admin::attributes.view
                    :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                        'entity_type' => 'products',
                        ['code', 'NOTIN', ['SKU', 'price', 'quantity', 'status']]
                    ])->sortBy('sort_order')"
                    :entity="$product"
                    :url="route('admin.products.update', $product->id)"   
                    :allow-edit="true"
                />
            </div>
            
            {!! view_render_event('admin.products.view.attributes.view.after', ['product' => $product]) !!}
        </x-slot>
    </x-admin::accordion>
</div>

{!! view_render_event('admin.products.view.attributes.before', ['product' => $product]) !!}
