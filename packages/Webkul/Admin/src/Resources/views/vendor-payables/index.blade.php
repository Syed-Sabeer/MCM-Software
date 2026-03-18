<x-admin::layouts>
    <x-slot:title>Vendor Payables</x-slot>

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
            <div class="text-xl font-bold dark:text-white">Vendor Payables</div>
        </div>

        <x-admin::datagrid :src="route('admin.vendor_payables.index')" />
    </div>
</x-admin::layouts>
