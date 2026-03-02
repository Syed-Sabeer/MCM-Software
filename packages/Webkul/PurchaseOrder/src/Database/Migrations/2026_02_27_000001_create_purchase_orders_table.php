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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po_number')->unique();
            $table->string('job_number')->nullable();
            $table->text('description')->nullable();
            $table->decimal('sales_tax_percent', 12, 4)->default(0);
            $table->decimal('freight', 12, 4)->default(0);
            $table->decimal('sub_total', 12, 4)->default(0);
            $table->decimal('tax_amount', 12, 4)->default(0);
            $table->decimal('grand_total', 12, 4)->default(0);
            $table->integer('person_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
