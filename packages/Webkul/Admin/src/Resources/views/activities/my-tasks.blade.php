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
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-cyan-50 p-4 shadow-sm dark:border-blue-900/40 dark:from-gray-900 dark:to-gray-900">
                <div class="text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-300">Total Tasks</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $allTasksCount }}</div>
            </div>

            <div class="rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-teal-50 p-4 shadow-sm dark:border-emerald-900/40 dark:from-gray-900 dark:to-gray-900">
                <div class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Upcoming Open</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $upcomingTasksCount }}</div>
            </div>

            <div class="rounded-xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-4 shadow-sm dark:border-amber-900/40 dark:from-gray-900 dark:to-gray-900">
                <div class="text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-300">Overdue Open</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $overdueTasksCount }}</div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <x-admin::datagrid :src="route('admin.activities.my_tasks_data')">
                <x-admin::shimmer.datagrid />
            </x-admin::datagrid>
        </div>
    </div>
</x-admin::layouts>
