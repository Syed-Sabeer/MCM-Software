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
        $requirementGroup = [
            'requirements' => $jobOrder->requirements,
            'totals'       => $this->requirementVendorAggregator->totals($jobOrder->requirements),
        ];

        return $this->downloadPDF(
            view('admin::job-orders.requirement-sheet-pdf', compact('jobOrder', 'requirementGroup'))->render(),
            'Requirement_Sheet_' . ($jobOrder->job_order_number ?: $jobOrder->id)
        );
    }

    public function downloadRequirementSheetCsv(int $id): StreamedResponse
    {
        $jobOrder = $this->findJobOrderForExport($id);

        return response()->streamDownload(function () use ($jobOrder) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Item', 'Material', 'Color', 'Per Item Required', 'Required', 'Received', 'Balance']);

            foreach ($jobOrder->requirements as $requirement) {
                fputcsv($handle, [
                    $requirement->item_codes ?: '',
                    $requirement->material_name,
                    $requirement->color_name ?: $requirement->color_code ?: '',
                    $this->formatRequirementQty($requirement->qty_per_unit).' '.$requirement->unit,
                    $this->formatRequirementQty($requirement->required_qty).' '.$requirement->unit,
                    $this->formatRequirementQty($requirement->received_qty),
                    $this->formatRequirementQty($requirement->balance_qty),
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Total Requirement from Vendor']);
            fputcsv($handle, ['Material', 'Color', 'Total Required', 'Received', 'Balance']);

            foreach ($this->requirementVendorAggregator->totals($jobOrder->requirements) as $total) {
                fputcsv($handle, [
                    $total['material_name'],
                    $total['color_label'],
                    $this->formatRequirementQty($total['required_qty']).' '.$total['unit'],
                    $this->formatRequirementQty($total['received_qty']).' '.$total['unit'],
                    $this->formatRequirementQty($total['balance_qty']).' '.$total['unit'],
                ]);
            }

            fclose($handle);
        }, 'Requirement_Sheet_' . ($jobOrder->job_order_number ?: $jobOrder->id) . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function formatRequirementQty($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
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
