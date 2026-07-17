<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('proforma_invoice_id')->unique();
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
            $table->decimal('tax_amount', 12, 4)->default(0);
            $table->decimal('adjustment_amount', 12, 4)->default(0);
            $table->decimal('grand_total', 12, 4)->default(0);
            $table->decimal('advance_applied', 12, 4)->default(0);
            $table->decimal('received_amount', 12, 4)->default(0);
            $table->decimal('remaining_amount', 12, 4)->default(0);
            $table->string('status', 30)->default('issued')->index();
            $table->string('payment_term')->nullable();
            $table->string('customer_po_reference')->nullable();
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->string('attachment_path')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamp('customer_visible_at')->nullable()->index();
            $table->timestamps();

            $table->foreign('proforma_invoice_id')->references('id')->on('proforma_invoices')->restrictOnDelete();
            $table->foreign('quote_id')->references('id')->on('quotes')->nullOnDelete();
            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreign('person_id')->references('id')->on('persons')->nullOnDelete();
            $table->foreign('sales_owner_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->unsignedInteger('product_id')->nullable();
            $table->unsignedInteger('color_variant_id')->nullable();
            $table->string('color_variant_name')->nullable();
            $table->string('preview_image')->nullable();
            $table->string('item_name');
            $table->string('item_code')->nullable();
            $table->text('description')->nullable();
            $table->decimal('qty', 12, 4)->default(0);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 12, 4)->default(0);
            $table->decimal('line_total', 12, 4)->default(0);
            $table->integer('sort_order')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });

        Schema::create('invoice_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('receipt_number')->unique();
            $table->date('payment_date');
            $table->decimal('amount', 12, 4);
            $table->string('payment_method')->nullable();
            $table->string('reference_no')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('received_by')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();

            $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_receipts');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
