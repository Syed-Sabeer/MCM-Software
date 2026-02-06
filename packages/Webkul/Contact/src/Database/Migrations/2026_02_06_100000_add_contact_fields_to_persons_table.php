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
        Schema::table('persons', function (Blueprint $table) {
            $table->string('salutation')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->string('cell_phone')->nullable();
            $table->string('direct_phone')->nullable();
            $table->string('email_secondary')->nullable();
            $table->date('birth_date')->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('mailing_street')->nullable();
            $table->string('mailing_city')->nullable();
            $table->string('mailing_state')->nullable();
            $table->string('mailing_postcode')->nullable();
            $table->string('mailing_country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn([
                'salutation',
                'first_name',
                'last_name',
                'title',
                'description',
                'cell_phone',
                'direct_phone',
                'email_secondary',
                'birth_date',
                'phone',
                'email',
                'mailing_street',
                'mailing_city',
                'mailing_state',
                'mailing_postcode',
                'mailing_country',
            ]);
        });
    }
};
