<?php

namespace Webkul\Admin\Http\Controllers\PurchaseOrder;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\PurchaseOrder\VendorPayableDataGrid;
use Webkul\Admin\Http\Controllers\Controller;

class VendorPayableController extends Controller
{
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(VendorPayableDataGrid::class)->process();
        }

        return view('admin::vendor-payables.index');
    }
}
