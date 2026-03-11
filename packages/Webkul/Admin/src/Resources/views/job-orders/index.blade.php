<x-admin::layouts>
    <x-slot:title>Job Orders</x-slot>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
            <div>
                <div class="text-xl font-bold dark:text-white">Job Orders</div>
                <div class="text-sm text-gray-500">Factory execution orders created from proforma invoices.</div>
            </div>
        </div>
        <x-admin::datagrid :src="route('admin.job_orders.index')" />
    </div>
</x-admin::layouts>
