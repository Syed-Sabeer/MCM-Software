<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Quote\Models\ProformaInvoice;
use Webkul\Quote\Repositories\InvoiceRepository;

uses(DatabaseTransactions::class);

it('creates one final invoice from a proforma and applies advances and later payments', function () {
    $organizationId = DB::table('organizations')->insertGetId([
        'name' => 'Invoice Test '.str()->uuid(), 'type' => 'customer', 'created_at' => now(), 'updated_at' => now(),
    ]);
    $proformaId = DB::table('proforma_invoices')->insertGetId([
        'proforma_number'     => 'PF-TEST-'.str()->random(10),
        'organization_id'     => $organizationId,
        'issue_date'          => now()->toDateString(),
        'subtotal'            => 1000,
        'grand_total'         => 1000,
        'received_amount'     => 250,
        'remaining_amount'    => 750,
        'status'              => 'partially_paid',
        'customer_visible_at' => now(),
        'created_at'          => now(),
        'updated_at'          => now(),
    ]);
    DB::table('proforma_invoice_items')->insert([
        'proforma_invoice_id' => $proformaId,
        'item_name'           => 'Test item',
        'item_code'           => 'ITEM-1',
        'qty'                 => 10,
        'unit_price'          => 100,
        'line_subtotal'       => 1000,
        'line_total'          => 1000,
        'sort_order'          => 0,
        'created_at'          => now(),
        'updated_at'          => now(),
    ]);
    DB::table('proforma_receipts')->insert([
        'proforma_invoice_id' => $proformaId,
        'receipt_number'      => 'RCP-TEST-'.str()->random(10),
        'payment_date'        => now()->toDateString(),
        'amount'              => 250,
        'created_at'          => now(),
        'updated_at'          => now(),
    ]);

    $repository = app(InvoiceRepository::class);
    $invoice = $repository->createFromProforma(ProformaInvoice::findOrFail($proformaId));

    expect((float) $invoice->advance_applied)->toBe(250.0)
        ->and((float) $invoice->remaining_amount)->toBe(750.0)
        ->and($invoice->status)->toBe('partially_paid')
        ->and($invoice->items)->toHaveCount(1)
        ->and(ProformaInvoice::find($proformaId)->converted_to_invoice_id)->toBe($invoice->id);

    $invoice = $repository->addReceipt($invoice->id, [
        'payment_date' => now()->toDateString(), 'amount' => 300, 'payment_method' => 'Bank transfer',
    ]);

    expect((float) $invoice->received_amount)->toBe(300.0)
        ->and((float) $invoice->remaining_amount)->toBe(450.0);

    expect(fn () => $repository->addReceipt($invoice->id, [
        'payment_date' => now()->toDateString(), 'amount' => 451,
    ]))->toThrow(ValidationException::class);
});
