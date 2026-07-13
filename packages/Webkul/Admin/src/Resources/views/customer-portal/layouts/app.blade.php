@php
    $organization = $organization ?? ($portalOrganization ?? null);
    $brandColor = core()->getConfigData('general.settings.menu_color.brand_color') ?? '#0E90D9';

    $navItems = [
        ['label' => 'Dashboard', 'route' => 'customer_portal.dashboard', 'icon' => 'icon-dashboard'],
        ['label' => 'Quotes', 'route' => 'customer_portal.quotes.index', 'icon' => 'icon-quote'],
        ['label' => 'Proformas', 'route' => 'customer_portal.proformas.index', 'icon' => 'icon-sales'],
        ['label' => 'Job Orders', 'route' => 'customer_portal.job_orders.index', 'icon' => 'icon-activity'],
        ['label' => 'Products', 'route' => 'customer_portal.products.index', 'icon' => 'icon-product'],
    ];
@endphp

<!DOCTYPE html>

<html
    class="{{ request()->cookie('dark_mode') ? 'dark' : '' }}"
    lang="{{ app()->getLocale() }}"
    dir="{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'rtl' : 'ltr' }}"
>
<head>
    {!! view_render_event('admin.layout.head.before') !!}

    <title>{{ $title ?? 'Customer Portal' }}</title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="content-language" content="{{ app()->getLocale() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="base-url" content="{{ url()->to('/') }}">

    {{ vite()->set(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js']) }}

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet" />

    @if ($favicon = core()->getConfigData('general.design.admin_logo.favicon'))
        <link type="image/x-icon" href="{{ Storage::url($favicon) }}" rel="shortcut icon" sizes="16x16">
    @else
        <link type="image/x-icon" href="{{ vite()->asset('images/favicon.ico') }}" rel="shortcut icon" sizes="16x16" />
    @endif

    @stack('meta')
    @stack('styles')

    <style>
        :root {
            --brand-color: {{ $brandColor }};
            --admin-sidebar-width: 200px;
        }

        @media (min-width: 1024px) {
            html[dir='ltr'] .admin-main-content { margin-left: var(--admin-sidebar-width); }
            html[dir='rtl'] .admin-main-content { margin-right: var(--admin-sidebar-width); }
        }

        {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
    </style>

    {!! view_render_event('admin.layout.head.after') !!}
</head>

<body class="h-full font-inter dark:bg-gray-950">
    {!! view_render_event('admin.layout.body.before') !!}

    <div id="app" class="h-full">
        <x-admin::flash-group />
        <x-admin::modal.confirm />

        <header class="sticky top-0 z-[10001] flex items-center justify-between gap-1 border-b border-gray-200 bg-white px-4 py-2.5 transition-all dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-1.5">
                <button
                    type="button"
                    class="inline-flex rounded-md p-1.5 text-2xl hover:bg-gray-100 dark:hover:bg-gray-950 lg:hidden"
                    @click="$refs.portalMobileSidebar.classList.toggle('hidden')"
                    aria-label="Toggle menu"
                >
                    <span class="icon-menu"></span>
                </button>

                <a href="{{ route('customer_portal.dashboard') }}">
                    @if ($logo = core()->getConfigData('general.general.admin_logo.logo_image'))
                        <img class="h-10" src="{{ asset('/storage/'.$logo) }}" alt="{{ config('app.name') }}" />
                    @else
                        <img
                            class="h-10 max-sm:hidden"
                            src="{{ request()->cookie('dark_mode') ? vite()->asset('images/dark-logo.svg') : vite()->asset('images/logo.svg') }}"
                            id="logo-image"
                            alt="{{ config('app.name') }}"
                        />

                        <img
                            class="h-10 sm:hidden"
                            src="{{ request()->cookie('dark_mode') ? vite()->asset('images/mobile-dark-logo.svg') : vite()->asset('images/mobile-light-logo.svg') }}"
                            id="mobile-logo-image"
                            alt="{{ config('app.name') }}"
                        />
                    @endif
                </a>
            </div>

            <div class="flex items-center gap-2.5">
                <v-dark>
                    <div class="flex">
                        <span class="{{ request()->cookie('dark_mode') ? 'icon-light' : 'icon-dark' }} cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"></span>
                    </div>
                </v-dark>

                <x-admin::dropdown position="bottom-{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'left' : 'right' }}">
                    <x-slot:toggle>
                        @php($user = auth()->guard('user')->user())

                        @if ($user?->image)
                            <button class="flex h-9 w-9 cursor-pointer overflow-hidden rounded-full hover:opacity-80 focus:opacity-80">
                                <img src="{{ $user->image_url }}" class="h-full w-full object-cover" />
                            </button>
                        @else
                            <button class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-pink-400 font-semibold leading-6 text-white">
                                {{ substr($user?->name ?? 'C', 0, 1) }}
                            </button>
                        @endif
                    </x-slot>

                    <x-slot:content class="mt-2 border-t-0 !p-0">
                        <div class="grid gap-1 py-2.5">
                            <div class="px-5 py-2 text-sm text-gray-500 dark:text-gray-300">
                                {{ $organization?->name ?: 'Customer Portal' }}
                            </div>

                            <form method="POST" action="{{ route('customer_portal.logout') }}" id="customerPortalLogout">
                                @csrf
                                @method('DELETE')
                            </form>

                            <a
                                class="cursor-pointer px-5 py-2 text-base text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-950"
                                href="{{ route('customer_portal.logout') }}"
                                onclick="event.preventDefault(); document.getElementById('customerPortalLogout').submit();"
                            >
                                @lang('admin::app.layouts.sign-out')
                            </a>
                        </div>
                    </x-slot>
                </x-admin::dropdown>
            </div>
        </header>

        <div class="group/container sidebar-not-collapsed flex gap-4" ref="appLayout">
            <div
                class="duration-80 fixed top-[60px] z-[10002] h-full w-[200px] border-gray-200 bg-white pt-4 transition-all dark:border-gray-800 dark:bg-gray-900 max-lg:hidden ltr:border-r rtl:border-l"
            >
                <div class="journal-scroll h-[calc(100vh-100px)] overflow-hidden">
                    <nav class="sidebar-rounded grid w-full gap-2">
                        @foreach ($navItems as $item)
                            @php($isActive = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])))

                            <div class="px-4 group/item {{ $isActive ? 'active' : 'inactive' }}">
                                <a
                                    class="flex cursor-pointer items-center gap-2 rounded-lg p-1.5 transition-all {{ $isActive ? 'bg-brandColor' : 'hover:bg-gray-100 hover:dark:bg-gray-950' }} peer"
                                    href="{{ route($item['route']) }}"
                                >
                                    <span class="{{ $item['icon'] }} text-2xl {{ $isActive ? 'text-white' : '' }}"></span>

                                    <div class="flex flex-1 items-center justify-between whitespace-nowrap font-medium {{ $isActive ? 'text-white' : 'text-gray-600 dark:text-gray-300' }}">
                                        <p>{{ $item['label'] }}</p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </nav>
                </div>
            </div>

            <div
                class="fixed inset-0 top-[60px] z-[10002] hidden bg-black/40 lg:hidden"
                ref="portalMobileSidebar"
                @click.self="$refs.portalMobileSidebar.classList.add('hidden')"
            >
                <div class="h-full w-[260px] border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-gray-900 ltr:border-r rtl:border-l">
                    <nav class="grid w-full gap-2">
                        @foreach ($navItems as $item)
                            @php($isActive = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])))

                            <div class="px-4">
                                <a class="flex items-center gap-2 rounded-lg p-2 {{ $isActive ? 'bg-brandColor text-white' : 'text-gray-600 dark:text-gray-300' }}" href="{{ route($item['route']) }}">
                                    <span class="{{ $item['icon'] }} text-2xl"></span>
                                    <span class="font-medium">{{ $item['label'] }}</span>
                                </a>
                            </div>
                        @endforeach
                    </nav>
                </div>
            </div>

            <div class="admin-main-content flex min-h-[calc(100vh-62px)] max-w-full flex-1 flex-col bg-gray-100 pt-3 transition-all duration-300 dark:bg-gray-950">
                <div class="px-4 pb-6">
                    <div class="mb-5 flex items-center justify-between gap-4 max-sm:flex-wrap">
                        <div class="grid gap-1.5">
                            <p class="text-2xl font-semibold dark:text-white">
                                {{ $title ?? 'Customer Portal' }}
                            </p>

                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $subtitle ?? ($organization ? 'Customer view for '.$organization->name : 'Customer portal') }}
                            </p>
                        </div>
                    </div>

                    @yield('content')
                </div>

                <div class="mt-auto pt-6">
                    <div class="border-t bg-white py-5 text-center text-sm font-normal dark:border-gray-800 dark:bg-gray-900 dark:text-white max-md:py-3">
                        <p>{!! core()->getConfigData('general.settings.footer.label') !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/x-template" id="v-dark-template">
        <div class="flex">
            <span
                class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                :class="[isDarkMode ? 'icon-light' : 'icon-dark']"
                @click="toggle"
            ></span>
        </div>
    </script>

    <script type="module">
        app.component('v-dark', {
            template: '#v-dark-template',

            data() {
                return {
                    isDarkMode: {{ request()->cookie('dark_mode') ?? 0 }},
                    logo: "{{ vite()->asset('images/logo.svg') }}",
                    dark_logo: "{{ vite()->asset('images/dark-logo.svg') }}",
                };
            },

            methods: {
                toggle() {
                    this.isDarkMode = parseInt(this.isDarkModeCookie()) ? 0 : 1;

                    var expiryDate = new Date();
                    expiryDate.setMonth(expiryDate.getMonth() + 1);

                    document.cookie = 'dark_mode=' + this.isDarkMode + '; path=/; expires=' + expiryDate.toGMTString();
                    document.documentElement.classList.toggle('dark', this.isDarkMode === 1);

                    const logo = document.getElementById('logo-image');

                    if (logo) {
                        logo.src = this.isDarkMode ? this.dark_logo : this.logo;
                    }
                },

                isDarkModeCookie() {
                    const cookies = document.cookie.split(';');

                    for (const cookie of cookies) {
                        const [name, value] = cookie.trim().split('=');

                        if (name === 'dark_mode') {
                            return value;
                        }
                    }

                    return 0;
                },
            },
        });
    </script>

    @stack('scripts')

    {!! view_render_event('admin.layout.vue-app-mount.before') !!}

    <script>
        window.addEventListener('load', function () {
            app.mount('#app');
        });
    </script>

    {!! view_render_event('admin.layout.vue-app-mount.after') !!}

    {!! view_render_event('admin.layout.body.after') !!}
</body>
</html>
