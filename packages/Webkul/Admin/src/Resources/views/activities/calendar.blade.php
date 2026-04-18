<x-admin::layouts>
    <x-slot:title>
        Activities Calendar
    </x-slot>

    <div class="mb-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <x-admin::breadcrumbs name="activities.calendar" />
                <h1 class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">Activities Calendar</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Track all upcoming events and activities in one timeline.</p>
            </div>

            <a
                href="{{ route('admin.activities.index', ['view-type' => 'table']) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:border-gray-600 dark:hover:bg-gray-800"
            >
                <span class="icon-list text-lg"></span>
                All Activities
            </a>
        </div>
    </div>

    <v-activities-calendar-page>
        <div class="light-shimmer-bg dark:shimmer h-[560px] rounded-xl"></div>
    </v-activities-calendar-page>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-activities-calendar-page-template">
            <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_340px]">
                <section class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2 px-1">
                        <div class="inline-flex rounded-lg border border-gray-200 p-1 dark:border-gray-700">
                            <button
                                type="button"
                                class="rounded-md px-3 py-1.5 text-xs font-semibold transition"
                                :class="calendarView === 'week' ? 'bg-brandColor text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800'"
                                @click="calendarView = 'week'"
                            >
                                Week
                            </button>

                            <button
                                type="button"
                                class="rounded-md px-3 py-1.5 text-xs font-semibold transition"
                                :class="calendarView === 'month' ? 'bg-brandColor text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800'"
                                @click="calendarView = 'month'"
                            >
                                Month
                            </button>
                        </div>

                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Click an event to open details.
                        </div>
                    </div>

                    <v-vue-cal
                        hide-view-selector
                        :active-view="calendarView"
                        :watchRealTime="true"
                        :twelveHour="true"
                        :disable-views="calendarView === 'week' ? ['years', 'year', 'month', 'day'] : ['years', 'year', 'week', 'day']"
                        style="height: calc(100vh - 270px); min-height: 500px;"
                        :class="{'vuecal--dark': theme === 'dark'}"
                        :events="events"
                        :time-format="'h:mm a'"
                        :events-on-month-view="'short'"
                        :events-count-on-year-view="3"
                        :overlapping-events-stacked="true"
                        :min-event-width="72"
                        :cell-click-hold="false"
                        :sticky-events="true"
                        :events-overlap="true"
                        :detailed-time="true"
                        @ready="getActivities"
                        @view-change="getActivities"
                        @event-click="goToActivity"
                        locale="{{ app()->getLocale() }}"
                    >
                        <template #event="{ event }">
                            <div class="vuecal__event-content">
                                <div class="truncate text-[12px] font-semibold">@{{ event.title }}</div>
                                <div class="mt-0.5 text-[11px] opacity-90">@{{ formatTime(event.start) }}</div>
                            </div>
                        </template>
                    </v-vue-cal>
                </section>

                <aside class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Upcoming Events</h2>
                        <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">@{{ upcomingEvents.length }}</span>
                    </div>

                    <div class="grid max-h-[calc(100vh-340px)] gap-2 overflow-y-auto pr-1">
                        <template v-if="upcomingEvents.length">
                            <a
                                v-for="event in upcomingEvents"
                                :key="`upcoming-${event.id}`"
                                :href="eventEditUrl(event.id)"
                                class="rounded-lg border border-gray-200 bg-gray-50 p-3 transition hover:-translate-y-[1px] hover:border-gray-300 hover:bg-white dark:border-gray-700 dark:bg-gray-800/60 dark:hover:border-gray-600 dark:hover:bg-gray-800"
                            >
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">@{{ event.title || 'Untitled activity' }}</div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">@{{ formatDateTime(event.start) }}</div>
                                <div v-if="event.description" class="mt-1 line-clamp-2 text-xs text-gray-600 dark:text-gray-300">@{{ event.description }}</div>
                            </a>
                        </template>

                        <template v-else>
                            <div class="rounded-lg border border-dashed border-gray-300 px-3 py-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                No upcoming activities.
                            </div>
                        </template>
                    </div>
                </aside>
            </div>
        </script>

        <script type="module">
            app.component('v-activities-calendar-page', {
                template: '#v-activities-calendar-page-template',

                data() {
                    return {
                        events: [],
                        calendarView: 'week',
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                        editRouteTemplate: @json(route('admin.activities.edit', ['id' => '__id__'])),
                    };
                },

                computed: {
                    upcomingEvents() {
                        const now = new Date();

                        return [...this.events]
                            .filter((event) => event.start && new Date(event.start) >= now)
                            .sort((a, b) => new Date(a.start) - new Date(b.start))
                            .slice(0, 12);
                    },
                },

                mounted() {
                    this.$emitter.on('change-theme', (theme) => {
                        this.theme = theme;
                    });
                },

                methods: {
                    getActivities({ startDate, endDate }) {
                        this.$axios.get("{{ route('admin.activities.get', ['view_type' => 'calendar']) }}" + `&startDate=${new Date(startDate).toLocaleDateString('en-US')}&endDate=${new Date(endDate).toLocaleDateString('en-US')}`)
                            .then((response) => {
                                this.events = this.processEvents(response?.data?.activities || []);
                            })
                            .catch(() => {
                                this.events = [];
                            });
                    },

                    processEvents(events) {
                        return events.map((event) => {
                            if (!event.background || ['#fff', '#ffffff'].includes(String(event.background).toLowerCase())) {
                                const palette = ['#0E90D9', '#10B981', '#F59E0B', '#6366F1', '#EF4444', '#06B6D4'];
                                const hash = this.hashString(String(event.id || event.title || '0'));

                                event.background = palette[Math.abs(hash) % palette.length];
                                event.textColor = '#ffffff';
                            }

                            return event;
                        });
                    },

                    hashString(str) {
                        let hash = 0;

                        for (let i = 0; i < str.length; i++) {
                            hash = ((hash << 5) - hash) + str.charCodeAt(i);
                            hash |= 0;
                        }

                        return hash;
                    },

                    formatTime(date) {
                        if (!date) {
                            return '';
                        }

                        const dateObj = new Date(date);
                        let hours = dateObj.getHours();
                        const minutes = dateObj.getMinutes().toString().padStart(2, '0');
                        const ampm = hours >= 12 ? 'PM' : 'AM';

                        hours = hours % 12;
                        hours = hours || 12;

                        return `${hours}:${minutes} ${ampm}`;
                    },

                    formatDateTime(value) {
                        if (!value) {
                            return '-';
                        }

                        const date = new Date(value);

                        if (Number.isNaN(date.getTime())) {
                            return String(value);
                        }

                        return date.toLocaleString(undefined, {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                        });
                    },

                    goToActivity(event) {
                        if (event?.id) {
                            window.location.href = this.eventEditUrl(event.id);
                        }
                    },

                    eventEditUrl(id) {
                        return this.editRouteTemplate.replace('__id__', String(id));
                    },
                },
            });
        </script>
    @endPushOnce

    @pushOnce('styles')
        <style>
            .vuecal__event {
                border: 0;
                border-radius: 10px;
                padding: 4px 7px;
                color: #fff !important;
                box-shadow: 0 7px 16px rgba(15, 23, 42, 0.16);
            }

            .vuecal__event:hover {
                transform: translateY(-1px);
                box-shadow: 0 11px 20px rgba(15, 23, 42, 0.2);
            }

            .vuecal__cell-more-events {
                border-radius: 999px;
                background: rgba(14, 144, 217, 0.1);
                color: #0369a1;
                font-size: 11px;
                font-weight: 700;
                padding: 3px 8px;
            }

            .vuecal__cell-events-count {
                border-radius: 999px;
                background: rgba(14, 144, 217, 0.84);
                padding: 0 6px;
                font-size: 11px;
                color: #fff;
            }

            .vuecal--dark {
                background: #111827 !important;
                color: #fff !important;
                border-color: #334155 !important;
            }

            .vuecal--dark .vuecal__header,
            .vuecal--dark .vuecal__header-weekdays,
            .vuecal--dark .vuecal__header-months {
                background: #1f2937 !important;
                color: #fff !important;
            }

            .vuecal--dark .vuecal__day,
            .vuecal--dark .vuecal__month-view,
            .vuecal--dark .vuecal__week-view,
            .vuecal--dark .vuecal__day--weekend,
            .vuecal--dark .vuecal__day--selected {
                background: #111827 !important;
                color: #fff !important;
                border-color: #374151 !important;
            }
        </style>
    @endPushOnce
</x-admin::layouts>
