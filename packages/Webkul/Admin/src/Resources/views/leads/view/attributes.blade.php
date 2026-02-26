{!! view_render_event('admin.leads.view.attributes.before', ['lead' => $lead]) !!}

<div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800">
    <x-admin::accordion class="select-none !border-none">
        <x-slot:header class="!p-0">
            <div class="flex w-full items-center justify-between gap-4 font-semibold dark:text-white">
                <h4>Case Information</h4>

                @if (bouncer()->hasPermission('leads.edit'))
                    <a
                        href="{{ route('admin.leads.edit', $lead->id) }}"
                        class="icon-edit rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                        target="_blank"
                    ></a>
                @endif
            </div>
        </x-slot>

        <x-slot:content class="mt-4 !px-0 !pb-0">
            {!! view_render_event('admin.leads.view.attributes.form_controls.before', ['lead' => $lead]) !!}

            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form @submit="handleSubmit($event, () => {})">
                    {!! view_render_event('admin.leads.view.attributes.form_controls.attributes.view.before', ['lead' => $lead]) !!}

                    <div class="grid grid-cols-[1fr_2fr] items-center gap-1 mb-2">
                        <div class="label dark:text-white">Case No</div>
                        <div class="font-medium dark:text-white">
                            @if (bouncer()->hasPermission('leads.edit'))
                                <x-admin::form.control-group.controls.inline.text
                                    name="case_no"
                                    :value="$lead->case_no ?? ''"
                                    :value-label="$lead->case_no ?: '--'"
                                    position="left"
                                    rules="required"
                                    label="Case No"
                                    placeholder="00001"
                                    ::errors="errors"
                                    :url="route('admin.leads.attributes.update', $lead->id)"
                                    :allow-edit="true"
                                />
                            @else
                                {{ $lead->case_no ?? '--' }}
                            @endif
                        </div>
                    </div>

                    <x-admin::attributes.view
                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                            'entity_type' => 'leads',
                            ['code', 'NOTIN', ['title', 'description', 'lead_pipeline_id', 'lead_pipeline_stage_id', 'lead_type_id', 'lead_value', 'expected_close_date']]
                        ])"
                        :entity="$lead"
                        :url="route('admin.leads.attributes.update', $lead->id)"
                        :allow-edit="true"
                    />

                    {!! view_render_event('admin.leads.view.attributes.form_controls.attributes.view.after', ['lead' => $lead]) !!}
                </form>
            </x-admin::form>

            {!! view_render_event('admin.leads.view.attributes.form_controls.after', ['lead' => $lead]) !!}
        </x-slot>
    </x-admin::accordion>
</div>

{!! view_render_event('admin.leads.view.attributes.before', ['lead' => $lead]) !!}
