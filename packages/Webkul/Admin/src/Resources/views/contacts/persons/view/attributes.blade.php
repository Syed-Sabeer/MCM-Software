{!! view_render_event('admin.contacts.persons.view.attributes.before', ['person' => $person]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800">
    <x-admin::accordion class="select-none !border-none">
        <x-slot:header class="!p-0">
            <h4 class="font-semibold dark:text-white">
                @lang('admin::app.contacts.persons.view.about-person')
            </h4>
        </x-slot>

        <x-slot:content class="mt-4 !px-0 !pb-0 space-y-6">
            {!! view_render_event('admin.contacts.persons.view.attributes.form_controls.before', ['person' => $person]) !!}

            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, () => {})">
                    <!-- ABOUT Section - all fields from create form -->
                    <div>
                        <h5 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white">ABOUT</h5>
                        <div class="grid gap-4 md:grid-cols-2">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Salutation</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.text
                                    name="salutation"
                                    :value="json_encode($person->salutation ?? '')"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>@lang('admin::app.contacts.persons.view.type')</x-admin::form.control-group.label>
                                @php
                                    $typeLabels = [
                                        'customer' => __('admin::app.contacts.persons.view.types.customer'),
                                        'vendor' => __('admin::app.contacts.persons.view.types.vendor'),
                                        'employee' => __('admin::app.contacts.persons.view.types.employee'),
                                        'partner' => __('admin::app.contacts.persons.view.types.partner'),
                                        'other' => __('admin::app.contacts.persons.view.types.other'),
                                    ];
                                    $typeColors = [
                                        'customer' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        'vendor' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        'employee' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                        'partner' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                        'other' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    ];
                                    $currentType = $person->type ?? 'customer';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$currentType] ?? $typeColors['other'] }}">
                                    {{ $typeLabels[$currentType] ?? ucfirst($currentType) }}
                                </span>
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>First Name</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.text
                                    name="first_name"
                                    :value="json_encode($person->first_name ?? '')"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Last Name</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.text
                                    name="last_name"
                                    :value="json_encode($person->last_name ?? '')"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Title</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.text
                                    name="title"
                                    :value="json_encode($person->title ?? '')"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group class="md:col-span-2">
                                <x-admin::form.control-group.label>Description</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.text
                                    name="description"
                                    :value="json_encode($person->description ?? '')"
                                    multiline="true"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>

                            {!! view_render_event('admin.contacts.persons.view.attributes.form_controls.attributes_view.before', ['person' => $person]) !!}

                            <x-admin::attributes.view
                                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    'entity_type' => 'persons',
                                    ['code', 'IN', ['job_title', 'user_id', 'organization_id']]
                                ])"
                                :entity="$person"
                                :url="route('admin.contacts.persons.update', $person->id)"
                                :allow-edit="true"
                            />

                            {!! view_render_event('admin.contacts.persons.view.attributes.form_controls.attributes_view.after', ['person' => $person]) !!}

                            @php
                                $phoneVal = fn ($p) => is_array($p ?? null) ? ($p[0]['value'] ?? '') : ($p ?? '');
                            @endphp
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Cell Phone</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.text
                                    name="cell_phone"
                                    :value="json_encode($phoneVal($person->cell_phone))"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Direct Phone</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.text
                                    name="direct_phone"
                                    :value="json_encode($phoneVal($person->direct_phone))"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Secondary Email</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.text
                                    name="email_secondary"
                                    :value="json_encode($phoneVal($person->email_secondary))"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Birth Date</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.date
                                    name="birth_date"
                                    :value="json_encode(optional($person->birth_date)->format('Y-m-d') ?? '')"
                                    :url="route('admin.contacts.persons.update', $person->id)"
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
                                <x-admin::form.control-group.controls.inline.text
                                    name="phone"
                                    :value="json_encode($phoneVal($person->phone))"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Email</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.text
                                    name="email"
                                    :value="json_encode($phoneVal($person->email))"
                                    :url="route('admin.contacts.persons.update', $person->id)"
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
                                    :value="json_encode($person->mailing_street ?? '')"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>City</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.text
                                    name="mailing_city"
                                    :value="json_encode($person->mailing_city ?? '')"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>State/Province</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.text
                                    name="mailing_state"
                                    :value="json_encode($person->mailing_state ?? '')"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Postal Code</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.text
                                    name="mailing_postcode"
                                    :value="json_encode($person->mailing_postcode ?? '')"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Country</x-admin::form.control-group.label>
                                <x-admin::form.control-group.controls.inline.text
                                    name="mailing_country"
                                    :value="json_encode($person->mailing_country ?? '')"
                                    :url="route('admin.contacts.persons.update', $person->id)"
                                />
                            </x-admin::form.control-group>
                        </div>
                    </div>
                </form>
            </x-admin::form>

            {!! view_render_event('admin.contacts.persons.view.attributes.form_controls.after', ['person' => $person]) !!}
        </x-slot>
    </x-admin::accordion>
</div>

{!! view_render_event('admin.contacts.persons.view.attributes.after', ['person' => $person]) !!}
