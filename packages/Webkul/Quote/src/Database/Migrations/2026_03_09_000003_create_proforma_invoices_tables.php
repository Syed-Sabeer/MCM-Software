<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proforma_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('proforma_number')->unique();
            $table->unsignedInteger('quote_id')->nullable();
            $table->unsignedInteger('organization_id');
            $table->unsignedInteger('person_id')->nullable();
            $table->unsignedInteger('sales_owner_id')->nullable();
            $table->string('subject')->nullable();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->json('billing_address')->nullable();
            $table->json('shipping_address')->nullable();
            $table->decimal('subtotal', 12, 4)->default(0);
            $table->decimal('discount_percent', 12, 4)->nullable()->default(0);
            $table->decimal('discount_amount', 12, 4)->default(0);
            $table->decimal('tax_amount', 12, 4)->default(0);
            $table->decimal('adjustment_amount', 12, 4)->default(0);
            $table->decimal('grand_total', 12, 4)->default(0);
            $table->decimal('received_amount', 12, 4)->default(0);
            $table->decimal('remaining_amount', 12, 4)->default(0);
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->string('customer_po_reference')->nullable();
            $table->string('source_type')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();

            $table->foreign('quote_id')->references('id')->on('quotes')->nullOnDelete();
            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreign('person_id')->references('id')->on('persons')->nullOnDelete();
            $table->foreign('sales_owner_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('proforma_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proforma_invoice_id');
            $table->unsignedInteger('product_id')->nullable();
            $table->string('item_name');
            $table->string('item_code')->nullable();
            $table->text('description')->nullable();
            $table->decimal('qty', 12, 4)->default(0);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 12, 4)->default(0);
            $table->decimal('discount_percent', 12, 4)->nullable()->default(0);
            $table->decimal('discount_amount', 12, 4)->nullable()->default(0);
            $table->decimal('tax_percent', 12, 4)->nullable()->default(0);
            $table->decimal('tax_amount', 12, 4)->nullable()->default(0);
            $table->decimal('line_subtotal', 12, 4)->default(0);
            $table->decimal('line_total', 12, 4)->default(0);
            $table->integer('sort_order')->nullable();
            $table->timestamps();

            $table->foreign('proforma_invoice_id')->references('id')->on('proforma_invoices')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });

        Schema::create('proforma_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proforma_invoice_id');
            $table->string('receipt_number')->nullable();
            $table->date('payment_date');
            $table->decimal('amount', 12, 4);
            $table->string('payment_method')->nullable();
            $table->string('reference_no')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('received_by')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();

            $table->foreign('proforma_invoice_id')->references('id')->on('proforma_invoices')->cascadeOnDelete();
            $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_receipts');
        Schema::dropIfExists('proforma_invoice_items');
        Schema::dropIfExists('proforma_invoices');
    }
};
