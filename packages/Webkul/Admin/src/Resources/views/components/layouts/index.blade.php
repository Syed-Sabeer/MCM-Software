<!DOCTYPE html>

<html
    class="{{ request()->cookie('dark_mode') ? 'dark' : '' }}"
    lang="{{ app()->getLocale() }}"
    dir="{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'rtl' : 'ltr' }}"
>

<head>

    {!! view_render_event('admin.layout.head.before') !!}

    <title>{{ $title ?? '' }}</title>

    <meta charset="UTF-8">

    <meta
        http-equiv="X-UA-Compatible"
        content="IE=edge"
    >
    <meta
        http-equiv="content-language"
        content="{{ app()->getLocale() }}"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <meta
        name="base-url"
        content="{{ url()->to('/') }}"
    >
    <meta
        name="currency"
        content="{{
            json_encode([
                'code'   => config('app.currency'),
                'symbol' => core()->currencySymbol(config('app.currency'))])
            }}
        "
    >

    @stack('meta')

    {{
        vite()->set(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])
    }}

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap"
        rel="stylesheet"
    />

    <link
        rel="preload"
        as="image"
        href="{{ url('cache/logo/bagisto.png') }}"
    >

    @if ($favicon = core()->getConfigData('general.design.admin_logo.favicon'))
        <link
            type="image/x-icon"
            href="{{ Storage::url($favicon) }}"
            rel="shortcut icon"
            sizes="16x16"
        >
    @else
        <link
            type="image/x-icon"
            href="{{ vite()->asset('images/favicon.ico') }}"
            rel="shortcut icon"
            sizes="16x16"
        />
    @endif

    @php
        $brandColor = core()->getConfigData('general.settings.menu_color.brand_color') ?? '#0E90D9';
    @endphp

    @stack('styles')

    <style>
        :root {
            --brand-color: {{ $brandColor }};
            --admin-sidebar-width: 200px;
        }

        @media (min-width: 1024px) {
            html[dir='ltr'] .admin-main-content {
                margin-left: var(--admin-sidebar-width);
            }

            html[dir='rtl'] .admin-main-content {
                margin-right: var(--admin-sidebar-width);
            }
        }

        {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
    </style>

    {!! view_render_event('admin.layout.head.after') !!}
</head>

<body class="h-full font-inter dark:bg-gray-950">
    {!! view_render_event('admin.layout.body.before') !!}

    <div
        id="app"
        class="h-full"
    >
        <!-- Flash Message Blade Component -->
        <x-admin::flash-group />

        <!-- Confirm Modal Blade Component -->
        <x-admin::modal.confirm />

        {!! view_render_event('admin.layout.content.before') !!}

        <!-- Page Header Blade Component -->
        <x-admin::layouts.header />

        <div
            class="group/container sidebar-not-collapsed flex gap-4"
            ref="appLayout"
        >
            <!-- Page Sidebar Blade Component -->
            <x-admin::layouts.sidebar.desktop />

            <div class="admin-main-content flex min-h-[calc(100vh-62px)] max-w-full flex-1 flex-col bg-gray-100 pt-3 transition-all duration-300 dark:bg-gray-950">
                <!-- Page Content Blade Component -->
                <div class="px-4 pb-6">
                    {{ $slot }}
                </div>

                <!-- Powered By -->
                <div class="mt-auto pt-6">
                    <div class="border-t bg-white py-5 text-center text-sm font-normal dark:border-gray-800 dark:bg-gray-900 dark:text-white max-md:py-3">
                        <p>{!! core()->getConfigData('general.settings.footer.label') !!}</p>
                    </div>
                </div>
            </div>
        </div>

        @if (auth()->guard('user')->check())
            <v-task-fab></v-task-fab>
        @endif

        {!! view_render_event('admin.layout.content.after') !!}
    </div>

    {!! view_render_event('admin.layout.body.after') !!}

    @if (auth()->guard('user')->check())
        <script type="text/x-template" id="v-task-fab-template">
            <div class="task-fab-wrap">
                <button
                    type="button"
                    class="task-fab-btn"
                    @click="toggle"
                    aria-label="My tasks"
                >
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M9 11.4L11.6 14L16 9.6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 4H17C18.1046 4 19 4.89543 19 6V18C19 19.1046 18.1046 20 17 20H7C5.89543 20 5 19.1046 5 18V6C5 4.89543 5.89543 4 7 4Z" stroke="currentColor" stroke-width="1.8"/>
                    </svg>

                    <span
                        v-if="badgeCount > 0"
                        class="task-fab-badge"
                    >
                        @{{ badgeCount > 99 ? '99+' : badgeCount }}
                    </span>
                </button>

                <transition name="task-fab-panel">
                    <div
                        v-if="isOpen"
                        class="task-fab-panel"
                    >
                        <div class="task-fab-panel-head">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">My Tasks</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-300">Assigned and not past due</p>
                            </div>

                            <a
                                :href="viewAllUrl"
                                class="text-xs font-medium text-blue-600 hover:underline dark:text-blue-300"
                            >
                                View all
                            </a>
                        </div>

                        <div class="task-fab-panel-body">
                            <template v-if="loading">
                                <p class="text-xs text-gray-500 dark:text-gray-300">Loading tasks...</p>
                            </template>

                            <template v-else-if="tasks.length === 0">
                                <p class="text-xs text-gray-500 dark:text-gray-300">No active tasks right now.</p>
                            </template>

                            <template v-else>
                                <div
                                    v-for="task in tasks"
                                    :key="task.id"
                                    class="task-fab-item"
                                >
                                    <a :href="taskUrl(task.id)" class="block">
                                        <p class="task-fab-item-title">@{{ task.title || 'Untitled task' }}</p>
                                        <p class="task-fab-item-date">Due: @{{ formatDate(task.schedule_from) }}</p>
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                </transition>
            </div>
        </script>

        <script type="module">
            app.component('v-task-fab', {
                template: '#v-task-fab-template',

                data() {
                    return {
                        isOpen: false,
                        loading: false,
                        tasks: [],
                        badgeCount: 0,
                        viewAllUrl: @json(route('admin.activities.my_tasks')),
                        taskEditRouteTemplate: @json(route('admin.activities.edit', ['id' => '__id__'])),
                    };
                },

                mounted() {
                    this.fetchSummary();

                    this.refreshTimer = setInterval(() => {
                        this.fetchSummary();
                    }, 60000);
                },

                beforeUnmount() {
                    if (this.refreshTimer) {
                        clearInterval(this.refreshTimer);
                    }
                },

                methods: {
                    toggle() {
                        this.isOpen = !this.isOpen;

                        if (this.isOpen && this.tasks.length === 0) {
                            this.fetchSummary();
                        }
                    },

                    async fetchSummary() {
                        this.loading = true;

                        try {
                            const response = await this.$axios.get(@json(route('admin.activities.my_tasks_summary')));
                            const data = response?.data || {};

                            this.tasks = Array.isArray(data.tasks) ? data.tasks : [];
                            this.badgeCount = Number(data.badge_count || 0);

                            if (data.view_all_url) {
                                this.viewAllUrl = data.view_all_url;
                            }
                        } catch (error) {
                            console.error(error);
                        } finally {
                            this.loading = false;
                        }
                    },

                    taskUrl(taskId) {
                        return this.taskEditRouteTemplate.replace('__id__', String(taskId));
                    },

                    formatDate(value) {
                        if (!value) {
                            return '-';
                        }

                        const date = new Date(value);

                        if (Number.isNaN(date.getTime())) {
                            return value;
                        }

                        return date.toLocaleDateString(undefined, {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit',
                        });
                    },
                },
            });
        </script>

        <style>
            .task-fab-wrap {
                position: fixed;
                right: 18px;
                bottom: 22px;
                z-index: 60;
            }

            .task-fab-btn {
                position: relative;
                display: inline-flex;
                height: 52px;
                width: 52px;
                align-items: center;
                justify-content: center;
                border-radius: 9999px;
                background: #0f172a;
                color: #fff;
                box-shadow: 0 12px 28px rgba(2, 6, 23, 0.35);
                transition: transform 0.15s ease;
            }

            .task-fab-btn:hover {
                transform: translateY(-1px);
            }

            .task-fab-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                min-width: 20px;
                height: 20px;
                border-radius: 9999px;
                background: #ef4444;
                color: #fff;
                font-size: 11px;
                font-weight: 700;
                line-height: 20px;
                text-align: center;
                padding: 0 5px;
            }

            .task-fab-panel {
                position: absolute;
                right: 0;
                bottom: 62px;
                width: min(350px, calc(100vw - 30px));
                border-radius: 14px;
                border: 1px solid #e5e7eb;
                background: #fff;
                box-shadow: 0 18px 45px rgba(15, 23, 42, 0.22);
                overflow: hidden;
            }

            .dark .task-fab-panel {
                border-color: #1f2937;
                background: #111827;
            }

            .task-fab-panel-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 10px 12px;
                border-bottom: 1px solid #f3f4f6;
            }

            .dark .task-fab-panel-head {
                border-bottom-color: #1f2937;
            }

            .task-fab-panel-body {
                max-height: 270px;
                overflow-y: auto;
                padding: 10px 12px;
                display: grid;
                gap: 8px;
            }

            .task-fab-item {
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                padding: 8px 10px;
                background: #f9fafb;
            }

            .dark .task-fab-item {
                border-color: #374151;
                background: #1f2937;
            }

            .task-fab-item-title {
                font-size: 12px;
                font-weight: 600;
                color: #111827;
            }

            .dark .task-fab-item-title {
                color: #f3f4f6;
            }

            .task-fab-item-date {
                margin-top: 3px;
                font-size: 11px;
                color: #6b7280;
            }

            .dark .task-fab-item-date {
                color: #d1d5db;
            }

            .task-fab-panel-enter-active,
            .task-fab-panel-leave-active {
                transition: all 0.16s ease;
            }

            .task-fab-panel-enter-from,
            .task-fab-panel-leave-to {
                opacity: 0;
                transform: translateY(6px);
            }
        </style>
    @endif

    @stack('scripts')

    {!! view_render_event('admin.layout.vue-app-mount.before') !!}

    <script>
        /**
         * Load event, the purpose of using the event is to mount the application
         * after all of our `Vue` components which is present in blade file have
         * been registered in the app. No matter what `app.mount()` should be
         * called in the last.
         */
        window.addEventListener("load", function(event) {
            app.mount("#app");
        });
    </script>

    {!! view_render_event('admin.layout.vue-app-mount.after') !!}
</body>

</html>
