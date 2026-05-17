{!! view_render_event('admin.leads.view.person.before', ['lead' => $lead]) !!}

@php
    $leadContacts = $lead->relationLoaded('persons') && $lead->persons->isNotEmpty()
        ? $lead->persons
        : collect($lead?->person ? [$lead->person] : []);

    $contactRows = $leadContacts->map(function ($contact) {
        return [
            'model'   => $contact,
            'name'    => trim(($contact->first_name ?? '').' '.($contact->last_name ?? '')) ?: ($contact->name ?: 'Contact #'.$contact->id),
            'company' => $contact->organization?->name ?: '--',
        ];
    });
@endphp

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800">
    <x-admin::accordion class="select-none !border-none">
        <x-slot:header class="!p-0">
            <div class="flex w-full items-center justify-between gap-4 font-semibold dark:text-white">
                <h4>Contacts ({{ $contactRows->count() }})</h4>

                @if (bouncer()->hasPermission('leads.edit'))
                    <a
                        href="{{ route('admin.leads.edit', $lead->id) }}#contact-information"
                        class="text-xs font-semibold text-brandColor hover:underline"
                    >
                        + New
                    </a>
                @endif
            </div>
        </x-slot>

        <x-slot:content class="mt-4 !px-0 !pb-0">
            @if ($contactRows->isNotEmpty())
                <div class="flex flex-col gap-3">
                    @foreach ($contactRows->take(3) as $contactRow)
                        <div class="flex items-center gap-2">
                            <x-admin::avatar :name="$contactRow['name']" />

                            <div class="min-w-0">
                                <a
                                    href="{{ route('admin.contacts.persons.view', $contactRow['model']->id) }}"
                                    class="block truncate font-semibold text-brandColor"
                                    target="_blank"
                                >
                                    {{ $contactRow['name'] }}
                                </a>

                                <p class="truncate text-xs text-gray-500 dark:text-gray-300">
                                    {{ $contactRow['company'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach

                    <v-modal size="medium">
                        <template v-slot:toggle>
                            <button
                                type="button"
                                class="w-fit text-xs font-semibold text-brandColor hover:underline"
                            >
                                View All Contacts
                            </button>
                        </template>

                        <template v-slot:header="{ toggle }">
                            <div class="flex items-center justify-between gap-2.5 border-b px-4 py-3 dark:border-gray-800">
                                <div class="flex flex-col">
                                    <h3 class="text-base font-semibold dark:text-white">
                                        Contacts ({{ $contactRows->count() }})
                                    </h3>

                                    <p class="text-xs text-gray-500 dark:text-gray-300">
                                        {{ $lead->title }}
                                    </p>
                                </div>

                                <span
                                    class="icon-cross-large cursor-pointer text-3xl hover:rounded-md hover:bg-gray-100 dark:hover:bg-gray-950"
                                    @click="toggle"
                                ></span>
                            </div>
                        </template>

                        <template v-slot:content>
                            <div class="flex flex-col gap-3 border-b px-4 py-3 dark:border-gray-800">
                                @foreach ($contactRows as $contactRow)
                                    <div class="flex items-center justify-between gap-3 rounded border border-gray-200 p-3 dark:border-gray-800">
                                        <div class="flex min-w-0 items-center gap-2">
                                            <x-admin::avatar :name="$contactRow['name']" />

                                            <div class="min-w-0">
                                                <a
                                                    href="{{ route('admin.contacts.persons.view', $contactRow['model']->id) }}"
                                                    class="block truncate font-semibold text-brandColor"
                                                    target="_blank"
                                                >
                                                    {{ $contactRow['name'] }}
                                                </a>

                                                <p class="truncate text-xs text-gray-500 dark:text-gray-300">
                                                    {{ $contactRow['company'] }}
                                                </p>
                                            </div>
                                        </div>

                                        @if (bouncer()->hasPermission('leads.edit'))
                                            <form
                                                method="POST"
                                                action="{{ route('admin.leads.contacts.detach', ['id' => $lead->id, 'person_id' => $contactRow['model']->id]) }}"
                                                onsubmit="return confirm('Remove this contact from the case?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="icon-delete rounded-md p-1.5 text-2xl text-gray-600 transition-all hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                                    title="Remove contact"
                                                ></button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </template>

                        <template v-slot:footer>
                            @if (bouncer()->hasPermission('leads.edit'))
                                <div class="flex justify-end px-4 py-3">
                                    <a
                                        href="{{ route('admin.leads.edit', $lead->id) }}#contact-information"
                                        class="primary-button"
                                    >
                                        Add New
                                    </a>
                                </div>
                            @endif
                        </template>
                    </v-modal>
                </div>
            @else
                <div class="flex flex-col gap-2">
                    <p class="text-sm text-gray-500 dark:text-gray-300">
                        No contacts added.
                    </p>

                    @if (bouncer()->hasPermission('leads.edit'))
                        <a
                            href="{{ route('admin.leads.edit', $lead->id) }}#contact-information"
                            class="w-fit text-xs font-semibold text-brandColor hover:underline"
                        >
                            + New
                        </a>
                    @endif
                </div>
            @endif
        </x-slot>
    </x-admin::accordion>
</div>

{!! view_render_event('admin.leads.view.person.after', ['lead' => $lead]) !!}
