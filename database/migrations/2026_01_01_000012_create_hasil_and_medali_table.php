<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_pertandingan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertandingan_id')->unique()->constrained('pertandingan')->cascadeOnDelete();
            $table->foreignId('pemenang_peserta_id')->nullable()->constrained('peserta_pertandingan')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->foreignId('diinput_oleh')->constrained('users')->cascadeOnDelete();
            $table->dateTime('diinput_pada');
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('alasan_perubahan')->nullable();
            $table->timestamps();
        });

        Schema::create('medali', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomor_lomba_id')->constrained('nomor_lomba')->cascadeOnDelete();
            $table->foreignId('kontingen_id')->constrained('kontingen')->cascadeOnDelete();
            $table->string('peserta_type', 100);
            $table->unsignedBigInteger('peserta_id');
            $table->enum('jenis', ['emas', 'perak', 'perunggu']);
            $table->timestamps();

            $table->index(['kontingen_id', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medali');
        Schema::dropIfExists('hasil_pertandingan');
    }
};
