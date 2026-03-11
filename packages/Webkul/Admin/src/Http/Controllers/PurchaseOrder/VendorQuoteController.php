<?php

namespace Webkul\Admin\Http\Controllers\PurchaseOrder;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\PurchaseOrder\VendorQuoteDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Requests\VendorQuoteRequest;
use Webkul\PurchaseOrder\Models\VendorQuote;
use Webkul\PurchaseOrder\Repositories\JobOrderRepository;
use Webkul\PurchaseOrder\Repositories\VendorQuoteRepository;

class VendorQuoteController extends Controller
{
    public function __construct(
        protected VendorQuoteRepository $vendorQuoteRepository,
        protected JobOrderRepository $jobOrderRepository
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

        if (request()->filled('job_order_id')) {
            $jobOrder = $this->jobOrderRepository->with(['requirements', 'organization'])->findOrFail(request('job_order_id'));
        }

        $vendors = app(\Webkul\Contact\Repositories\OrganizationRepository::class)
            ->whereIn('type', ['vendor', 'Vendor'])
            ->orderBy('name')
            ->get(['id', 'name']);

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
            $quote = $this->vendorQuoteRepository->createFromJobOrder($jobOrder, $payload);
        } else {
            $quote = $this->vendorQuoteRepository->create($payload);
        }

        Event::dispatch('vendor_quote.create.after', $quote);

        session()->flash('success', 'Vendor quote created successfully.');

        return redirect()->route('admin.vendor_quotes.view', $quote->id);
    }

    public function view(int $id): View
    {
        $vendorQuote = $this->vendorQuoteRepository->with(['organization', 'person', 'jobOrder.organization', 'items.requirement'])->findOrFail($id);

        return view('admin::vendor-quotes.view', compact('vendorQuote'));
    }

    public function edit(int $id): View
    {
        $vendorQuote = $this->vendorQuoteRepository->with(['organization', 'person', 'jobOrder', 'items'])->findOrFail($id);
        $vendors = app(\Webkul\Contact\Repositories\OrganizationRepository::class)
            ->whereIn('type', ['vendor', 'Vendor'])
            ->orderBy('name')
            ->get(['id', 'name']);

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
