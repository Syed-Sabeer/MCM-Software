<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_quote_items', function (Blueprint $table) {
            if (! Schema::hasColumn('vendor_quote_items', 'vendor_id')) {
                $table->unsignedInteger('vendor_id')->nullable()->after('requirement_id');
                $table->foreign('vendor_id')->references('id')->on('organizations')->onDelete('set null');
            }

            if (! Schema::hasColumn('vendor_quote_items', 'color')) {
                $table->string('color')->nullable()->after('material_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendor_quote_items', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_quote_items', 'vendor_id')) {
                $table->dropForeign(['vendor_id']);
                $table->dropColumn('vendor_id');
            }

            if (Schema::hasColumn('vendor_quote_items', 'color')) {
                $table->dropColumn('color');
            }
        });
    }
};
