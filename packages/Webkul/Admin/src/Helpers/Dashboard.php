<?php

namespace Webkul\Admin\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Admin\Helpers\Reporting\Activity;
use Webkul\Admin\Helpers\Reporting\Lead;
use Webkul\Admin\Helpers\Reporting\Organization;
use Webkul\Admin\Helpers\Reporting\Person;
use Webkul\Admin\Helpers\Reporting\Product;
use Webkul\Admin\Helpers\Reporting\Quote;

class Dashboard
{
    /**
     * Create a controller instance.
     *
     * @return void
     */
    public function __construct(
        protected Lead $leadReporting,
        protected Activity $activityReporting,
        protected Product $productReporting,
        protected Person $personReporting,
        protected Organization $organizationReporting,
        protected Quote $quoteReporting,
    ) {}

    /**
     * Returns the overall revenue statistics.
     */
    public function getRevenueStats(): array
    {
        return [
            'total_won_revenue'  => $this->leadReporting->getTotalWonLeadValueProgress(),
            'total_lost_revenue' => $this->leadReporting->getTotalLostLeadValueProgress(),
        ];
    }

    /**
     * Returns the overall statistics.
     */
    public function getOverAllStats(): array
    {
        return [
            'total_leads'           => $this->leadReporting->getTotalLeadsProgress(),
            'average_lead_value'    => $this->leadReporting->getAverageLeadValueProgress(),
            'average_leads_per_day' => $this->leadReporting->getAverageLeadsPerDayProgress(),
            'total_quotations'      => $this->quoteReporting->getTotalQuotesProgress(),
            'total_persons'         => $this->personReporting->getTotalPersonsProgress(),
            'total_organizations'   => $this->organizationReporting->getTotalOrganizationsProgress(),
        ];
    }

    /**
     * Returns leads statistics.
     */
    public function getTotalLeadsStats(): array
    {
        return [
            'all'  => [
                'over_time' => $this->leadReporting->getTotalLeadsOverTime(),
            ],

            'won'  => [
                'over_time' => $this->leadReporting->getTotalWonLeadsOverTime(),
            ],
            'lost' => [
                'over_time' => $this->leadReporting->getTotalLostLeadsOverTime(),
            ],
        ];
    }

    /**
     * Returns leads revenue statistics by sources.
     */
    public function getLeadsStatsBySources(): mixed
    {
        return $this->leadReporting->getTotalWonLeadValueBySources();
    }

    /**
     * Returns leads revenue statistics by types.
     */
    public function getLeadsStatsByTypes(): mixed
    {
        return $this->leadReporting->getTotalWonLeadValueByTypes();
    }

    /**
     * Returns open leads statistics by states.
     */
    public function getOpenLeadsByStates(): mixed
    {
        return $this->leadReporting->getOpenLeadsByStates();
    }

    /**
     * Returns top selling products statistics.
     */
    public function getTopSellingProducts(): Collection
    {
        return $this->productReporting->getTopSellingProductsByRevenue(5);
    }

    /**
     * Returns top selling products statistics.
     */
    public function getTopPersons(): Collection
    {
        return $this->personReporting->getTopCustomersByRevenue(5);
    }

    /**
     * Returns the ERP dashboard statistics.
     */
    public function getErpOverview(): array
    {
        $salesPeriod = $this->normalizePeriodKey(request()->query('sales_period'), '7d');
        $customersPeriod = $this->normalizePeriodKey(request()->query('customers_period'), '1y');
        $productsPeriod = $this->normalizePeriodKey(request()->query('products_period'), '1y');

        return [
            'period_options' => $this->getPeriodOptions(),
            'quote_status' => $this->getQuoteStatusStats(),
            'cases_by_stage' => $this->getCasesByStageStats(),
            'sales_purchasing' => $this->getSalesPurchasingStats($salesPeriod),
            'top_customers' => $this->getTopCustomersStats($customersPeriod),
            'best_products' => $this->getBestProductsStats($productsPeriod),
        ];
    }

    /**
     * Get the start date.
     *
     * @return \Carbon\Carbon
     */
    public function getStartDate(): Carbon
    {
        return $this->leadReporting->getStartDate();
    }

    /**
     * Get the end date.
     *
     * @return \Carbon\Carbon
     */
    public function getEndDate(): Carbon
    {
        return $this->leadReporting->getEndDate();
    }

    /**
     * Returns date range
     */
    public function getDateRange(): string
    {
        return $this->getStartDate()->format('d M').' - '.$this->getEndDate()->format('d M');
    }

    protected function getQuoteStatusStats(): array
    {
        $summary = DB::table('quotes')
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->get()
            ->reduce(function (array $carry, $row) {
                $status = $this->normalizeQuoteLifecycleStatus($row->status);

                $carry[$status] = ($carry[$status] ?? 0) + (int) $row->aggregate;

                return $carry;
            }, ['open' => 0, 'closed' => 0]);

        return [
            'total_open' => $summary['open'] ?? 0,
            'total_closed' => $summary['closed'] ?? 0,
            'all_route' => route('admin.quotes.index'),
            'routes' => [
                'open' => route('admin.quotes.index', ['status' => 'open']),
                'closed' => route('admin.quotes.index', ['status' => 'closed']),
            ],
            'items' => [
                [
                    'label' => 'Open Quotes',
                    'value' => $summary['open'] ?? 0,
                    'color' => '#c2410c',
                    'route' => route('admin.quotes.index', ['status' => 'open']),
                ],
                [
                    'label' => 'Closed Quotes',
                    'value' => $summary['closed'] ?? 0,
                    'color' => '#0f766e',
                    'route' => route('admin.quotes.index', ['status' => 'closed']),
                ],
            ],
        ];
    }

    protected function getCasesByStageStats(): array
    {
        $items = DB::table('lead_pipeline_stages')
            ->leftJoin('leads', 'leads.lead_pipeline_stage_id', '=', 'lead_pipeline_stages.id')
            ->select(
                'lead_pipeline_stages.id',
                'lead_pipeline_stages.name',
                'lead_pipeline_stages.code',
                'lead_pipeline_stages.sort_order'
            )
            ->selectRaw('COUNT(leads.id) as aggregate')
            ->groupBy(
                'lead_pipeline_stages.id',
                'lead_pipeline_stages.name',
                'lead_pipeline_stages.code',
                'lead_pipeline_stages.sort_order'
            )
            ->orderBy('lead_pipeline_stages.sort_order')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->name,
                'code' => $row->code,
                'value' => (int) $row->aggregate,
                'route' => route('admin.leads.index', ['stage' => $row->id]),
            ])
            ->values()
            ->all();

        return [
            'total' => array_sum(array_column($items, 'value')),
            'all_route' => route('admin.leads.index'),
            'items' => $items,
        ];
    }

    protected function getSalesPurchasingStats(string $periodKey): array
    {
        $range = $this->resolvePeriodRange($periodKey, $this->getEarliestDocumentDate());
        $salesSeries = $this->getDocumentSeries(
            'proforma_invoices',
            'COALESCE(issue_date, created_at)',
            'grand_total',
            $range,
            fn ($query) => $query->where('status', '!=', 'cancelled')
        );
        $purchasingSeries = $this->getDocumentSeries(
            'purchase_orders',
            'COALESCE(completion_date, created_at)',
            'grand_total',
            $range,
            fn ($query) => $query->where('status', '!=', 'cancelled')
        );

        $salesTotal = array_sum($salesSeries['values']);
        $purchasingTotal = array_sum($purchasingSeries['values']);

        return [
            'selected_period' => $periodKey,
            'date_range_label' => $range['label'],
            'labels' => $salesSeries['labels'],
            'sales' => $salesSeries['values'],
            'purchasing' => $purchasingSeries['values'],
            'sales_route' => route('admin.proforma_invoices.index'),
            'purchasing_route' => route('admin.purchase_orders.index'),
            'sales_total' => $salesTotal,
            'purchasing_total' => $purchasingTotal,
            'sales_total_formatted' => $this->formatMoney($salesTotal, 'USD'),
            'purchasing_total_formatted' => $this->formatMoney($purchasingTotal, 'PKR'),
        ];
    }

    protected function getTopCustomersStats(string $periodKey): array
    {
        $range = $this->resolvePeriodRange($periodKey, $this->getEarliestSalesDate());

        $items = DB::table('proforma_invoices')
            ->leftJoin('organizations', 'organizations.id', '=', 'proforma_invoices.organization_id')
            ->select(
                'proforma_invoices.organization_id',
                DB::raw("COALESCE(organizations.name, 'Unknown Customer') as organization_name")
            )
            ->selectRaw('COUNT(proforma_invoices.id) as invoice_count')
            ->selectRaw('SUM(proforma_invoices.grand_total) as total_amount')
            ->where('proforma_invoices.status', '!=', 'cancelled')
            ->whereRaw(
                "DATE(COALESCE(proforma_invoices.issue_date, proforma_invoices.created_at)) BETWEEN ? AND ?",
                [$range['start']->toDateString(), $range['end']->toDateString()]
            )
            ->groupBy('proforma_invoices.organization_id', 'organizations.name')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'id' => $row->organization_id ? (int) $row->organization_id : null,
                'name' => $row->organization_name,
                'invoice_count' => (int) $row->invoice_count,
                'total_amount' => (float) $row->total_amount,
                'total_amount_formatted' => $this->formatMoney((float) $row->total_amount, 'USD'),
                'route' => $row->organization_id ? route('admin.contacts.organizations.view', $row->organization_id) : null,
            ])
            ->values()
            ->all();

        return [
            'selected_period' => $periodKey,
            'date_range_label' => $range['label'],
            'items' => $items,
        ];
    }

    protected function getBestProductsStats(string $periodKey): array
    {
        $range = $this->resolvePeriodRange($periodKey, $this->getEarliestSalesDate());

        $items = DB::table('proforma_invoice_items')
            ->join('proforma_invoices', 'proforma_invoices.id', '=', 'proforma_invoice_items.proforma_invoice_id')
            ->leftJoin('products', 'products.id', '=', 'proforma_invoice_items.product_id')
            ->select('proforma_invoice_items.product_id')
            ->selectRaw("
                COALESCE(products.name, proforma_invoice_items.item_name, 'Unnamed Product') as product_name,
                COALESCE(products.internal_code, products.sku, proforma_invoice_items.item_code, '-') as product_code
            ")
            ->selectRaw('SUM(proforma_invoice_items.qty) as total_qty')
            ->selectRaw('COUNT(DISTINCT proforma_invoices.id) as invoice_count')
            ->where('proforma_invoices.status', '!=', 'cancelled')
            ->whereRaw(
                "DATE(COALESCE(proforma_invoices.issue_date, proforma_invoices.created_at)) BETWEEN ? AND ?",
                [$range['start']->toDateString(), $range['end']->toDateString()]
            )
            ->groupBy(
                'proforma_invoice_items.product_id',
                DB::raw("COALESCE(products.name, proforma_invoice_items.item_name, 'Unnamed Product')"),
                DB::raw("COALESCE(products.internal_code, products.sku, proforma_invoice_items.item_code, '-')")
            )
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'id' => $row->product_id ? (int) $row->product_id : null,
                'name' => $row->product_name,
                'code' => $row->product_code,
                'qty' => round((float) $row->total_qty, 2),
                'invoice_count' => (int) $row->invoice_count,
                'route' => $row->product_id ? route('admin.products.view', $row->product_id) : null,
            ])
            ->values()
            ->all();

        return [
            'selected_period' => $periodKey,
            'date_range_label' => $range['label'],
            'items' => $items,
        ];
    }

    protected function getDocumentSeries(
        string $table,
        string $dateExpression,
        string $amountColumn,
        array $range,
        ?callable $scope = null
    ): array {
        $bucketSql = $range['bucket'] === 'day'
            ? "DATE($dateExpression)"
            : "DATE_FORMAT($dateExpression, '%Y-%m-01')";

        $query = DB::table($table)
            ->selectRaw("$bucketSql as bucket")
            ->selectRaw("SUM($amountColumn) as aggregate")
            ->whereRaw(
                "DATE($dateExpression) BETWEEN ? AND ?",
                [$range['start']->toDateString(), $range['end']->toDateString()]
            )
            ->groupBy('bucket')
            ->orderBy('bucket');

        if ($scope) {
            $scope($query);
        }

        $results = $query->get()->keyBy('bucket');
        $labels = [];
        $values = [];
        $cursor = $range['start']->copy();

        while ($cursor->lte($range['end'])) {
            $bucketKey = $range['bucket'] === 'day'
                ? $cursor->toDateString()
                : $cursor->copy()->startOfMonth()->toDateString();

            $labels[] = $range['bucket'] === 'day'
                ? $cursor->format('d M')
                : $cursor->format('M Y');
            $values[] = round((float) ($results[$bucketKey]->aggregate ?? 0), 2);

            $cursor = $range['bucket'] === 'day'
                ? $cursor->addDay()
                : $cursor->addMonth()->startOfMonth();
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    protected function resolvePeriodRange(string $periodKey, ?Carbon $fallbackStart = null): array
    {
        $end = now()->endOfDay();

        return match ($periodKey) {
            '30d' => [
                'start' => now()->subDays(29)->startOfDay(),
                'end' => $end,
                'bucket' => 'day',
                'label' => 'Last 30 days',
            ],
            '6m' => [
                'start' => now()->subMonths(5)->startOfMonth(),
                'end' => $end,
                'bucket' => 'month',
                'label' => 'Last 6 months',
            ],
            '1y' => [
                'start' => now()->subMonths(11)->startOfMonth(),
                'end' => $end,
                'bucket' => 'month',
                'label' => 'Last 12 months',
            ],
            'all' => [
                'start' => ($fallbackStart ?: now()->subMonths(11))->copy()->startOfMonth(),
                'end' => $end,
                'bucket' => 'month',
                'label' => 'All time',
            ],
            default => [
                'start' => now()->subDays(6)->startOfDay(),
                'end' => $end,
                'bucket' => 'day',
                'label' => 'Last 7 days',
            ],
        };
    }

    protected function getPeriodOptions(): array
    {
        return [
            ['value' => '7d', 'label' => '7 Days'],
            ['value' => '30d', 'label' => '30 Days'],
            ['value' => '6m', 'label' => '6 Months'],
            ['value' => '1y', 'label' => '1 Year'],
            ['value' => 'all', 'label' => 'All Time'],
        ];
    }

    protected function normalizePeriodKey(?string $periodKey, string $default): string
    {
        return in_array($periodKey, ['7d', '30d', '6m', '1y', 'all'], true)
            ? $periodKey
            : $default;
    }

    protected function normalizeQuoteLifecycleStatus(?string $status): string
    {
        $status = strtolower((string) $status);

        return in_array($status, ['closed', 'approved', 'rejected', 'expired', 'cancelled'], true)
            ? 'closed'
            : 'open';
    }

    protected function getEarliestDocumentDate(): ?Carbon
    {
        $salesDate = $this->getEarliestSalesDate();
        $purchaseDate = DB::table('purchase_orders')
            ->selectRaw('MIN(DATE(COALESCE(completion_date, created_at))) as first_date')
            ->value('first_date');

        $purchaseDate = $purchaseDate ? Carbon::parse($purchaseDate) : null;

        if ($salesDate && $purchaseDate) {
            return $salesDate->lte($purchaseDate) ? $salesDate : $purchaseDate;
        }

        return $salesDate ?: $purchaseDate;
    }

    protected function getEarliestSalesDate(): ?Carbon
    {
        $firstDate = DB::table('proforma_invoices')
            ->selectRaw('MIN(DATE(COALESCE(issue_date, created_at))) as first_date')
            ->value('first_date');

        return $firstDate ? Carbon::parse($firstDate) : null;
    }

    protected function formatMoney(float $amount, string $currency): string
    {
        return sprintf('%s %s', $currency, number_format($amount, 2));
    }
}
