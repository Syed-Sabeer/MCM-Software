<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Webkul\Admin\Support\DocumentStatusOptions;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('document_statuses')) {
            return;
        }

        Schema::create('document_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('type', 80);
            $table->string('name');
            $table->string('value');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['type', 'value']);
            $table->index('type');
        });

        $now = now();
        $rows = [];

        foreach (DocumentStatusOptions::TYPES as $type => $statuses) {
            foreach ($statuses as $index => $value) {
                $rows[] = [
                    'type'       => $type,
                    'name'       => Str::headline($value),
                    'value'      => $value,
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('document_statuses')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('document_statuses');
    }
};
