<?php

namespace Webkul\Admin\Http\Controllers\Quote;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Admin\DataGrids\Quote\ProformaInvoiceDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\ProformaInvoiceRequest;
use Webkul\Admin\Http\Requests\ProformaReceiptRequest;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Core\Traits\PDFHandler;
use Webkul\Quote\Models\ProformaInvoice;
use Webkul\Quote\Repositories\ProformaInvoiceRepository;
use Webkul\Quote\Repositories\QuoteRepository;

class ProformaInvoiceController extends Controller
{
    use PDFHandler;

    public function __construct(
        protected ProformaInvoiceRepository $proformaInvoiceRepository,
        protected QuoteRepository $quoteRepository
    ) {
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(ProformaInvoiceDataGrid::class)->process();
        }

        return view('admin::proforma-invoices.index');
    }

    public function create(): View
    {
        $quote = null;
        $nextProformaNumber = ProformaInvoice::generateNextProformaNumber();

        if (request()->filled('quote_id')) {
            $quote = $this->quoteRepository->with('items', 'person', 'organization')->find(request('quote_id'));
        }

        return view('admin::proforma-invoices.create', compact('quote', 'nextProformaNumber'));
    }

    public function store(ProformaInvoiceRequest $request): RedirectResponse
    {
        Event::dispatch('proforma_invoice.create.before');

        $payload = $this->prepareProformaPayload($request);

        if ($request->filled('quote_id')) {
            $quote = $this->quoteRepository->with('items', 'person', 'organization')->findOrFail($request->input('quote_id'));
            $proforma = $this->proformaInvoiceRepository->createFromQuote($quote, $payload);

            $quote->forceFill(['status' => 'closed'])->save();
        } else {
            $proforma = $this->proformaInvoiceRepository->create($payload);
        }

        Event::dispatch('proforma_invoice.create.after', $proforma);

        session()->flash('success', 'Proforma invoice created successfully.');

        return redirect()->route('admin.proforma_invoices.edit', $proforma->id);
    }

    public function edit(int $id): View
    {
        $proformaInvoice = $this->proformaInvoiceRepository->with(['items', 'receipts.receivedBy', 'organization', 'person', 'quote', 'salesOwner'])->findOrFail($id);

        return view('admin::proforma-invoices.edit', compact('proformaInvoice'));
    }

    public function view(int $id): View
    {
        $proformaInvoice = $this->proformaInvoiceRepository->with(['items', 'receipts.receivedBy', 'organization', 'person', 'quote', 'salesOwner'])->findOrFail($id);

        return view('admin::proforma-invoices.view', compact('proformaInvoice'));
    }

    public function print(int $id): Response|StreamedResponse
    {
        $proformaInvoice = $this->proformaInvoiceRepository
            ->with(['organization', 'salesOwner', 'quote', 'items'])
            ->findOrFail($id);

        return $this->downloadPDF(
            view('admin::proforma-invoices.pdf', compact('proformaInvoice'))->render(),
            'Proforma_' . ($proformaInvoice->proforma_number ?: $proformaInvoice->id) . '_' . $proformaInvoice->created_at->format('d-m-Y')
        );
    }

    public function update(ProformaInvoiceRequest $request, int $id): RedirectResponse
    {
        Event::dispatch('proforma_invoice.update.before', $id);

        $existing = $this->proformaInvoiceRepository->findOrFail($id);
        $payload = $this->prepareProformaPayload($request, $existing);
        $proforma = $this->proformaInvoiceRepository->update($payload, $id);

        Event::dispatch('proforma_invoice.update.after', $proforma);

        session()->flash('success', 'Proforma invoice updated successfully.');

        return redirect()->route('admin.proforma_invoices.edit', $id);
    }

    public function storeReceipt(ProformaReceiptRequest $request, int $id): RedirectResponse
    {
        Event::dispatch('proforma_invoice.receipt.create.before', $id);

        $this->proformaInvoiceRepository->addReceipt($id, $this->prepareReceiptPayload($request));

        Event::dispatch('proforma_invoice.receipt.create.after', $id);

        session()->flash('success', 'Receipt recorded successfully.');

        return redirect()->route('admin.proforma_invoices.edit', $id);
    }

    public function deleteReceipt(int $id, int $receiptId): RedirectResponse
    {
        Event::dispatch('proforma_invoice.receipt.delete.before', [$id, $receiptId]);

        $this->proformaInvoiceRepository->deleteReceipt($id, $receiptId);

        Event::dispatch('proforma_invoice.receipt.delete.after', [$id, $receiptId]);

        session()->flash('success', 'Receipt deleted successfully.');

        return redirect()->route('admin.proforma_invoices.edit', $id);
    }

    public function changeStatus(int $id): RedirectResponse
    {
        $status = request()->validate([
            'status' => ['required', 'string', 'max:50'],
        ])['status'];

        Event::dispatch('proforma_invoice.status.update.before', [$id, $status]);

        $this->proformaInvoiceRepository->update([
            'status' => $status,
        ], $id);

        Event::dispatch('proforma_invoice.status.update.after', [$id, $status]);

        session()->flash('success', 'Proforma status updated successfully.');

        return redirect()->back();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->proformaInvoiceRepository->findOrFail($id);

        try {
            Event::dispatch('proforma_invoice.delete.before', $id);

            $this->proformaInvoiceRepository->delete($id);

            Event::dispatch('proforma_invoice.delete.after', $id);

            return response()->json([
                'message' => 'Proforma invoice deleted successfully.',
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Proforma invoice can not be deleted.',
            ], 400);
        }
    }

    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $invoices = $this->proformaInvoiceRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        DB::beginTransaction();

        try {
            foreach ($invoices as $invoice) {
                Event::dispatch('proforma_invoice.delete.before', $invoice->id);
                $this->proformaInvoiceRepository->delete($invoice->id);
                Event::dispatch('proforma_invoice.delete.after', $invoice->id);
            }

            DB::commit();

            return response()->json([
                'message' => 'Proforma invoices deleted successfully.',
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'message' => 'Proforma invoices can not be deleted.',
            ], 400);
        }
    }

    protected function prepareProformaPayload(ProformaInvoiceRequest $request, ?ProformaInvoice $existing = null): array
    {
        $payload = $request->validated();

        $payload['person_id'] = $payload['person_id'] ?: null;
        $payload['sales_owner_id'] = $payload['sales_owner_id'] ?? auth()->id();
        $payload['created_by'] = $existing?->created_by ?? auth()->id();

        if ($request->hasFile('attachment')) {
            if ($existing?->attachment_path) {
                Storage::disk('public')->delete($existing->attachment_path);
            }

            $payload['attachment_path'] = $request->file('attachment')->store('proforma-attachments', 'public');
        } elseif ($existing) {
            $payload['attachment_path'] = $existing->attachment_path;
        }

        return $payload;
    }

    protected function prepareReceiptPayload(ProformaReceiptRequest $request): array
    {
        $payload = $request->validated();
        $payload['received_by'] = auth()->id();

        if ($request->hasFile('attachment')) {
            $payload['attachment_path'] = $request->file('attachment')->store('proforma-receipts', 'public');
        }

        return $payload;
    }
}
