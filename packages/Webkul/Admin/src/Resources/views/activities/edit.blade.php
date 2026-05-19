<x-admin::layouts>
    @php
        $activityType = $activity->type ?? 'meeting';
        $activityLabel = match ($activityType) {
            'task'    => 'Task',
            'call'    => 'Log a Call',
            'meeting' => 'Event',
            'note'    => 'Comment',
            'file'    => 'File',
            default   => ucfirst((string) $activityType),
        };
        $defaultAssignee = auth()->guard('user')->check()
            ? [
                'id'    => auth()->guard('user')->user()->id,
                'name'  => auth()->guard('user')->user()->name,
                'email' => auth()->guard('user')->user()->email,
            ]
            : null;
        $activityPayload = $activity->toArray();

        foreach (['schedule_from', 'schedule_to'] as $scheduleField) {
            $activityPayload[$scheduleField] = $activity->{$scheduleField}
                ? $activity->{$scheduleField}->format('Y-m-d H:i:s')
                : null;
        }
    @endphp

    <x-slot:title>
        Edit {{ $activityLabel }}
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs
                    name="activities.edit"
                    :entity="$activity"
                />

                <div class="text-xl font-bold text-gray-900 dark:text-white">
                    Edit {{ $activityLabel }}
                </div>

                {{-- <p class="text-sm text-gray-600 dark:text-gray-300">
                    Activity #{{ $activity->id }} uses the same field layout as your company activity forms.
                </p> --}}
            </div>
        </div>

        <v-activity-edit-page
            :activity='@json($activityPayload)'
            :entity='@json($activityEntity ?? new stdClass())'
            entity-control-name="{{ $entityControlName ?? '' }}"
            update-url="{{ route('admin.activities.update', $activity->id) }}"
            type-label="{{ $activityLabel }}"
        ></v-activity-edit-page>
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-activity-edit-page-template">
            <form
                :action="updateUrl"
                method="POST"
                enctype="multipart/form-data"
                class="grid gap-4"
                @submit="prepareSubmit"
            >
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="type" :value="activity.type">

                <input
                    v-if="shouldSendEntityBinding"
                    type="hidden"
                    :name="entityControlName"
                    :value="entity?.id || ''"
                >

                <input v-if="activity.type === 'task'" type="hidden" name="schedule_from" :value="normalizedDueDate">
                <input v-if="activity.type === 'task'" type="hidden" name="schedule_to" :value="normalizedDueDate">
                <input v-if="activity.type === 'meeting'" type="hidden" name="schedule_from" :value="scheduleFrom">
                <input v-if="activity.type === 'meeting'" type="hidden" name="schedule_to" :value="scheduleTo">
                <input v-if="showsOrganizationLookup" type="hidden" name="organization_id" :value="selectedOrganizationId">
                <input v-if="showsAssigneeLookup" type="hidden" name="user_id" :value="primaryAssignedUserId">
                <input v-if="activity.type === 'file'" type="hidden" name="entity_type" :value="fileEntityType">
                <input v-if="activity.type === 'file'" type="hidden" name="entity_id" :value="fileEntityId">

                <input
                    v-for="(contact, index) in contacts.selected"
                    v-if="showsContactsLookup"
                    :key="`contact-input-${contact.id}`"
                    type="hidden"
                    :name="`participants[persons][${index}]`"
                    :value="contact.id"
                >

                <input
                    v-for="(assignee, index) in assignees.selected"
                    v-if="showsAssigneeLookup"
                    :key="`assignee-input-${assignee.id}`"
                    type="hidden"
                    :name="`participants[users][${index}]`"
                    :value="assignee.id"
                >

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                @{{ typeLabel }} Details
                            </p>
                            {{-- <p class="text-sm text-gray-600 dark:text-gray-300">
                                Type: @{{ activity.type }}
                            </p> --}}
                        </div>

                        <button type="submit" class="primary-button">
                            Save Changes
                        </button>
                    </div>

                    <template v-if="['task', 'meeting', 'call'].includes(activity.type)">
                        <div class="grid gap-4 rounded-xl p-2" :class="activityFormClass">
                            <template v-if="activity.type !== 'note'">
                                <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                    <x-admin::form.control-group.label class="required">Subject</x-admin::form.control-group.label>
                                    <input v-model="form.title" type="text" name="title" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" maxlength="200">
                                </x-admin::form.control-group>
                            </template>

                            <template v-if="activity.type === 'call' || activity.type === 'meeting'">
                                <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                    <x-admin::form.control-group.label>@{{ activity.type === 'call' ? 'Comment' : 'Description' }}</x-admin::form.control-group.label>
                                    <textarea v-model="form.comment" name="comment" rows="4" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
                                </x-admin::form.control-group>
                            </template>

                            <template v-if="activity.type === 'task'">
                                <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                    <x-admin::form.control-group.label class="required">Due Date</x-admin::form.control-group.label>
                                    <input v-model="form.due_date" type="date" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                </x-admin::form.control-group>
                            </template>

                            <template v-if="activity.type === 'meeting'">
                                <div class="grid grid-cols-4 gap-4">
                                    <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                        <x-admin::form.control-group.label class="required">Start Date</x-admin::form.control-group.label>
                                        <input v-model="form.start_date" type="date" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                        <x-admin::form.control-group.label class="required">Start Time</x-admin::form.control-group.label>
                                        <input v-model="form.start_time" type="time" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                        <x-admin::form.control-group.label class="required">End Date</x-admin::form.control-group.label>
                                        <input v-model="form.end_date" type="date" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                        <x-admin::form.control-group.label class="required">End Time</x-admin::form.control-group.label>
                                        <input v-model="form.end_time" type="time" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    </x-admin::form.control-group>
                                </div>
                            </template>

                            <template v-if="showsContactsLookup">
                                <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                    <x-admin::form.control-group.label>@{{ activity.type === 'call' ? 'Name' : 'Contacts' }}</x-admin::form.control-group.label>
                                    <div class="relative" ref="contactsLookup">
                                        <div class="rounded-xl border bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-900" :class="lookupAccentClass">
                                            <div class="mb-2 flex flex-wrap gap-2" v-if="contacts.selected.length">
                                                <span
                                                    v-for="contact in contacts.selected"
                                                    :key="`contact-chip-${contact.id}`"
                                                    class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium"
                                                    :class="lookupChipClass"
                                                >
                                                    @{{ contact.name }}
                                                    <button type="button" class="icon-cross-large text-sm" @click.stop="removeContact(contact.id)"></button>
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <input
                                                    v-model="contacts.search"
                                                    type="text"
                                                    class="w-full border-0 bg-transparent px-1 py-1 text-sm text-gray-900 outline-none placeholder:text-gray-400 dark:text-white"
                                                    placeholder="Search contacts"
                                                    @focus="openLookup('contacts')"
                                                    @input="searchContacts"
                                                >

                                                <button type="button" class="text-xl text-gray-500" @click="toggleLookup('contacts')">
                                                    <span :class="contacts.open ? 'icon-up-arrow' : 'icon-down-arrow'"></span>
                                                </button>
                                            </div>

                                            <div v-if="contacts.open" class="mt-3 max-h-[156px] overflow-y-auto rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-950">
                                                <div v-if="contacts.loading" class="px-3 py-2 text-sm text-gray-500">Searching...</div>

                                                <template v-else-if="contacts.results.length">
                                                    <button
                                                        v-for="person in contacts.results"
                                                        :key="`contact-option-${person.id}`"
                                                        type="button"
                                                        class="flex w-full items-start justify-between rounded-lg px-3 py-2 text-left dark:hover:bg-gray-900"
                                                        :class="lookupHoverClass"
                                                        @click="selectContact(person)"
                                                    >
                                                        <span class="min-w-0">
                                                            <span class="block text-sm font-medium text-gray-800 dark:text-white">@{{ person.name }}</span>
                                                            <span class="block text-xs text-gray-500">@{{ person.organization_name || person.email || 'Contact' }}</span>
                                                        </span>
                                                    </button>
                                                </template>

                                                <div v-else class="px-3 py-2 text-sm text-gray-500">
                                                    @{{ contacts.search.length < 1 ? 'Start typing to search contacts.' : 'No contacts found.' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </x-admin::form.control-group>
                            </template>

                            <template v-if="activity.type === 'task' || activity.type === 'meeting'">
                                <div class="grid grid-cols-2 gap-4">
                                    <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                        <x-admin::form.control-group.label class="required">Related To</x-admin::form.control-group.label>
                                        <div class="relative" ref="organizationLookup">
                                            <div class="rounded-xl border bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-900" :class="lookupAccentClass">
                                                <div v-if="organization.selected" class="mb-2">
                                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium" :class="lookupChipClass">
                                                        @{{ organization.selected.name }}
                                                        <button type="button" class="icon-cross-large text-sm" @click.stop="clearOrganization"></button>
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <input
                                                        v-model="organization.search"
                                                        type="text"
                                                        class="w-full border-0 bg-transparent px-1 py-1 text-sm text-gray-900 outline-none placeholder:text-gray-400 dark:text-white"
                                                        placeholder="Search company"
                                                        @focus="openLookup('organization')"
                                                        @input="searchOrganizations"
                                                    >

                                                    <button type="button" class="text-xl text-gray-500" @click="toggleLookup('organization')">
                                                        <span :class="organization.open ? 'icon-up-arrow' : 'icon-down-arrow'"></span>
                                                    </button>
                                                </div>

                                                <div v-if="organization.open" class="mt-3 max-h-[156px] overflow-y-auto rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-950">
                                                    <div v-if="organization.loading" class="px-3 py-2 text-sm text-gray-500">Searching...</div>

                                                    <template v-else-if="organization.results.length">
                                                        <button
                                                            v-for="company in organization.results"
                                                            :key="`company-option-${company.id}`"
                                                            type="button"
                                                            class="flex w-full items-start justify-between rounded-lg px-3 py-2 text-left dark:hover:bg-gray-900"
                                                            :class="lookupHoverClass"
                                                            @click="selectOrganization(company)"
                                                        >
                                                            <span class="block text-sm font-medium text-gray-800 dark:text-white">@{{ company.name }}</span>
                                                        </button>
                                                    </template>

                                                    <div v-else class="px-3 py-2 text-sm text-gray-500">
                                                        @{{ organization.search.length < 1 ? 'Start typing to search companies.' : 'No companies found.' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group style="margin-bottom: 0 !important;">
                                        <x-admin::form.control-group.label class="required">Assigned To</x-admin::form.control-group.label>
                                        <div class="relative" ref="assigneesLookup">
                                            <div class="rounded-xl border bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-900" :class="lookupAccentClass">
                                                <div class="mb-2 flex flex-wrap gap-2" v-if="assignees.selected.length">
                                                    <span
                                                        v-for="assignee in assignees.selected"
                                                        :key="`assignee-chip-${assignee.id}`"
                                                        class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium"
                                                        :class="lookupChipClass"
                                                    >
                                                        @{{ assignee.name }}
                                                        <button type="button" class="icon-cross-large text-sm" @click.stop="removeAssignee(assignee.id)"></button>
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <input
                                                        v-model="assignees.search"
                                                        type="text"
                                                        class="w-full border-0 bg-transparent px-1 py-1 text-sm text-gray-900 outline-none placeholder:text-gray-400 dark:text-white"
                                                        placeholder="Search employees"
                                                        @focus="openLookup('assignees')"
                                                        @input="searchAssignees"
                                                    >

                                                    <button type="button" class="text-xl text-gray-500" @click="toggleLookup('assignees')">
                                                        <span :class="assignees.open ? 'icon-up-arrow' : 'icon-down-arrow'"></span>
                                                    </button>
                                                </div>

                                                <div v-if="assignees.open" class="mt-3 max-h-[156px] overflow-y-auto rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-950">
                                                    <div v-if="assignees.loading" class="px-3 py-2 text-sm text-gray-500">Searching...</div>

                                                    <template v-else-if="assignees.results.length">
                                                        <button
                                                            v-for="user in assignees.results"
                                                            :key="`assignee-option-${user.id}`"
                                                            type="button"
                                                            class="flex w-full items-start justify-between rounded-lg px-3 py-2 text-left dark:hover:bg-gray-900"
                                                            :class="lookupHoverClass"
                                                            @click="selectAssignee(user)"
                                                        >
                                                            <span class="min-w-0">
                                                                <span class="block text-sm font-medium text-gray-800 dark:text-white">@{{ user.name }}</span>
                                                                <span class="block text-xs text-gray-500">@{{ user.email || 'Employee' }}</span>
                                                            </span>
                                                        </button>
                                                    </template>

                                                    <div v-else class="px-3 py-2 text-sm text-gray-500">
                                                        @{{ assignees.search.length < 1 ? 'Start typing to search employees.' : 'No employees found.' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </x-admin::form.control-group>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template v-else-if="activity.type === 'note'">
                        <div class="grid gap-4 rounded-xl bg-orange-50/60 p-2 dark:bg-orange-950/20">
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">Comment</x-admin::form.control-group.label>
                                <textarea v-model="form.comment" name="comment" rows="6" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
                            </x-admin::form.control-group>
                        </div>
                    </template>

                    <template v-else-if="activity.type === 'file'">
                        <div class="grid gap-4 rounded-xl bg-cyan-50/60 p-2 dark:bg-cyan-950/20">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Title</x-admin::form.control-group.label>
                                <input v-model="form.title" type="text" name="title" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Description</x-admin::form.control-group.label>
                                <textarea v-model="form.comment" name="comment" rows="4" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>File Name</x-admin::form.control-group.label>
                                <input v-model="form.name" type="text" name="name" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            </x-admin::form.control-group>

                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label>Replace File</x-admin::form.control-group.label>
                                <input type="file" name="file" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-brandColor file:px-3 file:py-2 file:text-white dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <p v-if="currentFileName" class="mt-2 text-xs text-gray-500 dark:text-gray-400">Current file: @{{ currentFileName }}</p>
                            </x-admin::form.control-group>
                        </div>
                    </template>

                    <template v-else>
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-200">
                            Editing is not supported for this activity type on the inline page yet.
                        </div>
                    </template>
                </div>
            </form>
        </script>

        <script type="module">
            app.component('v-activity-edit-page', {
                template: '#v-activity-edit-page-template',

                props: ['activity', 'entity', 'entityControlName', 'updateUrl', 'typeLabel'],

                data() {
                    return {
                        searchRequestTimeout: null,
                        defaultAssignee: @json($defaultAssignee),
                        form: {
                            title: this.activity?.title || '',
                            comment: this.activity?.comment || '',
                            due_date: '',
                            start_date: '',
                            start_time: '',
                            end_date: '',
                            end_time: '',
                            name: this.activity?.files?.[0]?.name || '',
                        },
                        contacts: {
                            open: false,
                            search: '',
                            loading: false,
                            results: [],
                            selected: [],
                        },
                        organization: {
                            open: false,
                            search: '',
                            loading: false,
                            results: [],
                            selected: null,
                        },
                        assignees: {
                            open: false,
                            search: '',
                            loading: false,
                            results: [],
                            selected: [],
                        },
                    };
                },

                computed: {
                    showsContactsLookup() {
                        return ['task', 'meeting', 'call'].includes(this.activity.type);
                    },

                    showsOrganizationLookup() {
                        return ['task', 'meeting'].includes(this.activity.type);
                    },

                    showsAssigneeLookup() {
                        return ['task', 'meeting'].includes(this.activity.type);
                    },

                    normalizedDueDate() {
                        return this.form.due_date ? `${this.form.due_date} 00:00:00` : '';
                    },

                    scheduleFrom() {
                        return this.form.start_date && this.form.start_time
                            ? `${this.form.start_date} ${this.form.start_time}:00`
                            : '';
                    },

                    scheduleTo() {
                        return this.form.end_date && this.form.end_time
                            ? `${this.form.end_date} ${this.form.end_time}:00`
                            : '';
                    },

                    selectedOrganizationId() {
                        return this.organization.selected?.id || '';
                    },

                    primaryAssignedUserId() {
                        return this.assignees.selected[0]?.id || '';
                    },

                    shouldSendEntityBinding() {
                        return this.entityControlName && this.entityControlName !== 'organization_id';
                    },

                    currentFileName() {
                        return this.activity?.files?.[0]?.name || '';
                    },

                    fileEntityType() {
                        return this.activity?.entity_type || (this.entity && this.entity.billing_street !== undefined ? 'organizations' : 'persons');
                    },

                    fileEntityId() {
                        return this.activity?.entity_id || this.entity?.id || '';
                    },

                    activityFormClass() {
                        return {
                            'bg-violet-50/70 dark:bg-violet-950/20': this.activity.type === 'task',
                            'bg-blue-50/70 dark:bg-blue-950/20': this.activity.type === 'meeting',
                            'bg-cyan-50/60 dark:bg-cyan-950/20': this.activity.type === 'call',
                        };
                    },

                    lookupAccentClass() {
                        return {
                            'border-violet-200': this.activity.type === 'task',
                            'border-blue-200': this.activity.type === 'meeting',
                            'border-cyan-200': this.activity.type === 'call',
                        };
                    },

                    lookupChipClass() {
                        return {
                            'bg-violet-100 text-violet-800 dark:bg-violet-900/50 dark:text-violet-200': this.activity.type === 'task',
                            'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200': this.activity.type === 'meeting',
                            'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/50 dark:text-cyan-200': this.activity.type === 'call',
                        };
                    },

                    lookupHoverClass() {
                        return {
                            'hover:bg-violet-50': this.activity.type === 'task',
                            'hover:bg-blue-50': this.activity.type === 'meeting',
                            'hover:bg-cyan-50': this.activity.type === 'call',
                        };
                    },
                },

                methods: {
                    parseScheduleValue(value) {
                        if (! value) {
                            return null;
                        }

                        const normalizedValue = String(value).replace('T', ' ').replace(/\.\d+Z?$/, '').replace(/Z$/, '');
                        const match = normalizedValue.match(/^(\d{4})-(\d{2})-(\d{2})(?:\s+(\d{2}):(\d{2})(?::(\d{2}))?)?/);

                        if (! match) {
                            const parsedDate = new Date(value);

                            return Number.isNaN(parsedDate.getTime()) ? null : parsedDate;
                        }

                        return new Date(
                            Number(match[1]),
                            Number(match[2]) - 1,
                            Number(match[3]),
                            Number(match[4] || 0),
                            Number(match[5] || 0),
                            Number(match[6] || 0)
                        );
                    },

                    formatDateInput(date) {
                        if (! date || Number.isNaN(date.getTime())) {
                            return '';
                        }

                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');

                        return `${year}-${month}-${day}`;
                    },

                    formatTimeInput(date) {
                        if (! date || Number.isNaN(date.getTime())) {
                            return '';
                        }

                        const hours = String(date.getHours()).padStart(2, '0');
                        const minutes = String(date.getMinutes()).padStart(2, '0');

                        return `${hours}:${minutes}`;
                    },

                    initializeFromActivity() {
                        if (this.activity.type === 'task' && this.activity.schedule_from) {
                            const dueDate = this.parseScheduleValue(this.activity.schedule_from);

                            this.form.due_date = this.formatDateInput(dueDate);
                        }

                        if (this.activity.type === 'meeting') {
                            const scheduleFrom = this.parseScheduleValue(this.activity.schedule_from);
                            const scheduleTo = this.parseScheduleValue(this.activity.schedule_to);

                            this.form.start_date = this.formatDateInput(scheduleFrom);
                            this.form.start_time = this.formatTimeInput(scheduleFrom);
                            this.form.end_date = this.formatDateInput(scheduleTo) || this.form.start_date;
                            this.form.end_time = this.formatTimeInput(scheduleTo) || this.form.start_time;
                        }

                        this.contacts.selected = (this.activity?.participants || [])
                            .map((participant) => participant?.person)
                            .filter((person) => person && person.id);

                        this.assignees.selected = (this.activity?.participants || [])
                            .map((participant) => participant?.user)
                            .filter((user) => user && user.id);

                        if (! this.assignees.selected.length && this.defaultAssignee && this.showsAssigneeLookup) {
                            this.assignees.selected = [{ ...this.defaultAssignee }];
                        }

                        const currentOrganization = this.activity?.organizations?.[0]
                            || (this.activity?.entity_type === 'organizations' ? this.entity : null);

                        if (currentOrganization?.id) {
                            this.organization.selected = {
                                id: currentOrganization.id,
                                name: currentOrganization.name,
                            };
                        }
                    },

                    prepareSubmit() {
                        this.closeLookups();
                    },

                    toggleLookup(type) {
                        this[type].open = ! this[type].open;

                        if (this[type].open) {
                            this.dispatchSearch(type);
                        }
                    },

                    openLookup(type) {
                        this[type].open = true;
                        this.dispatchSearch(type);
                    },

                    closeLookups() {
                        this.contacts.open = false;
                        this.organization.open = false;
                        this.assignees.open = false;
                    },

                    dispatchSearch(type) {
                        if (type === 'contacts') {
                            this.searchContacts();
                        }

                        if (type === 'organization') {
                            this.searchOrganizations();
                        }

                        if (type === 'assignees') {
                            this.searchAssignees();
                        }
                    },

                    runSearch(callback) {
                        clearTimeout(this.searchRequestTimeout);
                        this.searchRequestTimeout = setTimeout(callback, 200);
                    },

                    searchContacts() {
                        this.runSearch(() => {
                            this.contacts.loading = true;

                            this.$axios.get('{{ route('admin.activities.search_persons') }}', {
                                params: { query: this.contacts.search },
                            }).then((response) => {
                                const selectedIds = this.contacts.selected.map(item => Number(item.id));
                                this.contacts.results = (response.data.data || []).filter(item => !selectedIds.includes(Number(item.id)));
                                this.contacts.loading = false;
                            }).catch(() => {
                                this.contacts.results = [];
                                this.contacts.loading = false;
                            });
                        });
                    },

                    searchOrganizations() {
                        this.runSearch(() => {
                            this.organization.loading = true;

                            this.$axios.get('{{ route('admin.activities.search_organizations') }}', {
                                params: { query: this.organization.search },
                            }).then((response) => {
                                const selectedId = Number(this.organization.selected?.id || 0);
                                this.organization.results = (response.data.data || []).filter(item => Number(item.id) !== selectedId);
                                this.organization.loading = false;
                            }).catch(() => {
                                this.organization.results = [];
                                this.organization.loading = false;
                            });
                        });
                    },

                    searchAssignees() {
                        this.runSearch(() => {
                            this.assignees.loading = true;

                            this.$axios.get('{{ route('admin.activities.search_employee_users') }}', {
                                params: { query: this.assignees.search },
                            }).then((response) => {
                                const selectedIds = this.assignees.selected.map(item => Number(item.id));
                                this.assignees.results = (response.data.data || []).filter(item => !selectedIds.includes(Number(item.id)));
                                this.assignees.loading = false;
                            }).catch(() => {
                                this.assignees.results = [];
                                this.assignees.loading = false;
                            });
                        });
                    },

                    selectContact(person) {
                        if (this.contacts.selected.some(item => Number(item.id) === Number(person.id))) {
                            return;
                        }

                        this.contacts.selected.push(person);
                        this.contacts.search = '';
                        this.contacts.results = this.contacts.results.filter(item => Number(item.id) !== Number(person.id));
                    },

                    removeContact(contactId) {
                        this.contacts.selected = this.contacts.selected.filter(item => Number(item.id) !== Number(contactId));
                    },

                    selectOrganization(company) {
                        this.organization.selected = company;
                        this.organization.search = '';
                        this.organization.results = [];
                        this.organization.open = false;
                    },

                    clearOrganization() {
                        this.organization.selected = null;
                    },

                    selectAssignee(user) {
                        if (this.assignees.selected.some(item => Number(item.id) === Number(user.id))) {
                            return;
                        }

                        this.assignees.selected.push(user);
                        this.assignees.search = '';
                        this.assignees.results = this.assignees.results.filter(item => Number(item.id) !== Number(user.id));
                    },

                    removeAssignee(assigneeId) {
                        this.assignees.selected = this.assignees.selected.filter(item => Number(item.id) !== Number(assigneeId));
                    },

                    handleOutsideClick(event) {
                        if (this.$refs.contactsLookup && !this.$refs.contactsLookup.contains(event.target)) {
                            this.contacts.open = false;
                        }

                        if (this.$refs.organizationLookup && !this.$refs.organizationLookup.contains(event.target)) {
                            this.organization.open = false;
                        }

                        if (this.$refs.assigneesLookup && !this.$refs.assigneesLookup.contains(event.target)) {
                            this.assignees.open = false;
                        }
                    },
                },

                mounted() {
                    this.initializeFromActivity();
                    window.addEventListener('click', this.handleOutsideClick);
                },

                beforeUnmount() {
                    window.removeEventListener('click', this.handleOutsideClick);
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
