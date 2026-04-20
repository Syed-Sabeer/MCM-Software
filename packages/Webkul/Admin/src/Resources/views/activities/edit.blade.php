<x-admin::layouts>
    @php
        $activityType = $activity->type ?? 'meeting';

        $activityLabel = match ($activityType) {
            'task' => 'Task',
            'call' => 'Log a Call',
            default => 'Event',
        };

        $componentName = match ($activityType) {
            'task' => 'task',
            'call' => 'activity',
            default => 'event',
        };

        $openEventName = match ($activityType) {
            'task' => 'open-task-activity',
            'call' => 'open-log-call-activity',
            default => 'open-event-activity',
        };

        $activityPayload = $activity->toArray();
    @endphp

    <x-slot:title>
        Edit {{ $activityLabel }}
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs
                    name="activities.edit"
                    :entity="$activity"
                />

                <div class="text-xl font-bold text-gray-900 dark:text-white">
                    Edit {{ $activityLabel }}
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-300">
                    The correct editor for this activity type opens automatically.
                </p>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <p class="text-base font-semibold text-gray-900 dark:text-white">
                        Opening {{ $activityLabel }} editor
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Type: {{ $activityType }}
                    </p>
                </div>
            </div>

            <div class="hidden">
                @if ($componentName === 'task')
                    <x-admin::activities.actions.task :entity="$activityEntity" entity-control-name="organization_id" />
                @elseif ($componentName === 'activity')
                    <x-admin::activities.actions.activity :entity="$activityEntity" entity-control-name="organization_id" />
                @else
                    <x-admin::activities.actions.event :entity="$activityEntity" entity-control-name="organization_id" />
                @endif
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            window.addEventListener('load', function () {
                window.dispatchEvent(new CustomEvent('{{ $openEventName }}', {
                    detail: {
                        activity: @json($activityPayload),
                    },
                }));
            });
        </script>
    @endPushOnce
</x-admin::layouts>
