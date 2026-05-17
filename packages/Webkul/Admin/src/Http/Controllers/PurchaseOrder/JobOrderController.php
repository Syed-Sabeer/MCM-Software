<?php

namespace Webkul\Admin\Http\Controllers\PurchaseOrder;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Admin\DataGrids\PurchaseOrder\JobOrderDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\JobOrderRequest;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Contact\Models\Organization;
use Webkul\Core\Traits\PDFHandler;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\PurchaseOrder\Repositories\JobOrderRepository;
use Webkul\PurchaseOrder\Support\RequirementVendorAggregator;
use Webkul\Quote\Repositories\ProformaInvoiceRepository;

class JobOrderController extends Controller
{
    use PDFHandler;

    public function __construct(
        protected JobOrderRepository $jobOrderRepository,
        protected ProformaInvoiceRepository $proformaInvoiceRepository,
        protected RequirementVendorAggregator $requirementVendorAggregator
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
        $jobOrder = $this->findJobOrderForExport($id);
        $requirementVendors = $this->resolveRequirementVendors($jobOrder);
        $vendorRequirementTotals = $this->requirementVendorAggregator->totals($jobOrder->requirements);

        return view('admin::job-orders.view', compact('jobOrder', 'requirementVendors', 'vendorRequirementTotals'));
    }

    public function edit(int $id): View
    {
        $jobOrder = $this->jobOrderRepository->with(['organization', 'person', 'proformaInvoice', 'items.product'])->findOrFail($id);

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

    public function downloadJobCardPdf(int $id): Response|StreamedResponse
    {
        $jobOrder = $this->findJobOrderForExport($id);

        return $this->downloadPDF(
            view('admin::job-orders.job-card-pdf', compact('jobOrder'))->render(),
            'Job_Card_' . ($jobOrder->job_order_number ?: $jobOrder->id)
        );
    }

    public function downloadJobCardCsv(int $id): StreamedResponse
    {
        $jobOrder = $this->findJobOrderForExport($id);

        return response()->streamDownload(function () use ($jobOrder) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Job Order', 'Item', 'Section', 'Process', 'Requirement Qty', 'Unit']);

            foreach ($jobOrder->jobCards as $jobCard) {
                $itemLabel = $jobCard->display_item_label;

                foreach ($jobCard->sections as $section) {
                    if ($section->items->isEmpty()) {
                        fputcsv($handle, [
                            $jobOrder->job_order_number,
                            $itemLabel,
                            $section->section_name,
                            '',
                            '',
                            '',
                        ]);

                        continue;
                    }

                    foreach ($section->items as $sectionItem) {
                        fputcsv($handle, [
                            $jobOrder->job_order_number,
                            $itemLabel,
                            $section->section_name,
                            $sectionItem->name,
                            $sectionItem->qty,
                            $sectionItem->unit,
                        ]);
                    }
                }
            }

            fclose($handle);
        }, 'Job_Card_' . ($jobOrder->job_order_number ?: $jobOrder->id) . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function downloadRequirementSheetPdf(int $id): Response|StreamedResponse
    {
        $jobOrder = $this->findJobOrderForExport($id);
        $requirementVendors = $this->resolveRequirementVendors($jobOrder)->keyBy('id');

        $scope = request('vendor_scope') === 'all' ? 'all' : 'single';
        $selectedVendorId = (int) request('vendor_id', 0);

        if ($scope === 'all') {
            $vendorRequirementGroups = $requirementVendors->map(function ($vendor) use ($jobOrder) {
                $requirements = $jobOrder->requirements->filter(function ($requirement) use ($vendor) {
                    return in_array((int) $vendor->id, array_map('intval', (array) ($requirement->vendor_ids ?? [])), true);
                })->values();

                return [
                    'vendor'       => $vendor,
                    'requirements' => $requirements,
                    'totals'       => $this->requirementVendorAggregator->totals($requirements),
                ];
            })->filter(fn ($group) => $group['requirements']->isNotEmpty())->values();

            if ($vendorRequirementGroups->isEmpty()) {
                $vendorRequirementGroups = collect([[
                    'vendor'       => null,
                    'requirements' => collect(),
                    'totals'       => collect(),
                ]]);
            }
        } else {
            if ($selectedVendorId <= 0 && $requirementVendors->isNotEmpty()) {
                $selectedVendorId = (int) $requirementVendors->first()->id;
            }

            $selectedVendor = $selectedVendorId > 0 ? $requirementVendors->get($selectedVendorId) : null;
            $requirements = $jobOrder->requirements
                ->filter(function ($requirement) use ($selectedVendorId) {
                    if ($selectedVendorId <= 0) {
                        return true;
                    }

                    return in_array($selectedVendorId, array_map('intval', (array) ($requirement->vendor_ids ?? [])), true);
                })
                ->values();

            $vendorRequirementGroups = collect([[
                'vendor'       => $selectedVendor,
                'requirements' => $requirements,
                'totals'       => $this->requirementVendorAggregator->totals($requirements),
            ]]);
        }

        return $this->downloadPDF(
            view('admin::job-orders.requirement-sheet-pdf', compact('jobOrder', 'vendorRequirementGroups', 'scope'))->render(),
            'Requirement_Sheet_' . ($jobOrder->job_order_number ?: $jobOrder->id)
        );
    }

    public function downloadRequirementSheetCsv(int $id): StreamedResponse
    {
        $jobOrder = $this->findJobOrderForExport($id);

        return response()->streamDownload(function () use ($jobOrder) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Job Order', 'Item', 'Material', 'Color', 'Per Item Required', 'Required', 'Received', 'Balance', 'Unit', 'Status']);

            foreach ($jobOrder->requirements as $requirement) {
                fputcsv($handle, [
                    $jobOrder->job_order_number,
                    $requirement->item_codes ?: '',
                    $requirement->material_name,
                    $requirement->color_name ?: $requirement->color_code ?: '',
                    $requirement->qty_per_unit,
                    $requirement->required_qty,
                    $requirement->received_qty,
                    $requirement->balance_qty,
                    $requirement->unit,
                    $requirement->status,
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Total Requirement from Vendor']);
            fputcsv($handle, ['Job Order', 'Material', 'Color', 'Total Required', 'Received', 'Balance', 'Unit']);

            foreach ($this->requirementVendorAggregator->totals($jobOrder->requirements) as $total) {
                fputcsv($handle, [
                    $jobOrder->job_order_number,
                    $total['material_name'],
                    $total['color_label'],
                    $total['required_qty'],
                    $total['received_qty'],
                    $total['balance_qty'],
                    $total['unit'],
                ]);
            }

            fclose($handle);
        }, 'Requirement_Sheet_' . ($jobOrder->job_order_number ?: $jobOrder->id) . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function findJobOrderForExport(int $id): JobOrder
    {
        return $this->jobOrderRepository->with([
            'organization',
            'person',
            'proformaInvoice',
            'proformaInvoice.items',
            'items.product',
            'items.proformaInvoiceItem',
            'requirements',
            'jobCards.jobOrderItem.product',
            'jobCards.jobOrderItem.proformaInvoiceItem',
            'jobCards.sections.items',
            'vendorQuotes',
            'purchaseOrders',
        ])->findOrFail($id);
    }

    protected function resolveRequirementVendors(JobOrder $jobOrder)
    {
        $vendorIds = $jobOrder->requirements
            ->flatMap(fn ($requirement) => (array) ($requirement->vendor_ids ?? []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($vendorIds->isEmpty()) {
            return collect();
        }

        return Organization::query()
            ->whereIn('id', $vendorIds->all())
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
