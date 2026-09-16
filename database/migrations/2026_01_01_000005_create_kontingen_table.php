<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kontingen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('nama');
            $table->string('slug');
            $table->string('provinsi', 100);
            $table->string('kota', 100);
            $table->string('nama_ofisial', 150);
            $table->string('no_hp_ofisial', 20);
            $table->string('email', 150);
            $table->string('surat_mandat_path')->nullable();
            $table->string('logo_path')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'nonaktif'])->default('menunggu');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['event_id', 'provinsi', 'kota']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kontingen');
    }
};
