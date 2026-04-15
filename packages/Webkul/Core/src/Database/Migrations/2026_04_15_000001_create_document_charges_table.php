<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('document_charges')) {
            return;
        }

        Schema::create('document_charges', function (Blueprint $table) {
            $table->id();
            $table->morphs('chargeable');
            $table->string('name');
            $table->string('type', 20);
            $table->decimal('value', 12, 4)->default(0);
            $table->decimal('amount', 12, 4)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_charges');
    }
};
