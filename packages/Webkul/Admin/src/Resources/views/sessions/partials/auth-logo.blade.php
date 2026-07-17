@php($authLogo = core()->getConfigData('general.general.admin_logo.logo_image') ?: core()->getConfigData('general.design.admin_logo.logo_image'))

<a href="{{ route('admin.session.create') }}" class="inline-flex justify-center">
    @if ($authLogo)
        <img class="max-h-12 max-w-[190px] object-contain" src="{{ asset('/storage/'.$authLogo) }}" alt="{{ config('app.name') }}">
    @else
        <img class="h-11 max-w-[190px]" src="{{ request()->cookie('dark_mode') ? vite()->asset('images/dark-logo.svg') : vite()->asset('images/logo.svg') }}" alt="{{ config('app.name') }}">
    @endif
</a>
