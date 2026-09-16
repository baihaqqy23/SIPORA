<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('logo_path')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->date('tanggal_patokan_umur');
            $table->dateTime('pendaftaran_mulai');
            $table->dateTime('pendaftaran_selesai');
            $table->unsignedTinyInteger('maks_nomor_lomba_per_atlet')->nullable()->default(3);
            $table->enum('status', ['draft', 'pendaftaran_dibuka', 'pendaftaran_ditutup', 'berlangsung', 'selesai'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
