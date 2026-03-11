<?php

namespace Webkul\Admin\Http\Controllers\PurchaseOrder;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Admin\DataGrids\PurchaseOrder\PurchaseOrderDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Requests\PurchaseOrderRequest;
use Webkul\Core\Traits\PDFHandler;
use Webkul\PurchaseOrder\Models\PurchaseOrder;
use Webkul\PurchaseOrder\Repositories\JobOrderRepository;
use Webkul\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Webkul\PurchaseOrder\Repositories\VendorQuoteRepository;

class PurchaseOrderController extends Controller
{
    use PDFHandler;

    public function __construct(
        protected PurchaseOrderRepository $purchaseOrderRepository,
        protected VendorQuoteRepository $vendorQuoteRepository,
        protected JobOrderRepository $jobOrderRepository
    ) {
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(PurchaseOrderDataGrid::class)->process();
        }

        return view('admin::purchase-orders.index');
    }

    public function create(): View
    {
        $nextPoNumber = PurchaseOrder::generateNextPoNumber();
        $vendorQuote = null;
        $jobOrder = null;

        if (request()->filled('vendor_quote_id')) {
            $vendorQuote = $this->vendorQuoteRepository->with(['items', 'organization', 'jobOrder'])->findOrFail(request('vendor_quote_id'));
        }

        if (request()->filled('job_order_id')) {
            $jobOrder = $this->jobOrderRepository->with(['requirements', 'organization'])->findOrFail(request('job_order_id'));
        }

        $vendors = app(\Webkul\Contact\Repositories\OrganizationRepository::class)
            ->whereIn('type', ['vendor', 'Vendor'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin::purchase-orders.create', compact('nextPoNumber', 'vendorQuote', 'jobOrder', 'vendors'));
    }

    public function store(PurchaseOrderRequest $request): RedirectResponse
    {
        Event::dispatch('purchase_order.create.before');

        $payload = $request->validated();
        $payload['user_id'] = auth()->id();

        if ($request->hasFile('attachment')) {
            $payload['attachment_path'] = $request->file('attachment')->store('purchase-orders', 'public');
        }

        if ($request->filled('vendor_quote_id')) {
            $vendorQuote = $this->vendorQuoteRepository->with('items', 'jobOrder')->findOrFail($request->input('vendor_quote_id'));
            $purchaseOrder = $this->purchaseOrderRepository->createFromVendorQuote($vendorQuote, $payload);
        } elseif ($request->filled('job_order_id') && ! $request->filled('organization_id')) {
            $jobOrder = $this->jobOrderRepository->with('requirements')->findOrFail($request->input('job_order_id'));
            $purchaseOrder = $this->purchaseOrderRepository->createFromJobOrder($jobOrder, $payload);
        } else {
            $purchaseOrder = $this->purchaseOrderRepository->create($payload);
        }

        Event::dispatch('purchase_order.create.after', $purchaseOrder);

        session()->flash('success', 'Purchase order created successfully.');

        return redirect()->route('admin.purchase_orders.view', $purchaseOrder->id);
    }

    public function view(int $id): View
    {
        $purchaseOrder = $this->purchaseOrderRepository->with(['items.requirement', 'organization', 'person', 'user', 'jobOrder', 'vendorQuote', 'receipts.items', 'payables'])->findOrFail($id);

        return view('admin::purchase-orders.view', compact('purchaseOrder'));
    }

    public function edit(int $id): View
    {
        $purchaseOrder = $this->purchaseOrderRepository->with(['items', 'organization', 'jobOrder', 'vendorQuote'])->findOrFail($id);
        $vendors = app(\Webkul\Contact\Repositories\OrganizationRepository::class)
            ->whereIn('type', ['vendor', 'Vendor'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin::purchase-orders.edit', compact('purchaseOrder', 'vendors'));
    }

    public function update(PurchaseOrderRequest $request, int $id): RedirectResponse
    {
        Event::dispatch('purchase_order.update.before', $id);

        $existing = $this->purchaseOrderRepository->findOrFail($id);
        $payload = $request->validated();

        if ($request->hasFile('attachment')) {
            if ($existing->attachment_path) {
                Storage::disk('public')->delete($existing->attachment_path);
            }

            $payload['attachment_path'] = $request->file('attachment')->store('purchase-orders', 'public');
        } else {
            $payload['attachment_path'] = $existing->attachment_path;
        }

        $purchaseOrder = $this->purchaseOrderRepository->update($payload, $id);

        Event::dispatch('purchase_order.update.after', $purchaseOrder);

        session()->flash('success', 'Purchase order updated successfully.');

        return redirect()->route('admin.purchase_orders.view', $id);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Event::dispatch('purchase_order.delete.before', $id);
            $this->purchaseOrderRepository->delete($id);
            Event::dispatch('purchase_order.delete.after', $id);

            return response()->json(['message' => 'Purchase order deleted successfully.']);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Purchase order cannot be deleted.'], 400);
        }
    }

    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        foreach ($massDestroyRequest->input('indices') as $id) {
            $this->purchaseOrderRepository->delete($id);
        }

        return response()->json(['message' => 'Purchase orders deleted successfully.']);
    }

    public function print(int $id): Response|StreamedResponse
    {
        $purchaseOrder = $this->purchaseOrderRepository->with(['items', 'organization', 'jobOrder', 'vendorQuote'])->findOrFail($id);

        return $this->downloadPDF(
            view('admin::purchase-orders.pdf', compact('purchaseOrder'))->render(),
            'PO_' . $purchaseOrder->po_number . '_' . $purchaseOrder->created_at->format('d-m-Y')
        );
    }
}
