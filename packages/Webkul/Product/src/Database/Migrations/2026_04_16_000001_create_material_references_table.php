<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('material_references')) {
            Schema::create('material_references', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->decimal('qty', 12, 4)->default(0);
                $table->string('unit', 100);
                $table->string('color_name')->nullable();
                $table->string('color_code', 20)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('material_reference_vendor')) {
            Schema::create('material_reference_vendor', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('material_reference_id');
                $table->unsignedInteger('organization_id');
                $table->timestamps();

                $table->unique(['material_reference_id', 'organization_id'], 'material_reference_vendor_unique');
                $table->foreign('material_reference_id')->references('id')->on('material_references')->cascadeOnDelete();
                $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('material_reference_vendor');
        Schema::dropIfExists('material_references');
    }
};
