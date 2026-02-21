
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
                                <x-admin::form.control-group.control type="text" name="name" :value="old('name', $product->name)" />
                            </x-admin::form.control-group>
                            <x-admin::form.control-group>
                                <div class="flex items-center justify-between gap-2">
                                    <x-admin::form.control-group.label>@lang('admin::app.products.create.category')</x-admin::form.control-group.label>
                                    <span class="flex gap-2 text-sm">
                                        <a href="{{ route('admin.product_categories.create') }}" target="_blank" class="text-brandColor hover:underline">@lang('admin::app.products.categories.add')</a>
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
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
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

                    <!-- Description Section -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Description</p>
                        <div class="flex flex-col gap-4">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>@lang('admin::app.products.create.additional-info')</x-admin::form.control-group.label>
                                <textarea name="additional_info" id="additional_info_editor" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" rows="6">{{ old('additional_info', $product->additional_info) }}</textarea>
                            </x-admin::form.control-group>
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>@lang('admin::app.products.create.shipping-info')</x-admin::form.control-group.label>
                                <textarea name="shipping_info" id="shipping_info_editor" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" rows="6">{{ old('shipping_info', $product->shipping_info) }}</textarea>
                            </x-admin::form.control-group>
                        </div>
                    </div>

                    <!-- Images Section -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Images</p>
                        <div class="flex flex-col gap-4">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>@lang('admin::app.products.create.cover-image')</x-admin::form.control-group.label>
                                @if ($product->cover_image)
                                    <p class="mb-1 flex flex-wrap gap-2">
                                        <img src="{{ asset('storage/' . $product->cover_image) }}" alt="" class="h-20 w-20 rounded border border-gray-200 object-cover dark:border-gray-700" />
                                        <a href="{{ asset('storage/' . $product->cover_image) }}" target="_blank" class="text-brandColor hover:underline text-sm">View</a>
                                    </p>
                                @endif
                                <input type="file" name="cover_image" id="cover_image_input" accept="image/*" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                <div id="cover_image_preview" class="mt-2 flex flex-wrap gap-2"></div>
                            </x-admin::form.control-group>
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>@lang('admin::app.products.create.other-images')</x-admin::form.control-group.label>
                                @if ($product->otherImages->isNotEmpty())
                                    <p class="mb-1 flex flex-wrap gap-2">
                                        @foreach ($product->otherImages as $img)
                                            <img src="{{ asset('storage/' . $img->path) }}" alt="" class="h-16 w-16 rounded border border-gray-200 object-cover dark:border-gray-700" />
                                        @endforeach
                                    </p>
                                @endif
                                <div id="other-images-container" class="flex flex-col gap-2">
                                    <div class="flex gap-2 items-center other-image-row">
                                        <input type="file" name="other_images[]" accept="image/*" class="other-image-file flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                        <button type="button" class="secondary-button remove-other-image" style="display:none;">&times;</button>
                                    </div>
                                </div>
                                <div id="other_images_preview" class="mt-2 flex flex-wrap gap-2"></div>
                                <button type="button" id="add-other-image" class="mt-1 text-sm font-semibold text-brandColor hover:underline">@lang('admin::app.products.create.add-more')</button>
                            </x-admin::form.control-group>
                        </div>
                    </div>

                    <!-- Key Points -->
                    @php
                        $keyPointsData = old('key_points') ?? $product->keyPoints->map(fn($kp) => ['key_heading' => $kp->key_heading, 'key_point' => $kp->key_point])->toArray();
                        $pricingChartsData = old('pricing_charts') ?? $product->pricingCharts->map(fn($pc) => ['heading' => $pc->heading, 'type' => $pc->type, 'tiers' => $pc->tiers->map(fn($t) => ['quantity' => $t->quantity, 'price' => $t->price])->toArray()])->toArray();
                    @endphp
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">@lang('admin::app.products.create.key-points')</p>
                        <div id="key-points-container" class="flex flex-col gap-3">
                                    @forelse($keyPointsData as $idx => $kp)
                                        <div class="key-point-row flex flex-col gap-2 rounded border border-gray-200 p-2 dark:border-gray-700">
                                            <input type="text" name="key_points[{{ $idx }}][key_heading]" placeholder="@lang('admin::app.products.create.key-heading')" value="{{ $kp['key_heading'] ?? '' }}" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                            <textarea name="key_points[{{ $idx }}][key_point]" placeholder="@lang('admin::app.products.create.key-point')" rows="2" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $kp['key_point'] ?? '' }}</textarea>
                                            <button type="button" class="secondary-button remove-key-point w-fit" @if(count($keyPointsData) <= 1) style="display:none;" @endif>&times; Remove</button>
                                        </div>
                                    @empty
                                        <div class="key-point-row flex flex-col gap-2 rounded border border-gray-200 p-2 dark:border-gray-700">
                                            <input type="text" name="key_points[0][key_heading]" placeholder="@lang('admin::app.products.create.key-heading')" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                            <textarea name="key_points[0][key_point]" placeholder="@lang('admin::app.products.create.key-point')" rows="2" class="rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"></textarea>
                                            <button type="button" class="secondary-button remove-key-point w-fit" style="display:none;">&times; Remove</button>
                                        </div>
                                    @endforelse
                                </div>
                        <button type="button" id="add-key-point" class="mt-1 text-sm font-semibold text-brandColor hover:underline">@lang('admin::app.products.create.add-more')</button>
                    </div>

                    <!-- Pricing Section -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">@lang('admin::app.products.create.pricing-charts')</p>
                        <div id="pricing-charts-container" class="flex flex-col gap-4">
                                    @forelse($pricingChartsData as $cIdx => $chart)
                                        <div class="pricing-chart-block rounded-lg border border-gray-200 p-3 dark:border-gray-700" data-chart-index="{{ $cIdx }}">
                                            <div class="mb-2 flex items-center justify-between">
                                                <span class="text-sm font-semibold dark:text-white">@lang('admin::app.products.create.pricing-chart') {{ $cIdx + 1 }}</span>
                                                <button type="button" class="secondary-button remove-pricing-chart w-fit text-xs" @if(count($pricingChartsData) <= 1) style="display:none;" @endif>&times; Remove chart</button>
                                            </div>
                                            <div class="mb-2 flex gap-2">
                                                <input type="text" name="pricing_charts[{{ $cIdx }}][heading]" placeholder="@lang('admin::app.products.create.key-heading')" value="{{ $chart['heading'] ?? '' }}" class="flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                                <input type="text" name="pricing_charts[{{ $cIdx }}][type]" placeholder="Type" value="{{ $chart['type'] ?? '' }}" class="w-32 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                            </div>
                                            <div class="chart-tiers" data-chart-index="{{ $cIdx }}">
                                                @foreach($chart['tiers'] ?? [] as $tIdx => $tier)
                                                    <div class="chart-tier-row mb-2 flex gap-2">
                                                        <input type="number" name="pricing_charts[{{ $cIdx }}][tiers][{{ $tIdx }}][quantity]" placeholder="@lang('admin::app.products.create.quantity')" value="{{ $tier['quantity'] ?? '' }}" step="1" min="0" class="w-24 rounded border border-gray-200 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                                        <input type="number" name="pricing_charts[{{ $cIdx }}][tiers][{{ $tIdx }}][price]" placeholder="Price" value="{{ $tier['price'] ?? '' }}" step="0.01" min="0" class="w-24 rounded border border-gray-200 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                                        <button type="button" class="secondary-button remove-tier w-fit text-xs" @if(count($chart['tiers'] ?? []) <= 1) style="display:none;" @endif>&times;</button>
                                                    </div>
                                                @endforeach
                                                @if(empty($chart['tiers']))
                                                    <div class="chart-tier-row mb-2 flex gap-2">
                                                        <input type="number" name="pricing_charts[{{ $cIdx }}][tiers][0][quantity]" placeholder="@lang('admin::app.products.create.quantity')" step="1" min="0" class="w-24 rounded border border-gray-200 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                                        <input type="number" name="pricing_charts[{{ $cIdx }}][tiers][0][price]" placeholder="Price" step="0.01" min="0" class="w-24 rounded border border-gray-200 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                                        <button type="button" class="secondary-button remove-tier w-fit text-xs" style="display:none;">&times;</button>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="add-tier-btn mt-1 text-xs font-semibold text-brandColor hover:underline" data-chart-index="{{ $cIdx }}">@lang('admin::app.products.create.add-more') (quantity/price)</button>
                                        </div>
                                    @empty
                                        <div class="pricing-chart-block rounded-lg border border-gray-200 p-3 dark:border-gray-700" data-chart-index="0">
                                            <div class="mb-2 flex items-center justify-between">
                                                <span class="text-sm font-semibold dark:text-white">@lang('admin::app.products.create.pricing-chart') 1</span>
                                                <button type="button" class="secondary-button remove-pricing-chart w-fit text-xs" style="display:none;">&times; Remove chart</button>
                                            </div>
                                            <div class="mb-2 flex gap-2">
                                                <input type="text" name="pricing_charts[0][heading]" placeholder="@lang('admin::app.products.create.key-heading')" class="flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                                <input type="text" name="pricing_charts[0][type]" placeholder="Type" class="w-32 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                            </div>
                                            <div class="chart-tiers" data-chart-index="0">
                                                <div class="chart-tier-row mb-2 flex gap-2">
                                                    <input type="number" name="pricing_charts[0][tiers][0][quantity]" placeholder="@lang('admin::app.products.create.quantity')" step="1" min="0" class="w-24 rounded border border-gray-200 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                                    <input type="number" name="pricing_charts[0][tiers][0][price]" placeholder="Price" step="0.01" min="0" class="w-24 rounded border border-gray-200 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                                    <button type="button" class="secondary-button remove-tier w-fit text-xs" style="display:none;">&times;</button>
                                                </div>
                                            </div>
                                            <button type="button" class="add-tier-btn mt-1 text-xs font-semibold text-brandColor hover:underline" data-chart-index="0">@lang('admin::app.products.create.add-more') (quantity/price)</button>
                                        </div>
                                    @endforelse
                                </div>
                        <button type="button" id="add-pricing-chart" class="mt-2 text-sm font-semibold text-brandColor hover:underline">@lang('admin::app.products.create.add-new-pricing-chart')</button>
                    </div>

                </div>

                <!-- Right column: Colors -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">@lang('admin::app.products.create.colors')</p>
                        <div id="colors-container" class="flex flex-col gap-2">
                            @php $productColors = old('colors') ?? $product->colors->map(fn($c) => ['name' => $c->name, 'color_code' => $c->color_code])->toArray(); @endphp
                            @if (!empty($productColors))
                                @foreach ($productColors as $idx => $c)
                                    <div class="flex gap-2 items-center color-row">
                                        <input type="text" name="colors[{{ $idx }}][name]" placeholder="@lang('admin::app.products.create.color-name')" value="{{ $c['name'] ?? '' }}" class="flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                        <input type="text" name="colors[{{ $idx }}][color_code]" value="{{ $c['color_code'] ?? '#000000' }}" class="color-code-input w-24 rounded border border-gray-200 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="#hex" />
                                        <input type="color" class="color-picker h-9 w-12 cursor-pointer rounded border border-gray-200 dark:border-gray-700" value="{{ $c['color_code'] ?? '#000000' }}" title="Pick color" />
                                        <button type="button" class="secondary-button remove-color" @if(count($productColors) <= 1) style="display:none;" @endif>&times;</button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex gap-2 items-center color-row">
                                    <input type="text" name="colors[0][name]" placeholder="@lang('admin::app.products.create.color-name')" class="flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                    <input type="text" name="colors[0][color_code]" value="#000000" class="color-code-input w-24 rounded border border-gray-200 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="#hex" />
                                    <input type="color" class="color-picker h-9 w-12 cursor-pointer rounded border border-gray-200 dark:border-gray-700" value="#000000" title="Pick color" />
                                    <button type="button" class="secondary-button remove-color" style="display:none;">&times;</button>
                                </div>
                            @endif
                        </div>
                        <button type="button" id="add-color" class="mt-1 text-sm font-semibold text-brandColor hover:underline">@lang('admin::app.products.create.add-more')</button>
                    </div>
                </div>
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.products.edit.form.after') !!}

    @pushOnce('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        (function() {
            var productEditColorIndex = 0;

            function initProductEdit() {
                if (document.querySelector('#additional_info_editor') && typeof ClassicEditor !== 'undefined') {
                    ClassicEditor.create(document.querySelector('#additional_info_editor'), { removePlugins: ['MediaEmbed'], heading: { options: [] } }).catch(console.error);
                }
                if (document.querySelector('#shipping_info_editor') && typeof ClassicEditor !== 'undefined') {
                    ClassicEditor.create(document.querySelector('#shipping_info_editor'), { removePlugins: ['MediaEmbed'], heading: { options: [] } }).catch(console.error);
                }
                productEditColorIndex = document.querySelectorAll('#colors-container .color-row').length;
                var coverInput = document.getElementById('cover_image_input');
                if (coverInput) {
                    coverInput.addEventListener('change', function() {
                        var preview = document.getElementById('cover_image_preview');
                        if (!preview) return;
                        preview.innerHTML = '';
                        if (this.files && this.files[0]) {
                            var r = new FileReader();
                            r.onload = function(e) {
                                var img = document.createElement('img');
                                img.src = e.target.result;
                                img.className = 'h-20 w-20 rounded border border-gray-200 object-cover dark:border-gray-700';
                                preview.appendChild(img);
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
                    }
                    return;
                }
                if (t.classList && t.classList.contains('remove-other-image')) {
                    e.preventDefault();
                    var rows = document.querySelectorAll('#other-images-container .other-image-row');
                    if (rows.length > 1 && t.closest('.other-image-row')) t.closest('.other-image-row').remove();
                    return;
                }
                if (t.classList && t.classList.contains('remove-color')) {
                    e.preventDefault();
                    var rows = document.querySelectorAll('#colors-container .color-row');
                    if (rows.length > 1 && t.closest('.color-row')) t.closest('.color-row').remove();
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
                        row.querySelector('textarea[name*="key_point"]').value = '';
                        row.querySelector('textarea[name*="key_point"]').name = 'key_points[' + kpIndex + '][key_point]';
                        row.querySelector('.remove-key-point').style.display = 'inline-block';
                        container.appendChild(row);
                    }
                    return;
                }
                if (t.classList && t.classList.contains('remove-key-point')) {
                    e.preventDefault();
                    var rows = document.querySelectorAll('#key-points-container .key-point-row');
                    if (rows.length > 1 && t.closest('.key-point-row')) t.closest('.key-point-row').remove();
                    return;
                }
                if (t.id === 'add-pricing-chart') {
                    e.preventDefault();
                    var container = document.getElementById('pricing-charts-container');
                    var firstBlock = container && container.querySelector('.pricing-chart-block');
                    if (container && firstBlock) {
                        var chartIndex = document.querySelectorAll('#pricing-charts-container .pricing-chart-block').length;
                        var block = firstBlock.cloneNode(true);
                        block.setAttribute('data-chart-index', chartIndex);
                        block.querySelector('.font-semibold').textContent = 'Pricing Chart ' + (chartIndex + 1);
                        block.querySelector('.remove-pricing-chart').style.display = 'inline-block';
                        block.querySelector('input[name*="[heading]"]').value = '';
                        block.querySelector('input[name*="[heading]"]').name = 'pricing_charts[' + chartIndex + '][heading]';
                        block.querySelector('input[name*="[type]"]').value = '';
                        block.querySelector('input[name*="[type]"]').name = 'pricing_charts[' + chartIndex + '][type]';
                        var tiersContainer = block.querySelector('.chart-tiers');
                        tiersContainer.setAttribute('data-chart-index', chartIndex);
                        tiersContainer.innerHTML = '';
                        var firstTier = firstBlock.querySelector('.chart-tier-row');
                        var tierRow = firstTier.cloneNode(true);
                        tierRow.querySelector('input[name*="[quantity]"]').value = '';
                        tierRow.querySelector('input[name*="[quantity]"]').name = 'pricing_charts[' + chartIndex + '][tiers][0][quantity]';
                        tierRow.querySelector('input[name*="[price]"]').value = '';
                        tierRow.querySelector('input[name*="[price]"]').name = 'pricing_charts[' + chartIndex + '][tiers][0][price]';
                        tierRow.querySelector('.remove-tier').style.display = 'none';
                        tiersContainer.appendChild(tierRow);
                        var addTierBtn = block.querySelector('.add-tier-btn');
                        addTierBtn.setAttribute('data-chart-index', chartIndex);
                        container.appendChild(block);
                    }
                    return;
                }
                if (t.classList && t.classList.contains('remove-pricing-chart')) {
                    e.preventDefault();
                    var blocks = document.querySelectorAll('#pricing-charts-container .pricing-chart-block');
                    if (blocks.length > 1 && t.closest('.pricing-chart-block')) t.closest('.pricing-chart-block').remove();
                    return;
                }
                if (t.classList && t.classList.contains('add-tier-btn')) {
                    e.preventDefault();
                    var block = t.closest('.pricing-chart-block');
                    var chartIndex = block.getAttribute('data-chart-index');
                    var tiersContainer = block.querySelector('.chart-tiers');
                    var tierRows = tiersContainer.querySelectorAll('.chart-tier-row');
                    var tierIndex = tierRows.length;
                    var firstTier = tierRows[0];
                    if (firstTier) {
                        var newTier = firstTier.cloneNode(true);
                        newTier.querySelector('input[name*="[quantity]"]').value = '';
                        newTier.querySelector('input[name*="[quantity]"]').name = 'pricing_charts[' + chartIndex + '][tiers][' + tierIndex + '][quantity]';
                        newTier.querySelector('input[name*="[price]"]').value = '';
                        newTier.querySelector('input[name*="[price]"]').name = 'pricing_charts[' + chartIndex + '][tiers][' + tierIndex + '][price]';
                        newTier.querySelector('.remove-tier').style.display = tierIndex > 0 ? 'inline-block' : 'none';
                        tiersContainer.appendChild(newTier);
                        tierRows = tiersContainer.querySelectorAll('.chart-tier-row');
                        if (tierRows.length > 1) { firstTier.querySelector('.remove-tier').style.display = 'inline-block'; }
                    }
                    return;
                }
                if (t.classList && t.classList.contains('remove-tier')) {
                    e.preventDefault();
                    var tiersContainer = t.closest('.chart-tiers');
                    if (tiersContainer) {
                        var rows = tiersContainer.querySelectorAll('.chart-tier-row');
                        if (rows.length > 1) t.closest('.chart-tier-row').remove();
                    }
                    return;
                }
            });

            document.body.addEventListener('input', function(e) {
                if (e.target.classList && e.target.classList.contains('color-picker')) {
                    var row = e.target.closest('.color-row');
                    if (row) { var inp = row.querySelector('.color-code-input'); if (inp) inp.value = e.target.value; }
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
