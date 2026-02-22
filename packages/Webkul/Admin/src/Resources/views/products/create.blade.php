
<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.products.create.title')
    </x-slot>

    {!! view_render_event('admin.products.create.form.before') !!}

    <x-admin::form
        :action="route('admin.products.store')"
        method="POST"
        enctype="multipart/form-data"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    {!! view_render_event('admin.products.create.breadcrumbs.before') !!}

                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="products.create" />

                    {!! view_render_event('admin.products.create.breadcrumbs.after') !!}

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.products.create.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.products.create.save_button.before') !!}

                        <!-- Create button for Product -->
                        @if (bouncer()->hasPermission('settings.user.groups.create'))
                            <button
                                type="submit"
                                class="primary-button"
                            >
                                @lang('admin::app.products.create.save-btn')
                            </button>
                        @endif

                        {!! view_render_event('admin.products.create.save_button.after') !!}
                    </div>
                </div>
            </div>

            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left column -->
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <!-- General Section: Product Name, Category, Style | Size -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.products.create.general')
                        </p>
                        <div class="flex flex-col gap-4">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">@lang('admin::app.products.create.product-name')</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="name" id="product_name_input" :value="old('name')" />
                            </x-admin::form.control-group>
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>@lang('admin::app.products.create.slug')</x-admin::form.control-group.label>
                                <div class="relative">
                                    <x-admin::form.control-group.control type="text" name="slug" id="product_slug_input" :value="old('slug')" placeholder="auto-generated-from-name" class="pr-10" />
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
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </x-admin::form.control-group>
                            <div class="grid grid-cols-2 gap-4">
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>@lang('admin::app.products.create.style')</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.control type="text" name="style" :value="old('style')" />
                                </x-admin::form.control-group>
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>@lang('admin::app.products.create.size')</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.control type="text" name="size" :value="old('size')" />
                                </x-admin::form.control-group>
                            </div>
                        </div>
                    </div>

                    <!-- Images Section -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">Images</p>
                        <div class="flex flex-col gap-6">
                            <!-- Cover Image - Single Column -->
                            <div>
                                <x-admin::form.control-group.label>@lang('admin::app.products.create.cover-image')</x-admin::form.control-group.label>
                                <input type="file" name="cover_image" id="cover_image_input" accept="image/*" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                <div id="cover_image_preview" class="mt-3"></div>
                            </div>

                            <!-- Other Images with Color Association -->
                            <div>
                                <x-admin::form.control-group.label>@lang('admin::app.products.create.other-images')</x-admin::form.control-group.label>
                                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Add images and optionally link to a color:</p>
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
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.products.create.key-points')
                        </p>
                        <div id="key-points-container" class="flex flex-col gap-3">
                            <div class="key-point-row flex items-center gap-3">
                                <input type="text" name="key_points[0][key_heading]" placeholder="@lang('admin::app.products.create.key-heading')" class="w-1/3 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                <input type="text" name="key_points[0][key_point]" placeholder="@lang('admin::app.products.create.key-point')" class="flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                <button type="button" class="remove-key-point text-gray-400 hover:text-red-500" style="display:none;" title="Remove">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" id="add-key-point" class="mt-1 text-sm font-semibold text-brandColor hover:underline">@lang('admin::app.products.create.add-more')</button>
                    </div>

                    <!-- Description Section: Additional Info, Shipping Info -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            Description
                        </p>
                        <div class="flex flex-col gap-4">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>@lang('admin::app.products.create.additional-info')</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="textarea"
                                    name="additional_info"
                                    id="additional_info"
                                    :value="old('additional_info')"
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
                                    :value="old('shipping_info')"
                                    rows="6"
                                    :tinymce="true"
                                />
                            </x-admin::form.control-group>
                        </div>
                    </div>

                </div>

                <!-- Right column: Colors -->
                <div class="flex w-[480px] max-w-full flex-col gap-2 max-sm:w-full">
                    <!-- Colors Section -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.products.create.colors')
                        </p>
                        <div id="colors-container" class="flex flex-col gap-3">
                            <div class="flex gap-2 items-center color-row">
                                <input type="text" name="colors[0][name]" placeholder="@lang('admin::app.products.create.color-name')" class="color-name-input flex-1 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                <input type="text" name="colors[0][color_code]" value="#000000" class="color-code-input w-28 rounded border border-gray-200 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="#hex" />
                                <input type="color" class="color-picker h-9 w-12 cursor-pointer rounded border border-gray-200 dark:border-gray-700" value="#000000" title="Pick color" />
                                <button type="button" class="secondary-button remove-color" style="display:none;">&times;</button>
                            </div>
                        </div>
                        <button type="button" id="add-color" class="mt-1 text-sm font-semibold text-brandColor hover:underline">@lang('admin::app.products.create.add-more')</button>
                    </div>

                    <!-- Pricing Charts Section -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">@lang('admin::app.products.create.pricing-charts')</p>
                        <div id="pricing-charts-container" class="flex flex-col gap-4">
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

    {!! view_render_event('admin.products.create.form.after') !!}

    @pushOnce('scripts')
    <script>
        (function() {
            var productCreateColorIndex = 0;

            var currentColors = [];

            function syncColorsToImageDropdowns() {
                var colorRows = document.querySelectorAll('#colors-container .color-row');
                currentColors = [];
                colorRows.forEach(function(row, idx) {
                    var nameInput = row.querySelector('.color-name-input');
                    var codeInput = row.querySelector('.color-code-input');
                    var name = nameInput ? nameInput.value.trim() : '';
                    var code = codeInput ? codeInput.value.trim() : '#000000';
                    if (name || code !== '#000000') {
                        currentColors.push({ index: idx, name: name || code, code: code, value: 'new_' + idx });
                    }
                });

                document.querySelectorAll('.color-dropdown-wrapper').forEach(function(wrapper) {
                    var hiddenInput = wrapper.querySelector('.color-dropdown-value');
                    var currentVal = hiddenInput ? hiddenInput.value : '';
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

            function initProductCreate() {
                productCreateColorIndex = document.querySelectorAll('#colors-container .color-row').length;
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
                document.getElementById('other-images-container') && document.getElementById('other-images-container').addEventListener('change', function(e) {
                    if (!e.target.classList || !e.target.classList.contains('other-image-file')) return;
                    var preview = document.getElementById('other_images_preview');
                    if (!preview) return;
                    preview.innerHTML = '';
                    var inputs = document.querySelectorAll('#other-images-container .other-image-file');
                    inputs.forEach(function(inp) {
                        if (inp.files && inp.files[0]) {
                            var r = new FileReader();
                            r.onload = (function(fileInput) {
                                return function(e) {
                                    var img = document.createElement('img');
                                    img.src = e.target.result;
                                    img.className = 'h-16 w-16 rounded border border-gray-200 object-cover dark:border-gray-700';
                                    img.title = fileInput.files[0].name;
                                    preview.appendChild(img);
                                };
                            })(inp);
                            r.readAsDataURL(inp.files[0]);
                        }
                    });
                });
                var nameInput = document.getElementById('product_name_input');
                var slugInput = document.getElementById('product_slug_input');
                var slugStatus = document.getElementById('slug_status');
                var slugMessage = document.getElementById('slug_message');
                var slugCheckTimeout = null;

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

                    fetch('{{ route("admin.products.check_slug") }}?slug=' + encodeURIComponent(slug))
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
                        tpl.querySelector('input[name*="[name]"]').name = 'colors[' + productCreateColorIndex + '][name]';
                        tpl.querySelector('.color-code-input').value = '#000000';
                        tpl.querySelector('.color-code-input').name = 'colors[' + productCreateColorIndex + '][color_code]';
                        tpl.querySelector('.color-picker').value = '#000000';
                        tpl.querySelector('.remove-color').style.display = 'inline-block';
                        container.appendChild(tpl);
                        productCreateColorIndex++;
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
                if (t.classList && t.classList.contains('duplicate-type-btn')) {
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
                if (t.classList && t.classList.contains('add-type-btn')) {
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
                if (t.classList && t.classList.contains('remove-chart-type')) {
                    e.preventDefault();
                    var typesContainer = t.closest('.chart-types-container');
                    var typeBlocks = typesContainer.querySelectorAll('.chart-type-block');
                    if (typeBlocks.length > 1 && t.closest('.chart-type-block')) t.closest('.chart-type-block').remove();
                    return;
                }
                // Add tier to type
                if (t.classList && t.classList.contains('add-tier-btn')) {
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
                document.addEventListener('DOMContentLoaded', initProductCreate);
            } else {
                initProductCreate();
            }
        })();
    </script>
    @endPushOnce
</x-admin::layouts>
