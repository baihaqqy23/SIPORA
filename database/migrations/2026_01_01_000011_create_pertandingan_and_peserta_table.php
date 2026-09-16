<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertandingan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomor_lomba_id')->constrained('nomor_lomba')->cascadeOnDelete();
            $table->foreignId('lapangan_id')->nullable()->constrained('lapangan')->nullOnDelete();
            $table->string('babak', 50);
            $table->integer('urutan_bracket')->nullable();
            $table->foreignId('parent_pertandingan_id')->nullable()->constrained('pertandingan')->nullOnDelete();
            $table->date('tanggal')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->unsignedSmallInteger('durasi_menit')->nullable();
            $table->enum('status', [
                'draft', 'terjadwal', 'dipublikasikan',
                'berlangsung', 'selesai', 'ditunda', 'dibatalkan',
            ])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tanggal', 'lapangan_id']);
        });

        Schema::create('peserta_pertandingan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertandingan_id')->constrained('pertandingan')->cascadeOnDelete();
            $table->string('peserta_type', 100)->nullable();
            $table->unsignedBigInteger('peserta_id')->nullable();
            $table->unsignedTinyInteger('slot');
            $table->unsignedTinyInteger('lintasan')->nullable();
            $table->integer('skor')->nullable();
            $table->decimal('catatan_waktu', 8, 3)->nullable();
            $table->decimal('nilai', 6, 2)->nullable();
            $table->enum('hasil', ['menang', 'kalah', 'seri', 'wo', 'diskualifikasi', 'cedera'])->nullable();
            $table->unsignedTinyInteger('peringkat')->nullable();
            $table->timestamps();

            $table->unique(['pertandingan_id', 'slot']);
            $table->index(['peserta_type', 'peserta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_pertandingan');
        Schema::dropIfExists('pertandingan');
    }
};
