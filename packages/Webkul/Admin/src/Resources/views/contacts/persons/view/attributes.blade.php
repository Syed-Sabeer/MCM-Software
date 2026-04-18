{!! view_render_event('admin.contacts.persons.view.attributes.before', ['person' => $person]) !!}

@php
    $readValue = function ($value) {
        if (is_array($value)) {
            return trim((string) ($value[0]['value'] ?? ''));
        }

        return trim((string) $value);
    };

    $typeLabels = [
        'customer' => __('admin::app.contacts.persons.view.types.customer'),
        'vendor'   => __('admin::app.contacts.persons.view.types.vendor'),
        'employee' => __('admin::app.contacts.persons.view.types.employee'),
        'partner'  => __('admin::app.contacts.persons.view.types.partner'),
        'other'    => __('admin::app.contacts.persons.view.types.other'),
    ];

    $typeColors = [
        'customer' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'vendor'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'employee' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'partner'  => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        'other'    => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    ];

    $currentType = strtolower((string) ($person->type ?? 'customer'));

    $ownerName = trim((string) optional($person->user)->name);
    $primaryPhone = $readValue($person->phone);
    $primaryEmail = $readValue($person->email);
    $cellPhone = $readValue($person->cell_phone);
    $directPhone = $readValue($person->direct_phone);
    $secondaryEmail = $readValue($person->email_secondary);

    $mailingAddress = collect([
        $person->mailing_street,
        collect([
            $person->mailing_city,
            $person->mailing_state,
            $person->mailing_postcode,
            $person->mailing_country,
        ])->filter(fn ($value) => filled(trim((string) $value)))->implode(', '),
    ])->filter(fn ($value) => filled(trim((string) $value)))->values();
@endphp

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 text-sm dark:border-gray-800">
    <div class="flex flex-col gap-2">
        <p class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
            About
        </p>

        <div class="flex flex-col gap-3 dark:text-white">
            @if ($person->salutation)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Salutation</p>
                    <p>{{ $person->salutation }}</p>
                </div>
            @endif

            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Type</p>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeColors[$currentType] ?? $typeColors['other'] }}">
                    {{ $typeLabels[$currentType] ?? ucfirst($currentType) }}
                </span>
            </div>

            @if ($person->first_name || $person->last_name)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Full Name</p>
                    <p>{{ trim(($person->first_name ?? '') . ' ' . ($person->last_name ?? '')) }}</p>
                </div>
            @endif

            @if ($person->title)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Title</p>
                    <p>{{ $person->title }}</p>
                </div>
            @endif

            @if ($person->job_title)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Job Title</p>
                    <p>{{ $person->job_title }}</p>
                </div>
            @endif

            @if ($ownerName)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Sales Owner</p>
                    <p>{{ $ownerName }}</p>
                </div>
            @endif

            @if ($currentType === 'employee' && optional($person->user)->role)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Role</p>
                    <p>{{ $person->user->role->name }}</p>
                </div>
            @endif

            @if ($person->birth_date)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Birth Date</p>
                    <p>{{ $person->birth_date->format('d M Y') }}</p>
                </div>
            @endif

            @if ($person->description)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Description</p>
                    <p class="whitespace-pre-line break-words">{{ $person->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="border-t border-gray-200 pt-4 dark:border-gray-800">
        <p class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
            Get In Touch
        </p>

        <div class="mt-2 flex flex-col gap-3 dark:text-white">
            @if ($primaryPhone)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Phone</p>
                    <p>{{ $primaryPhone }}</p>
                </div>
            @endif

            @if ($cellPhone)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Cell Phone</p>
                    <p>{{ $cellPhone }}</p>
                </div>
            @endif

            @if ($directPhone)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Direct Phone</p>
                    <p>{{ $directPhone }}</p>
                </div>
            @endif

            @if ($primaryEmail)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Email</p>
                    <p class="break-all">{{ $primaryEmail }}</p>
                </div>
            @endif

            @if ($secondaryEmail)
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Secondary Email</p>
                    <p class="break-all">{{ $secondaryEmail }}</p>
                </div>
            @endif

            @if (! $primaryPhone && ! $cellPhone && ! $directPhone && ! $primaryEmail && ! $secondaryEmail)
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    No contact details added yet.
                </p>
            @endif
        </div>
    </div>

    <div class="border-t border-gray-200 pt-4 dark:border-gray-800">
        <p class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
            Mailing Address
        </p>

        <div class="mt-2 flex flex-col gap-1 dark:text-white" style="max-width: 320px; word-wrap: break-word; word-break: break-word;">
            @forelse ($mailingAddress as $line)
                <span>{{ $line }}</span>
            @empty
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    No mailing address added yet.
                </p>
            @endforelse
        </div>
    </div>
</div>

{!! view_render_event('admin.contacts.persons.view.attributes.after', ['person' => $person]) !!}
