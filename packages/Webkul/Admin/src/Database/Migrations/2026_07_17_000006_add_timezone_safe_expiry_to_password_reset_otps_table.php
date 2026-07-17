<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('password_reset_otps') && ! Schema::hasColumn('password_reset_otps', 'expires_at_epoch')) {
            Schema::table('password_reset_otps', function (Blueprint $table) {
                $table->unsignedBigInteger('expires_at_epoch')->nullable()->index()->after('expires_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('password_reset_otps') && Schema::hasColumn('password_reset_otps', 'expires_at_epoch')) {
            Schema::table('password_reset_otps', function (Blueprint $table) {
                $table->dropIndex(['expires_at_epoch']);
                $table->dropColumn('expires_at_epoch');
            });
        }
    }
};
