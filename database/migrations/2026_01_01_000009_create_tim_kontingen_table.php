<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tim_kontingen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontingen_id')->constrained('kontingen')->cascadeOnDelete();
            $table->foreignId('nomor_lomba_id')->constrained('nomor_lomba')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('anggota_tim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tim_kontingen_id')->constrained('tim_kontingen')->cascadeOnDelete();
            $table->foreignId('atlet_id')->constrained('atlet')->cascadeOnDelete();
            $table->enum('peran', ['inti', 'cadangan'])->default('inti');
            $table->timestamps();

            $table->unique(['tim_kontingen_id', 'atlet_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_tim');
        Schema::dropIfExists('tim_kontingen');
    }
};
