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
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('category_id')->nullable()->after('price');
            $table->string('style')->nullable()->after('category_id');
            $table->string('size', 100)->nullable()->after('style');
            $table->string('cover_image', 500)->nullable()->after('size');
            $table->longText('additional_info')->nullable()->after('cover_image');
            $table->longText('shipping_info')->nullable()->after('additional_info');

            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id',
                'style',
                'size',
                'cover_image',
                'additional_info',
                'shipping_info',
            ]);
        });
    }
};
