<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.products.categories.edit')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs name="products" />
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.product_categories.index') }}" class="text-brandColor hover:underline">@lang('admin::app.products.categories.title')</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-xl font-bold dark:text-white">@lang('admin::app.products.categories.edit')</span>
                </div>
            </div>
        </div>

        <x-admin::form :action="route('admin.product_categories.update', $category->id)" method="POST">
            @method('PUT')
            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label class="required">@lang('admin::app.products.categories.name')</x-admin::form.control-group.label>
                    <x-admin::form.control-group.control type="text" name="name" :value="old('name', $category->name)" />
                    <x-admin::form.control-group.error name="name" />
                </x-admin::form.control-group>
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="primary-button">Save</button>
                    <a href="{{ route('admin.product_categories.index') }}" class="secondary-button">@lang('admin::app.products.categories.back')</a>
                </div>
            </div>
        </x-admin::form>
    </div>
</x-admin::layouts>
