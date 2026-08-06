<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('photo');
            $table->decimal('latitude',10,8);
            $table->decimal('longitude',11,8);
            $table->text('address');
            $table->string('ai_category')->nullable();
            $table->decimal('ai_confidence',5,2)->nullable();
            $table->text('ai_response')->nullable();
            $table->timestamp('classified_at')->nullable();
            $table->enum('verification_status',['pending','valid','hoax'])->default('pending');
            $table->enum('status',['menunggu','diproses','selesai','ditolak'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
