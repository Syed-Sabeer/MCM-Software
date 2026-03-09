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
        Schema::table('quotes', function (Blueprint $table) {
            if (! Schema::hasColumn('quotes', 'quote_number')) {
                $table->string('quote_number')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('quotes', 'organization_id')) {
                $table->unsignedInteger('organization_id')->nullable()->after('person_id');
                $table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();
            }

            if (! Schema::hasColumn('quotes', 'quote_date')) {
                $table->date('quote_date')->nullable()->after('subject');
            }

            if (! Schema::hasColumn('quotes', 'status')) {
                $table->string('status')->default('draft')->after('grand_total');
            }

            if (! Schema::hasColumn('quotes', 'notes')) {
                $table->text('notes')->nullable()->after('description');
            }

            if (! Schema::hasColumn('quotes', 'terms')) {
                $table->text('terms')->nullable()->after('notes');
            }

            if (! Schema::hasColumn('quotes', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('terms');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            if (Schema::hasColumn('quotes', 'organization_id')) {
                $table->dropForeign(['organization_id']);
            }

            $dropColumns = array_filter([
                Schema::hasColumn('quotes', 'quote_number') ? 'quote_number' : null,
                Schema::hasColumn('quotes', 'organization_id') ? 'organization_id' : null,
                Schema::hasColumn('quotes', 'quote_date') ? 'quote_date' : null,
                Schema::hasColumn('quotes', 'status') ? 'status' : null,
                Schema::hasColumn('quotes', 'notes') ? 'notes' : null,
                Schema::hasColumn('quotes', 'terms') ? 'terms' : null,
                Schema::hasColumn('quotes', 'attachment_path') ? 'attachment_path' : null,
            ]);

            if (! empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
