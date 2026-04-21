<x-admin::layouts>
    <x-slot:title>
        Tasks
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs name="activities" />

                    <div class="text-xl font-bold text-gray-900 dark:text-white">
                        Tasks
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Your assigned tasks in a searchable, paginated table.
                    </p>
                </div>

                <a
                    href="{{ route('admin.activities.calendar') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:border-gray-600 dark:hover:bg-gray-800"
                >
                    <span class="icon-calendar text-lg"></span>
                    Events
                </a>

                <button
                    type="button"
                    class="primary-button"
                    onclick="window.dispatchEvent(new Event('open-task-activity'))"
                >
                    <span class="icon-add text-lg"></span>
                    New Task
                </button>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="group relative overflow-hidden rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition-all hover:-translate-y-0.5 hover:border-brandColor/40 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                <div class="absolute inset-x-0 top-0 h-1 bg-brandColor"></div>

                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Tasks</div>
                        <div class="mt-2 text-3xl font-bold leading-none text-gray-900 dark:text-white">{{ $allTasksCount }}</div>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brandColor/10 text-brandColor dark:bg-brandColor/15">
                        <span class="icon-calendar text-xl"></span>
                    </div>
                </div>

                <div class="mt-4 h-px w-full bg-gray-100 dark:bg-gray-800"></div>
                <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">All assigned tasks in one place.</div>
            </div>

            <div class="group relative overflow-hidden rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition-all hover:-translate-y-0.5 hover:border-emerald-400/40 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                <div class="absolute inset-x-0 top-0 h-1 bg-emerald-500"></div>

                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Upcoming Open</div>
                        <div class="mt-2 text-3xl font-bold leading-none text-gray-900 dark:text-white">{{ $upcomingTasksCount }}</div>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                        <span class="icon-check text-xl"></span>
                    </div>
                </div>

                <div class="mt-4 h-px w-full bg-gray-100 dark:bg-gray-800"></div>
                <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">Tasks due today or later.</div>
            </div>

            <div class="group relative overflow-hidden rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition-all hover:-translate-y-0.5 hover:border-amber-400/40 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                <div class="absolute inset-x-0 top-0 h-1 bg-amber-500"></div>

                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Overdue Open</div>
                        <div class="mt-2 text-3xl font-bold leading-none text-gray-900 dark:text-white">{{ $overdueTasksCount }}</div>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">
                        <span class="icon-warning text-xl"></span>
                    </div>
                </div>

                <div class="mt-4 h-px w-full bg-gray-100 dark:bg-gray-800"></div>
                <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">Tasks that need attention now.</div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <x-admin::datagrid :src="route('admin.activities.my_tasks_data')">
                <x-admin::shimmer.datagrid />
            </x-admin::datagrid>
        </div>

        <div class="hidden">
            <x-admin::activities.actions.task :entity="(object) []" entity-control-name="" />
        </div>
    </div>
</x-admin::layouts>
