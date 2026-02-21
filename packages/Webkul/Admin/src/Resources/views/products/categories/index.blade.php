<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.products.categories.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs name="products" />
                <div class="text-xl font-bold dark:text-white">
                    @lang('admin::app.products.categories.title')
                </div>
            </div>
            <div class="flex items-center gap-x-2.5">
                <a href="{{ route('admin.product_categories.create') }}" class="primary-button">
                    @lang('admin::app.products.categories.add')
                </a>
                <a href="{{ route('admin.products.create') }}" class="secondary-button">
                    @lang('admin::app.products.index.create-btn')
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        <div class="box-shadow rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[300px] table-auto border-collapse text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-800 dark:text-white">@lang('admin::app.products.categories.name')</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-300">{{ $category->name }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.product_categories.edit', $category->id) }}" class="text-brandColor hover:underline">@lang('admin::app.products.categories.edit')</a>
                                    <form action="{{ route('admin.product_categories.destroy', $category->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline dark:text-red-400">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No categories yet. <a href="{{ route('admin.product_categories.create') }}" class="text-brandColor hover:underline">@lang('admin::app.products.categories.add')</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin::layouts>
