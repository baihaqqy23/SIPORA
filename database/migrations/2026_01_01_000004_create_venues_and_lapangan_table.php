<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('nama');
            $table->text('alamat')->nullable();
            $table->unsignedInteger('kapasitas')->nullable();
            $table->time('jam_operasional_mulai')->default('07:00:00');
            $table->time('jam_operasional_selesai')->default('21:00:00');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lapangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cabor_venue', function (Blueprint $table) {
            $table->foreignId('cabang_olahraga_id')->constrained('cabang_olahraga')->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->primary(['cabang_olahraga_id', 'venue_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cabor_venue');
        Schema::dropIfExists('lapangan');
        Schema::dropIfExists('venues');
    }
};
