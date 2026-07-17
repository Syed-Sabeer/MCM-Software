<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('customer_portal_users')) {
            return;
        }

        foreach (['quotes', 'proforma_invoices', 'job_orders'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'customer_visible_at')) {
                continue;
            }

            DB::table($table)
                ->whereNull('customer_visible_at')
                ->where('status', '!=', 'draft')
                ->whereExists(function (Builder $query) use ($table) {
                    $query->selectRaw('1')
                        ->from('customer_portal_users')
                        ->whereColumn('customer_portal_users.organization_id', $table.'.organization_id')
                        ->where('customer_portal_users.status', 'active');
                })
                ->update(['customer_visible_at' => now()]);
        }
    }

    public function down(): void
    {
        // Publication may be changed manually later, so this data update is not reversible safely.
    }
};
