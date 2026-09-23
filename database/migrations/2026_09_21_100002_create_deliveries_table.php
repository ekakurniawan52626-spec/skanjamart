<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('courier_id')->constrained()->restrictOnDelete();

            // Token rahasia untuk link kurir (tanpa login): /kurir/{token}
            $table->string('token', 64)->unique();

            // assigned = kurir ditugaskan, on_the_way = sedang mengantar, delivered = sudah tiba
            $table->string('status', 20)->default('assigned');

            // Tiap berapa menit kurir mengirim lokasi (diatur admin)
            $table->unsignedSmallInteger('ping_interval_minutes')->default(15);

            // Titik tujuan (opsional) dan perkiraan tiba manual (opsional, cadangan)
            $table->decimal('dest_lat', 10, 7)->nullable();
            $table->decimal('dest_lng', 10, 7)->nullable();
            $table->timestamp('estimated_arrival_at')->nullable();

            // Lokasi terakhir kurir (salinan dari courier_locations biar cepat dibaca)
            $table->decimal('last_lat', 10, 7)->nullable();
            $table->decimal('last_lng', 10, 7)->nullable();
            $table->string('last_note')->nullable();
            $table->timestamp('last_ping_at')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
