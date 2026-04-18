<x-admin::layouts>
    <x-slot:title>
        My Tasks
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">My Tasks</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Assigned tasks with overdue and full list view.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-emerald-200 bg-white p-4 dark:border-emerald-900 dark:bg-gray-900">
                <h2 class="text-base font-semibold text-emerald-700 dark:text-emerald-300">Upcoming Tasks</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Due date not passed.</p>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $upcomingTasks->count() }}</p>
            </div>

            <div class="rounded-lg border border-amber-200 bg-white p-4 dark:border-amber-900 dark:bg-gray-900">
                <h2 class="text-base font-semibold text-amber-700 dark:text-amber-300">Overdue Tasks</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Due date already passed.</p>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $overdueTasks->count() }}</p>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Overdue Tasks</h2>

            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left dark:border-gray-800">
                            <th class="py-2 pr-3 font-medium text-gray-600 dark:text-gray-300">Task</th>
                            <th class="py-2 pr-3 font-medium text-gray-600 dark:text-gray-300">Due</th>
                            <th class="py-2 pr-3 font-medium text-gray-600 dark:text-gray-300">Status</th>
                            <th class="py-2 pr-3 font-medium text-gray-600 dark:text-gray-300">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($overdueTasks as $task)
                            <tr class="border-b border-gray-100 dark:border-gray-800/60">
                                <td class="py-2 pr-3 text-gray-900 dark:text-gray-100">{{ $task->title ?: 'Untitled task' }}</td>
                                <td class="py-2 pr-3 text-gray-600 dark:text-gray-300">{{ $task->schedule_from ? \Carbon\Carbon::parse($task->schedule_from)->format('d M Y') : '-' }}</td>
                                <td class="py-2 pr-3">
                                    <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">Overdue</span>
                                </td>
                                <td class="py-2 pr-3">
                                    <a href="{{ route('admin.activities.edit', $task->id) }}" class="text-blue-600 hover:underline dark:text-blue-300">Open</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 text-gray-500 dark:text-gray-400" colspan="4">No overdue tasks.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">All Assigned Tasks</h2>

            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left dark:border-gray-800">
                            <th class="py-2 pr-3 font-medium text-gray-600 dark:text-gray-300">Task</th>
                            <th class="py-2 pr-3 font-medium text-gray-600 dark:text-gray-300">Due</th>
                            <th class="py-2 pr-3 font-medium text-gray-600 dark:text-gray-300">Status</th>
                            <th class="py-2 pr-3 font-medium text-gray-600 dark:text-gray-300">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($allTasks as $task)
                            @php
                                $isDone = (int) $task->is_done === 1;
                                $isOverdue = ! $isDone && $task->schedule_from && \Carbon\Carbon::parse($task->schedule_from)->isPast();
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-gray-800/60">
                                <td class="py-2 pr-3 text-gray-900 dark:text-gray-100">{{ $task->title ?: 'Untitled task' }}</td>
                                <td class="py-2 pr-3 text-gray-600 dark:text-gray-300">{{ $task->schedule_from ? \Carbon\Carbon::parse($task->schedule_from)->format('d M Y') : '-' }}</td>
                                <td class="py-2 pr-3">
                                    @if ($isDone)
                                        <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Done</span>
                                    @elseif ($isOverdue)
                                        <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">Overdue</span>
                                    @else
                                        <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">Open</span>
                                    @endif
                                </td>
                                <td class="py-2 pr-3">
                                    <a href="{{ route('admin.activities.edit', $task->id) }}" class="text-blue-600 hover:underline dark:text-blue-300">Open</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 text-gray-500 dark:text-gray-400" colspan="4">No tasks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin::layouts>
