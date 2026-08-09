<?php

namespace Webkul\Admin\Http\Controllers\Quote;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Admin\DataGrids\Quote\InvoiceDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\ProformaReceiptRequest;
use Webkul\Core\Traits\PDFHandler;
use Webkul\Quote\Models\Invoice;
use Webkul\Quote\Models\InvoiceReceipt;
use Webkul\Quote\Models\ProformaInvoice;
use Webkul\Quote\Repositories\InvoiceRepository;

class InvoiceController extends Controller
{
    use PDFHandler;

    public function __construct(protected InvoiceRepository $invoiceRepository) {}

    public function index(): View|JsonResponse
    {
        return request()->ajax()
            ? datagrid(InvoiceDataGrid::class)->process()
            : view('admin::invoices.index');
    }

    public function store(int $proformaId): RedirectResponse
    {
        $proforma = ProformaInvoice::findOrFail($proformaId);
        Event::dispatch('invoice.create.before', $proforma);
        $invoice = $this->invoiceRepository->createFromProforma($proforma);
        Event::dispatch('invoice.create.after', $invoice);

        return redirect()->route('admin.invoices.view', $invoice->id)
            ->with('success', 'Final invoice created and the recorded advance was applied.');
    }

    public function view(int $id): View
    {
        $invoice = $this->invoiceRepository->with([
            'items', 'receipts.receivedBy', 'proformaInvoice.receipts', 'quote',
            'organization', 'person', 'salesOwner', 'additionalCharges',
        ])->findOrFail($id);

        return view('admin::invoices.view', compact('invoice'));
    }

    public function print(int $id): Response|StreamedResponse
    {
        $invoice = $this->invoiceRepository->with([
            'items', 'receipts', 'proformaInvoice.quote', 'quote', 'organization', 'salesOwner', 'additionalCharges',
        ])->findOrFail($id);

        return $this->downloadPDF(
            view('admin::invoices.pdf', compact('invoice'))->render(),
            'Invoice_'.$invoice->invoice_number.'_'.$invoice->created_at->format('d-m-Y')
        );
    }

    public function storeReceipt(ProformaReceiptRequest $request, int $id): RedirectResponse
    {
        $payload = $request->validated();
        $payload['received_by'] = auth()->id();

        if ($request->hasFile('attachment')) {
            $payload['attachment_path'] = $request->file('attachment')->store('invoice-receipts', 'public');
        }

        $this->invoiceRepository->addReceipt($id, $payload);

        return back()->with('success', 'Invoice payment recorded successfully.');
    }

    public function deleteReceipt(int $id, int $receiptId): RedirectResponse
    {
        $receipt = InvoiceReceipt::where('invoice_id', $id)->findOrFail($receiptId);
        if ($receipt->attachment_path) {
            Storage::disk('public')->delete($receipt->attachment_path);
        }
        $this->invoiceRepository->deleteReceipt($id, $receiptId);

        return back()->with('success', 'Invoice payment deleted successfully.');
    }

    public function changeStatus(int $id): RedirectResponse
    {
        $status = request()->validate(['status' => ['required', 'in:issued,cancelled']])['status'];
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => $status]);

        if ($status === 'issued') {
            $this->invoiceRepository->recalculate($invoice->id);
        }

        return back()->with('success', 'Invoice status updated successfully.');
    }
}
