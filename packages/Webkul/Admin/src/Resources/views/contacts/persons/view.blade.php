<x-admin::layouts>
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
                        name="contacts.persons.view"
                        :entity="$person"
                    />
                </div>

                {!! view_render_event('admin.contact.persons.view.tags.before', ['person' => $person]) !!}

                <!-- Tags -->
                <x-admin::tags
                    :attach-endpoint="route('admin.contacts.persons.tags.attach', $person->id)"
                    :detach-endpoint="route('admin.contacts.persons.tags.detach', $person->id)"
                    :added-tags="$person->tags"
                />

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

                    <!-- Task Activity Action -->
                    <x-admin::activities.actions.task
                        :entity="$person"
                        entity-control-name="person_id"
                    />

                    {!! view_render_event('admin.contact.persons.view.actions.after', ['person' => $person]) !!}
                </div>
            </div>

            <!-- Person Details Section -->
            <div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800">
                <x-admin::accordion class="select-none !border-none">
                    <x-slot:header class="!p-0">
                        <h4 class="font-semibold dark:text-white">Person Details</h4>                    </x-slot>

                    <x-slot:content class="mt-4 !px-0 !pb-0 space-y-6">
                        <!-- ABOUT Section -->
                        <div>
                            <h5 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white">ABOUT</h5>
                            <div class="grid gap-4 md:grid-cols-2">
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Salutation</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.text
                                        name="salutation"
                                        value="{{ $person->salutation ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>First Name</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.text
                                        name="first_name"
                                        value="{{ $person->first_name ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Last Name</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.text
                                        name="last_name"
                                        value="{{ $person->last_name ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Title</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.text
                                        name="title"
                                        value="{{ $person->title ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="md:col-span-2">
                                    <x-admin::form.control-group.label>Description</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.text
                                        name="description"
                                        value="{{ $person->description ?? '' }}"
                                        multiline="true"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Cell Phone</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.phone
                                        name="cell_phone"
                                        value="{{ $person->cell_phone ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Direct Phone</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.phone
                                        name="direct_phone"
                                        value="{{ $person->direct_phone ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Secondary Email</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.email
                                        name="email_secondary"
                                        value="{{ $person->email_secondary ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Birth Date</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.date
                                        name="birth_date"
                                        value="{{ $person->birth_date ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>
                            </div>
                        </div>

                        <!-- GET IN TOUCH Section -->
                        <div class="border-t border-gray-200 pt-4 dark:border-gray-800">
                            <h5 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white">GET IN TOUCH</h5>
                            <div class="grid gap-4 md:grid-cols-2">
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Phone</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.phone
                                        name="phone"
                                        value="{{ $person->phone ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Email</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.email
                                        name="email"
                                        value="{{ $person->email ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>
                            </div>
                        </div>

                        <!-- MAILING ADDRESS Section -->
                        <div class="border-t border-gray-200 pt-4 dark:border-gray-800">
                            <h5 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white">MAILING ADDRESS</h5>
                            <div class="grid gap-4 md:grid-cols-2">
                                <x-admin::form.control-group class="md:col-span-2">
                                    <x-admin::form.control-group.label>Street</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.text
                                        name="mailing_street"
                                        value="{{ $person->mailing_street ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>City</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.text
                                        name="mailing_city"
                                        value="{{ $person->mailing_city ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>State/Province</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.text
                                        name="mailing_state"
                                        value="{{ $person->mailing_state ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Postal Code</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.text
                                        name="mailing_postcode"
                                        value="{{ $person->mailing_postcode ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>Country</x-admin::form.control-group.label>
                                    <x-admin::form.control-group.controls.inline.text
                                        name="mailing_country"
                                        value="{{ $person->mailing_country ?? '' }}"
                                        url="{{ route('admin.contacts.persons.update', $person->id) }}"
                                    />
                                </x-admin::form.control-group>
                            </div>
                        </div>
                    </x-slot>
                </x-admin::accordion>
            </div>

            <!-- Person Attributes -->
            @include ('admin::contacts.persons.view.attributes')

            <!-- Contact Organization -->
            @include ('admin::contacts.persons.view.organization')
        </div>

        {!! view_render_event('admin.contact.persons.view.left.after', ['person' => $person]) !!}

        <!-- Right Panel -->
        <div class="flex w-full flex-col gap-4 rounded-lg">
            {!! view_render_event('admin.contact.persons.view.right.before', ['person' => $person]) !!}

            <!-- Stages Navigation -->
            <x-admin::activities :endpoint="route('admin.contacts.persons.activities.index', $person->id)" />

            {!! view_render_event('admin.contact.persons.view.right.after', ['person' => $person]) !!}
        </div>
    </div>
</x-admin::layouts>
