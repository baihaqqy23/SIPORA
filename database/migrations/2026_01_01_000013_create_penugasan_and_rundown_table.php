<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan_panitia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('panitia_id')->constrained('panitia')->cascadeOnDelete();
            $table->foreignId('cabang_olahraga_id')->nullable()->constrained('cabang_olahraga')->cascadeOnDelete();
            $table->foreignId('pertandingan_id')->nullable()->constrained('pertandingan')->cascadeOnDelete();
            $table->enum('peran', [
                'ketua_pelaksana', 'pj_cabor', 'wasit', 'juri',
                'pencatat_skor', 'lo_kontingen', 'medis', 'keamanan', 'dokumentasi',
            ]);
            $table->timestamps();

            $table->index('panitia_id');
            $table->index('pertandingan_id');
        });

        Schema::create('acara_rundown', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai')->nullable();
            $table->string('judul', 150);
            $table->string('lokasi', 150)->nullable();
            $table->string('penanggung_jawab', 150)->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('dipublikasikan')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acara_rundown');
        Schema::dropIfExists('penugasan_panitia');
    }
};
