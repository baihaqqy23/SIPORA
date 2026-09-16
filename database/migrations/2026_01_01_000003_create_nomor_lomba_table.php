<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nomor_lomba', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabang_olahraga_id')->constrained('cabang_olahraga')->cascadeOnDelete();
            $table->string('nama');
            $table->enum('gender', ['putra', 'putri', 'campuran']);
            $table->enum('jenis', ['perorangan', 'beregu'])->default('perorangan');
            $table->unsignedTinyInteger('jumlah_anggota')->nullable();
            $table->unsignedTinyInteger('jumlah_cadangan')->nullable();
            $table->unsignedTinyInteger('umur_min')->nullable();
            $table->unsignedTinyInteger('umur_maks')->nullable();
            $table->enum('format_pertandingan', ['gugur_tunggal', 'round_robin', 'heat', 'penilaian'])->default('gugur_tunggal');
            $table->unsignedTinyInteger('kuota_per_kontingen')->nullable()->default(2);
            $table->unsignedSmallInteger('kapasitas_total')->nullable();
            $table->unsignedTinyInteger('jumlah_perunggu')->default(1);
            $table->enum('status_bracket', ['belum', 'tergenerate', 'terkunci'])->default('belum');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomor_lomba');
    }
};
