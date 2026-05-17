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
use Webkul\PurchaseOrder\Repositories\JobOrderRequirementRepository;
use Webkul\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Webkul\PurchaseOrder\Repositories\VendorQuoteRepository;
use Webkul\PurchaseOrder\Support\RequirementVendorAggregator;

class PurchaseOrderController extends Controller
{
    use PDFHandler;

    public function __construct(
        protected PurchaseOrderRepository $purchaseOrderRepository,
        protected VendorQuoteRepository $vendorQuoteRepository,
        protected JobOrderRepository $jobOrderRepository,
        protected JobOrderRequirementRepository $jobOrderRequirementRepository,
        protected RequirementVendorAggregator $requirementVendorAggregator
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
        $selectedRequirementIds = collect((array) request('requirement_ids'))->filter()->map(fn ($id) => (int) $id)->all();

        if (request()->filled('vendor_quote_id')) {
            $vendorQuote = $this->vendorQuoteRepository->with(['items.requirement', 'organization', 'jobOrder'])->findOrFail(request('vendor_quote_id'));

            if ($vendorQuote->job_order_id && ! request()->filled('job_order_id')) {
                $jobOrder = $this->refreshJobOrderRequirementsIfNeeded((int) $vendorQuote->job_order_id);
            }
        }

        if (request()->filled('job_order_id')) {
            $jobOrder = $this->jobOrderRepository->with(['requirements', 'organization'])->findOrFail(request('job_order_id'));
            $jobOrder = $this->refreshJobOrderRequirementsIfNeeded($jobOrder->id);

            if ($selectedRequirementIds) {
                $jobOrder->setRelation(
                    'requirements',
                    $jobOrder->requirements->whereIn('id', $selectedRequirementIds)->values()
                );
            }

            $jobOrder->setRelation(
                'vendorRequirementTotals',
                $this->requirementVendorAggregator->totals($jobOrder->requirements)
            );
        }

        $vendors = app(\Webkul\Contact\Repositories\OrganizationRepository::class)
            ->whereRaw("LOWER(TRIM(type)) IN ('vendor', 'vendors')")
            ->orderBy('name')
            ->get();

        return view('admin::purchase-orders.create', compact('nextPoNumber', 'vendorQuote', 'jobOrder', 'vendors'));
    }

    public function store(PurchaseOrderRequest $request): RedirectResponse
    {
        Event::dispatch('purchase_order.create.before');

        $payload = $request->validated();
        if (empty($payload['po_number']) || PurchaseOrder::where('po_number', $payload['po_number'])->exists()) {
            $payload['po_number'] = PurchaseOrder::generateNextPoNumber();
        }

        $payload['user_id'] = auth()->id();

        if ($request->hasFile('attachment')) {
            $payload['attachment_path'] = $request->file('attachment')->store('purchase-orders', 'public');
        }

        if ($request->filled('vendor_quote_id') && ! ($request->filled('job_order_id') && ! $request->filled('organization_id'))) {
            $vendorQuote = $this->vendorQuoteRepository->with(['items.requirement', 'jobOrder'])->findOrFail($request->input('vendor_quote_id'));

            $items = $vendorQuote->items->map(fn ($item) => [
                'requirement_id' => $item->requirement_id,
                'item' => $item->material_name,
                'material_name' => $item->material_name,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'ordered_quantity' => $item->quantity,
                'received_quantity' => 0,
                'pending_quantity' => $item->quantity,
                'unit' => $item->unit ?: optional($item->requirement)->unit ?: 'PCS',
                'price' => $item->unit_price,
                'expected_receive_date' => $item->expected_receive_date?->toDateString(),
                'line_status' => 'open',
                'vendor_id' => collect((array) optional($item->requirement)->vendor_ids)->filter()->map(fn ($id) => (int) $id)->first() ?: $vendorQuote->organization_id,
            ])->toArray();

            $groupedItems = collect($items)->groupBy(fn ($it) => (int) ($it['vendor_id'] ?? 0))->filter(fn ($g, $vendorId) => $vendorId > 0);

            if ($groupedItems->count() > 1) {
                $createdPurchaseOrders = [];

                foreach ($groupedItems as $vendorId => $group) {
                    $vendorPayload = $payload;
                    $vendorPayload['po_number'] = PurchaseOrder::generateNextPoNumber();
                    $vendorPayload['organization_id'] = (int) $vendorId;
                    $vendorPayload['items'] = collect($group)->map(function ($it) {
                        unset($it['vendor_id']);

                        return $it;
                    })->values()->all();

                    // Apply charges to each split PO: prefer request payload charges, fallback to vendor quote charges
                    $vendorPayload['charges'] = $payload['charges'] ?? app(\Webkul\Core\Support\DocumentChargeManager::class)->extract($vendorQuote, 'vendor_quote');

                    $createdPurchaseOrders[] = $this->purchaseOrderRepository->create($vendorPayload);
                }

                foreach ($createdPurchaseOrders as $createdPurchaseOrder) {
                    Event::dispatch('purchase_order.create.after', $createdPurchaseOrder);
                }

                $this->vendorQuoteRepository->update(['status' => 'selected'], $vendorQuote->id);

                session()->flash('success', 'Purchase orders created successfully for ' . count($createdPurchaseOrders) . ' vendors.');

                return redirect()->route('admin.purchase_orders.index');
            }

            // Single vendor (or fallback)
            $singleGroup = $groupedItems->first();
            $singleVendorId = $groupedItems->keys()->first() ?? $vendorQuote->organization_id;

            $payload['organization_id'] = (int) $singleVendorId;
            $payload['items'] = collect($singleGroup ?? $items)->map(function ($it) {
                unset($it['vendor_id']);

                return $it;
            })->values()->all();

            // Ensure charges are present for the single-vendor PO as well
            $payload['charges'] = $payload['charges'] ?? app(\Webkul\Core\Support\DocumentChargeManager::class)->extract($vendorQuote, 'vendor_quote');

            $purchaseOrder = $this->purchaseOrderRepository->create($payload);
            $this->vendorQuoteRepository->update(['status' => 'selected'], $vendorQuote->id);

        } elseif ($request->filled('job_order_id') && ! $request->filled('organization_id')) {
            $jobOrder = $this->refreshJobOrderRequirementsIfNeeded((int) $request->input('job_order_id'));
            $requirementsById = $jobOrder->requirements->keyBy('id');

            $payload['items'] = collect($payload['items'] ?? [])->map(function ($item) use ($requirementsById, $payload) {
                $vendorId = (int) ($item['vendor_id'] ?? 0);

                if ($vendorId <= 0) {
                    $requirementId = (int) ($item['requirement_id'] ?? 0);
                    $requirement = $requirementsById->get($requirementId);

                    if ($requirement) {
                        $vendorId = (int) collect((array) ($requirement->vendor_ids ?? []))
                            ->filter()
                            ->map(fn ($id) => (int) $id)
                            ->first();
                    }
                }

                if ($vendorId <= 0 && ! empty($payload['organization_id'])) {
                    $vendorId = (int) $payload['organization_id'];
                }

                $item['vendor_id'] = $vendorId > 0 ? $vendorId : null;

                return $item;
            })->values()->all();

            $groupedItems = collect($payload['items'] ?? [])
                ->groupBy(fn ($item) => (int) ($item['vendor_id'] ?? 0))
                ->filter(fn ($items, $vendorId) => $vendorId > 0);

            if ($groupedItems->count() > 1) {
                $createdPurchaseOrders = [];

                foreach ($groupedItems as $vendorId => $items) {
                    $vendorPayload = $payload;
                    $vendorPayload['po_number'] = PurchaseOrder::generateNextPoNumber();
                    $vendorPayload['organization_id'] = (int) $vendorId;
                    $vendorPayload['items'] = collect($items)->map(function ($item) {
                        unset($item['vendor_id']);

                        return $item;
                    })->values()->all();

                    // Charges entered on a combined draft should be applied to each split PO (use request charges if present)
                    $vendorPayload['charges'] = $payload['charges'] ?? [];

                    $createdPurchaseOrders[] = $this->purchaseOrderRepository->create($vendorPayload);
                }

                foreach ($createdPurchaseOrders as $createdPurchaseOrder) {
                    Event::dispatch('purchase_order.create.after', $createdPurchaseOrder);
                }

                if ($request->filled('vendor_quote_id')) {
                    $this->vendorQuoteRepository->update(['status' => 'selected'], $request->input('vendor_quote_id'));
                }

                session()->flash('success', 'Purchase orders created successfully for ' . count($createdPurchaseOrders) . ' vendors.');

                return redirect()->route('admin.purchase_orders.index');
            }

            $singleVendorId = (int) $groupedItems->keys()->first();
            if ($singleVendorId > 0) {
                $payload['organization_id'] = $singleVendorId;
            }

            if ($singleVendorId <= 0 && empty($payload['organization_id'])) {
                session()->flash('error', 'Please select at least one vendor in Vendor PO Items.');

                return redirect()->back()->withInput();
            }

            $payload['items'] = collect($payload['items'] ?? [])->map(function ($item) {
                unset($item['vendor_id']);

                return $item;
            })->all();

            $purchaseOrder = $this->purchaseOrderRepository->createFromJobOrder($jobOrder, $payload);

            if ($request->filled('vendor_quote_id')) {
                $this->vendorQuoteRepository->update(['status' => 'selected'], $request->input('vendor_quote_id'));
            }
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
            ->whereRaw("LOWER(TRIM(type)) IN ('vendor', 'vendors')")
            ->orderBy('name')
            ->get();

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

    protected function refreshJobOrderRequirementsIfNeeded(int $jobOrderId)
    {
        $jobOrder = $this->jobOrderRepository->with(['requirements', 'items.product.consumptions', 'organization'])->findOrFail($jobOrderId);

        $requirementsNeedRefresh = $jobOrder->requirements->isEmpty()
            || $jobOrder->requirements->contains(function ($requirement) {
                return blank($requirement->item_codes);
            });

        if ($requirementsNeedRefresh) {
            $this->jobOrderRequirementRepository->regenerateForJobOrder($jobOrder);

            $jobOrder = $this->jobOrderRepository->with(['requirements', 'items.product.consumptions', 'organization'])->findOrFail($jobOrderId);
        }

        return $jobOrder;
    }
}
