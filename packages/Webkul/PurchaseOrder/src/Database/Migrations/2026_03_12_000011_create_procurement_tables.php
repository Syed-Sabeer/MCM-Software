<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vendor_quote_number')->unique();
            $table->unsignedInteger('job_order_id')->nullable();
            $table->unsignedInteger('organization_id');
            $table->unsignedInteger('person_id')->nullable();
            $table->date('issue_date');
            $table->date('expected_response_date')->nullable();
            $table->string('status', 50)->default('draft');
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('job_order_id')->references('id')->on('job_orders')->onDelete('set null');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('vendor_quote_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vendor_quote_id');
            $table->unsignedInteger('requirement_id')->nullable();
            $table->string('material_name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 4)->default(0);
            $table->string('unit', 100)->nullable();
            $table->decimal('unit_price', 12, 4)->nullable();
            $table->decimal('total', 12, 4)->nullable();
            $table->string('vendor_lead_time')->nullable();
            $table->date('expected_receive_date')->nullable();
            $table->integer('sort_order')->nullable();
            $table->timestamps();

            $table->foreign('vendor_quote_id')->references('id')->on('vendor_quotes')->onDelete('cascade');
            $table->foreign('requirement_id')->references('id')->on('job_order_requirements')->onDelete('set null');
        });

        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('goods_receipt_number')->unique();
            $table->unsignedInteger('purchase_order_id');
            $table->unsignedInteger('vendor_id')->nullable();
            $table->date('receipt_date');
            $table->unsignedInteger('received_by')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('status', 50)->default('posted');
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('organizations')->onDelete('set null');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('goods_receipt_id');
            $table->unsignedInteger('purchase_order_item_id');
            $table->unsignedInteger('requirement_id')->nullable();
            $table->string('material_name');
            $table->decimal('received_qty', 12, 4)->default(0);
            $table->string('unit', 100)->nullable();
            $table->decimal('unit_price', 12, 4)->default(0);
            $table->decimal('line_total', 12, 4)->default(0);
            $table->timestamps();

            $table->foreign('goods_receipt_id')->references('id')->on('goods_receipts')->onDelete('cascade');
            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items')->onDelete('cascade');
            $table->foreign('requirement_id')->references('id')->on('job_order_requirements')->onDelete('set null');
        });

        Schema::create('vendor_payables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_order_id')->nullable();
            $table->unsignedInteger('goods_receipt_id')->nullable();
            $table->unsignedInteger('organization_id');
            $table->string('payable_number')->nullable()->unique();
            $table->date('payable_date');
            $table->decimal('total_amount', 12, 4)->default(0);
            $table->decimal('paid_amount', 12, 4)->default(0);
            $table->decimal('remaining_amount', 12, 4)->default(0);
            $table->string('status', 50)->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            $table->foreign('goods_receipt_id')->references('id')->on('goods_receipts')->onDelete('set null');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_payables');
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('vendor_quote_items');
        Schema::dropIfExists('vendor_quotes');
    }
};
