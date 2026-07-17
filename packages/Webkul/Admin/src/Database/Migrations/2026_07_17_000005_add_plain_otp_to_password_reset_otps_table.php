<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('password_reset_otps') && ! Schema::hasColumn('password_reset_otps', 'otp')) {
            Schema::table('password_reset_otps', function (Blueprint $table) {
                $table->string('otp', 6)->nullable()->after('account_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('password_reset_otps') && Schema::hasColumn('password_reset_otps', 'otp')) {
            Schema::table('password_reset_otps', function (Blueprint $table) {
                $table->dropColumn('otp');
            });
        }
    }
};
