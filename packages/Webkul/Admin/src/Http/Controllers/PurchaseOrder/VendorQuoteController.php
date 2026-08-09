<?php

namespace Webkul\Admin\Http\Controllers\PurchaseOrder;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Admin\DataGrids\PurchaseOrder\VendorQuoteDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Requests\VendorQuoteRequest;
use Webkul\Core\Traits\PDFHandler;
use Webkul\PurchaseOrder\Models\VendorQuote;
use Webkul\PurchaseOrder\Repositories\JobOrderRepository;
use Webkul\PurchaseOrder\Repositories\VendorQuoteRepository;
use Webkul\PurchaseOrder\Support\RequirementVendorAggregator;

class VendorQuoteController extends Controller
{
    use PDFHandler;

    public function __construct(
        protected VendorQuoteRepository $vendorQuoteRepository,
        protected JobOrderRepository $jobOrderRepository,
        protected RequirementVendorAggregator $requirementVendorAggregator
    ) {
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(VendorQuoteDataGrid::class)->process();
        }

        return view('admin::vendor-quotes.index');
    }

    public function create(): View
    {
        $jobOrder = null;
        $nextVendorQuoteNumber = VendorQuote::generateNextNumber();
        $selectedRequirementIds = collect((array) request('requirement_ids'))->filter()->map(fn ($id) => (int) $id)->all();

        if (request()->filled('job_order_id')) {
            $jobOrder = $this->jobOrderRepository->with(['requirements', 'organization'])->findOrFail(request('job_order_id'));

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

        return view('admin::vendor-quotes.create', compact('jobOrder', 'nextVendorQuoteNumber', 'vendors'));
    }

    public function store(VendorQuoteRequest $request): RedirectResponse
    {
        Event::dispatch('vendor_quote.create.before');

        $payload = $request->validated();
        $payload['created_by'] = auth()->id();

        if ($request->hasFile('attachment')) {
            $payload['attachment_path'] = $request->file('attachment')->store('vendor-quotes', 'public');
        }

        if ($request->filled('job_order_id')) {
            $jobOrder = $this->jobOrderRepository->with('requirements')->findOrFail($request->input('job_order_id'));
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
                $createdVendorQuotes = [];

                foreach ($groupedItems as $vendorId => $items) {
                    $vendorPayload = $payload;
                    $vendorPayload['vendor_quote_number'] = VendorQuote::generateNextNumber();
                    $vendorPayload['organization_id'] = (int) $vendorId;
                    $vendorPayload['items'] = collect($items)->values()->all();

                    $createdVendorQuotes[] = $this->vendorQuoteRepository->createFromJobOrder($jobOrder, $vendorPayload);
                }

                foreach ($createdVendorQuotes as $createdVendorQuote) {
                    Event::dispatch('vendor_quote.create.after', $createdVendorQuote);
                }

                session()->flash('success', 'Vendor quotes created successfully for ' . count($createdVendorQuotes) . ' vendors.');

                return redirect()->route('admin.vendor_quotes.index');
            }

            $singleVendorId = (int) $groupedItems->keys()->first();

            if ($singleVendorId > 0) {
                $payload['organization_id'] = $singleVendorId;
            }

            if ($singleVendorId <= 0 && empty($payload['organization_id'])) {
                session()->flash('error', 'Please select at least one vendor in Vendor Quote Items.');

                return redirect()->back()->withInput();
            }

            $payload['items'] = collect($payload['items'] ?? [])->values()->all();

            $quote = $this->vendorQuoteRepository->createFromJobOrder($jobOrder, $payload);
        } else {
            $groupedItems = collect($payload['items'] ?? [])
                ->groupBy(fn ($item) => (int) ($item['vendor_id'] ?? 0))
                ->filter(fn ($items, $vendorId) => $vendorId > 0);

            if ($groupedItems->count() > 1) {
                $createdVendorQuotes = [];

                foreach ($groupedItems as $vendorId => $items) {
                    $vendorPayload = $payload;
                    $vendorPayload['vendor_quote_number'] = VendorQuote::generateNextNumber();
                    $vendorPayload['organization_id'] = (int) $vendorId;
                    $vendorPayload['items'] = collect($items)->values()->all();

                    $createdVendorQuotes[] = $this->vendorQuoteRepository->create($vendorPayload);
                }

                foreach ($createdVendorQuotes as $createdVendorQuote) {
                    Event::dispatch('vendor_quote.create.after', $createdVendorQuote);
                }

                session()->flash('success', 'Vendor quotes created successfully for '.count($createdVendorQuotes).' vendors.');

                return redirect()->route('admin.vendor_quotes.index');
            }

            $singleVendorId = (int) $groupedItems->keys()->first();

            if ($singleVendorId <= 0) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['items' => 'Please assign a vendor to every Vendor Quote item.']);
            }

            $payload['organization_id'] = $singleVendorId;
            $payload['items'] = $groupedItems->first()->values()->all();
            $quote = $this->vendorQuoteRepository->create($payload);
        }

        Event::dispatch('vendor_quote.create.after', $quote);

        session()->flash('success', 'Vendor quote created successfully.');

        return redirect()->route('admin.vendor_quotes.view', $quote->id);
    }

    public function view(int $id): View
    {
        $vendorQuote = $this->vendorQuoteRepository->with(['organization', 'person', 'jobOrder.organization', 'items.requirement', 'purchaseOrders'])->findOrFail($id);

        return view('admin::vendor-quotes.view', compact('vendorQuote'));
    }

    public function print(int $id): Response|StreamedResponse
    {
        $vendorQuote = $this->vendorQuoteRepository
            ->with(['organization', 'jobOrder', 'items.requirement'])
            ->findOrFail($id);

        return $this->downloadPDF(
            view('admin::vendor-quotes.pdf', compact('vendorQuote'))->render(),
            'Vendor_Quote_' . ($vendorQuote->vendor_quote_number ?: $vendorQuote->id) . '_' . $vendorQuote->created_at->format('d-m-Y')
        );
    }

    public function edit(int $id): View
    {
        $vendorQuote = $this->vendorQuoteRepository->with(['organization', 'person', 'jobOrder.requirements', 'items.requirement', 'purchaseOrders'])->findOrFail($id);
        $vendors = app(\Webkul\Contact\Repositories\OrganizationRepository::class)
            ->whereRaw("LOWER(TRIM(type)) IN ('vendor', 'vendors')")
            ->orderBy('name')
            ->get();

        return view('admin::vendor-quotes.edit', compact('vendorQuote', 'vendors'));
    }

    public function update(VendorQuoteRequest $request, int $id): RedirectResponse
    {
        $existing = $this->vendorQuoteRepository->findOrFail($id);
        $payload = $request->validated();

        if ($request->hasFile('attachment')) {
            if ($existing->attachment_path) {
                Storage::disk('public')->delete($existing->attachment_path);
            }

            $payload['attachment_path'] = $request->file('attachment')->store('vendor-quotes', 'public');
        } else {
            $payload['attachment_path'] = $existing->attachment_path;
        }

        $this->vendorQuoteRepository->update($payload, $id);

        session()->flash('success', 'Vendor quote updated successfully.');

        return redirect()->route('admin.vendor_quotes.view', $id);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->vendorQuoteRepository->delete($id);
            return response()->json(['message' => 'Vendor quote deleted successfully.']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Vendor quote cannot be deleted.'], 400);
        }
    }

    public function massDestroy(MassDestroyRequest $request): JsonResponse
    {
        foreach ($request->input('indices') as $id) {
            $this->vendorQuoteRepository->delete($id);
        }

        return response()->json(['message' => 'Vendor quotes deleted successfully.']);
    }
}
