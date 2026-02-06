{!! view_render_event('admin.contacts.persons.view.organization.before', ['person' => $person]) !!}

@if ($person?->organization)
    <div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800">
        <h4 class="flex items-center justify-between font-semibold dark:text-white">
            @lang('admin::app.contacts.persons.view.about-organization')

            <a
                href="{{ route('admin.contacts.organizations.edit', $person->organization->id) }}"
                class="icon-edit rounded-md p-1 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                target="_blank"
            ></a>
        </h4>

        <div class="flex gap-2">
            {!! view_render_event('admin.contacts.persons.view.organization.avatar.before', ['person' => $person]) !!}

            <!-- Organization Initials -->
            <x-admin::avatar :name="$person->organization->name" />

            {!! view_render_event('admin.contacts.persons.view.organization.avatar.after', ['person' => $person]) !!}

            <!-- Organization Details -->
            <div class="flex flex-col gap-1">
                {!! view_render_event('admin.contacts.persons.view.organization.name.before', ['person' => $person]) !!}

                <span class="font-semibold text-brandColor">
                    {{ $person->organization->name }}
                </span>

                {!! view_render_event('admin.contacts.persons.view.organization.name.after', ['person' => $person]) !!}


                {!! view_render_event('admin.contacts.persons.view.organization.address.before', ['person' => $person]) !!}

                @php
                    $organization = $person->organization;
                @endphp

                @if (
                    $organization->billing_street
                    || $organization->billing_city
                    || $organization->billing_state
                    || $organization->billing_postcode
                    || $organization->billing_country
                )
                    <div class="flex flex-col gap-0.5 dark:text-white">
                        @if ($organization->billing_street)
                            <span>
                                {{ $organization->billing_street }}
                            </span>
                        @endif

                        @if ($organization->billing_postcode || $organization->billing_city)
                            <span>
                                {{ trim(($organization->billing_postcode ?? '') . ' ' . ($organization->billing_city ?? '')) }}
                            </span>
                        @endif

                        @if ($organization->billing_state)
                            <span>
                                {{ $organization->billing_state }}
                            </span>
                        @endif

                        @if ($organization->billing_country)
                            <span>
                                {{ $organization->billing_country }}
                            </span>
                        @endif
                    </div>
                @endif

                {!! view_render_event('admin.contacts.persons.view.organization.address.after', ['person' => $person]) !!}
            </div>
        </div>
    </div>
@endif

{!! view_render_event('admin.contacts.persons.view.organization.after', ['person' => $person]) !!}
