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
        if (! Schema::hasTable('product_production_sections')) {
            Schema::create('product_production_sections', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('product_id');
                $table->string('section_name');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('product_production_section_items')) {
            Schema::create('product_production_section_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_production_section_id');
                $table->string('name');
                $table->decimal('qty', 12, 4)->default(0);
                $table->string('unit');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('product_production_section_id', 'pps_items_section_id_foreign')
                    ->references('id')
                    ->on('product_production_sections')
                    ->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_production_section_items');
        Schema::dropIfExists('product_production_sections');
    }
};
