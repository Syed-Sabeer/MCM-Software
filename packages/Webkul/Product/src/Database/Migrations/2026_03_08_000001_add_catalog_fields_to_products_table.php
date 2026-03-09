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
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'internal_code')) {
                $table->string('internal_code')->nullable()->after('sku');
            }

            if (! Schema::hasColumn('products', 'customer_organization_id')) {
                $table->unsignedInteger('customer_organization_id')->nullable()->after('name');
                $table->foreign('customer_organization_id')
                    ->references('id')
                    ->on('organizations')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'customer_organization_id')) {
                $table->dropForeign(['customer_organization_id']);
                $table->dropColumn('customer_organization_id');
            }

            if (Schema::hasColumn('products', 'internal_code')) {
                $table->dropColumn('internal_code');
            }
        });
    }
};
