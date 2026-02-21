<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'organization_id')) {
                $table->unsignedInteger('organization_id')->nullable()->after('person_id');
                $table->index('organization_id');
            }
        });

        DB::statement(
            'UPDATE leads '
            .'JOIN persons ON leads.person_id = persons.id '
            .'SET leads.organization_id = persons.organization_id '
            .'WHERE leads.organization_id IS NULL'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('leads', 'organization_id')) {
            return;
        }

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
            $table->dropColumn('organization_id');
        });
    }
};
