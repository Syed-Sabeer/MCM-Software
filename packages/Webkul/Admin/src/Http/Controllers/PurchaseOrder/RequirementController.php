<?php

namespace Webkul\Admin\Http\Controllers\PurchaseOrder;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\PurchaseOrder\RequirementDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\PurchaseOrder\Repositories\JobOrderRequirementRepository;

class RequirementController extends Controller
{
    public function __construct(
        protected JobOrderRequirementRepository $jobOrderRequirementRepository
    ) {
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(RequirementDataGrid::class)->process();
        }

        return view('admin::requirements.index');
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->jobOrderRequirementRepository->delete($id);

            return response()->json(['message' => 'Requirement deleted successfully.']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Requirement cannot be deleted.'], 400);
        }
    }

    public function massDestroy(MassDestroyRequest $request): JsonResponse
    {
        foreach ($request->input('indices') as $id) {
            $this->jobOrderRequirementRepository->delete($id);
        }

        return response()->json(['message' => 'Requirements deleted successfully.']);
    }
}
