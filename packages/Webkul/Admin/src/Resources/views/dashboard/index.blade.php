<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.dashboard.index.title')
    </x-slot>

    {!! view_render_event('admin.dashboard.index.header.before') !!}

    <div class="mb-5 flex items-center justify-between gap-4 max-sm:flex-wrap">
        {!! view_render_event('admin.dashboard.index.header.left.before') !!}

        <div class="grid gap-1.5">
            <p class="text-2xl font-semibold dark:text-white">
                Hi {{ auth()->guard('user')->user()->name ?? 'there' }}
            </p>

            <p class="text-sm text-gray-600 dark:text-gray-300">
                Welcome back. Here is your business snapshot with upcoming events and active tasks.
            </p>
        </div>

        {!! view_render_event('admin.dashboard.index.header.left.after') !!}

        {!! view_render_event('admin.dashboard.index.header.right.before') !!}

        <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-800 dark:bg-gray-900">
            <span class="text-gray-500 dark:text-gray-400">Sales: <strong class="text-gray-900 dark:text-white">USD</strong></span>
            <span class="text-gray-300 dark:text-gray-700">|</span>
            <span class="text-gray-500 dark:text-gray-400">Purchasing: <strong class="text-gray-900 dark:text-white">PKR</strong></span>
        </div>

        {!! view_render_event('admin.dashboard.index.header.right.after') !!}
    </div>

    {!! view_render_event('admin.dashboard.index.header.after') !!}

    <v-erp-dashboard>
        <div class="grid gap-4">
            <div class="light-shimmer-bg dark:shimmer h-[160px] rounded-xl"></div>
            <div class="light-shimmer-bg dark:shimmer h-[360px] rounded-xl"></div>
            <div class="light-shimmer-bg dark:shimmer h-[360px] rounded-xl"></div>
        </div>
    </v-erp-dashboard>

    @pushOnce('styles')
        <style>
            .erp-dashboard-card {
                border: 1px solid rgba(226, 232, 240, 0.9);
                border-radius: 14px;
                background: rgba(255, 255, 255, 0.98);
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
            }

            .dark .erp-dashboard-card {
                border-color: rgba(51, 65, 85, 0.95);
                background: rgba(17, 24, 39, 0.98);
                box-shadow: 0 12px 30px rgba(2, 6, 23, 0.25);
            }

            .erp-period-pill {
                border: 1px solid rgba(203, 213, 225, 1);
                background: #fff;
            }

            .dark .erp-period-pill {
                border-color: rgba(71, 85, 105, 0.8);
                background: rgba(15, 23, 42, 0.95);
            }

            .erp-stat-card {
                position: relative;
                overflow: hidden;
                border: 1px solid rgba(226, 232, 240, 0.9);
                border-radius: 14px;
                background:
                    linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98)),
                    radial-gradient(circle at top right, rgba(14, 144, 217, 0.08), transparent 55%);
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
            }

            .erp-stat-link {
                display: block;
                color: inherit;
                text-decoration: none;
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            }

            .erp-stat-link:hover {
                transform: translateY(-2px);
                border-color: color-mix(in srgb, var(--brand-color) 25%, white);
                box-shadow: 0 16px 30px rgba(15, 23, 42, 0.08);
            }

            .dark .erp-stat-card {
                border-color: rgba(51, 65, 85, 0.95);
                background: linear-gradient(180deg, rgba(17, 24, 39, 1), rgba(15, 23, 42, 1));
            }

            .erp-card-title {
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.18em;
                text-transform: uppercase;
                color: #64748b;
            }

            .dark .erp-card-title {
                color: #94a3b8;
            }

            .erp-mini-list {
                max-height: 308px;
                overflow: auto;
            }

            .erp-mini-list::-webkit-scrollbar {
                width: 6px;
            }

            .erp-mini-list::-webkit-scrollbar-thumb {
                background: rgba(148, 163, 184, 0.45);
                border-radius: 999px;
            }

            .erp-stat-card::before {
                content: "";
                position: absolute;
                inset: 0 auto auto 0;
                height: 3px;
                width: 100%;
                background: linear-gradient(90deg, var(--brand-color), rgba(14, 144, 217, 0.18));
            }

            .erp-stat-icon {
                height: 44px;
                width: 44px;
                border-radius: 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: var(--brand-color);
                background: color-mix(in srgb, var(--brand-color) 12%, white);
            }

            .dark .erp-stat-icon {
                background: color-mix(in srgb, var(--brand-color) 18%, #0f172a);
            }

            .erp-brand-chip {
                border: 1px solid color-mix(in srgb, var(--brand-color) 18%, white);
                background: color-mix(in srgb, var(--brand-color) 8%, white);
                color: var(--brand-color);
            }

            .dark .erp-brand-chip {
                border-color: color-mix(in srgb, var(--brand-color) 26%, #0f172a);
                background: color-mix(in srgb, var(--brand-color) 12%, #0f172a);
            }

            .erp-vertical-chart {
                border: 1px solid rgba(226, 232, 240, 0.78);
                border-radius: 12px;
                background: rgba(248, 250, 252, 0.72);
                padding: 14px;
            }

            .dark .erp-vertical-chart {
                border-color: rgba(51, 65, 85, 0.85);
                background: rgba(2, 6, 23, 0.48);
            }

            .erp-link-card {
                transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
            }

            .erp-link-card:hover {
                transform: translateY(-2px);
                border-color: color-mix(in srgb, var(--brand-color) 22%, white);
                box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
            }

            .erp-dashboard-shell {
                position: relative;
                overflow: hidden;
                border-radius: 18px;
                background:
                    radial-gradient(circle at top right, rgba(14, 144, 217, 0.1), transparent 45%),
                    linear-gradient(180deg, #f8fbff 0%, #f8fafc 100%);
                padding: 14px;
            }

            .dark .erp-dashboard-shell {
                background:
                    radial-gradient(circle at top right, rgba(14, 144, 217, 0.14), transparent 45%),
                    linear-gradient(180deg, rgba(2, 6, 23, 0.95), rgba(15, 23, 42, 0.95));
            }

            .erp-stats-row {
                display: grid;
                gap: 12px;
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }

            @media (min-width: 640px) {
                .erp-stats-row {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (min-width: 1280px) {
                .erp-stats-row {
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                }
            }

            .erp-action-row {
                transition: transform 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease;
            }

            .erp-action-row:hover {
                transform: translateY(-1px);
                border-color: color-mix(in srgb, var(--brand-color) 20%, white);
                box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
            }

            .dashboard-mini-calendar {
                border: 1px solid rgba(226, 232, 240, 0.95);
                border-radius: 12px;
                overflow: hidden;
                background: #fff;
            }

            .dashboard-mini-calendar .vuecal__header,
            .dashboard-mini-calendar .vuecal__weekdays-headings,
            .dashboard-mini-calendar .vuecal__title-bar {
                background: linear-gradient(90deg, #eef6ff, #f8fbff);
            }

            .dashboard-mini-calendar .vuecal__weekday {
                color: #334155;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.02em;
            }

            .dashboard-mini-calendar .vuecal__cell-date {
                color: #0f172a;
                font-size: 12px;
                font-weight: 600;
            }

            .dashboard-mini-calendar .vuecal__event {
                border: 0;
                border-radius: 8px;
                box-shadow: 0 6px 14px rgba(15, 23, 42, 0.16);
                padding: 2px 6px;
                cursor: pointer;
            }

            .dashboard-mini-calendar .vuecal__event:hover {
                transform: translateY(-1px);
                box-shadow: 0 10px 18px rgba(15, 23, 42, 0.2);
            }

            .dashboard-mini-calendar .mini-event-title {
                display: block;
                width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-size: 11px;
                font-weight: 700;
                line-height: 1.2;
            }

            .dashboard-mini-calendar .vuecal__cell-more-events {
                border-radius: 999px;
                background: rgba(14, 144, 217, 0.12);
                color: #075985;
                font-size: 11px;
                font-weight: 700;
            }

            .dark .dashboard-mini-calendar {
                border-color: #334155;
                background: #111827;
            }

            .dark .dashboard-mini-calendar .vuecal__header,
            .dark .dashboard-mini-calendar .vuecal__weekdays-headings,
            .dark .dashboard-mini-calendar .vuecal__title-bar {
                background: #1f2937;
            }

            .dark .dashboard-mini-calendar .vuecal__weekday,
            .dark .dashboard-mini-calendar .vuecal__cell-date,
            .dark .dashboard-mini-calendar .vuecal__title,
            .dark .dashboard-mini-calendar .vuecal__arrow {
                color: #f8fafc !important;
            }

            .dark .dashboard-mini-calendar .vuecal__day {
                background: #111827;
                border-color: #334155;
            }

            .dark .dashboard-mini-calendar .vuecal__cell-more-events {
                background: rgba(14, 144, 217, 0.24);
                color: #bfdbfe;
            }
        </style>
    @endPushOnce

    @pushOnce('scripts')
        <script
            type="module"
            src="{{ vite()->asset('js/chart.js') }}"
        ></script>

        <script
            type="text/x-template"
            id="v-erp-dashboard-template"
        >
            <div class="erp-dashboard-shell mt-3.5 grid gap-4">
                <section class="erp-stats-row">
                    <a :href="quoteStatus.routes?.open || '#'" class="erp-stat-card erp-stat-link p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="erp-card-title">Open Quotes</p>
                                <p class="mt-3 text-[30px] font-semibold leading-none text-gray-900 dark:text-white">@{{ quoteStatus.total_open }}</p>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Pending commercial decisions.</p>
                            </div>

                            <span class="erp-stat-icon">
                                <i class="icon-quote text-xl"></i>
                            </span>
                        </div>
                    </a>

                    <a :href="quoteStatus.routes?.closed || '#'" class="erp-stat-card erp-stat-link p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="erp-card-title">Closed Quotes</p>
                                <p class="mt-3 text-[30px] font-semibold leading-none text-gray-900 dark:text-white">@{{ quoteStatus.total_closed }}</p>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Decision finalized with notes.</p>
                            </div>

                            <span class="erp-stat-icon">
                                <i class="icon-check text-xl"></i>
                            </span>
                        </div>
                    </a>

                    <a :href="salesPurchasing.sales_route || '#'" class="erp-stat-card erp-stat-link p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="erp-card-title">Sales</p>
                                <p class="mt-3 text-[30px] font-semibold leading-none text-gray-900 dark:text-white">@{{ salesPurchasing.sales_total_formatted }}</p>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">@{{ salesPurchasing.date_range_label }}</p>
                            </div>

                            <span class="erp-stat-icon">
                                <i class="icon-sales-person text-xl"></i>
                            </span>
                        </div>
                    </a>

                    <a :href="salesPurchasing.purchasing_route || '#'" class="erp-stat-card erp-stat-link p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="erp-card-title">Purchasing</p>
                                <p class="mt-3 text-[30px] font-semibold leading-none text-gray-900 dark:text-white">@{{ salesPurchasing.purchasing_total_formatted }}</p>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">@{{ salesPurchasing.date_range_label }}</p>
                            </div>

                            <span class="erp-stat-icon">
                                <i class="icon-product text-xl"></i>
                            </span>
                        </div>
                    </a>
                </section>

                <div class="flex gap-4 max-xl:flex-wrap">
                    <div class="flex flex-1 flex-col gap-4 max-xl:flex-auto">
                        <section class="erp-dashboard-card p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="erp-card-title">Sales vs Purchasing</p>
                                    <h2 class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">Trend Comparison</h2>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="option in periodOptions"
                                        :key="`sales-${option.value}`"
                                        type="button"
                                        class="erp-period-pill rounded-full px-3 py-1.5 text-xs font-semibold transition-all"
                                        :class="filters.salesPeriod === option.value ? 'bg-brandColor text-white shadow-sm' : 'text-gray-600 hover:border-gray-300 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'"
                                        @click="updatePeriod('salesPeriod', option.value)"
                                    >
                                        @{{ option.label }}
                                    </button>
                                </div>
                            </div>

                            <div class="mt-5 h-[290px]">
                                <canvas ref="salesPurchasingChart"></canvas>
                            </div>
                        </section>

                        <section class="grid gap-4 xl:grid-cols-2">
                            <div class="erp-dashboard-card p-5">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="erp-card-title">Customers</p>
                                        <h2 class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">Top 5 Customers</h2>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="option in periodOptions"
                                            :key="`customers-${option.value}`"
                                            type="button"
                                            class="erp-period-pill rounded-full px-3 py-1.5 text-xs font-semibold transition-all"
                                            :class="filters.customersPeriod === option.value ? 'bg-brandColor text-white shadow-sm' : 'text-gray-600 hover:border-gray-300 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'"
                                            @click="updatePeriod('customersPeriod', option.value)"
                                        >
                                            @{{ option.label }}
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-5 grid gap-4">
                                    <div class="erp-vertical-chart h-[250px]">
                                        <canvas ref="topCustomersChart"></canvas>
                                    </div>

                                    <div class="erp-mini-list grid gap-3">
                                        <a
                                            v-for="(customer, index) in topCustomers.items"
                                            :key="customer.name + '-' + index"
                                            :href="customer.route || '#'"
                                            class="erp-link-card block rounded-xl border border-gray-200 bg-gray-50/70 p-3 dark:border-gray-800 dark:bg-gray-950/60"
                                        >
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">@{{ customer.name }}</p>
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">@{{ customer.invoice_count }} proforma invoice(s)</p>
                                                </div>

                                                <div class="text-right">
                                                    <p class="text-sm font-semibold text-brandColor">@{{ customer.total_amount_formatted }}</p>
                                                </div>
                                            </div>
                                        </a>

                                        <div v-if="! topCustomers.items.length" class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                            No customer sales found for the selected period.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="erp-dashboard-card p-5">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="erp-card-title">Products</p>
                                        <h2 class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">Best Products</h2>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="option in periodOptions"
                                            :key="`products-${option.value}`"
                                            type="button"
                                            class="erp-period-pill rounded-full px-3 py-1.5 text-xs font-semibold transition-all"
                                            :class="filters.productsPeriod === option.value ? 'bg-brandColor text-white shadow-sm' : 'text-gray-600 hover:border-gray-300 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'"
                                            @click="updatePeriod('productsPeriod', option.value)"
                                        >
                                            @{{ option.label }}
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-5 grid gap-4">
                                    <div class="erp-vertical-chart h-[250px]">
                                        <canvas ref="bestProductsChart"></canvas>
                                    </div>

                                    <div class="erp-mini-list grid gap-3">
                                        <a
                                            v-for="(product, index) in bestProducts.items"
                                            :key="product.name + '-' + index"
                                            :href="product.route || '#'"
                                            class="erp-link-card block rounded-xl border border-gray-200 bg-gray-50/70 p-3 dark:border-gray-800 dark:bg-gray-950/60"
                                        >
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">@{{ product.name }}</p>
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Code: @{{ product.code || '-' }}</p>
                                                </div>

                                                <div class="text-right">
                                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">@{{ product.qty }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">@{{ product.invoice_count }} invoice(s)</p>
                                                </div>
                                            </div>
                                        </a>

                                        <div v-if="! bestProducts.items.length" class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                            No product movement found for the selected period.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="flex w-[378px] max-w-full flex-col gap-4 max-sm:w-full">
                        <div class="erp-dashboard-card p-4">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <div>
                                    <p class="erp-card-title">Calendar</p>
                                    <h2 class="mt-1 text-base font-semibold text-gray-900 dark:text-white">Upcoming Activities</h2>
                                </div>

                                <a :href="calendarPageUrl" class="text-xs font-semibold text-brandColor hover:underline">
                                    Open calendar
                                </a>
                            </div>

                            <v-vue-cal
                                class="dashboard-mini-calendar"
                                hide-view-selector
                                active-view="month"
                                :watchRealTime="true"
                                :twelveHour="true"
                                :disable-views="['years', 'year', 'week', 'day']"
                                :events="miniCalendarEvents"
                                :time-format="'h:mm a'"
                                :events-on-month-view="'short'"
                                :cell-click-hold="false"
                                :sticky-events="true"
                                :events-overlap="true"
                                :class="{'vuecal--dark': isDarkMode()}"
                                style="height: 280px;"
                                @ready="getMiniCalendarActivities"
                                @view-change="getMiniCalendarActivities"
                                @event-click="showEventDetails"
                                locale="{{ app()->getLocale() }}"
                            >
                                <template #event="{ event }">
                                    <div
                                        class="vuecal__event-content"
                                        @click.stop="showEventDetails(event)"
                                        v-tooltip="{
                                            content: eventTooltip(event),
                                            html: true,
                                            placement: 'top',
                                            trigger: 'hover',
                                            delay: { show: 120, hide: 80 }
                                        }"
                                    >
                                        <div class="mini-event-title">@{{ event.title }}</div>
                                    </div>
                                </template>
                            </v-vue-cal>

                            <div
                                v-if="selectedEvent"
                                class="mt-3 rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/70"
                            >
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">@{{ selectedEvent.title || 'Untitled event' }}</div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-300">@{{ formatDateTime(selectedEvent.start) }} - @{{ formatDateTime(selectedEvent.end) }}</div>
                                <div v-if="selectedEvent.description" class="mt-2 text-xs text-gray-600 dark:text-gray-300">@{{ selectedEvent.description }}</div>

                                <button
                                    type="button"
                                    class="mt-3 rounded-md bg-brandColor px-3 py-1.5 text-xs font-semibold text-white"
                                    @click="openSelectedEvent"
                                >
                                    Open details
                                </button>
                            </div>
                        </div>

                        <div class="erp-dashboard-card p-4">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <div>
                                    <p class="erp-card-title">Tasks</p>
                                    <h2 class="mt-1 text-base font-semibold text-gray-900 dark:text-white">Latest Active Tasks</h2>
                                </div>

                                <a :href="tasksViewAllUrl" class="text-xs font-semibold text-brandColor hover:underline">
                                    View all
                                </a>
                            </div>

                            <div class="grid gap-2">
                                <template v-if="latestTasks.length">
                                    <a
                                        v-for="task in latestTasks"
                                        :key="`dashboard-task-${task.id}`"
                                        :href="taskEditUrl(task.id)"
                                        class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 transition hover:-translate-y-[1px] hover:border-gray-300 hover:bg-white dark:border-gray-700 dark:bg-gray-900 dark:hover:border-gray-600"
                                    >
                                        <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">@{{ task.title || 'Untitled task' }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Due: @{{ formatShortDate(task.schedule_from) }}</p>
                                    </a>
                                </template>

                                <div v-else class="rounded-lg border border-dashed border-gray-300 px-3 py-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                    No active tasks due.
                                </div>
                            </div>
                        </div>

                        <div class="erp-dashboard-card p-5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="erp-card-title">Quotes</p>
                                    <h2 class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">Status Split</h2>
                                </div>

                                <a :href="quoteStatus.all_route || '#'" class="erp-brand-chip rounded-full px-3 py-1 text-xs font-semibold">
                                    Open: @{{ quoteStatus.total_open }}
                                </a>
                            </div>

                            <div class="mt-5 h-[235px]">
                                <canvas ref="quoteStatusChart"></canvas>
                            </div>

                            <div class="mt-4 grid gap-2">
                                <component
                                    v-for="item in quoteStatus.items"
                                    :key="item.label"
                                    :is="item.route ? 'a' : 'div'"
                                    :href="item.route || undefined"
                                    class="erp-action-row flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50/70 px-3 py-2 dark:border-gray-800 dark:bg-gray-950/60"
                                >
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full" :style="{ backgroundColor: item.color }"></span>
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">@{{ item.label }}</span>
                                    </div>

                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">@{{ item.value }}</span>
                                </component>
                            </div>
                        </div>

                        <div class="erp-dashboard-card p-5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="erp-card-title">Cases</p>
                                    <h2 class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">Pipeline Status</h2>
                                </div>

                                <a :href="casesByStage.all_route || '#'" class="erp-brand-chip rounded-full px-3 py-1 text-xs font-semibold">
                                    Total Cases: @{{ casesByStage.total }}
                                </a>
                            </div>

                            <div class="mt-5 h-[270px]">
                                <canvas ref="casesChart"></canvas>
                            </div>
                            <div class="mt-4 grid gap-2">
                                <component
                                    v-for="item in casesByStage.items"
                                    :key="item.label"
                                    :is="item.route ? 'a' : 'div'"
                                    :href="item.route || undefined"
                                    class="erp-action-row flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50/70 px-3 py-2 dark:border-gray-800 dark:bg-gray-950/60"
                                >
                                    <span class="text-sm text-gray-600 dark:text-gray-300">@{{ item.label }}</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">@{{ item.value }}</span>
                                </component>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-erp-dashboard', {
                template: '#v-erp-dashboard-template',

                data() {
                    return {
                        loading: true,
                        periodOptions: [],
                        filters: {
                            salesPeriod: '7d',
                            customersPeriod: '1y',
                            productsPeriod: '1y',
                        },
                        quoteStatus: {
                            total_open: 0,
                            total_closed: 0,
                            items: [],
                        },
                        casesByStage: {
                            total: 0,
                            items: [],
                        },
                        salesPurchasing: {
                            date_range_label: '',
                            labels: [],
                            sales: [],
                            purchasing: [],
                            sales_total_formatted: 'USD 0.00',
                            purchasing_total_formatted: 'PKR 0.00',
                        },
                        topCustomers: {
                            items: [],
                        },
                        bestProducts: {
                            items: [],
                        },
                        miniCalendarEvents: [],
                        selectedEvent: null,
                        latestTasks: [],
                        tasksViewAllUrl: @json(route('admin.activities.my_tasks')),
                        activityEditRouteTemplate: @json(route('admin.activities.edit', ['id' => '__id__'])),
                        calendarPageUrl: @json(route('admin.activities.calendar')),
                        charts: {},
                    };
                },

                mounted() {
                    this.fetchDashboard();
                    this.fetchLatestTasks();
                },

                methods: {
                    updatePeriod(filterKey, value) {
                        if (this.filters[filterKey] === value) {
                            return;
                        }

                        this.filters[filterKey] = value;
                        this.fetchDashboard();
                    },

                    fetchDashboard() {
                        this.loading = true;

                        this.$axios.get("{{ route('admin.dashboard.stats') }}", {
                            params: {
                                type: 'erp-overview',
                                sales_period: this.filters.salesPeriod,
                                customers_period: this.filters.customersPeriod,
                                products_period: this.filters.productsPeriod,
                            },
                        }).then((response) => {
                            const stats = response?.data?.statistics || {};

                            this.periodOptions = stats.period_options || [];
                            this.quoteStatus = stats.quote_status || this.quoteStatus;
                            this.casesByStage = stats.cases_by_stage || this.casesByStage;
                            this.salesPurchasing = stats.sales_purchasing || this.salesPurchasing;
                            this.topCustomers = stats.top_customers || this.topCustomers;
                            this.bestProducts = stats.best_products || this.bestProducts;

                            this.$nextTick(() => {
                                this.renderCharts();
                            });
                        }).finally(() => {
                            this.loading = false;
                        });
                    },

                    fetchLatestTasks() {
                        this.$axios.get(@json(route('admin.activities.my_tasks_summary')))
                            .then((response) => {
                                const payload = response?.data || {};

                                this.latestTasks = Array.isArray(payload.tasks) ? payload.tasks.slice(0, 5) : [];

                                if (payload.view_all_url) {
                                    this.tasksViewAllUrl = payload.view_all_url;
                                }
                            })
                            .catch(() => {
                                this.latestTasks = [];
                            });
                    },

                    getMiniCalendarActivities({ startDate, endDate }) {
                        this.$axios.get("{{ route('admin.activities.get', ['view_type' => 'calendar']) }}" + `&startDate=${new Date(startDate).toLocaleDateString('en-US')}&endDate=${new Date(endDate).toLocaleDateString('en-US')}`)
                            .then((response) => {
                                this.miniCalendarEvents = this.processMiniEvents(response?.data?.activities || []);

                                if (this.selectedEvent?.id) {
                                    this.selectedEvent = this.miniCalendarEvents.find((event) => Number(event.id) === Number(this.selectedEvent.id)) || null;
                                }
                            })
                            .catch(() => {
                                this.miniCalendarEvents = [];
                                this.selectedEvent = null;
                            });
                    },

                    processMiniEvents(events) {
                        return events.map((event) => {
                            if (! event.background || ['#fff', '#ffffff'].includes(String(event.background).toLowerCase())) {
                                const colors = ['#0E90D9', '#10B981', '#F59E0B', '#6366F1', '#EF4444'];
                                const hash = this.hashString(String(event.id || event.title || '0'));

                                event.background = colors[Math.abs(hash) % colors.length];
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

                    formatShortDate(value) {
                        if (! value) {
                            return '-';
                        }

                        const date = new Date(value);

                        if (Number.isNaN(date.getTime())) {
                            return value;
                        }

                        return date.toLocaleDateString(undefined, {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit',
                        });
                    },

                    formatDateTime(value) {
                        if (! value) {
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

                    eventTooltip(event) {
                        const title = event?.title || 'Untitled event';
                        const description = event?.description ? `<div class='mt-1 text-xs text-gray-200'>${event.description}</div>` : '';

                        return `
                            <div class='min-w-[220px]'>
                                <div class='text-sm font-semibold text-white'>${title}</div>
                                <div class='mt-1 text-xs text-gray-300'>${this.formatDateTime(event?.start)} - ${this.formatDateTime(event?.end)}</div>
                                ${description}
                            </div>
                        `;
                    },

                    taskEditUrl(id) {
                        return this.activityEditRouteTemplate.replace('__id__', String(id));
                    },

                    showEventDetails(event) {
                        if (! event) {
                            return;
                        }

                        this.selectedEvent = event;
                    },

                    openSelectedEvent() {
                        if (! this.selectedEvent?.id) {
                            return;
                        }

                        window.location.href = this.taskEditUrl(this.selectedEvent.id);
                    },

                    goToActivity(event) {
                        if (event?.id) {
                            window.location.href = this.taskEditUrl(event.id);
                        }
                    },

                    renderCharts() {
                        if (typeof Chart === 'undefined') {
                            return;
                        }

                        this.renderQuoteStatusChart();
                        this.renderCasesChart();
                        this.renderSalesPurchasingChart();
                        this.renderTopCustomersChart();
                        this.renderBestProductsChart();
                    },

                    destroyChart(name) {
                        if (this.charts[name]) {
                            this.charts[name].destroy();
                            this.charts[name] = null;
                        }
                    },

                    isDarkMode() {
                        return document.documentElement.classList.contains('dark');
                    },

                    commonTickColor() {
                        return this.isDarkMode() ? '#cbd5e1' : '#475569';
                    },

                    commonGridColor() {
                        return this.isDarkMode() ? 'rgba(148, 163, 184, 0.12)' : 'rgba(148, 163, 184, 0.18)';
                    },

                    brandColor() {
                        return getComputedStyle(document.documentElement).getPropertyValue('--brand-color').trim() || '#0E90D9';
                    },

                    withAlpha(hex, alpha) {
                        if (! hex.startsWith('#') || (hex.length !== 7 && hex.length !== 4)) {
                            return `rgba(14, 144, 217, ${alpha})`;
                        }

                        const normalized = hex.length === 4
                            ? `#${hex[1]}${hex[1]}${hex[2]}${hex[2]}${hex[3]}${hex[3]}`
                            : hex;

                        const r = parseInt(normalized.slice(1, 3), 16);
                        const g = parseInt(normalized.slice(3, 5), 16);
                        const b = parseInt(normalized.slice(5, 7), 16);

                        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
                    },

                    renderQuoteStatusChart() {
                        this.destroyChart('quoteStatus');

                        const canvas = this.$refs.quoteStatusChart;

                        if (! canvas) {
                            return;
                        }

                        this.charts.quoteStatus = new Chart(canvas, {
                            type: 'doughnut',
                            data: {
                                labels: this.quoteStatus.items.map(item => item.label),
                                datasets: [{
                                    data: this.quoteStatus.items.map(item => item.value),
                                    backgroundColor: [
                                        this.brandColor(),
                                        '#0f766e',
                                    ],
                                    borderWidth: 0,
                                    hoverOffset: 8,
                                }],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '68%',
                                plugins: {
                                    legend: {
                                        display: false,
                                    },
                                },
                            },
                        });
                    },

                    renderCasesChart() {
                        this.destroyChart('cases');

                        const canvas = this.$refs.casesChart;

                        if (! canvas) {
                            return;
                        }

                        this.charts.cases = new Chart(canvas, {
                            type: 'bar',
                            data: {
                                labels: this.casesByStage.items.map(item => item.label),
                                datasets: [{
                                    label: 'Cases',
                                    data: this.casesByStage.items.map(item => item.value),
                                    borderRadius: 10,
                                    backgroundColor: this.brandColor(),
                                    maxBarThickness: 36,
                                }],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                indexAxis: 'y',
                                plugins: {
                                    legend: {
                                        display: false,
                                    },
                                },
                                scales: {
                                    x: {
                                        ticks: {
                                            color: this.commonTickColor(),
                                        },
                                        grid: {
                                            color: this.commonGridColor(),
                                        },
                                    },
                                    y: {
                                        ticks: {
                                            color: this.commonTickColor(),
                                        },
                                        grid: {
                                            display: false,
                                        },
                                    },
                                },
                            },
                        });
                    },

                    renderSalesPurchasingChart() {
                        this.destroyChart('salesPurchasing');

                        const canvas = this.$refs.salesPurchasingChart;

                        if (! canvas) {
                            return;
                        }

                        this.charts.salesPurchasing = new Chart(canvas, {
                            type: 'line',
                            data: {
                                labels: this.salesPurchasing.labels,
                                datasets: [
                                    {
                                        label: 'Sales (USD)',
                                        data: this.salesPurchasing.sales,
                                        borderColor: this.brandColor(),
                                        backgroundColor: this.withAlpha(this.brandColor(), 0.14),
                                        tension: 0.35,
                                        fill: true,
                                        yAxisID: 'ySales',
                                    },
                                    {
                                        label: 'Purchasing (PKR)',
                                        data: this.salesPurchasing.purchasing,
                                        borderColor: '#0f766e',
                                        backgroundColor: 'rgba(15, 118, 110, 0.08)',
                                        tension: 0.35,
                                        fill: true,
                                        yAxisID: 'yPurchasing',
                                    }
                                ],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                plugins: {
                                    legend: {
                                        labels: {
                                            color: this.commonTickColor(),
                                        },
                                    },
                                },
                                scales: {
                                    x: {
                                        ticks: {
                                            color: this.commonTickColor(),
                                        },
                                        grid: {
                                            color: this.commonGridColor(),
                                        },
                                    },
                                    ySales: {
                                        position: 'left',
                                        ticks: {
                                            color: this.brandColor(),
                                        },
                                        grid: {
                                            color: this.commonGridColor(),
                                        },
                                    },
                                    yPurchasing: {
                                        position: 'right',
                                        ticks: {
                                            color: '#0f766e',
                                        },
                                        grid: {
                                            drawOnChartArea: false,
                                        },
                                    },
                                },
                            },
                        });
                    },

                    renderTopCustomersChart() {
                        this.destroyChart('topCustomers');

                        const canvas = this.$refs.topCustomersChart;

                        if (! canvas) {
                            return;
                        }

                        this.charts.topCustomers = new Chart(canvas, {
                            type: 'bar',
                            data: {
                                labels: this.topCustomers.items.map(item => item.name),
                                datasets: [{
                                    label: 'Sales (USD)',
                                    data: this.topCustomers.items.map(item => item.total_amount),
                                    borderRadius: 10,
                                    backgroundColor: this.brandColor(),
                                    maxBarThickness: 26,
                                }],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                indexAxis: 'y',
                                plugins: {
                                    legend: {
                                        display: false,
                                    },
                                },
                                scales: {
                                    x: {
                                        ticks: {
                                            color: this.commonTickColor(),
                                        },
                                        grid: {
                                            color: this.commonGridColor(),
                                        },
                                    },
                                    y: {
                                        ticks: {
                                            color: this.commonTickColor(),
                                        },
                                        grid: {
                                            display: false,
                                        },
                                    },
                                },
                            },
                        });
                    },

                    renderBestProductsChart() {
                        this.destroyChart('bestProducts');

                        const canvas = this.$refs.bestProductsChart;

                        if (! canvas) {
                            return;
                        }

                        this.charts.bestProducts = new Chart(canvas, {
                            type: 'bar',
                            data: {
                                labels: this.bestProducts.items.map(item => item.name),
                                datasets: [{
                                    label: 'Quantity',
                                    data: this.bestProducts.items.map(item => item.qty),
                                    borderRadius: 10,
                                    backgroundColor: this.brandColor(),
                                    maxBarThickness: 28,
                                }],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                indexAxis: 'y',
                                plugins: {
                                    legend: {
                                        display: false,
                                    },
                                },
                                scales: {
                                    x: {
                                        ticks: {
                                            color: this.commonTickColor(),
                                        },
                                        grid: {
                                            color: this.commonGridColor(),
                                        },
                                    },
                                    y: {
                                        ticks: {
                                            color: this.commonTickColor(),
                                        },
                                        grid: {
                                            display: false,
                                        },
                                    },
                                },
                            },
                        });
                    },
                },

                beforeUnmount() {
                    Object.keys(this.charts).forEach((chartKey) => this.destroyChart(chartKey));
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
