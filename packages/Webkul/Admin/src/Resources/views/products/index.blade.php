<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.products.index.title')
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <!-- Breadcrumbs -->
                <x-admin::breadcrumbs name="products" />

                <div class="text-xl font-bold dark:text-white">
                    @lang('admin::app.products.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('admin.products.index.create_button.before') !!}

                <!-- Create button for Product -->
                @if (bouncer()->hasPermission('products.create'))
                    <div class="flex items-center gap-x-2.5">
                        <a
                            href="{{ route('admin.products.create') }}"
                            class="primary-button"
                        >
                            @lang('admin::app.products.index.create-btn')
                        </a>
                    </div>
                @endif

                {!! view_render_event('admin.products.index.create_button.after') !!}
            </div>
        </div>

        {!! view_render_event('admin.products.index.datagrid.before') !!}

        <x-admin::datagrid :src="route('admin.products.index')">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>

        {!! view_render_event('admin.products.index.datagrid.after') !!}

    @pushOnce('scripts')
    <script>
        document.body.addEventListener('change', function(e) {
            if (!e.target.classList || !e.target.classList.contains('product-publish-toggle')) return;
            var input = e.target;
            var url = input.getAttribute('data-url');
            var token = input.getAttribute('data-csrf');
            if (!url) return;
            input.disabled = true;
            fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ _token: token })
            }).then(function(r) { return r.json(); }).then(function(data) {
                input.disabled = false;
                if (typeof window.$emitter !== 'undefined') {
                    window.$emitter.emit('add-flash', { type: 'success', message: data.message || 'Updated.' });
                }
                if (data.publish_on_website !== undefined) input.checked = !!data.publish_on_website;
            }).catch(function() {
                input.disabled = false;
                input.checked = !input.checked;
                if (typeof window.$emitter !== 'undefined') {
                    window.$emitter.emit('add-flash', { type: 'error', message: 'Failed to update.' });
                }
            });
        });
    </script>
    @endPushOnce
    </div>
</x-admin::layouts>
