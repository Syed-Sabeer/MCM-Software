<?php

namespace Webkul\Admin\Http\Controllers\CustomerPortal;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\Quote\Models\Invoice;
use Webkul\Quote\Models\ProformaInvoice;
use Webkul\Quote\Models\Quote;

class VisibilityController extends Controller
{
    public function quote(int $id): RedirectResponse
    {
        abort_unless(bouncer()->hasPermission('quotes.edit'), 403);

        return $this->toggle(Quote::findOrFail($id), 'quote');
    }

    public function proforma(int $id): RedirectResponse
    {
        abort_unless(bouncer()->hasPermission('proforma_invoices.edit'), 403);
        $record = ProformaInvoice::findOrFail($id);
        abort_if($record->status === 'draft' && ! $record->customer_visible_at, 422, 'Draft proformas cannot be published.');

        return $this->toggle($record, 'proforma invoice');
    }

    public function jobOrder(int $id): RedirectResponse
    {
        abort_unless(bouncer()->hasPermission('job_orders.edit'), 403);
        $record = JobOrder::findOrFail($id);
        abort_if($record->status === 'draft' && ! $record->customer_visible_at, 422, 'Draft job orders cannot be published.');

        return $this->toggle($record, 'job order');
    }

    public function invoice(int $id): RedirectResponse
    {
        abort_unless(bouncer()->hasPermission('invoices.edit'), 403);

        return $this->toggle(Invoice::findOrFail($id), 'final invoice');
    }

    protected function toggle($record, string $type): RedirectResponse
    {
        $published = $record->customer_visible_at === null;
        $record->forceFill(['customer_visible_at' => $published ? now() : null])->save();
        Log::info('Customer document visibility changed', [
            'document_type' => $type, 'document_id' => $record->id, 'published' => $published, 'actor_id' => auth()->id(),
        ]);

        return back()->with('success', ucfirst($type).' '.($published ? 'published to' : 'hidden from').' the customer portal.');
    }
}
