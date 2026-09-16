<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('kontingen_id')->constrained('kontingen')->cascadeOnDelete();
            $table->foreignId('nomor_lomba_id')->constrained('nomor_lomba')->cascadeOnDelete();
            $table->foreignId('atlet_id')->nullable()->constrained('atlet')->cascadeOnDelete();
            $table->foreignId('tim_kontingen_id')->nullable()->constrained('tim_kontingen')->cascadeOnDelete();
            $table->enum('status', [
                'draft', 'menunggu', 'revisi', 'diverifikasi_cabor',
                'disetujui', 'ditolak', 'ditolak_sistem', 'dibatalkan',
            ])->default('menunggu');
            $table->text('catatan_terakhir')->nullable();
            $table->foreignId('diverifikasi_cabor_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('diverifikasi_cabor_pada')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('disetujui_pada')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['nomor_lomba_id', 'atlet_id']);
            $table->unique(['nomor_lomba_id', 'tim_kontingen_id']);
            $table->index('status');
            $table->index('kontingen_id');
        });

        Schema::create('riwayat_verifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status_sebelum', 30);
            $table->string('status_sesudah', 30);
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_verifikasi');
        Schema::dropIfExists('pendaftaran');
    }
};
