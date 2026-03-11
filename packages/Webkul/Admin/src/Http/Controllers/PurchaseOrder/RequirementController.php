<?php

namespace Webkul\Admin\Http\Controllers\PurchaseOrder;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\PurchaseOrder\RequirementDataGrid;
use Webkul\Admin\Http\Controllers\Controller;

class RequirementController extends Controller
{
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(RequirementDataGrid::class)->process();
        }

        return view('admin::requirements.index');
    }
}
