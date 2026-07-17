@php($authLogo = core()->getConfigData('general.general.admin_logo.logo_image') ?: core()->getConfigData('general.design.admin_logo.logo_image'))

<a href="{{ route('admin.session.create') }}" class="inline-flex justify-center">
    @if ($authLogo)
        <img class="object-contain" style="display:block;width:auto;height:auto;max-width:190px;max-height:46px;" src="{{ asset('/storage/'.$authLogo) }}" alt="{{ config('app.name') }}">
    @else
        <img class="object-contain" style="display:block;width:auto;height:auto;max-width:190px;max-height:46px;" src="{{ request()->cookie('dark_mode') ? vite()->asset('images/dark-logo.svg') : vite()->asset('images/logo.svg') }}" alt="{{ config('app.name') }}">
    @endif
</a>
