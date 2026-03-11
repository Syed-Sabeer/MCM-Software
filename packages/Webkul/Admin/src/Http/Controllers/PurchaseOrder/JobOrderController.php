<?php

namespace Webkul\Admin\Http\Controllers\PurchaseOrder;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\PurchaseOrder\JobOrderDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\JobOrderRequest;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\PurchaseOrder\Repositories\JobOrderRepository;
use Webkul\Quote\Repositories\ProformaInvoiceRepository;

class JobOrderController extends Controller
{
    public function __construct(
        protected JobOrderRepository $jobOrderRepository,
        protected ProformaInvoiceRepository $proformaInvoiceRepository
    ) {
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(JobOrderDataGrid::class)->process();
        }

        return view('admin::job-orders.index');
    }

    public function create(): View
    {
        $proformaInvoice = $this->proformaInvoiceRepository->with(['items', 'organization', 'person'])->findOrFail(request('proforma_invoice_id'));
        $nextJobOrderNumber = JobOrder::generateNextNumber();

        return view('admin::job-orders.create', compact('proformaInvoice', 'nextJobOrderNumber'));
    }

    public function store(JobOrderRequest $request): RedirectResponse
    {
        Event::dispatch('job_order.create.before');

        $proformaInvoice = $this->proformaInvoiceRepository->with('items')->findOrFail($request->input('proforma_invoice_id'));
        $jobOrder = $this->jobOrderRepository->createFromProforma($proformaInvoice, $request->validated());

        Event::dispatch('job_order.create.after', $jobOrder);

        session()->flash('success', 'Job order created successfully.');

        return redirect()->route('admin.job_orders.view', $jobOrder->id);
    }

    public function view(int $id): View
    {
        $jobOrder = $this->jobOrderRepository->with(['organization', 'person', 'proformaInvoice', 'items', 'requirements', 'jobCards.sections.items', 'vendorQuotes', 'purchaseOrders'])->findOrFail($id);

        return view('admin::job-orders.view', compact('jobOrder'));
    }

    public function edit(int $id): View
    {
        $jobOrder = $this->jobOrderRepository->with(['organization', 'person', 'proformaInvoice', 'items'])->findOrFail($id);

        return view('admin::job-orders.edit', compact('jobOrder'));
    }

    public function update(JobOrderRequest $request, int $id): RedirectResponse
    {
        Event::dispatch('job_order.update.before', $id);

        $jobOrder = $this->jobOrderRepository->update($request->validated(), $id);

        Event::dispatch('job_order.update.after', $jobOrder);

        session()->flash('success', 'Job order updated successfully.');

        return redirect()->route('admin.job_orders.view', $id);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->jobOrderRepository->delete($id);
            return response()->json(['message' => 'Job order deleted successfully.']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Job order cannot be deleted.'], 400);
        }
    }

    public function massDestroy(MassDestroyRequest $request): JsonResponse
    {
        foreach ($request->input('indices') as $id) {
            $this->jobOrderRepository->delete($id);
        }

        return response()->json(['message' => 'Job orders deleted successfully.']);
    }
}
