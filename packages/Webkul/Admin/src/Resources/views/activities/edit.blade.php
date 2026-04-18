<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.activities.edit.title')
    </x-slot>

    {!! view_render_event('admin.activities.edit.form.before') !!}

    <x-admin::form
        :action="route('admin.activities.update', $activity->id)"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs
                        name="activities.edit"
                        :entity="$activity"
                    />

                    <!-- Page Title -->
                    <div class="text-xl font-bold dark:text-gray-300">
                        Edit Event
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            @php
                $scheduleFrom = old('schedule_from') ?? $activity->schedule_from;
                $scheduleTo = old('schedule_to') ?? $activity->schedule_to;

                $scheduleFromDate = $scheduleFrom ? \Carbon\Carbon::parse($scheduleFrom)->format('Y-m-d') : '';
                $scheduleFromTime = $scheduleFrom ? \Carbon\Carbon::parse($scheduleFrom)->format('H:i') : '';
                $scheduleToDate = $scheduleTo ? \Carbon\Carbon::parse($scheduleTo)->format('Y-m-d') : '';
                $scheduleToTime = $scheduleTo ? \Carbon\Carbon::parse($scheduleTo)->format('H:i') : '';
            @endphp

            <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                    Edit Event
                </p>

                <div style="display: grid !important; grid-template-columns: repeat(2, 1fr) !important; gap: 1rem !important;">
                    {!! view_render_event('admin.activities.edit.form_controls.before') !!}

                    <!-- Title -->
                    <x-admin::form.control-group style="margin: 0 !important;">
                        <x-admin::form.control-group.label class="required">
                            @lang('admin::app.activities.edit.title')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="title"
                            id="title"
                            rules="required"
                            :value="old('title') ?? $activity->title"
                            :label="trans('admin::app.activities.edit.title')"
                            :placeholder="trans('admin::app.activities.edit.title')"
                        />

                        <x-admin::form.control-group.error control-name="title" />
                    </x-admin::form.control-group>

                    <!-- Participants -->
                    <x-admin::form.control-group style="margin: 0 !important;">
                        <x-admin::form.control-group.label>
                            @lang('admin::app.activities.edit.participants')
                        </x-admin::form.control-group.label>

                        <v-multi-lookup-component>
                            <div
                                class="relative rounded border border-gray-200 px-2 py-1 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                role="button"
                            >
                                <ul class="flex flex-wrap items-center gap-1">
                                    <li>
                                        <input
                                            type="text"
                                            class="w-full px-1 py-1 dark:bg-gray-900 dark:text-gray-300"
                                            placeholder="@lang('admin::app.activities.edit.participants')"
                                        />
                                    </li>
                                </ul>

                                <span class="icon-down-arrow absolute top-1.5 text-2xl ltr:right-1.5 rtl:left-1.5"></span>
                            </div>
                        </v-multi-lookup-component>
                    </x-admin::form.control-group>
                </div>

                <div class="mt-4">
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">
                            Schedule
                        </x-admin::form.control-group.label>

                        <input type="hidden" name="schedule_from" id="schedule_from" value="{{ $scheduleFrom }}">
                        <input type="hidden" name="schedule_to" id="schedule_to" value="{{ $scheduleTo }}">

                        <div style="display: grid !important; grid-template-columns: repeat(4, 1fr) !important; gap: 1rem !important;" class="max-lg:grid-cols-2 max-sm:grid-cols-1">
                            <div>
                                <x-admin::form.control-group.label class="required">
                                    Start Date
                                </x-admin::form.control-group.label>

                                <x-admin::flat-picker.date class="!w-full" ::allow-input="true">
                                    <input
                                        type="date"
                                        id="schedule_from_date"
                                        value="{{ $scheduleFromDate }}"
                                        class="flex w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                        placeholder="Start Date"
                                    />
                                </x-admin::flat-picker.date>
                            </div>

                            <div>
                                <x-admin::form.control-group.label class="required">
                                    Start Time
                                </x-admin::form.control-group.label>

                                <input
                                    type="time"
                                    id="schedule_from_time"
                                    value="{{ $scheduleFromTime }}"
                                    class="flex w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                />
                            </div>

                            <div>
                                <x-admin::form.control-group.label>
                                    End Date
                                </x-admin::form.control-group.label>

                                <x-admin::flat-picker.date class="!w-full" ::allow-input="true">
                                    <input
                                        type="date"
                                        id="schedule_to_date"
                                        value="{{ $scheduleToDate }}"
                                        class="flex w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                        placeholder="End Date"
                                    />
                                </x-admin::flat-picker.date>
                            </div>

                            <div>
                                <x-admin::form.control-group.label>
                                    End Time
                                </x-admin::form.control-group.label>

                                <input
                                    type="time"
                                    id="schedule_to_time"
                                    value="{{ $scheduleToTime }}"
                                    class="flex w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                />
                            </div>
                        </div>

                        <x-admin::form.control-group.error control-name="schedule_from" />
                        <x-admin::form.control-group.error control-name="schedule_to" />
                    </x-admin::form.control-group>
                </div>

                <!-- Comment -->
                <x-admin::form.control-group class="mt-4">
                    <x-admin::form.control-group.label>
                        @lang('admin::app.activities.edit.comment')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="textarea"
                        name="comment"
                        id="comment"
                        :value="old('comment') ?? $activity->comment"
                        :label="trans('admin::app.activities.edit.comment')"
                        :placeholder="trans('admin::app.activities.edit.comment')"
                    />

                    <x-admin::form.control-group.error control-name="comment" />
                </x-admin::form.control-group>

                {!! view_render_event('admin.activities.edit.form_controls.after') !!}

                <div class="mt-4 flex items-center justify-start gap-2.5">
                    {!! view_render_event('admin.activities.edit.save_button.before') !!}

                    <button
                        type="submit"
                        class="primary-button"
                    >
                        Save Event
                    </button>

                    {!! view_render_event('admin.activities.edit.save_button.after') !!}
                </div>
            </div>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.activities.edit.form.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-multi-lookup-component-template"
        >
            <!-- Search Button -->
            <div class="relative">
                <div class="relative rounded border border-gray-200 px-2 py-1 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800" role="button">
                    <ul class="flex flex-wrap items-center gap-1">
                        <!-- Added Participants -->
                        <template v-for="userType in ['users', 'persons']">
                            <template v-if="! addedParticipants[userType].length">
                                <input
                                    type="hidden"
                                    :name="`participants[${userType}][]`"
                                    value=""
                                />
                            </template>

                            <li
                                class="flex items-center gap-1 rounded-md bg-slate-100 pl-2 dark:bg-slate-950 dark:text-gray-300"
                                v-for="(user, index) in addedParticipants[userType]"
                            >
                                <!-- Person and User Hidden Input Field -->
                                <input
                                    type="hidden"
                                    :name="`participants[${userType}][]`"
                                    :value="user.id"
                                />

                                @{{ user.name }}

                                <span
                                    class="icon-cross-large cursor-pointer p-0.5 text-xl"
                                    @click="remove(userType, user)"
                                ></span>
                            </li>
                        </template>

                        <!-- Search Input Box -->
                        <li>
                            <input
                                type="text"
                                class="w-full px-1 py-1 dark:bg-gray-900 dark:text-gray-300"
                                placeholder="@lang('admin::app.activities.edit.participants')"
                                v-model.lazy="searchTerm"
                                v-debounce="500"
                            />
                        </li>
                    </ul>

                    <!-- Search and Spinner Icon -->
                    <div>
                        <template v-if="! isSearching.users && ! isSearching.persons">
                            <span
                                class="absolute top-1.5 text-2xl ltr:right-1.5 rtl:left-1.5"
                                :class="[searchTerm.length >= 2 ? 'icon-up-arrow' : 'icon-down-arrow']"
                            ></span>
                        </template>

                        <template v-else>
                            <x-admin::spinner class="absolute top-2 ltr:right-2 rtl:left-2" />
                        </template>
                    </div>
                </div>

                <!-- Search Dropdown -->
                <div
                    class="absolute z-10 w-full rounded bg-white shadow-[0px_10px_20px_0px_#0000001F] dark:bg-gray-900"
                    v-if="searchTerm.length >= 2"
                >
                    <ul class="flex flex-col gap-1 p-2">
                        <!-- Users and Person Searched Participants -->
                        <li
                            class="flex flex-col gap-2"
                            v-for="userType in ['users', 'persons']"
                        >
                            <h3 class="text-sm font-bold text-gray-600 dark:text-gray-400">
                                <template v-if="userType === 'users'">
                                    @lang('admin::app.activities.edit.users')
                                </template>

                                <template v-else>
                                    @lang('admin::app.activities.edit.persons')
                                </template>
                            </h3>

                            <ul>
                                <li
                                    class="rounded-sm px-5 py-2 text-sm text-gray-800 dark:text-gray-300"
                                    v-if="! searchedParticipants[userType].length && ! isSearching[userType]"
                                >
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        @lang('admin::app.activities.edit.no-result-found')
                                    </p>
                                </li>

                                <li
                                    class="cursor-pointer rounded-sm px-3 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                    v-for="user in searchedParticipants[userType]"
                                    @click="add(userType, user)"
                                >
                                    @{{ user.name }}
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-multi-lookup-component', {
                template: '#v-multi-lookup-component-template',

                data() {
                    return {
                        isSearching: {
                            users: false,

                            persons: false,
                        },

                        searchTerm: '',

                        addedParticipants: {
                            users: [],

                            persons: [],
                        },

                        searchedParticipants: {
                            users: [],

                            persons: [],
                        },

                        searchEnpoints: {
                            users: "{{ route('admin.settings.users.search') }}",

                            persons: "{{ route('admin.contacts.persons.search') }}",
                        },
                    };
                },

                watch: {
                    searchTerm(newVal, oldVal) {
                        this.search('users');

                        this.search('persons');
                    },
                },

                created() {
                    @json($activity->participants).forEach(participant => {
                        if (participant.user) {
                            this.addedParticipants.users.push(participant.user);
                        } else if (participant.person) {
                            this.addedParticipants.persons.push(participant.person);
                        }
                    });
                },

                methods: {
                    search(userType) {
                        if (this.searchTerm.length <= 1) {
                            this.searchedParticipants[userType] = [];

                            this.isSearching[userType] = false;

                            return;
                        }

                        this.isSearching[userType] = true;

                        this.$axios.get(this.searchEnpoints[userType], {
                                params: {
                                    search: 'name:' + this.searchTerm,
                                    searchFields: 'name:like',
                                }
                            })
                            .then ((response) => {
                                this.addedParticipants[userType].forEach(addedParticipant =>
                                    response.data.data = response.data.data.filter(participant => participant.id !== addedParticipant.id)
                                );

                                this.searchedParticipants[userType] = response.data.data;

                                this.isSearching[userType] = false;
                            })
                            .catch (function (error) {
                                this.isSearching[userType] = false;
                            });
                    },

                    add(userType, participant) {
                        this.addedParticipants[userType].push(participant);

                        this.searchTerm = '';

                        this.searchedParticipants = {
                            users: [],

                            persons: [],
                        };
                    },

                    remove(userType, participant) {
                        this.addedParticipants[userType] = this.addedParticipants[userType].filter(addedParticipant =>
                            addedParticipant.id !== participant.id
                        );
                    },
                },
            });
        </script>

        <script>
            (() => {
                const combineDateTime = (dateValue, timeValue) => {
                    if (! dateValue) {
                        return '';
                    }

                    return `${dateValue} ${timeValue || '00:00'}:00`;
                };

                document.addEventListener('DOMContentLoaded', () => {
                    const form = document.querySelector('form[action*="/activities/edit/"]');

                    if (! form) {
                        return;
                    }

                    form.addEventListener('submit', () => {
                        const fromDate = document.getElementById('schedule_from_date')?.value || '';
                        const fromTime = document.getElementById('schedule_from_time')?.value || '';
                        const toDate = document.getElementById('schedule_to_date')?.value || '';
                        const toTime = document.getElementById('schedule_to_time')?.value || '';

                        const fromHidden = document.getElementById('schedule_from');
                        const toHidden = document.getElementById('schedule_to');

                        if (fromHidden) {
                            fromHidden.value = combineDateTime(fromDate, fromTime);
                        }

                        if (toHidden) {
                            toHidden.value = toDate
                                ? combineDateTime(toDate, toTime)
                                : combineDateTime(fromDate, fromTime);
                        }
                    });
                });
            })();
        </script>
    @endPushOnce
</x-admin::layouts>
