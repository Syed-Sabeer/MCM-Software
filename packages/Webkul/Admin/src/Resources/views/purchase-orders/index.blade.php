<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.purchase-orders.index.title')
    </x-slot>

    <v-purchase-order>
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs name="purchase_orders" />
        
                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.purchase-orders.index.title')
                    </div>
                </div>
        
                <div class="flex items-center gap-x-2.5">
                    <div class="flex items-center gap-x-2.5">
                        @if (bouncer()->hasPermission('purchase_orders.create'))
                            <a 
                                href="{{ route('admin.purchase_orders.create') }}"
                                class="primary-button"
                            >
                                @lang('admin::app.purchase-orders.index.create-btn')
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        
            <x-admin::shimmer.datagrid />
        </div>
    </v-purchase-order>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-purchase-order-template"
        >
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                    <div class="flex flex-col gap-2">
                        <x-admin::breadcrumbs name="purchase_orders" />
        
                        <div class="text-xl font-bold dark:text-white">
                            @lang('admin::app.purchase-orders.index.title')
                        </div>
                    </div>

                    <div class="flex items-center gap-x-2.5">
                        <div class="flex items-center gap-x-2.5">
                            {!! view_render_event('admin.purchase_orders.index.create_button.before') !!}
                            
                            @if (bouncer()->hasPermission('purchase_orders.create'))
                                <a 
                                    href="{{ route('admin.purchase_orders.create') }}"
                                    class="primary-button"
                                >
                                    @lang('admin::app.purchase-orders.index.create-btn')
                                </a>
                            @endif
            
                            {!! view_render_event('admin.purchase_orders.index.create_button.after') !!}
                        </div>
                    </div>
                </div>
            
                {!! view_render_event('admin.purchase_orders.index.datagrid.before') !!}
            
                <x-admin::datagrid :src="route('admin.purchase_orders.index')" />

                {!! view_render_event('admin.purchase_orders.index.datagrid.after') !!}
            </div>
        </script>

        <script type="module">
            app.component('v-purchase-order', {
                template: '#v-purchase-order-template',
            });
        </script>
    @endPushOnce
</x-admin::layouts>
