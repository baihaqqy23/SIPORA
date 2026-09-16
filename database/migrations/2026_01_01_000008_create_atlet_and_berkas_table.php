<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atlet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontingen_id')->constrained('kontingen')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->string('nik', 255)->nullable();
            $table->date('tanggal_lahir');
            $table->enum('gender', ['L', 'P']);
            $table->string('asal_kota', 100);
            $table->string('foto_path')->nullable();
            $table->enum('status', ['aktif', 'nonaktif', 'didiskualifikasi'])->default('aktif');
            $table->text('catatan_status')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('kontingen_id');
            $table->index('nama');
        });

        Schema::create('berkas_atlet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atlet_id')->constrained('atlet')->cascadeOnDelete();
            $table->enum('jenis', ['akta', 'kk', 'kartu_pelajar', 'surat_sehat', 'lainnya']);
            $table->string('file_path');
            $table->string('nama_file_asli');
            $table->unsignedInteger('ukuran_byte');
            $table->string('mime', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berkas_atlet');
        Schema::dropIfExists('atlet');
    }
};
