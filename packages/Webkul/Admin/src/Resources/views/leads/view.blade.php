<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.leads.view.title', ['title' => $lead->title])
    </x-slot>

    <!-- Content -->
    <div class="relative flex gap-4 max-lg:flex-wrap">
        <!-- Left Panel -->
        {!! view_render_event('admin.leads.view.left.before', ['lead' => $lead]) !!}

        <div class="max-lg:min-w-full max-lg:max-w-full [&>div:last-child]:border-b-0 lg:sticky lg:top-[73px] flex min-w-[394px] max-w-[394px] flex-col self-start rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <!-- Case Information -->
            <div class="flex w-full flex-col gap-2 border-b border-gray-200 p-4 dark:border-gray-800">
                <!-- Breadcrumb's -->
                <div class="flex items-center justify-between">
                    <x-admin::breadcrumbs
                        name="leads.view"
                        :entity="$lead"
                    />
                </div>

                <div class="mb-2">
                    @if (($days = $lead->rotten_days) > 0)
                        @php
                            $lead->tags->prepend([
                                'name'  => '<span class="icon-rotten text-base"></span>' . trans('admin::app.leads.view.rotten-days', ['days' => $days]),
                                'color' => '#FEE2E2'
                            ]);
                        @endphp
                    @endif

                    {!! view_render_event('admin.leads.view.tags.before', ['lead' => $lead]) !!}

                    <!-- Tags -->
                    <x-admin::tags
                        :attach-endpoint="route('admin.leads.tags.attach', $lead->id)"
                        :detach-endpoint="route('admin.leads.tags.detach', $lead->id)"
                        :added-tags="$lead->tags"
                    />

                    {!! view_render_event('admin.leads.view.tags.after', ['lead' => $lead]) !!}
                </div>


                {!! view_render_event('admin.leads.view.title.before', ['lead' => $lead]) !!}

                <!-- Title -->
                <h1 class="text-lg font-bold dark:text-white truncate" title="{{ $lead->title }}">
                    {{ $lead->title }}
                </h1>

                {!! view_render_event('admin.leads.view.title.after', ['lead' => $lead]) !!}

                <!-- Activity Actions -->
                <div class="flex flex-wrap gap-2">
                    {!! view_render_event('admin.leads.view.actions.before', ['lead' => $lead]) !!}

                    @if (bouncer()->hasPermission('mail.compose'))
                        <!-- Mail Activity Action -->
                        <x-admin::activities.actions.mail
                            :entity="$lead"
                            entity-control-name="lead_id"
                        />
                    @endif

                    @if (bouncer()->hasPermission('activities.create'))
                        <!-- File Activity Action -->
                        <x-admin::activities.actions.file
                            :entity="$lead"
                            entity-control-name="lead_id"
                        />

                        <!-- Note Activity Action -->
                        <x-admin::activities.actions.note
                            :entity="$lead"
                            entity-control-name="lead_id"
                        />

                        <!-- Activity Action -->
                        <x-admin::activities.actions.activity
                            :entity="$lead"
                            entity-control-name="lead_id"
                        />

                        <!-- Task Action -->
                        <x-admin::activities.actions.task
                            :entity="$lead"
                            entity-control-name="lead_id"
                        />
                    @endif

                    {!! view_render_event('admin.leads.view.actions.after', ['lead' => $lead]) !!}
                </div>
            </div>

            <!-- Lead Attributes -->
            @include ('admin::leads.view.attributes')

            <!-- Contact Person -->
            @include ('admin::leads.view.person')

            <!-- Description Information -->
            <div class="flex w-full flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        Description Information
                    </p>
                </div>

                @if (bouncer()->hasPermission('leads.edit'))
                    <x-admin::form v-slot="{ meta, errors, handleSubmit }" as="div">
                        <form @submit="handleSubmit($event, () => {})">
                            <div class="flex flex-col gap-3 text-sm dark:text-white">
                                <div>
                                    <p class="mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Subject</p>
                                    <div class="font-medium dark:text-white">
                                        <x-admin::form.control-group.controls.inline.text
                                            name="title"
                                            :value="json_encode($lead->title ?? '')"
                                            :value-label="json_encode($lead->title ?: '--')"
                                            position="left"
                                            rules="required"
                                            label="Subject"
                                            placeholder="Subject"
                                            ::errors="errors"
                                            :url="route('admin.leads.attributes.update', $lead->id)"
                                            :allow-edit="true"
                                            :multiline="true"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Description</p>
                                    <div class="font-medium dark:text-white">
                                        <x-admin::form.control-group.controls.inline.text
                                            name="description"
                                            :value="json_encode($lead->description ?? '')"
                                            :value-label="json_encode($lead->description ?: '--')"
                                            position="left"
                                            label="Description"
                                            placeholder="Description"
                                            ::errors="errors"
                                            :url="route('admin.leads.attributes.update', $lead->id)"
                                            :allow-edit="true"
                                            :multiline="true"
                                        />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </x-admin::form>
                @else
                    <div class="flex flex-col gap-3 text-sm dark:text-white">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Subject</p>
                            <p class="break-words">{{ $lead->title ?? '--' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Description</p>
                            <p class="break-words whitespace-pre-wrap">{{ $lead->description ?? '--' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {!! view_render_event('admin.leads.view.left.after', ['lead' => $lead]) !!}

        {!! view_render_event('admin.leads.view.right.before', ['lead' => $lead]) !!}

        <!-- Right Panel -->
        <div class="flex w-full flex-col gap-4 rounded-lg">
            <!-- Stages Navigation -->
            @include ('admin::leads.view.stages')

            <!-- Activities -->
            {!! view_render_event('admin.leads.view.activities.before', ['lead' => $lead]) !!}

            <x-admin::activities
                :endpoint="route('admin.leads.activities.index', $lead->id)"
                :email-detach-endpoint="route('admin.leads.emails.detach', $lead->id)"
                :activeType="request()->query('from') === 'quotes' ? 'quotes' : 'all'"
                :extra-types="[
                    ['name' => 'products', 'label' => trans('admin::app.leads.view.tabs.products')],
                    ['name' => 'quotes', 'label' => trans('admin::app.leads.view.tabs.quotes')],
                ]"
            >
                <!-- Products -->
                <x-slot:products>
                    @include ('admin::leads.view.products')
                </x-slot>

                <!-- Quotes -->
                <x-slot:quotes>
                    @include ('admin::leads.view.quotes')
                </x-slot>
            </x-admin::activities>

            {!! view_render_event('admin.leads.view.activities.after', ['lead' => $lead]) !!}
        </div>

        {!! view_render_event('admin.leads.view.right.after', ['lead' => $lead]) !!}
    </div>
</x-admin::layouts>
