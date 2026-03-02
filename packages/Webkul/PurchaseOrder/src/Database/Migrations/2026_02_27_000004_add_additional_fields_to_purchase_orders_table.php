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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('description');
            $table->date('completion_date')->nullable()->after('notes');
            $table->date('last_delivery_date')->nullable()->after('completion_date');
            $table->string('payment_term')->nullable()->after('last_delivery_date');
            $table->string('shipping_method')->nullable()->after('payment_term');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn([
                'notes',
                'completion_date',
                'last_delivery_date',
                'payment_term',
                'shipping_method',
            ]);
        });
    }
};
