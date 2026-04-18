{!! view_render_event('admin.contacts.persons.view.organization.before', ['person' => $person]) !!}

@if ($person?->organization)
    @php
        $organization = $person->organization;

        $organizationAddress = collect([
            $organization->billing_street,
            collect([
                $organization->billing_city,
                $organization->billing_state,
                $organization->billing_postcode,
                $organization->billing_country,
            ])->filter(fn ($value) => filled(trim((string) $value)))->implode(', '),
        ])->filter(fn ($value) => filled(trim((string) $value)))->values();
    @endphp

    <div class="flex w-full flex-col gap-3 p-4 text-sm dark:text-white">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    Company
                </p>

                <a
                    href="{{ route('admin.' . $organizationRoutePrefix . '.organizations.view', $organization->id) }}"
                    class="mt-1 inline-block text-base font-semibold text-brandColor hover:underline"
                >
                    {{ $organization->name }}
                </a>
            </div>

            <a
                href="{{ route('admin.' . $organizationRoutePrefix . '.organizations.edit', $organization->id) }}"
                class="secondary-button !px-3 !py-1.5 !text-xs"
                target="_blank"
            >
                Edit
            </a>
        </div>

        <div class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 dark:border-gray-700">
            <x-admin::avatar :name="$organization->name" class="h-10 w-10 flex-shrink-0" />

            <div class="min-w-0 flex-1">
                @if ($organization->phone)
                    <div class="mb-2">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Phone</p>
                        <p>{{ $organization->phone }}</p>
                    </div>
                @endif

                @if ($organization->website)
                    <div class="mb-2">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Website</p>
                        <a
                            href="{{ $organization->website }}"
                            target="_blank"
                            class="break-all text-brandColor hover:underline"
                        >
                            {{ $organization->website }}
                        </a>
                    </div>
                @endif

                @if ($organizationAddress->isNotEmpty())
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Billing Address</p>

                        <div class="mt-1 flex flex-col gap-0.5" style="word-wrap: break-word; word-break: break-word;">
                            @foreach ($organizationAddress as $line)
                                <span>{{ $line }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

{!! view_render_event('admin.contacts.persons.view.organization.after', ['person' => $person]) !!}
