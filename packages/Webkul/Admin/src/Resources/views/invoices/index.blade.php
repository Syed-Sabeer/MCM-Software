<x-admin::layouts>
    <x-slot:title>Final Invoices</x-slot>

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Final Invoices</h1>
            <p class="mt-1 text-sm text-gray-500">Customer billing after proforma advances and subsequent payments.</p>
        </div>

        <x-admin::datagrid :src="route('admin.invoices.index')" />
    </div>
</x-admin::layouts>
