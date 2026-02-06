<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('organizations', function (Blueprint $table) {
            $table->unsignedInteger('parent_organization_id')->nullable()->after('id');

            $table->string('phone')->nullable()->after('address');
            $table->string('fax')->nullable()->after('phone');
            $table->string('website')->nullable()->after('fax');

            $table->string('type')->nullable()->after('website');
            $table->string('industry')->nullable()->after('type');
            $table->integer('employees')->nullable()->after('industry');
            $table->decimal('annual_revenue', 12, 2)->nullable()->after('employees');
            $table->text('description')->nullable()->after('annual_revenue');

            $table->string('billing_street')->nullable()->after('description');
            $table->string('billing_city')->nullable()->after('billing_street');
            $table->string('billing_state')->nullable()->after('billing_city');
            $table->string('billing_postcode')->nullable()->after('billing_state');
            $table->string('billing_country')->nullable()->after('billing_postcode');

            $table->string('shipping_street')->nullable()->after('billing_country');
            $table->string('shipping_city')->nullable()->after('shipping_street');
            $table->string('shipping_state')->nullable()->after('shipping_city');
            $table->string('shipping_postcode')->nullable()->after('shipping_state');
            $table->string('shipping_country')->nullable()->after('shipping_postcode');

            $table->foreign('parent_organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['parent_organization_id']);

            $table->dropColumn([
                'parent_organization_id',
                'phone',
                'fax',
                'website',
                'type',
                'industry',
                'employees',
                'annual_revenue',
                'description',
                'billing_street',
                'billing_city',
                'billing_state',
                'billing_postcode',
                'billing_country',
                'shipping_street',
                'shipping_city',
                'shipping_state',
                'shipping_postcode',
                'shipping_country',
            ]);
        });
    }
};

