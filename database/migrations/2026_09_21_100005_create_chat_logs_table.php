<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('question', 500);
            $table->string('intent', 30)->nullable();
            $table->foreignId('faq_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('answered')->default(true);
            $table->timestamps();

            $table->index(['answered', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_logs');
    }
};
