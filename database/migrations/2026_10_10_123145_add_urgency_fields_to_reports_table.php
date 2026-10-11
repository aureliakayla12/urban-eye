<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->enum('urgency_level', [
                'sangat_mendesak',
                'mendesak',
                'sedang',
                'rendah',
            ])->nullable()->index();

            $table->text('urgency_reason')->nullable();

            $table->timestamp('urgency_analyzed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['urgency_level']);
            $table->dropColumn([
                'urgency_level',
                'urgency_reason',
                'urgency_analyzed_at',
            ]);
        });
    }
};