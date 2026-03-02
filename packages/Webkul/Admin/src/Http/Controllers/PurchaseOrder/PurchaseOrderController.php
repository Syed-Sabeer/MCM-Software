<?php

namespace Webkul\Admin\Http\Controllers\PurchaseOrder;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Admin\DataGrids\PurchaseOrder\PurchaseOrderDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Core\Traits\PDFHandler;
use Webkul\PurchaseOrder\Repositories\PurchaseOrderRepository;

class PurchaseOrderController extends Controller
{
    use PDFHandler;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected PurchaseOrderRepository $purchaseOrderRepository
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(PurchaseOrderDataGrid::class)->process();
        }

        return view('admin::purchase-orders.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $nextPoNumber = \Webkul\PurchaseOrder\Models\PurchaseOrder::generateNextPoNumber();

        return view('admin::purchase-orders.create', compact('nextPoNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse
    {
        $this->validate(request(), [
            'po_number'   => 'required|unique:purchase_orders,po_number',
            'job_number'  => 'nullable|string|max:255',
        ]);

        Event::dispatch('purchase_order.create.before');

        $purchaseOrder = $this->purchaseOrderRepository->create(request()->all());

        Event::dispatch('purchase_order.create.after', $purchaseOrder);

        session()->flash('success', trans('admin::app.purchase-orders.index.create-success'));

        return redirect()->route('admin.purchase_orders.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $purchaseOrder = $this->purchaseOrderRepository->with('items')->findOrFail($id);

        return view('admin::purchase-orders.edit', compact('purchaseOrder'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id): RedirectResponse
    {
        $this->validate(request(), [
            'po_number'   => 'required|unique:purchase_orders,po_number,' . $id,
            'job_number'  => 'nullable|string|max:255',
        ]);

        Event::dispatch('purchase_order.update.before', $id);

        $purchaseOrder = $this->purchaseOrderRepository->update(request()->all(), $id);

        Event::dispatch('purchase_order.update.after', $purchaseOrder);

        session()->flash('success', trans('admin::app.purchase-orders.index.update-success'));

        return redirect()->route('admin.purchase_orders.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->purchaseOrderRepository->findOrFail($id);

        try {
            Event::dispatch('purchase_order.delete.before', $id);

            $this->purchaseOrderRepository->delete($id);

            Event::dispatch('purchase_order.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.purchase-orders.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.purchase-orders.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $purchaseOrders = $this->purchaseOrderRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        try {
            foreach ($purchaseOrders as $purchaseOrder) {
                Event::dispatch('purchase_order.delete.before', $purchaseOrder->id);

                $this->purchaseOrderRepository->delete($purchaseOrder->id);

                Event::dispatch('purchase_order.delete.after', $purchaseOrder->id);
            }

            return response()->json([
                'message' => trans('admin::app.purchase-orders.index.delete-success'),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.purchase-orders.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Print and download the PDF for the specified resource.
     */
    public function print(int $id): Response|StreamedResponse
    {
        $purchaseOrder = $this->purchaseOrderRepository->with('items')->findOrFail($id);

        return $this->downloadPDF(
            view('admin::purchase-orders.pdf', compact('purchaseOrder'))->render(),
            'PO_' . $purchaseOrder->po_number . '_' . $purchaseOrder->created_at->format('d-m-Y')
        );
    }
}
