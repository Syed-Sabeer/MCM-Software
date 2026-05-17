<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_references', function (Blueprint $table) {
            if (! Schema::hasColumn('unit_references', 'meter_conversion')) {
                $table->decimal('meter_conversion', 16, 8)->nullable()->after('name');
            }
        });

        $conversions = [
            'METER'      => 1,
            'METERS'     => 1,
            'M'          => 1,
            'INCH'       => 0.0254,
            'INCHES'     => 0.0254,
            'FOOT'       => 0.3048,
            'FEET'       => 0.3048,
            'FT'         => 0.3048,
            'CENTIMETER' => 0.01,
            'CENTIMETERS'=> 0.01,
            'CM'         => 0.01,
        ];

        foreach ($conversions as $unit => $conversion) {
            DB::table('unit_references')
                ->whereRaw('UPPER(TRIM(name)) = ?', [$unit])
                ->whereNull('meter_conversion')
                ->update(['meter_conversion' => $conversion]);
        }
    }

    public function down(): void
    {
        Schema::table('unit_references', function (Blueprint $table) {
            if (Schema::hasColumn('unit_references', 'meter_conversion')) {
                $table->dropColumn('meter_conversion');
            }
        });
    }
};
