@if (bouncer()->hasPermission('configuration'))
    <a
        href="{{ route('admin.configuration.index') }}"
        class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-brandColor text-white"
        title="@lang('admin::app.layouts.configuration')"
        aria-label="@lang('admin::app.layouts.configuration')"
    >
        <i class="icon-configuration text-2xl"></i>
    </a>
@endif
