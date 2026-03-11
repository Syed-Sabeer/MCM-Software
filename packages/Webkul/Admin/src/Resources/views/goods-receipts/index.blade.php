<x-admin::layouts>
    <x-slot:title>Goods Receipts</x-slot>
    <div class="flex flex-col gap-4"><div class="rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900"><div class="text-xl font-bold dark:text-white">Goods Receipts</div></div><x-admin::datagrid :src="route('admin.goods_receipts.index')" /></div>
</x-admin::layouts>
