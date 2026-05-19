{!! view_render_event('admin.leads.index.view_switcher.before') !!}

<div class="flex items-center gap-4 max-md:w-full max-md:!justify-between">
    <a
        href="{{ route('admin.settings.pipelines.create') }}"
        class="secondary-button border border-brandColor text-brandColor bg-transparent hover:bg-brandColor/10 whitespace-nowrap"
    >
        Add New Stage
    </a>

    <div class="flex items-center gap-0.5">
        {!! view_render_event('admin.leads.index.view_switcher.pipeline.view_type.before') !!}

        @if (request('view_type'))
            <a
                class="flex"
                href="{{ route('admin.leads.index') }}"
            >
                <span class="icon-kanban p-2 text-2xl"></span>
            </a>

            <span class="icon-list rounded-md bg-gray-100 p-2 text-2xl dark:bg-gray-950"></span>
        @else
            <span class="icon-kanban rounded-md bg-white p-2 text-2xl dark:bg-gray-900"></span>

            <a
                href="{{ route('admin.leads.index', ['view_type' => 'table']) }}"
                class="flex"
            >
                <span class="icon-list p-2 text-2xl"></span>
            </a>
        @endif

        {!! view_render_event('admin.leads.index.view_switcher.pipeline.view_type.after') !!}
    </div>
</div>

{!! view_render_event('admin.leads.index.view_switcher.after') !!}