<x-admin::layouts>
    @php
        $currentRouteName = request()->route()?->getName() ?? 'admin.contacts.organizations.index';
        $routePrefix = str_contains($currentRouteName, 'admin.customers.')
            ? 'customers'
            : (str_contains($currentRouteName, 'admin.vendors.') ? 'vendors' : 'contacts');
        $organizationLabel = $routePrefix === 'vendors' ? 'Vendor' : 'Company';
        $organizationPluralLabel = $routePrefix === 'vendors' ? 'Vendors' : 'Companies';
    @endphp

    <!-- Page Title -->
    <x-slot:title>
        {{ $organizationPluralLabel }}
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                {!! view_render_event('admin.organizations.index.breadcrumbs.before') !!}

                <!-- Breadcrumbs -->
                <x-admin::breadcrumbs :name="$routePrefix . '.organizations'" />

                {!! view_render_event('admin.organizations.index.breadcrumbs.before') !!}
                
                <div class="text-xl font-bold dark:text-gray-300">
                    {{ $organizationPluralLabel }}
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('admin.organizations.index.create_button.before') !!}

                    @if (bouncer()->hasPermission('contacts.organizations.create'))
                        <!-- Create button for person -->
                        <a
                            href="{{ route('admin.' . $routePrefix . '.organizations.create') }}"
                            class="primary-button"
                        >
                            Create {{ $organizationLabel }}
                        </a>
                    @endif

                    {!! view_render_event('admin.organizations.index.create_button.after') !!}
                </div>
            </div>
        </div>

        {!! view_render_event('admin.organizations.datagrid.index.before') !!}

        <x-admin::datagrid :src="route('admin.' . $routePrefix . '.organizations.index')" >
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>

        {!! view_render_event('admin.organizations.datagrid.index.after') !!}
    </div>
</x-admin::layouts>
