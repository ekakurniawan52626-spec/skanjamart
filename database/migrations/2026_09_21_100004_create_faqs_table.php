<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50)->default('umum');
            $table->string('question');
            $table->text('keywords')->nullable();   // kata kunci tambahan, pisahkan dengan koma
            $table->text('answer');
            $table->string('action_label', 60)->nullable(); // tombol di bawah jawaban (opsional)
            $table->string('action_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('hits')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
