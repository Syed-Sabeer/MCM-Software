
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.products.edit.title')
    </x-slot>

    {!! view_render_event('admin.products.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.products.update', $product->id)"
        encType="multipart/form-data"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs
                        name="products.edit"
                        :entity="$product"
                     />

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.products.edit.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.products.edit.create_button.before', ['product' => $product]) !!}

                        <!-- Edit button for Product -->
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.products.create.save-btn')
                        </button>

                        {!! view_render_event('admin.products.edit.create_button.after', ['product' => $product]) !!}
                    </div>
                </div>
            </div>

            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left column -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <!-- General Section -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">@lang('admin::app.products.create.general')</p>
                        <div class="flex flex-col gap-4">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">@lang('admin::app.products.create.product-name')</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="name" id="product_name_input" :value="old('name', $product->name)" />
                            </x-admin::form.control-group>
                            <div class="grid grid-cols-2 gap-4">
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>@lang('admin::app.products.create.slug')</x-admin::form.control-group.label>
                                    <div class="relative">
                                        <x-admin::form.control-group.control type="text" name="slug" id="product_slug_input" :value="old('slug', $product->slug)" placeholder="auto-generated-from-name" class="pr-10" />
                                        <span id="slug_status" class="absolute right-3 top-1/2 -translate-y-1/2 text-sm"></span>
                                    </div>
                                    <p id="slug_message" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Auto-generated from product name. Edit if needed.</p>
                                    <x-admin::form.control-group.error name="slug" />
                                </x-admin::form.control-group>
                                <x-admin::form.control-group>
                                    <div class="flex items-center justify-between gap-2">
                                        <x-admin::form.control-group.label>@lang('admin::app.products.create.category')</x-admin::form.control-group.label>
                                        <span class="flex gap-2 text-sm">
                                            {{-- <a href="{{ route('admin.product_categories.create') }}" target="_blank" class="text-brandColor hover:underline">@lang('admin::app.products.categories.add')</a> --}}
                                            <a href="{{ route('admin.product_categories.index') }}" target="_blank" class="text-brandColor hover:underline">@lang('admin::app.products.categories.manage')</a>
                                        </span>
                                    </div>
                                    <select name="category_id" class="custom-select flex min-h-[39px] w-full rounded-md border border-gray-200 px-3 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                        <option value="">-- @lang('admin::app.products.create.category') --</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </x-admin::form.control-group>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>@lang('admin::app.products.create.style')</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.control type="text" name="style" :value="old('style', $product->style)" />
                                </x-admin::form.control-group>
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>@lang('admin::app.products.create.size')</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.control type="text" name="size" :value="old('size', $product->size)" />
                                </x-admin::form.control-group>
                            </div>
                        </div>
                    </div>

                    <!-- Images Section -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Images</p>
                        <div class="flex flex-col gap-6">
                            <!-- Cover Image -->
                            <div>
                                <x-admin::form.control-group.label>@lang('admin::app.products.create.cover-image')</x-admin::form.control-group.label>
                                @if ($product->cover_image)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $product->cover_image) }}" target="_blank" title="Click to view full size">
                                            <img src="{{ asset('storage/' . $product->cover_image) }}" alt="{{ $product->name }}" class="h-20 w-20 rounded border border-gray-200 object-cover hover:opacity-80 cursor-pointer dark:border-gray-700" />
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="cover_image" id="cover_image_input" accept="image/*" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                <div id="cover_image_preview" class="mt-2"></div>
                            </div>

                            <!-- Other Images with Color Association -->
                            <div>
                                <x-admin::form.control-group.label>@lang('admin::app.products.create.other-images')</x-admin::form.control-group.label>

                                <!-- Existing Images -->
                                @if ($product->otherImages->isNotEmpty())
                                    <div class="mb-3 flex flex-wrap gap-3" id="existing-other-images">
                                        @foreach ($product->otherImages as $imgIdx => $img)
                                            <div class="existing-image-card relative rounded border border-gray-200 p-2 dark:border-gray-700" data-image-id="{{ $img->id }}" data-current-color-id="{{ $img->color_id }}" data-image-path="{{ asset('storage/' . $img->path) }}">
                                                <button type="button" class="remove-existing-image absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white text-xs hover:bg-red-600" title="Remove image">&times;</button>
                                                <img src="{{ asset('storage/' . $img->path) }}" alt="" class="existing-img-preview h-14 w-14 rounded border border-gray-200 object-cover dark:border-gray-700" />
                                                <input type="hidden" name="existing_image_ids[]" class="existing-image-id-input" value="{{ $img->id }}" />
                                                <input type="file" name="replace_images[{{ $img->id }}]" accept="image/*" class="replace-image-input hidden" />
                                                <div class="color-dropdown-wrapper existing-color-dropdown relative mt-1 w-full">
                                                    <input type="hidden" name="existing_image_colors[]" class="color-dropdown-value" value="" />
                                                    <div class="color-dropdown-trigger flex items-center gap-1 cursor-pointer rounded border border-gray-200 px-1 py-1 text-xs dark:border-gray-700 dark:bg-gray-800">
                                                        <span class="color-indicator h-3 w-3 rounded border border-gray-300 dark:border-gray-600" style="background:#fff;"></span>
                                                        <span class="color-dropdown-text flex-1 text-gray-600 dark:text-gray-300 truncate">-- Color --</span>
                                                        <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                    </div>
                                                    <div class="color-dropdown-menu absolute z-50 mt-1 hidden w-32 rounded border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                                        <input type="text" class="color-search-input w-full border-b border-gray-200 px-2 py-1 text-xs outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Search..." />
                                                        <div class="color-dropdown-options max-h-32 overflow-y-auto"></div>
                                                    </div>
                                                </div>
                                                <div class="mt-1 flex justify-between gap-1">
                                                    <a href="{{ asset('storage/' . $img->path) }}" target="_blank" class="view-image-link text-xs text-blue-600 hover:underline dark:text-blue-400">View</a>
                                                    <button type="button" class="replace-image-btn flex items-center gap-0.5 text-xs text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200" title="Replace image">
                                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                        Replace
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <input type="hidden" name="delete_image_ids" id="delete_image_ids" value="" />

                                <!-- New Images Upload -->
                                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Add new images:</p>
                                <div id="other-images-container" class="flex flex-col gap-3">
                                    <div class="flex gap-2 items-center other-image-row rounded border border-gray-100 p-2 dark:border-gray-800">
                                        <input type="file" name="other_images[]" accept="image/*" class="other-image-file flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                        <div class="color-dropdown-wrapper relative w-36">
                                            <input type="hidden" name="other_image_colors[]" class="color-dropdown-value" value="" />
                                            <div class="color-dropdown-trigger flex items-center gap-1 cursor-pointer rounded border border-gray-200 px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
                                                <span class="color-indicator h-4 w-4 rounded border border-gray-300 dark:border-gray-600" style="background:#fff;"></span>
                                                <span class="color-dropdown-text flex-1 text-gray-600 dark:text-gray-300">-- Color --</span>
                                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                            <div class="color-dropdown-menu absolute z-50 mt-1 hidden w-full rounded border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                                <input type="text" class="color-search-input w-full border-b border-gray-200 px-2 py-1.5 text-sm outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Search..." />
                                                <div class="color-dropdown-options max-h-40 overflow-y-auto"></div>
                                            </div>
                                        </div>
                                        <button type="button" class="secondary-button remove-other-image" style="display:none;">&times;</button>
                                    </div>
                                </div>
                                <div id="other_images_preview" class="mt-2 flex flex-wrap gap-2"></div>
                                <button type="button" id="add-other-image" class="mt-2 text-sm font-semibold text-brandColor hover:underline">@lang('admin::app.products.create.add-more')</button>
                            </div>
                        </div>
                    </div>

                    <!-- Key Points -->
                    @php
                        $keyPointsData = old('key_points') ?? $product->keyPoints->map(fn($kp) => ['key_heading' => $kp->key_heading, 'key_point' => $kp->key_point])->toArray();
                        $pricingChartsData = old('pricing_charts') ?? $product->pricingCharts->map(fn($pc) => [
                            'heading' => $pc->heading,
                            'types' => $pc->types->map(fn($t) => [
                                'type' => $t->type,
                                'tiers' => $t->tiers->map(fn($tier) => ['quantity' => $tier->quantity, 'price' => $tier->price])->toArray()
                            ])->toArray()
                        ])->toArray();
                    @endphp
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">@lang('admin::app.products.create.key-points')</p>
                        <div id="key-points-container" class="flex flex-col gap-3">
                            @forelse($keyPointsData as $idx => $kp)
                                <div class="key-point-row flex items-center gap-3">
                                    <input type="text" name="key_points[{{ $idx }}][key_heading]" placeholder="@lang('admin::app.products.create.key-heading')" value="{{ $kp['key_heading'] ?? '' }}" class="w-1/3 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                    <input type="text" name="key_points[{{ $idx }}][key_point]" placeholder="@lang('admin::app.products.create.key-point')" value="{{ $kp['key_point'] ?? '' }}" class="flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                    <button type="button" class="remove-key-point text-gray-400 hover:text-red-500" @if(count($keyPointsData) <= 1) style="display:none;" @else style="display:inline-flex;" @endif title="Remove">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            @empty
                                <div class="key-point-row flex items-center gap-3">
                                    <input type="text" name="key_points[0][key_heading]" placeholder="@lang('admin::app.products.create.key-heading')" class="w-1/3 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                    <input type="text" name="key_points[0][key_point]" placeholder="@lang('admin::app.products.create.key-point')" class="flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                    <button type="button" class="remove-key-point text-gray-400 hover:text-red-500" style="display:none;" title="Remove">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            @endforelse
                                </div>
                        <button type="button" id="add-key-point" class="mt-1 text-sm font-semibold text-brandColor hover:underline">@lang('admin::app.products.create.add-more')</button>
                    </div>

                    <!-- Description Section -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Description</p>
                        <div class="flex flex-col gap-4">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>@lang('admin::app.products.create.additional-info')</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="textarea"
                                    name="additional_info"
                                    id="additional_info"
                                    :value="old('additional_info', $product->additional_info)"
                                    rows="6"
                                    :tinymce="true"
                                />
                            </x-admin::form.control-group>
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>@lang('admin::app.products.create.shipping-info')</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="textarea"
                                    name="shipping_info"
                                    id="shipping_info"
                                    :value="old('shipping_info', $product->shipping_info)"
                                    rows="6"
                                    :tinymce="true"
                                />
                            </x-admin::form.control-group>
                        </div>
                    </div>

                </div>

                <!-- Right column: Colors -->
                <div class="flex w-[480px] max-w-full flex-col gap-2 max-sm:w-full">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">@lang('admin::app.products.create.colors')</p>
                        <div id="colors-container" class="flex flex-col gap-3">
                            @php $productColors = old('colors') ?? $product->colors->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'color_code' => $c->color_code])->toArray(); @endphp
                            @if (!empty($productColors))
                                @foreach ($productColors as $idx => $c)
                                    <div class="flex gap-2 items-center color-row" data-color-id="{{ $c['id'] ?? '' }}">
                                        <input type="text" name="colors[{{ $idx }}][name]" placeholder="@lang('admin::app.products.create.color-name')" value="{{ $c['name'] ?? '' }}" class="color-name-input flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                        <input type="text" name="colors[{{ $idx }}][color_code]" value="{{ $c['color_code'] ?? '#000000' }}" class="color-code-input w-28 rounded border border-gray-200 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="#hex" />
                                        <input type="color" class="color-picker h-9 w-12 cursor-pointer rounded border border-gray-200 dark:border-gray-700" value="{{ $c['color_code'] ?? '#000000' }}" title="Pick color" />
                                        <button type="button" class="secondary-button remove-color" @if(count($productColors) <= 1) style="display:none;" @endif>&times;</button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex gap-2 items-center color-row">
                                    <input type="text" name="colors[0][name]" placeholder="@lang('admin::app.products.create.color-name')" class="color-name-input flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                    <input type="text" name="colors[0][color_code]" value="#000000" class="color-code-input w-28 rounded border border-gray-200 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="#hex" />
                                    <input type="color" class="color-picker h-9 w-12 cursor-pointer rounded border border-gray-200 dark:border-gray-700" value="#000000" title="Pick color" />
                                    <button type="button" class="secondary-button remove-color" style="display:none;">&times;</button>
                                </div>
                            @endif
                        </div>
                        <button type="button" id="add-color" class="mt-1 text-sm font-semibold text-brandColor hover:underline">@lang('admin::app.products.create.add-more')</button>
                    </div>

                    <!-- Pricing Charts Section -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">@lang('admin::app.products.create.pricing-charts')</p>
                        <div id="pricing-charts-container" class="flex flex-col gap-4">
                            @forelse($pricingChartsData as $cIdx => $chart)
                                <div class="pricing-chart-block rounded-lg border border-gray-200 p-3 dark:border-gray-700" data-chart-index="{{ $cIdx }}">
                                    <div class="mb-3 flex items-center gap-2">
                                        <input type="text" name="pricing_charts[{{ $cIdx }}][heading]" placeholder="Chart Heading" value="{{ $chart['heading'] ?? '' }}" class="chart-heading-input flex-1 rounded border border-gray-200 px-3 py-2 text-sm font-semibold dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                        <button type="button" class="remove-pricing-chart text-gray-400 hover:text-red-500" @if(count($pricingChartsData) <= 1) style="display:none;" @endif title="Remove Chart">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                    <div class="chart-types-container ml-2 flex flex-col gap-3 border-l-2 border-gray-200 pl-3 dark:border-gray-600">
                                        @forelse($chart['types'] ?? [] as $tIdx => $type)
                                            <div class="chart-type-block rounded border border-gray-100 bg-gray-50 p-2 dark:border-gray-700 dark:bg-gray-800" data-type-index="{{ $tIdx }}">
                                                <div class="mb-2 flex items-center gap-2">
                                                    <input type="text" name="pricing_charts[{{ $cIdx }}][types][{{ $tIdx }}][type]" placeholder="Type Name" value="{{ $type['type'] ?? '' }}" class="type-name-input flex-1 rounded border border-gray-200 px-2 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" />
                                                    <button type="button" class="remove-chart-type text-gray-400 hover:text-red-500" @if(count($chart['types'] ?? []) <= 1) style="display:none;" @endif title="Remove Type">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                    </button>
                                                </div>
                                                <div class="type-tiers-container ml-2 flex flex-col gap-1">
                                                    @forelse($type['tiers'] ?? [] as $tierIdx => $tier)
                                                        <div class="tier-row flex items-center gap-2">
                                                            <input type="number" name="pricing_charts[{{ $cIdx }}][types][{{ $tIdx }}][tiers][{{ $tierIdx }}][quantity]" placeholder="Qty" value="{{ isset($tier['quantity']) ? intval($tier['quantity']) : '' }}" step="1" min="0" class="w-20 rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" />
                                                            <input type="number" name="pricing_charts[{{ $cIdx }}][types][{{ $tIdx }}][tiers][{{ $tierIdx }}][price]" placeholder="Price" value="{{ isset($tier['price']) ? rtrim(rtrim(number_format($tier['price'], 2, '.', ''), '0'), '.') : '' }}" step="0.01" min="0" class="w-20 rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" />
                                                            <button type="button" class="remove-tier text-gray-400 hover:text-red-500" @if(count($type['tiers'] ?? []) <= 1) style="display:none;" @endif title="Remove">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                            </button>
                                                        </div>
                                                    @empty
                                                        <div class="tier-row flex items-center gap-2">
                                                            <input type="number" name="pricing_charts[{{ $cIdx }}][types][{{ $tIdx }}][tiers][0][quantity]" placeholder="Qty" step="1" min="0" class="w-20 rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" />
                                                            <input type="number" name="pricing_charts[{{ $cIdx }}][types][{{ $tIdx }}][tiers][0][price]" placeholder="Price" step="0.01" min="0" class="w-20 rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" />
                                                            <button type="button" class="remove-tier text-gray-400 hover:text-red-500" style="display:none;" title="Remove">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                            </button>
                                                        </div>
                                                    @endforelse
                                                </div>
                                                <button type="button" class="add-tier-btn mt-1 text-xs text-brandColor hover:underline">+ Add Qty/Price</button>
                                            </div>
                                        @empty
                                            <div class="chart-type-block rounded border border-gray-100 bg-gray-50 p-2 dark:border-gray-700 dark:bg-gray-800" data-type-index="0">
                                                <div class="mb-2 flex items-center gap-2">
                                                    <input type="text" name="pricing_charts[{{ $cIdx }}][types][0][type]" placeholder="Type Name" class="type-name-input flex-1 rounded border border-gray-200 px-2 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" />
                                                    <button type="button" class="remove-chart-type text-gray-400 hover:text-red-500" style="display:none;" title="Remove Type">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                    </button>
                                                </div>
                                                <div class="type-tiers-container ml-2 flex flex-col gap-1">
                                                    <div class="tier-row flex items-center gap-2">
                                                        <input type="number" name="pricing_charts[{{ $cIdx }}][types][0][tiers][0][quantity]" placeholder="Qty" step="1" min="0" class="w-20 rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" />
                                                        <input type="number" name="pricing_charts[{{ $cIdx }}][types][0][tiers][0][price]" placeholder="Price" step="0.01" min="0" class="w-20 rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" />
                                                        <button type="button" class="remove-tier text-gray-400 hover:text-red-500" style="display:none;" title="Remove">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <button type="button" class="add-tier-btn mt-1 text-xs text-brandColor hover:underline">+ Add Qty/Price</button>
                                            </div>
                                        @endforelse
                                        <div class="mt-1 flex gap-3">
                                            <button type="button" class="add-type-btn text-xs font-semibold text-brandColor hover:underline">+ Add Type</button>
                                            <button type="button" class="duplicate-type-btn text-xs text-gray-500 hover:text-brandColor hover:underline">Duplicate Last Type</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="pricing-chart-block rounded-lg border border-gray-200 p-3 dark:border-gray-700" data-chart-index="0">
                                    <div class="mb-3 flex items-center gap-2">
                                        <input type="text" name="pricing_charts[0][heading]" placeholder="Chart Heading" class="chart-heading-input flex-1 rounded border border-gray-200 px-3 py-2 text-sm font-semibold dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                        <button type="button" class="remove-pricing-chart text-gray-400 hover:text-red-500" style="display:none;" title="Remove Chart">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                    <div class="chart-types-container ml-2 flex flex-col gap-3 border-l-2 border-gray-200 pl-3 dark:border-gray-600">
                                        <div class="chart-type-block rounded border border-gray-100 bg-gray-50 p-2 dark:border-gray-700 dark:bg-gray-800" data-type-index="0">
                                            <div class="mb-2 flex items-center gap-2">
                                                <input type="text" name="pricing_charts[0][types][0][type]" placeholder="Type Name" class="type-name-input flex-1 rounded border border-gray-200 px-2 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" />
                                                <button type="button" class="remove-chart-type text-gray-400 hover:text-red-500" style="display:none;" title="Remove Type">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </div>
                                            <div class="type-tiers-container ml-2 flex flex-col gap-1">
                                                <div class="tier-row flex items-center gap-2">
                                                    <input type="number" name="pricing_charts[0][types][0][tiers][0][quantity]" placeholder="Qty" step="1" min="0" class="w-20 rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" />
                                                    <input type="number" name="pricing_charts[0][types][0][tiers][0][price]" placeholder="Price" step="0.01" min="0" class="w-20 rounded border border-gray-200 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" />
                                                    <button type="button" class="remove-tier text-gray-400 hover:text-red-500" style="display:none;" title="Remove">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <button type="button" class="add-tier-btn mt-1 text-xs text-brandColor hover:underline">+ Add Qty/Price</button>
                                        </div>
                                        <div class="mt-1 flex gap-3">
                                            <button type="button" class="add-type-btn text-xs font-semibold text-brandColor hover:underline">+ Add Type</button>
                                            <button type="button" class="duplicate-type-btn text-xs text-gray-500 hover:text-brandColor hover:underline">Duplicate Last Type</button>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <div class="mt-2 flex gap-3">
                            <button type="button" id="add-pricing-chart" class="text-sm font-semibold text-brandColor hover:underline">+ Add Pricing Chart</button>
                            <button type="button" id="duplicate-pricing-chart" class="text-sm text-gray-500 hover:text-brandColor hover:underline">Duplicate Last Chart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.products.edit.form.after') !!}

    @pushOnce('scripts')
    <script>
        (function() {
            var productEditColorIndex = 0;

            var currentColors = [];
            var initialColorMapping = {};

            function syncColorsToImageDropdowns() {
                var colorRows = document.querySelectorAll('#colors-container .color-row');
                currentColors = [];
                colorRows.forEach(function(row, idx) {
                    var nameInput = row.querySelector('.color-name-input');
                    var codeInput = row.querySelector('.color-code-input');
                    var name = nameInput ? nameInput.value.trim() : '';
                    var code = codeInput ? codeInput.value.trim() : '#000000';
                    var colorId = row.getAttribute('data-color-id') || '';
                    if (name || code !== '#000000') {
                        var colorData = { index: idx, name: name || code, code: code, value: 'new_' + idx, dbId: colorId };
                        currentColors.push(colorData);
                        if (colorId) {
                            initialColorMapping[colorId] = colorData;
                        }
                    }
                });

                document.querySelectorAll('.color-dropdown-wrapper').forEach(function(wrapper) {
                    var hiddenInput = wrapper.querySelector('.color-dropdown-value');
                    var currentVal = hiddenInput ? hiddenInput.value : '';

                    if (!currentVal && wrapper.classList.contains('existing-color-dropdown')) {
                        var card = wrapper.closest('.existing-image-card');
                        if (card) {
                            var savedColorId = card.getAttribute('data-current-color-id');
                            if (savedColorId && initialColorMapping[savedColorId]) {
                                currentVal = initialColorMapping[savedColorId].value;
                                hiddenInput.value = currentVal;
                            }
                        }
                    }

                    var selectedColor = currentColors.find(function(c) { return c.value === currentVal; });
                    updateDropdownDisplay(wrapper, selectedColor);
                });
            }

            function updateDropdownDisplay(wrapper, selectedColor) {
                var indicator = wrapper.querySelector('.color-indicator');
                var textEl = wrapper.querySelector('.color-dropdown-text');
                if (selectedColor) {
                    indicator.style.backgroundColor = selectedColor.code;
                    textEl.textContent = selectedColor.name;
                } else {
                    indicator.style.backgroundColor = '#ffffff';
                    textEl.textContent = '-- Color --';
                }
            }

            function renderDropdownOptions(wrapper, filter) {
                var optionsContainer = wrapper.querySelector('.color-dropdown-options');
                var hiddenInput = wrapper.querySelector('.color-dropdown-value');
                var currentVal = hiddenInput ? hiddenInput.value : '';
                optionsContainer.innerHTML = '';

                var defaultOpt = document.createElement('div');
                defaultOpt.className = 'color-dropdown-option flex items-center gap-2 px-2 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700';
                defaultOpt.setAttribute('data-value', '');
                defaultOpt.setAttribute('data-color', '#ffffff');
                defaultOpt.innerHTML = '<span class="h-4 w-4 rounded border border-gray-300" style="background:#fff;"></span><span class="text-sm text-gray-600 dark:text-gray-300">-- None --</span>';
                optionsContainer.appendChild(defaultOpt);

                currentColors.forEach(function(c) {
                    if (filter && c.name.toLowerCase().indexOf(filter.toLowerCase()) === -1) return;
                    var opt = document.createElement('div');
                    opt.className = 'color-dropdown-option flex items-center gap-2 px-2 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700';
                    if (c.value === currentVal) opt.classList.add('bg-gray-50', 'dark:bg-gray-700');
                    opt.setAttribute('data-value', c.value);
                    opt.setAttribute('data-color', c.code);
                    opt.innerHTML = '<span class="h-4 w-4 rounded border border-gray-300" style="background:' + c.code + ';"></span><span class="text-sm text-gray-800 dark:text-gray-200">' + c.name + '</span>';
                    optionsContainer.appendChild(opt);
                });
            }

            function openDropdown(wrapper) {
                document.querySelectorAll('.color-dropdown-menu').forEach(function(m) { m.classList.add('hidden'); });
                var menu = wrapper.querySelector('.color-dropdown-menu');
                var searchInput = wrapper.querySelector('.color-search-input');
                menu.classList.remove('hidden');
                searchInput.value = '';
                renderDropdownOptions(wrapper, '');
                searchInput.focus();
            }

            function closeAllDropdowns() {
                document.querySelectorAll('.color-dropdown-menu').forEach(function(m) { m.classList.add('hidden'); });
            }

            function selectColorOption(wrapper, value, code, name) {
                var hiddenInput = wrapper.querySelector('.color-dropdown-value');
                hiddenInput.value = value;
                var selectedColor = value ? { code: code, name: name } : null;
                updateDropdownDisplay(wrapper, selectedColor);
                closeAllDropdowns();
            }

            function initProductEdit() {
                productEditColorIndex = document.querySelectorAll('#colors-container .color-row').length;
                syncColorsToImageDropdowns();

                document.getElementById('colors-container').addEventListener('input', function(e) {
                    if (e.target.classList.contains('color-name-input') || e.target.classList.contains('color-code-input')) {
                        syncColorsToImageDropdowns();
                    }
                });

                document.body.addEventListener('click', function(e) {
                    var trigger = e.target.closest('.color-dropdown-trigger');
                    if (trigger) {
                        e.stopPropagation();
                        var wrapper = trigger.closest('.color-dropdown-wrapper');
                        var menu = wrapper.querySelector('.color-dropdown-menu');
                        if (menu.classList.contains('hidden')) {
                            openDropdown(wrapper);
                        } else {
                            closeAllDropdowns();
                        }
                        return;
                    }
                    var option = e.target.closest('.color-dropdown-option');
                    if (option) {
                        var wrapper = option.closest('.color-dropdown-wrapper');
                        var value = option.getAttribute('data-value');
                        var code = option.getAttribute('data-color');
                        var nameEl = option.querySelector('span:last-child');
                        var name = nameEl ? nameEl.textContent : '';
                        selectColorOption(wrapper, value, code, name);
                        return;
                    }
                    if (!e.target.closest('.color-dropdown-menu')) {
                        closeAllDropdowns();
                    }
                });

                document.body.addEventListener('input', function(e) {
                    if (e.target.classList.contains('color-search-input')) {
                        var wrapper = e.target.closest('.color-dropdown-wrapper');
                        renderDropdownOptions(wrapper, e.target.value);
                    }
                });

                document.body.addEventListener('change', function(e) {
                    if (e.target.classList && e.target.classList.contains('replace-image-input')) {
                        var card = e.target.closest('.existing-image-card');
                        if (card && e.target.files && e.target.files[0]) {
                            var reader = new FileReader();
                            reader.onload = function(ev) {
                                var imgPreview = card.querySelector('.existing-img-preview');
                                var viewLink = card.querySelector('.view-image-link');
                                if (imgPreview) imgPreview.src = ev.target.result;
                                if (viewLink) viewLink.href = ev.target.result;
                            };
                            reader.readAsDataURL(e.target.files[0]);
                        }
                    }
                });

                var coverInput = document.getElementById('cover_image_input');
                if (coverInput) {
                    coverInput.addEventListener('change', function() {
                        var preview = document.getElementById('cover_image_preview');
                        if (!preview) return;
                        preview.innerHTML = '';
                        if (this.files && this.files[0]) {
                            var r = new FileReader();
                            r.onload = function(e) {
                                var link = document.createElement('a');
                                link.href = e.target.result;
                                link.target = '_blank';
                                link.title = 'Click to view';
                                var img = document.createElement('img');
                                img.src = e.target.result;
                                img.className = 'h-20 w-20 rounded border border-gray-200 object-cover hover:opacity-80 cursor-pointer dark:border-gray-700';
                                link.appendChild(img);
                                preview.appendChild(link);
                            };
                            r.readAsDataURL(this.files[0]);
                        }
                    });
                }
                var otherContainer = document.getElementById('other-images-container');
                if (otherContainer) {
                    otherContainer.addEventListener('change', function(e) {
                        if (!e.target.classList || !e.target.classList.contains('other-image-file')) return;
                        var preview = document.getElementById('other_images_preview');
                        if (!preview) return;
                        preview.innerHTML = '';
                        var inputs = document.querySelectorAll('#other-images-container .other-image-file');
                        inputs.forEach(function(inp) {
                            if (inp.files && inp.files[0]) {
                                var r = new FileReader();
                                r.onload = (function(fileInput) {
                                    return function(ev) {
                                        var img = document.createElement('img');
                                        img.src = ev.target.result;
                                        img.className = 'h-16 w-16 rounded border border-gray-200 object-cover dark:border-gray-700';
                                        img.title = fileInput.files[0].name;
                                        preview.appendChild(img);
                                    };
                                })(inp);
                                r.readAsDataURL(inp.files[0]);
                            }
                        });
                    });
                }
                var nameInput = document.getElementById('product_name_input');
                var slugInput = document.getElementById('product_slug_input');
                var slugStatus = document.getElementById('slug_status');
                var slugMessage = document.getElementById('slug_message');
                var slugCheckTimeout = null;
                var productId = {{ $product->id }};

                function slugify(text) {
                    return String(text).toLowerCase().trim()
                        .replace(/\s+/g, '-')
                        .replace(/[^a-z0-9-]/g, '')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');
                }

                function checkSlugAvailability(slug) {
                    if (!slug) {
                        slugStatus.innerHTML = '';
                        slugMessage.textContent = 'Auto-generated from product name. Edit if needed.';
                        slugMessage.className = 'mt-1 text-xs text-gray-500 dark:text-gray-400';
                        return;
                    }

                    slugStatus.innerHTML = '<span class="text-gray-400">...</span>';

                    fetch('{{ route("admin.products.check_slug") }}?slug=' + encodeURIComponent(slug) + '&product_id=' + productId)
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            if (data.available) {
                                slugStatus.innerHTML = '<span class="text-green-600">✓</span>';
                                slugMessage.textContent = 'Slug is available';
                                slugMessage.className = 'mt-1 text-xs text-green-600 dark:text-green-400';
                            } else {
                                slugStatus.innerHTML = '<span class="text-amber-500">!</span>';
                                slugMessage.textContent = 'Slug exists - will be auto-adjusted on save';
                                slugMessage.className = 'mt-1 text-xs text-amber-500 dark:text-amber-400';
                            }
                        })
                        .catch(function() {
                            slugStatus.innerHTML = '';
                            slugMessage.textContent = 'Could not verify slug';
                            slugMessage.className = 'mt-1 text-xs text-gray-500 dark:text-gray-400';
                        });
                }

                if (nameInput && slugInput) {
                    nameInput.addEventListener('input', function() {
                        slugInput.value = slugify(this.value);
                        clearTimeout(slugCheckTimeout);
                        slugCheckTimeout = setTimeout(function() {
                            checkSlugAvailability(slugInput.value);
                        }, 400);
                    });

                    slugInput.addEventListener('input', function() {
                        clearTimeout(slugCheckTimeout);
                        slugCheckTimeout = setTimeout(function() {
                            checkSlugAvailability(slugInput.value);
                        }, 400);
                    });
                }
            }

            document.body.addEventListener('click', function(e) {
                var t = e.target;
                if (t.id === 'add-other-image') {
                    e.preventDefault();
                    var container = document.getElementById('other-images-container');
                    var firstRow = container && container.querySelector('.other-image-row');
                    if (container && firstRow) {
                        var row = firstRow.cloneNode(true);
                        row.querySelector('input[type="file"]').value = '';
                        row.querySelector('.remove-other-image').style.display = 'inline-block';
                        var indicator = row.querySelector('.color-indicator');
                        if (indicator) indicator.style.backgroundColor = '#ffffff';
                        var hiddenInput = row.querySelector('.color-dropdown-value');
                        if (hiddenInput) hiddenInput.value = '';
                        var textEl = row.querySelector('.color-dropdown-text');
                        if (textEl) textEl.textContent = '-- Color --';
                        var menu = row.querySelector('.color-dropdown-menu');
                        if (menu) menu.classList.add('hidden');
                        container.appendChild(row);
                    }
                    return;
                }
                if (t.id === 'add-color') {
                    e.preventDefault();
                    var container = document.getElementById('colors-container');
                    var firstRow = container && container.querySelector('.color-row');
                    if (container && firstRow) {
                        var tpl = firstRow.cloneNode(true);
                        tpl.querySelector('input[name*="[name]"]').value = '';
                        tpl.querySelector('input[name*="[name]"]').name = 'colors[' + productEditColorIndex + '][name]';
                        tpl.querySelector('.color-code-input').value = '#000000';
                        tpl.querySelector('.color-code-input').name = 'colors[' + productEditColorIndex + '][color_code]';
                        tpl.querySelector('.color-picker').value = '#000000';
                        tpl.querySelector('.remove-color').style.display = 'inline-block';
                        container.appendChild(tpl);
                        productEditColorIndex++;
                        syncColorsToImageDropdowns();
                    }
                    return;
                }
                if (t.classList && t.classList.contains('remove-other-image')) {
                    e.preventDefault();
                    var rows = document.querySelectorAll('#other-images-container .other-image-row');
                    if (rows.length > 1 && t.closest('.other-image-row')) t.closest('.other-image-row').remove();
                    return;
                }
                if (t.classList && t.classList.contains('remove-existing-image')) {
                    e.preventDefault();
                    var card = t.closest('.existing-image-card');
                    if (card) {
                        var imageId = card.getAttribute('data-image-id');
                        var deleteInput = document.getElementById('delete_image_ids');
                        if (deleteInput && imageId) {
                            var currentIds = deleteInput.value ? deleteInput.value.split(',') : [];
                            if (!currentIds.includes(imageId)) {
                                currentIds.push(imageId);
                                deleteInput.value = currentIds.join(',');
                            }
                        }
                        card.remove();
                    }
                    return;
                }
                if (t.classList && t.classList.contains('replace-image-btn') || t.closest('.replace-image-btn')) {
                    e.preventDefault();
                    var btn = t.classList.contains('replace-image-btn') ? t : t.closest('.replace-image-btn');
                    var card = btn.closest('.existing-image-card');
                    if (card) {
                        var fileInput = card.querySelector('.replace-image-input');
                        if (fileInput) fileInput.click();
                    }
                    return;
                }
                if (t.classList && t.classList.contains('remove-color')) {
                    e.preventDefault();
                    var rows = document.querySelectorAll('#colors-container .color-row');
                    if (rows.length > 1 && t.closest('.color-row')) {
                        t.closest('.color-row').remove();
                        syncColorsToImageDropdowns();
                    }
                    return;
                }
                if (t.id === 'add-key-point') {
                    e.preventDefault();
                    var container = document.getElementById('key-points-container');
                    var first = container && container.querySelector('.key-point-row');
                    if (container && first) {
                        var kpIndex = document.querySelectorAll('#key-points-container .key-point-row').length;
                        var row = first.cloneNode(true);
                        row.querySelector('input[name*="key_heading"]').value = '';
                        row.querySelector('input[name*="key_heading"]').name = 'key_points[' + kpIndex + '][key_heading]';
                        row.querySelector('input[name*="key_point"]').value = '';
                        row.querySelector('input[name*="key_point"]').name = 'key_points[' + kpIndex + '][key_point]';
                        row.querySelector('.remove-key-point').style.display = 'inline-flex';
                        container.appendChild(row);
                    }
                    return;
                }
                if (t.closest && t.closest('.remove-key-point')) {
                    e.preventDefault();
                    var rows = document.querySelectorAll('#key-points-container .key-point-row');
                    var row = t.closest('.key-point-row');
                    if (rows.length > 1 && row) row.remove();
                    return;
                }
                // Duplicate last pricing chart (with values)
                if (t.id === 'duplicate-pricing-chart') {
                    e.preventDefault();
                    var container = document.getElementById('pricing-charts-container');
                    var allBlocks = container.querySelectorAll('.pricing-chart-block');
                    var lastBlock = allBlocks[allBlocks.length - 1];
                    if (container && lastBlock) {
                        var chartIndex = allBlocks.length;
                        var block = lastBlock.cloneNode(true);
                        block.setAttribute('data-chart-index', chartIndex);
                        block.querySelector('.remove-pricing-chart').style.display = 'inline-flex';

                        // Update heading name
                        var headingInput = block.querySelector('.chart-heading-input');
                        headingInput.name = 'pricing_charts[' + chartIndex + '][heading]';

                        // Update all types
                        block.querySelectorAll('.chart-type-block').forEach(function(typeBlock, typeIdx) {
                            typeBlock.setAttribute('data-type-index', typeIdx);
                            typeBlock.querySelector('.type-name-input').name = 'pricing_charts[' + chartIndex + '][types][' + typeIdx + '][type]';
                            typeBlock.querySelector('.remove-chart-type').style.display = typeIdx > 0 ? 'inline-flex' : 'none';

                            // Update all tiers in this type
                            typeBlock.querySelectorAll('.tier-row').forEach(function(tierRow, tierIdx) {
                                tierRow.querySelector('input[name*="[quantity]"]').name = 'pricing_charts[' + chartIndex + '][types][' + typeIdx + '][tiers][' + tierIdx + '][quantity]';
                                tierRow.querySelector('input[name*="[price]"]').name = 'pricing_charts[' + chartIndex + '][types][' + typeIdx + '][tiers][' + tierIdx + '][price]';
                                tierRow.querySelector('.remove-tier').style.display = tierIdx > 0 ? 'inline-flex' : 'none';
                            });
                        });

                        container.appendChild(block);
                        // Show remove on all charts
                        allBlocks[0].querySelector('.remove-pricing-chart').style.display = 'inline-flex';
                    }
                    return;
                }
                // Add new pricing chart
                if (t.id === 'add-pricing-chart') {
                    e.preventDefault();
                    var container = document.getElementById('pricing-charts-container');
                    var firstBlock = container && container.querySelector('.pricing-chart-block');
                    if (container && firstBlock) {
                        var chartIndex = document.querySelectorAll('#pricing-charts-container .pricing-chart-block').length;
                        var block = firstBlock.cloneNode(true);
                        block.setAttribute('data-chart-index', chartIndex);
                        block.querySelector('.remove-pricing-chart').style.display = 'inline-flex';
                        block.querySelector('.chart-heading-input').value = '';
                        block.querySelector('.chart-heading-input').name = 'pricing_charts[' + chartIndex + '][heading]';

                        // Reset types container with single type
                        var typesContainer = block.querySelector('.chart-types-container');
                        var firstTypeBlock = typesContainer.querySelector('.chart-type-block');
                        typesContainer.querySelectorAll('.chart-type-block').forEach(function(tb, i) { if (i > 0) tb.remove(); });
                        var typeBlock = typesContainer.querySelector('.chart-type-block');
                        typeBlock.setAttribute('data-type-index', 0);
                        typeBlock.querySelector('.type-name-input').value = '';
                        typeBlock.querySelector('.type-name-input').name = 'pricing_charts[' + chartIndex + '][types][0][type]';
                        typeBlock.querySelector('.remove-chart-type').style.display = 'none';

                        // Reset tiers
                        var tiersContainer = typeBlock.querySelector('.type-tiers-container');
                        tiersContainer.querySelectorAll('.tier-row').forEach(function(tr, i) { if (i > 0) tr.remove(); });
                        var tierRow = tiersContainer.querySelector('.tier-row');
                        tierRow.querySelector('input[name*="[quantity]"]').value = '';
                        tierRow.querySelector('input[name*="[quantity]"]').name = 'pricing_charts[' + chartIndex + '][types][0][tiers][0][quantity]';
                        tierRow.querySelector('input[name*="[price]"]').value = '';
                        tierRow.querySelector('input[name*="[price]"]').name = 'pricing_charts[' + chartIndex + '][types][0][tiers][0][price]';
                        tierRow.querySelector('.remove-tier').style.display = 'none';

                        container.appendChild(block);
                    }
                    return;
                }
                // Remove pricing chart
                if (t.closest('.remove-pricing-chart')) {
                    e.preventDefault();
                    var blocks = document.querySelectorAll('#pricing-charts-container .pricing-chart-block');
                    if (blocks.length > 1 && t.closest('.pricing-chart-block')) t.closest('.pricing-chart-block').remove();
                    return;
                }
                // Duplicate last type (with values)
                if (t.closest('.duplicate-type-btn')) {
                    e.preventDefault();
                    var chartBlock = t.closest('.pricing-chart-block');
                    var chartIndex = chartBlock.getAttribute('data-chart-index');
                    var typesContainer = chartBlock.querySelector('.chart-types-container');
                    var allTypes = typesContainer.querySelectorAll('.chart-type-block');
                    var lastType = allTypes[allTypes.length - 1];
                    if (lastType) {
                        var typeIndex = allTypes.length;
                        var newType = lastType.cloneNode(true);
                        newType.setAttribute('data-type-index', typeIndex);
                        newType.querySelector('.type-name-input').name = 'pricing_charts[' + chartIndex + '][types][' + typeIndex + '][type]';
                        newType.querySelector('.remove-chart-type').style.display = 'inline-flex';

                        // Update all tiers
                        newType.querySelectorAll('.tier-row').forEach(function(tierRow, tierIdx) {
                            tierRow.querySelector('input[name*="[quantity]"]').name = 'pricing_charts[' + chartIndex + '][types][' + typeIndex + '][tiers][' + tierIdx + '][quantity]';
                            tierRow.querySelector('input[name*="[price]"]').name = 'pricing_charts[' + chartIndex + '][types][' + typeIndex + '][tiers][' + tierIdx + '][price]';
                            tierRow.querySelector('.remove-tier').style.display = tierIdx > 0 ? 'inline-flex' : 'none';
                        });

                        // Insert before the buttons div
                        var buttonsDiv = typesContainer.querySelector('.mt-1.flex.gap-3');
                        typesContainer.insertBefore(newType, buttonsDiv);
                        // Show remove on first type
                        allTypes[0].querySelector('.remove-chart-type').style.display = 'inline-flex';
                    }
                    return;
                }
                // Add type to chart
                if (t.closest('.add-type-btn')) {
                    e.preventDefault();
                    var chartBlock = t.closest('.pricing-chart-block');
                    var chartIndex = chartBlock.getAttribute('data-chart-index');
                    var typesContainer = chartBlock.querySelector('.chart-types-container');
                    var typeBlocks = typesContainer.querySelectorAll('.chart-type-block');
                    var typeIndex = typeBlocks.length;
                    var firstTypeBlock = typeBlocks[0];
                    if (firstTypeBlock) {
                        var newType = firstTypeBlock.cloneNode(true);
                        newType.setAttribute('data-type-index', typeIndex);
                        newType.querySelector('.type-name-input').value = '';
                        newType.querySelector('.type-name-input').name = 'pricing_charts[' + chartIndex + '][types][' + typeIndex + '][type]';
                        newType.querySelector('.remove-chart-type').style.display = 'inline-flex';

                        // Reset tiers
                        var tiersContainer = newType.querySelector('.type-tiers-container');
                        tiersContainer.querySelectorAll('.tier-row').forEach(function(tr, i) { if (i > 0) tr.remove(); });
                        var tierRow = tiersContainer.querySelector('.tier-row');
                        tierRow.querySelector('input[name*="[quantity]"]').value = '';
                        tierRow.querySelector('input[name*="[quantity]"]').name = 'pricing_charts[' + chartIndex + '][types][' + typeIndex + '][tiers][0][quantity]';
                        tierRow.querySelector('input[name*="[price]"]').value = '';
                        tierRow.querySelector('input[name*="[price]"]').name = 'pricing_charts[' + chartIndex + '][types][' + typeIndex + '][tiers][0][price]';
                        tierRow.querySelector('.remove-tier').style.display = 'none';

                        // Insert before the buttons div
                        var buttonsDiv = t.closest('.mt-1.flex.gap-3');
                        typesContainer.insertBefore(newType, buttonsDiv);
                        // Show remove button on first type
                        firstTypeBlock.querySelector('.remove-chart-type').style.display = 'inline-flex';
                    }
                    return;
                }
                // Remove type from chart
                if (t.closest('.remove-chart-type')) {
                    e.preventDefault();
                    var typesContainer = t.closest('.chart-types-container');
                    var typeBlocks = typesContainer.querySelectorAll('.chart-type-block');
                    if (typeBlocks.length > 1 && t.closest('.chart-type-block')) t.closest('.chart-type-block').remove();
                    return;
                }
                // Add tier to type
                if (t.closest('.add-tier-btn')) {
                    e.preventDefault();
                    var typeBlock = t.closest('.chart-type-block');
                    var chartBlock = t.closest('.pricing-chart-block');
                    var chartIndex = chartBlock.getAttribute('data-chart-index');
                    var typeIndex = typeBlock.getAttribute('data-type-index');
                    var tiersContainer = typeBlock.querySelector('.type-tiers-container');
                    var tierRows = tiersContainer.querySelectorAll('.tier-row');
                    var tierIndex = tierRows.length;
                    var firstTier = tierRows[0];
                    if (firstTier) {
                        var newTier = firstTier.cloneNode(true);
                        newTier.querySelector('input[name*="[quantity]"]').value = '';
                        newTier.querySelector('input[name*="[quantity]"]').name = 'pricing_charts[' + chartIndex + '][types][' + typeIndex + '][tiers][' + tierIndex + '][quantity]';
                        newTier.querySelector('input[name*="[price]"]').value = '';
                        newTier.querySelector('input[name*="[price]"]').name = 'pricing_charts[' + chartIndex + '][types][' + typeIndex + '][tiers][' + tierIndex + '][price]';
                        newTier.querySelector('.remove-tier').style.display = 'inline-flex';
                        tiersContainer.appendChild(newTier);
                        // Show remove on first tier too
                        firstTier.querySelector('.remove-tier').style.display = 'inline-flex';
                    }
                    return;
                }
                // Remove tier
                if (t.closest('.remove-tier')) {
                    e.preventDefault();
                    var tiersContainer = t.closest('.type-tiers-container');
                    if (tiersContainer) {
                        var rows = tiersContainer.querySelectorAll('.tier-row');
                        if (rows.length > 1) t.closest('.tier-row').remove();
                    }
                    return;
                }
            });

            var colorNameMap = {
                'red': '#FF0000', 'darkred': '#8B0000', 'crimson': '#DC143C', 'firebrick': '#B22222', 'indianred': '#CD5C5C',
                'lightcoral': '#F08080', 'salmon': '#FA8072', 'lightsalmon': '#FFA07A', 'tomato': '#FF6347', 'coral': '#FF7F50',
                'orange': '#FFA500', 'darkorange': '#FF8C00', 'orangered': '#FF4500', 'texasorange': '#BF5700',
                'gold': '#FFD700', 'yellow': '#FFFF00', 'lightyellow': '#FFFFE0', 'lemonchiffon': '#FFFACD', 'khaki': '#F0E68C',
                'green': '#008000', 'lime': '#00FF00', 'limegreen': '#32CD32', 'forestgreen': '#228B22', 'darkgreen': '#006400',
                'seagreen': '#2E8B57', 'springgreen': '#00FF7F', 'mediumseagreen': '#3CB371', 'lightgreen': '#90EE90',
                'palegreen': '#98FB98', 'olive': '#808000', 'olivedrab': '#6B8E23', 'yellowgreen': '#9ACD32', 'greenyellow': '#ADFF2F',
                'kelly': '#4CBB17', 'kellygreen': '#4CBB17', 'army': '#4B5320', 'armygreen': '#4B5320',
                'teal': '#008080', 'darkcyan': '#008B8B', 'cyan': '#00FFFF', 'aqua': '#00FFFF', 'lightcyan': '#E0FFFF',
                'aquamarine': '#7FFFD4', 'turquoise': '#40E0D0', 'mediumturquoise': '#48D1CC', 'darkturquoise': '#00CED1',
                'blue': '#0000FF', 'darkblue': '#00008B', 'mediumblue': '#0000CD', 'navy': '#000080', 'midnightblue': '#191970',
                'royalblue': '#4169E1', 'royal': '#4169E1', 'steelblue': '#4682B4', 'dodgerblue': '#1E90FF', 'deepskyblue': '#00BFFF',
                'cornflowerblue': '#6495ED', 'skyblue': '#87CEEB', 'lightskyblue': '#87CEFA', 'lightsteelblue': '#B0C4DE',
                'lightblue': '#ADD8E6', 'powderblue': '#B0E0E6', 'cadetblue': '#5F9EA0',
                'carolinablue': '#56A0D3', 'carolina': '#56A0D3', 'sapphire': '#0F52BA',
                'purple': '#800080', 'indigo': '#4B0082', 'darkviolet': '#9400D3', 'darkorchid': '#9932CC', 'blueviolet': '#8A2BE2',
                'violet': '#EE82EE', 'magenta': '#FF00FF', 'fuchsia': '#FF00FF', 'orchid': '#DA70D6', 'mediumorchid': '#BA55D3',
                'mediumpurple': '#9370DB', 'plum': '#DDA0DD', 'lavender': '#E6E6FA', 'thistle': '#D8BFD8',
                'pink': '#FFC0CB', 'lightpink': '#FFB6C1', 'hotpink': '#FF69B4', 'deeppink': '#FF1493', 'palevioletred': '#DB7093',
                'mediumvioletred': '#C71585', 'rose': '#FF007F', 'rosegold': '#B76E79', 'azalea': '#F19CBB',
                'brown': '#A52A2A', 'saddlebrown': '#8B4513', 'sienna': '#A0522D', 'chocolate': '#D2691E', 'peru': '#CD853F',
                'sandybrown': '#F4A460', 'burlywood': '#DEB887', 'tan': '#D2B48C', 'rosybrown': '#BC8F8F', 'maroon': '#800000',
                'white': '#FFFFFF', 'snow': '#FFFAFA', 'ivory': '#FFFFF0', 'floralwhite': '#FFFAF0', 'ghostwhite': '#F8F8FF',
                'beige': '#F5F5DC', 'linen': '#FAF0E6', 'oldlace': '#FDF5E6', 'antiquewhite': '#FAEBD7', 'bisque': '#FFE4C4',
                'wheat': '#F5DEB3', 'cornsilk': '#FFF8DC', 'blanchedalmond': '#FFEBCD', 'papayawhip': '#FFEFD5', 'moccasin': '#FFE4B5',
                'peachpuff': '#FFDAB9', 'navajowhite': '#FFDEAD', 'cream': '#FFFDD0', 'offwhite': '#FAF9F6', 'natural': '#F5F5DC',
                'black': '#000000', 'gray': '#808080', 'grey': '#808080', 'darkgray': '#A9A9A9', 'darkgrey': '#A9A9A9',
                'dimgray': '#696969', 'dimgrey': '#696969', 'lightgray': '#D3D3D3', 'lightgrey': '#D3D3D3',
                'silver': '#C0C0C0', 'gainsboro': '#DCDCDC', 'whitesmoke': '#F5F5F5', 'charcoal': '#36454F', 'slate': '#708090',
                'slategray': '#708090', 'slategrey': '#708090', 'lightslategray': '#778899', 'lightslategrey': '#778899'
            };

            function getColorCodeFromName(name) {
                if (!name) return null;
                var normalized = name.toLowerCase().replace(/[\s\-_]/g, '');
                return colorNameMap[normalized] || null;
            }

            document.body.addEventListener('input', function(e) {
                if (e.target.classList && e.target.classList.contains('color-picker')) {
                    var row = e.target.closest('.color-row');
                    if (row) { var inp = row.querySelector('.color-code-input'); if (inp) inp.value = e.target.value; }
                }
                if (e.target.classList && e.target.classList.contains('color-name-input')) {
                    var colorCode = getColorCodeFromName(e.target.value);
                    if (colorCode) {
                        var row = e.target.closest('.color-row');
                        if (row) {
                            var codeInput = row.querySelector('.color-code-input');
                            var picker = row.querySelector('.color-picker');
                            if (codeInput) codeInput.value = colorCode;
                            if (picker) picker.value = colorCode;
                        }
                    }
                }
            });
            document.body.addEventListener('change', function(e) {
                if (e.target.classList && e.target.classList.contains('color-code-input')) {
                    var row = e.target.closest('.color-row');
                    var picker = row && row.querySelector('.color-picker');
                    if (picker && /^#[0-9A-Fa-f]{6}$/.test(e.target.value)) picker.value = e.target.value;
                }
            });

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initProductEdit);
            } else {
                initProductEdit();
            }
        })();
    </script>
    @endPushOnce
</x-admin::layouts>
