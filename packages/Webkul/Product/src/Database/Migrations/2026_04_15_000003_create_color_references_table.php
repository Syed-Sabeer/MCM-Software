<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('color_references')) {
            return;
        }

        Schema::create('color_references', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('color_references');
    }
};
