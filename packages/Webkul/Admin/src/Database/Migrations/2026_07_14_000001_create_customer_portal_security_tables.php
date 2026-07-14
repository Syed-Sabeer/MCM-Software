<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_portal_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('organization_id');
            $table->unsignedInteger('person_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status', 20)->default('active')->index();
            $table->string('role', 30)->default('member');
            $table->json('permissions')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->boolean('must_change_password')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->index(['organization_id', 'status']);
            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreign('person_id')->references('id')->on('persons')->nullOnDelete();
        });

        Schema::create('customer_portal_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_portal_user_id')->constrained('customer_portal_users')->cascadeOnDelete();
            $table->string('token_hash', 64)->unique();
            $table->timestamp('expires_at')->index();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_portal_password_resets', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        foreach (['quotes', 'proforma_invoices', 'job_orders'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->timestamp('customer_visible_at')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['quotes', 'proforma_invoices', 'job_orders'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('customer_visible_at');
            });
        }

        Schema::dropIfExists('customer_portal_password_resets');
        Schema::dropIfExists('customer_portal_invitations');
        Schema::dropIfExists('customer_portal_users');
    }
};
