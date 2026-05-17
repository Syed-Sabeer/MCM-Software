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
        if (Schema::hasTable('lead_persons')) {
            return;
        }

        Schema::create('lead_persons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_id')->unsigned();
            $table->integer('person_id')->unsigned();
            $table->timestamps();

            $table->unique(['lead_id', 'person_id']);
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('cascade');
        });

        DB::table('leads')
            ->whereNotNull('person_id')
            ->orderBy('id')
            ->select(['id', 'person_id', 'created_at', 'updated_at'])
            ->chunk(500, function ($leads) {
                foreach ($leads as $lead) {
                    DB::table('lead_persons')->insertOrIgnore([
                        'lead_id'    => $lead->id,
                        'person_id'  => $lead->person_id,
                        'created_at' => $lead->created_at,
                        'updated_at' => $lead->updated_at,
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_persons');
    }
};
