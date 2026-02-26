<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('case_no', 20)->nullable()->after('id');
        });

        // Backfill existing leads with case numbers 00001, 00002, etc.
        $prefix = DB::getTablePrefix();
        $leads = DB::table($prefix . 'leads')->orderBy('id')->get();
        foreach ($leads as $index => $lead) {
            DB::table($prefix . 'leads')
                ->where('id', $lead->id)
                ->update(['case_no' => str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT)]);
        }

        Schema::table('leads', function (Blueprint $table) {
            $table->string('case_no', 20)->nullable(false)->change();
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->unique('case_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropUnique(['case_no']);
            $table->dropColumn('case_no');
        });
    }
};
