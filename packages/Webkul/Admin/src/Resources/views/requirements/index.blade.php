<x-admin::layouts>
    <x-slot:title>Requirement Sheets</x-slot>
    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
            <div class="text-xl font-bold dark:text-white">Requirement Sheets</div>
            <div class="text-sm text-gray-500">Generated from product BOM x job order quantity.</div>
        </div>
        <x-admin::datagrid :src="route('admin.requirements.index', request()->only('job_order_id'))" />
    </div>
</x-admin::layouts>
