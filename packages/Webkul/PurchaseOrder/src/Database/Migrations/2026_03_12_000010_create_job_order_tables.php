<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('job_order_number')->unique();
            $table->unsignedBigInteger('proforma_invoice_id');
            $table->unsignedInteger('organization_id');
            $table->unsignedInteger('person_id')->nullable();
            $table->string('customer_po_reference')->nullable();
            $table->string('subject')->nullable();
            $table->date('issue_date');
            $table->date('required_delivery_date')->nullable();
            $table->string('status', 50)->default('open');
            $table->decimal('total_order_qty', 12, 4)->default(0);
            $table->text('remarks')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('proforma_invoice_id')->references('id')->on('proforma_invoices')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('job_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('job_order_id');
            $table->unsignedBigInteger('proforma_invoice_item_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->string('item_name');
            $table->string('item_code')->nullable();
            $table->text('description')->nullable();
            $table->decimal('qty', 12, 4)->default(0);
            $table->string('unit', 100)->nullable();
            $table->decimal('unit_price', 12, 4)->nullable();
            $table->decimal('line_total', 12, 4)->nullable();
            $table->integer('sort_order')->nullable();
            $table->timestamps();

            $table->foreign('job_order_id')->references('id')->on('job_orders')->onDelete('cascade');
            $table->foreign('proforma_invoice_item_id')->references('id')->on('proforma_invoice_items')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });

        Schema::create('job_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('job_order_id');
            $table->unsignedInteger('job_order_item_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->string('title')->nullable();
            $table->string('status', 50)->default('open');
            $table->text('remarks')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('job_order_id')->references('id')->on('job_orders')->onDelete('cascade');
            $table->foreign('job_order_item_id')->references('id')->on('job_order_items')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('job_card_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('job_card_id');
            $table->unsignedInteger('source_product_section_id')->nullable();
            $table->string('section_name');
            $table->integer('sort_order')->nullable();
            $table->string('status', 50)->default('not_started');
            $table->timestamps();

            $table->foreign('job_card_id')->references('id')->on('job_cards')->onDelete('cascade');
            $table->foreign('source_product_section_id')->references('id')->on('product_production_sections')->onDelete('set null');
        });

        Schema::create('job_card_section_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('job_card_section_id');
            $table->unsignedInteger('source_product_section_item_id')->nullable();
            $table->string('name');
            $table->decimal('qty', 12, 4)->nullable();
            $table->string('unit', 100)->nullable();
            $table->integer('sort_order')->nullable();
            $table->timestamps();

            $table->foreign('job_card_section_id')->references('id')->on('job_card_sections')->onDelete('cascade');
            $table->foreign('source_product_section_item_id')->references('id')->on('product_production_section_items')->onDelete('set null');
        });

        Schema::create('job_order_requirements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('job_order_id');
            $table->unsignedInteger('job_order_item_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->string('material_name');
            $table->string('unit', 100)->nullable();
            $table->decimal('qty_per_unit', 12, 4)->default(0);
            $table->decimal('ordered_qty', 12, 4)->default(0);
            $table->decimal('required_qty', 12, 4)->default(0);
            $table->decimal('received_qty', 12, 4)->default(0);
            $table->decimal('balance_qty', 12, 4)->default(0);
            $table->string('status', 50)->default('pending');
            $table->integer('sort_order')->nullable();
            $table->timestamps();

            $table->foreign('job_order_id')->references('id')->on('job_orders')->onDelete('cascade');
            $table->foreign('job_order_item_id')->references('id')->on('job_order_items')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_order_requirements');
        Schema::dropIfExists('job_card_section_items');
        Schema::dropIfExists('job_card_sections');
        Schema::dropIfExists('job_cards');
        Schema::dropIfExists('job_order_items');
        Schema::dropIfExists('job_orders');
    }
};
