<x-admin::layouts>
    <x-slot:title>Vendor Quotes</x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs name="vendor_quotes" />

                <div class="text-xl font-bold dark:text-white">Vendor Quotes</div>
            </div>

            @if (bouncer()->hasPermission('vendor_quotes.create'))
                <a href="{{ route('admin.vendor_quotes.create') }}" class="primary-button">
                    Create Vendor Quote
                </a>
            @endif
        </div>

        <x-admin::datagrid :src="route('admin.vendor_quotes.index')">
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>
    </div>
</x-admin::layouts>
