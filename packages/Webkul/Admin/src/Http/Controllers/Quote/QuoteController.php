<?php

namespace Webkul\Admin\Http\Controllers\Quote;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Prettus\Repository\Criteria\RequestCriteria;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Admin\DataGrids\Quote\QuoteDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Resources\QuoteResource;
use Webkul\Core\Traits\PDFHandler;
use Webkul\Core\Support\DocumentChargeManager;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Quote\Repositories\ProformaInvoiceRepository;
use Webkul\Quote\Repositories\QuoteRepository;

class QuoteController extends Controller
{
    use PDFHandler;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected QuoteRepository $quoteRepository,
        protected LeadRepository $leadRepository,
        protected ProformaInvoiceRepository $proformaInvoiceRepository,
        protected DocumentChargeManager $documentChargeManager
    ) {
        request()->request->add(['entity_type' => 'quotes']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(QuoteDataGrid::class)->process();
        }

        return view('admin::quotes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $lead = $this->leadRepository->find(request('id'));

        return view('admin::quotes.create', compact('lead'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): RedirectResponse
    {
        $this->validateQuote($request);

        Event::dispatch('quote.create.before');

        $quote = $this->quoteRepository->create($request->all());

        $leadId = request('lead_id');

        if ($leadId) {
            $lead = $this->leadRepository->find($leadId);

            $lead->quotes()->attach($quote->id);
        }

        Event::dispatch('quote.create.after', $quote);

        session()->flash('success', trans('admin::app.quotes.index.create-success'));

        return request()->query('from') === 'lead' && $leadId
            ? redirect()->route('admin.leads.view', ['id' => $leadId, 'from' => 'quotes'])
            : redirect()->route('admin.quotes.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $quote = $this->quoteRepository
            ->with(['organization', 'user', 'items'])
            ->findOrFail($id);

        return view('admin::quotes.edit', compact('quote'));
    }

    /**
     * Display quote details in read-only mode.
     */
    public function view(int $id): View
    {
        $quote = $this->quoteRepository
            ->with(['organization', 'person', 'user', 'items', 'proformaInvoices'])
            ->findOrFail($id);

        return view('admin::quotes.view', compact('quote'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeForm $request, int $id): RedirectResponse
    {
        $this->validateQuote($request, $id);

        Event::dispatch('quote.update.before', $id);

        $quote = $this->quoteRepository->update($request->all(), $id);

        $quote->leads()->detach();

        $leadId = request('lead_id');

        if ($leadId) {
            $lead = $this->leadRepository->find($leadId);

            $lead->quotes()->attach($quote->id);
        }

        Event::dispatch('quote.update.after', $quote);

        session()->flash('success', trans('admin::app.quotes.index.update-success'));

        return request()->query('from') === 'lead' && $leadId
            ? redirect()->route('admin.leads.view', ['id' => $leadId, 'from' => 'quotes'])
            : redirect()->route('admin.quotes.index');
    }

    /**
     * Search the quotes.
     */
    public function search(): AnonymousResourceCollection
    {
        $quotes = $this->quoteRepository
            ->pushCriteria(app(RequestCriteria::class))
            ->all();

        return QuoteResource::collection($quotes);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->quoteRepository->findOrFail($id);

        try {
            Event::dispatch('quote.delete.before', $id);

            $this->quoteRepository->delete($id);

            Event::dispatch('quote.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.quotes.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.quotes.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $quotes = $this->quoteRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        try {
            foreach ($quotes as $quotes) {
                Event::dispatch('quote.delete.before', $quotes->id);

                $this->quoteRepository->delete($quotes->id);

                Event::dispatch('quote.delete.after', $quotes->id);
            }

            return response()->json([
                'message' => trans('admin::app.quotes.index.delete-success'),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.quotes.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Print and download the for the specified resource.
     */
    public function print($id): Response|StreamedResponse
    {
        $quote = $this->quoteRepository
            ->with(['organization', 'user', 'items'])
            ->findOrFail($id);

        return $this->downloadPDF(
            view('admin::quotes.pdf', compact('quote'))->render(),
            'Quote_'.($quote->subject ?: $quote->quote_number).'_'.$quote->created_at->format('d-m-Y')
        );
    }

    /**
     * Update quote status.
     */
    public function changeStatus(int $id): RedirectResponse
    {
        $quote = $this->quoteRepository->findOrFail($id);

        $allowed = ['draft', 'sent', 'approved', 'rejected', 'expired', 'cancelled'];
        $status = request('status');

        if (! in_array($status, $allowed)) {
            abort(422, 'Invalid status.');
        }

        Event::dispatch('quote.status.update.before', [$quote, $status]);

        $this->quoteRepository->update([
            'status' => $status,
        ], $quote->id, ['status']);

        Event::dispatch('quote.status.update.after', [$quote->fresh(), $status]);

        session()->flash('success', 'Quote status updated successfully.');

        return redirect()->back();
    }

    /**
     * Duplicate quote.
     */
    public function duplicate(int $id): RedirectResponse
    {
        $quote = $this->quoteRepository->with('items')->findOrFail($id);

        DB::beginTransaction();

        try {
            $payload = $quote->toArray();
            unset($payload['id'], $payload['created_at'], $payload['updated_at']);

            $payload['subject'] = $payload['subject'].' (Copy)';
            $payload['status'] = 'draft';
            $payload['quote_number'] = null;
            $payload['charges'] = $this->documentChargeManager->extract($quote, 'quote');

            $payload['items'] = $quote->items->map(function ($item) {
                return [
                    'product_id'        => $item->product_id,
                    'item_name'         => $item->item_name ?: $item->name,
                    'item_code'         => $item->item_code ?: $item->sku,
                    'description'       => $item->description,
                    'qty'               => $item->qty ?: $item->quantity,
                    'unit'              => $item->unit,
                    'unit_price'        => $item->unit_price ?: $item->price,
                    'discount_percent'  => $item->discount_percent,
                    'discount_amount'   => $item->discount_amount,
                    'tax_percent'       => $item->tax_percent,
                    'tax_amount'        => $item->tax_amount,
                ];
            })->toArray();

            $newQuote = $this->quoteRepository->create($payload);
            DB::commit();

            session()->flash('success', 'Quote duplicated successfully.');

            return redirect()->route('admin.quotes.edit', $newQuote->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Convert quote to proforma.
     */
    public function convertToProforma(int $id): RedirectResponse
    {
        $quote = $this->quoteRepository->with('items', 'organization', 'person')->findOrFail($id);

        $allowOverride = (bool) request('allow_override', false);

        if ($quote->status !== 'approved' && ! $allowOverride) {
            return redirect()->back()->withErrors([
                'status' => 'Only approved quotes can be converted to proforma.',
            ]);
        }

        Event::dispatch('quote.convert_to_proforma.before', $quote);

        $proforma = $this->proformaInvoiceRepository->createFromQuote($quote, [
            'status' => request('issue_now') ? 'issued' : 'draft',
        ]);

        Event::dispatch('quote.convert_to_proforma.after', [$quote, $proforma]);

        session()->flash('success', 'Proforma invoice created successfully.');

        return redirect()->route('admin.proforma_invoices.edit', $proforma->id);
    }

    /**
     * Validate quote payload while preserving current attribute flow.
     */
    protected function validateQuote(AttributeForm $request, ?int $quoteId = null): void
    {
        $request->validate([
            'quote_number'           => ['nullable', 'string', 'max:50'],
            'organization_id'        => ['required', 'exists:organizations,id'],
            'person_id'              => ['nullable', 'exists:persons,id'],
            'user_id'                => ['required', 'exists:users,id'],
            'subject'                => ['nullable', 'string', 'max:255'],
            'payment_term'           => ['nullable', 'string', 'max:255'],
            'shipping_method'        => ['nullable', 'string', 'max:255'],
            'production_time'        => ['nullable', 'string', 'max:255'],
            'transit_time'           => ['nullable', 'string', 'max:255'],
            'quote_date'             => ['nullable', 'date'],
            'etd'                    => ['nullable', 'date'],
            'eta'                    => ['nullable', 'date'],
            'expired_at'             => ['nullable', 'date'],
            'charges'                => ['nullable', 'array'],
            'charges.*.name'         => ['required_with:charges.*.type,charges.*.value', 'string', 'max:255'],
            'charges.*.type'         => ['required_with:charges.*.name,charges.*.value', 'in:percentage,value'],
            'charges.*.value'        => ['required_with:charges.*.name,charges.*.type', 'numeric', 'min:0'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['nullable', 'exists:products,id'],
            'items.*.item_name'      => ['nullable', 'string', 'max:255'],
            'items.*.qty'            => ['nullable', 'numeric', 'gt:0'],
            'items.*.quantity'       => ['nullable', 'numeric', 'gt:0'],
            'items.*.unit_price'     => ['nullable', 'numeric', 'min:0'],
            'items.*.price'          => ['nullable', 'numeric', 'min:0'],
        ]);

        $organizationId = $request->input('organization_id');

        $customerTypeExists = DB::table('organizations')
            ->where('id', $organizationId)
            ->whereIn('type', ['customer', 'Customer'])
            ->exists();

        if (! $customerTypeExists) {
            throw ValidationException::withMessages([
                'organization_id' => 'Selected organization must be a customer.',
            ]);
        }

        if ($request->filled('person_id')) {
            $personOrgId = DB::table('persons')
                ->where('id', $request->input('person_id'))
                ->value('organization_id');

            if ((int) $personOrgId !== (int) $organizationId) {
                throw ValidationException::withMessages([
                    'person_id' => 'Selected person does not belong to selected customer.',
                ]);
            }
        }
    }
}
