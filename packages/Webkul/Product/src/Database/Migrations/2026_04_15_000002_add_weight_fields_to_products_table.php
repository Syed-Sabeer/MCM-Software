<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'weight')) {
                $table->decimal('weight', 12, 4)->nullable()->after('size');
            }

            if (! Schema::hasColumn('products', 'weight_unit')) {
                $table->string('weight_unit', 10)->nullable()->after('weight');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'weight_unit')) {
                $table->dropColumn('weight_unit');
            }

            if (Schema::hasColumn('products', 'weight')) {
                $table->dropColumn('weight');
            }
        });
    }
};
