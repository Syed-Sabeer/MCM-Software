<?php

namespace Webkul\Admin\Http\Controllers\Quote;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Webkul\Admin\DataGrids\Quote\ProformaInvoiceDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Quote\Repositories\ProformaInvoiceRepository;
use Webkul\Quote\Repositories\QuoteRepository;

class ProformaInvoiceController extends Controller
{
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

        if (request()->filled('quote_id')) {
            $quote = $this->quoteRepository->with('items', 'person', 'organization')->find(request('quote_id'));
        }

        return view('admin::proforma-invoices.create', compact('quote'));
    }

    public function store(): RedirectResponse
    {
        $this->validateForm();

        Event::dispatch('proforma_invoice.create.before');

        if (request()->filled('quote_id')) {
            $quote = $this->quoteRepository->with('items', 'person', 'organization')->findOrFail(request('quote_id'));
            $proforma = $this->proformaInvoiceRepository->createFromQuote($quote, request()->all());
        } else {
            $proforma = $this->proformaInvoiceRepository->create(request()->all());
        }

        Event::dispatch('proforma_invoice.create.after', $proforma);

        session()->flash('success', 'Proforma invoice created successfully.');

        return redirect()->route('admin.proforma_invoices.edit', $proforma->id);
    }

    public function edit(int $id): View
    {
        $proformaInvoice = $this->proformaInvoiceRepository->with(['items', 'receipts.receivedBy', 'organization', 'person', 'quote'])->findOrFail($id);

        return view('admin::proforma-invoices.edit', compact('proformaInvoice'));
    }

    public function view(int $id): View
    {
        $proformaInvoice = $this->proformaInvoiceRepository->with(['items', 'receipts.receivedBy', 'organization', 'person', 'quote'])->findOrFail($id);

        return view('admin::proforma-invoices.view', compact('proformaInvoice'));
    }

    public function update(int $id): RedirectResponse
    {
        $this->validateForm($id);

        Event::dispatch('proforma_invoice.update.before', $id);

        $proforma = $this->proformaInvoiceRepository->update(request()->all(), $id);

        Event::dispatch('proforma_invoice.update.after', $proforma);

        session()->flash('success', 'Proforma invoice updated successfully.');

        return redirect()->route('admin.proforma_invoices.edit', $id);
    }

    public function storeReceipt(int $id): RedirectResponse
    {
        request()->validate([
            'payment_date' => ['required', 'date'],
            'amount'       => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'notes'        => ['nullable', 'string'],
        ]);

        Event::dispatch('proforma_invoice.receipt.create.before', $id);

        $this->proformaInvoiceRepository->addReceipt($id, request()->all());

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
        $allowed = ['draft', 'issued', 'partially_paid', 'fully_paid', 'cancelled', 'ready_for_job_order', 'converted'];
        $status = request('status');

        if (! in_array($status, $allowed)) {
            abort(422, 'Invalid status.');
        }

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

    protected function validateForm(?int $id = null): void
    {
        request()->validate([
            'organization_id' => ['required', 'exists:organizations,id'],
            'person_id'       => ['nullable', 'exists:persons,id'],
            'quote_id'        => ['nullable', 'exists:quotes,id'],
            'subject'         => ['nullable', 'string', 'max:255'],
            'issue_date'      => ['required', 'date'],
            'due_date'        => ['nullable', 'date'],
            'status'          => ['nullable', 'string', 'max:50'],
            'items'           => ['required', 'array', 'min:1'],
            'items.*.product_id'      => ['nullable', 'exists:products,id'],
            'items.*.item_name'       => ['nullable', 'string', 'max:255'],
            'items.*.qty'             => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price'      => ['required', 'numeric', 'min:0'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_amount'      => ['nullable', 'numeric', 'min:0'],
        ]);

        $organizationType = DB::table('organizations')
            ->where('id', request('organization_id'))
            ->value('type');

        if (! in_array($organizationType, ['customer', 'Customer'])) {
            throw ValidationException::withMessages([
                'organization_id' => 'Organization must be customer type.',
            ]);
        }

        if (request()->filled('person_id')) {
            $personOrgId = DB::table('persons')->where('id', request('person_id'))->value('organization_id');

            if ((int) $personOrgId !== (int) request('organization_id')) {
                throw ValidationException::withMessages([
                    'person_id' => 'Selected person does not belong to selected organization.',
                ]);
            }
        }
    }
}
