<?php

namespace Webkul\Admin\Http\Controllers;

use Webkul\Admin\Helpers\Dashboard;

class DashboardController extends Controller
{
    /**
     * Request param functions
     *
     * @var array
     */
    protected $typeFunctions = [
        'erp-overview'         => 'getErpOverview',
        'over-all'             => 'getOverAllStats',
        'revenue-stats'        => 'getRevenueStats',
        'total-leads'          => 'getTotalLeadsStats',
        'revenue-by-sources'   => 'getLeadsStatsBySources',
        'revenue-by-types'     => 'getLeadsStatsByTypes',
        'top-selling-products' => 'getTopSellingProducts',
        'top-persons'          => 'getTopPersons',
        'open-leads-by-states' => 'getOpenLeadsByStates',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected Dashboard $dashboardHelper) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::dashboard.index')->with([
            'startDate'              => $this->dashboardHelper->getStartDate(),
            'endDate'                => $this->dashboardHelper->getEndDate(),
            'canViewBusinessDetails' => bouncer()->hasPermission('dashboard.business_details'),
            'canViewCustomerDetails' => bouncer()->hasPermission('dashboard.customer_details'),
            'canViewActivities'      => bouncer()->hasPermission('activities'),
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        $type = request()->query('type', 'erp-overview');

        abort_unless(isset($this->typeFunctions[$type]), 404);

        $stats = $this->dashboardHelper->{$this->typeFunctions[$type]}();

        if ($type === 'erp-overview') {
            if (! bouncer()->hasPermission('dashboard.business_details')) {
                unset(
                    $stats['quote_status'],
                    $stats['cases_by_stage'],
                    $stats['sales_purchasing'],
                    $stats['best_products']
                );
            }

            if (! bouncer()->hasPermission('dashboard.customer_details')) {
                unset($stats['top_customers']);
            }
        }

        return response()->json([
            'statistics' => $stats,
            'date_range' => $this->dashboardHelper->getDateRange(),
        ]);
    }
}
