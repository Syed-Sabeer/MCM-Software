<x-admin::layouts>
    <x-slot:title>
        Proforma Invoices
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="text-xl font-bold dark:text-white">
                    Proforma Invoices
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                @if (bouncer()->hasPermission('proforma_invoices.create'))
                    <a href="{{ route('admin.proforma_invoices.create') }}" class="primary-button">
                        Create Proforma
                    </a>
                @endif
            </div>
        </div>

        <x-admin::datagrid :src="route('admin.proforma_invoices.index')" />
    </div>
</x-admin::layouts>
