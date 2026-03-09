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
        if (Schema::hasTable('product_consumptions')) {
            return;
        }

        Schema::create('product_consumptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->string('name');
            $table->decimal('qty', 12, 4)->default(0);
            $table->string('unit');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_consumptions');
    }
};
