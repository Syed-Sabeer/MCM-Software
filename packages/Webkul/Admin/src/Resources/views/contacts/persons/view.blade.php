<x-admin::layouts>
    @php
        $currentRouteName = request()->route()?->getName() ?? 'admin.contacts.persons.view';
        $routePrefix = str_contains($currentRouteName, 'admin.customers.')
            ? 'customers'
            : (str_contains($currentRouteName, 'admin.vendors.')
                ? 'vendors'
                : (str_contains($currentRouteName, 'admin.employees.') ? 'employees' : 'contacts'));
        $organizationRoutePrefix = $routePrefix === 'employees' ? 'contacts' : $routePrefix;

        $activityTypes = [
            ['name' => 'all', 'label' => 'All'],
            ['name' => 'email', 'label' => 'Mail'],
            ['name' => 'call', 'label' => 'Log a Call'],
            ['name' => 'meeting', 'label' => 'Event'],
            ['name' => 'note', 'label' => 'Comment'],
            ['name' => 'file', 'label' => 'File'],
            ['name' => 'task', 'label' => 'Task'],
            ['name' => 'system', 'label' => 'Change Log'],
        ];
    @endphp

    <x-slot:title>
        @lang('admin::app.contacts.persons.view.title', ['name' => $person->name])
    </x-slot>

    <!-- Content -->
    <div class="flex gap-4 max-lg:flex-wrap">
        <!-- Left Panel -->
        {!! view_render_event('admin.contact.persons.view.left.before', ['person' => $person]) !!}

        <div class="max-lg:min-w-full max-lg:max-w-full [&>div:last-child]:border-b-0 lg:sticky lg:top-[73px] flex min-w-[394px] max-w-[394px] flex-col self-start rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <!-- Person Information -->
            <div class="flex w-full flex-col gap-2 border-b border-gray-200 p-4 dark:border-gray-800">
                <!-- Breadcrumbs -->
                <div class="flex items-center justify-between">
                    <x-admin::breadcrumbs
                        :name="$routePrefix . '.persons.view'"
                        :entity="$person"
                    />
                </div>

                {!! view_render_event('admin.contact.persons.view.tags.before', ['person' => $person]) !!}

                <!-- Tags -->
                    {{-- <x-admin::tags
                        :attach-endpoint="route('admin.' . $routePrefix . '.persons.tags.attach', $person->id)"
                        :detach-endpoint="route('admin.' . $routePrefix . '.persons.tags.detach', $person->id)"
                        :added-tags="$person->tags"
                    /> --}}

                {!! view_render_event('admin.contact.persons.view.tags.after', ['person' => $person]) !!}


                <!-- Title -->
                <div class="mb-4 flex flex-col gap-0.5">
                    {!! view_render_event('admin.contact.persons.view.title.before', ['person' => $person]) !!}

                    <h3 class="text-lg font-bold dark:text-white">
                        {{ $person->name }}
                    </h3>

                    <p class="dark:text-white">
                        {{ $person->job_title }}
                    </p>

                    {!! view_render_event('admin.contact.persons.view.title.after', ['person' => $person]) !!}
                </div>

                <!-- Activity Actions -->
                <div class="flex flex-wrap gap-2">
                    {!! view_render_event('admin.contact.persons.view.actions.before', ['person' => $person]) !!}

                    <!-- Mail Activity Action -->
                    <x-admin::activities.actions.mail
                        :entity="$person"
                        entity-control-name="person_id"
                    />

                    <!-- File Activity Action -->
                    <x-admin::activities.actions.file
                        :entity="$person"
                        entity-control-name="person_id"
                    />

                    <!-- Note Activity Action -->
                    <x-admin::activities.actions.note
                        :entity="$person"
                        entity-control-name="person_id"
                    />

                    <!-- Activity Action -->
                    <x-admin::activities.actions.activity
                        :entity="$person"
                        entity-control-name="person_id"
                    />

                    <!-- Event Activity Action -->
                    <x-admin::activities.actions.event
                        :entity="$person"
                        entity-control-name="person_id"
                    />

                    <!-- Task Activity Action -->
                    <x-admin::activities.actions.task
                        :entity="$person"
                        entity-control-name="person_id"
                    />

                    {!! view_render_event('admin.contact.persons.view.actions.after', ['person' => $person]) !!}
                </div>
            </div>

            <!-- About Person (all details from create form with inline editing) -->
            @include ('admin::contacts.persons.view.attributes')

            <!-- Contact Organization -->
            @include ('admin::contacts.persons.view.organization')
        </div>

        {!! view_render_event('admin.contact.persons.view.left.after', ['person' => $person]) !!}

        <!-- Right Panel -->
        <div class="flex w-full flex-col gap-4 rounded-lg">
            {!! view_render_event('admin.contact.persons.view.right.before', ['person' => $person]) !!}

            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Contact
                    </p>

                    <p class="text-lg font-semibold dark:text-white">
                        {{ $person->name }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        href="{{ route('admin.' . $routePrefix . '.persons.edit', $person->id) }}"
                        class="secondary-button"
                    >
                        Edit
                    </a>

                    <form
                        method="POST"
                        action="{{ route('admin.' . $routePrefix . '.persons.delete', $person->id) }}"
                        onsubmit="return confirm('Are you sure you want to delete this contact?');"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="danger-button">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex gap-4 max-xl:flex-wrap">
                <div class="min-w-[420px] flex-1">
                    <!-- Stages Navigation -->
                    <x-admin::activities
                        :endpoint="route('admin.' . $routePrefix . '.persons.activities.index', $person->id)"
                        :types="$activityTypes"
                    />
                </div>

                <div class="flex min-w-[320px] max-w-[340px] flex-col gap-4" style="width: 30% !important;">
                    @php
                        $personDisplayName = trim((string) $person->name) ?: (trim(implode(' ', array_filter([$person->first_name ?? null, $person->last_name ?? null]))) ?: 'Contact #' . $person->id);

                        $casesQuery = \Webkul\Lead\Models\Lead::query()
                            ->where('person_id', $person->id)
                            ->latest();

                        $casesCount = $casesQuery->count();
                        $recentCases = (clone $casesQuery)->take(3)->get();

                        $personFilesQuery = \Webkul\Activity\Models\Activity::query()
                            ->where('type', 'file')
                            ->where(function ($query) use ($person) {
                                $query->where(function ($query) use ($person) {
                                    $query->where('entity_type', 'persons')
                                        ->where('entity_id', $person->id);
                                })->orWhereHas('persons', function ($query) use ($person) {
                                    $query->where('persons.id', $person->id);
                                });
                            })
                            ->with('files')
                            ->latest();

                        $personFiles = (clone $personFilesQuery)
                            ->get()
                            ->flatMap(function ($activity) {
                                return $activity->files->map(function ($file) use ($activity) {
                                    $file->source_activity_title = $activity->title;

                                    return $file;
                                });
                            })
                            ->sortByDesc('created_at')
                            ->values();

                        $personFilesCount = $personFiles->count();
                        $recentPersonFiles = $personFiles->take(3);
                    @endphp

                    <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-base font-bold text-gray-900 dark:text-white">
                                Cases
                                @if ($casesCount)
                                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ $casesCount }})</span>
                                @endif
                            </p>

                            <a
                                href="{{ route('admin.leads.create', ['organization_id' => $person->organization_id, 'person_id' => $person->id]) }}"
                                class="inline-flex items-center gap-1 rounded bg-brandColor px-2.5 py-1 text-xs font-semibold text-white hover:bg-brandColor/90"
                            >
                                + New
                            </a>
                        </div>

                        @if ($casesCount)
                            <div class="flex flex-col gap-2.5 border-t border-gray-200 pt-3 dark:border-gray-700">
                                @foreach ($recentCases as $case)
                                    <a
                                        href="{{ route('admin.leads.view', $case->id) }}"
                                        class="flex items-start gap-3 rounded-md p-2 transition hover:bg-gray-50 dark:hover:bg-gray-800"
                                    >
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-brandColor hover:underline">
                                                {{ \Illuminate\Support\Str::limit($case->title, 35) }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                                {{ $personDisplayName }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            <div class="border-t border-gray-200 pt-3 text-center dark:border-gray-700">
                                <a
                                    href="{{ route('admin.leads.index', ['person_id' => $person->id]) }}"
                                    class="text-xs font-semibold text-brandColor hover:underline"
                                >
                                    View All Cases
                                </a>
                            </div>
                        @else
                            <p class="rounded-md bg-gray-50 p-3 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                No cases yet. Create a case for this contact.
                            </p>
                        @endif
                    </div>

                    <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-base font-bold text-gray-900 dark:text-white">
                                Files
                                @if ($personFilesCount)
                                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ $personFilesCount }})</span>
                                @endif
                            </p>
                        </div>

                        @if ($personFilesCount)
                            <div class="grid gap-3 border-t border-gray-200 pt-3 dark:border-gray-700">
                                @foreach ($recentPersonFiles as $file)
                                    @php
                                        $previewUrl = route('admin.activities.file_preview', $file->id);
                                        $downloadUrl = route('admin.activities.file_download', $file->id);
                                        $fileName = trim((string) ($file->name ?? '')) ?: (trim((string) ($file->source_activity_title ?? '')) ?: basename((string) $file->path));
                                        $storedExtension = pathinfo((string) $file->path, PATHINFO_EXTENSION);
                                        if ($storedExtension && ! str_contains(basename($fileName), '.')) {
                                            $fileName .= '.' . $storedExtension;
                                        }
                                        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                        $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp

                                    <div class="flex items-center gap-2 rounded-md border border-gray-200 p-2 dark:border-gray-700">
                                        <a href="{{ $isImage ? $previewUrl : $downloadUrl }}" target="{{ $isImage ? '_blank' : '_self' }}" class="flex h-12 w-12 flex-shrink-0 items-center justify-center overflow-hidden rounded-md bg-gray-100 dark:bg-gray-800">
                                            @if ($isImage)
                                                <img src="{{ $previewUrl }}" alt="{{ $fileName }}" class="h-full w-full object-cover transition hover:opacity-75" />
                                            @else
                                                <i class="icon-document text-2xl text-gray-400"></i>
                                            @endif
                                        </a>

                                        <div class="flex min-w-0 flex-1 items-start justify-between gap-2">
                                            <div class="min-w-0 flex-1">
                                                <a href="{{ $downloadUrl }}" class="block truncate text-xs font-medium text-brandColor hover:underline" title="{{ $fileName }}">
                                                    {{ $fileName }}
                                                </a>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ core()->formatDate($file->created_at) }}
                                                </p>
                                            </div>

                                            <a href="{{ $downloadUrl }}" class="flex-shrink-0 text-gray-400 hover:text-brandColor dark:text-gray-500" title="Download {{ $fileName }}">
                                                <i class="icon-download text-lg"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border-t border-gray-200 pt-3 text-center dark:border-gray-700">
                                <a
                                    href="{{ route('admin.activities.index', ['entity_type' => 'persons', 'entity_id' => $person->id, 'type' => 'file']) }}"
                                    class="text-xs font-semibold text-brandColor hover:underline"
                                >
                                    View All Files
                                </a>
                            </div>
                        @else
                            <p class="rounded-md bg-gray-50 p-3 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                No files yet. Upload files from this contact's activity panel.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {!! view_render_event('admin.contact.persons.view.right.after', ['person' => $person]) !!}
        </div>
    </div>
</x-admin::layouts>
