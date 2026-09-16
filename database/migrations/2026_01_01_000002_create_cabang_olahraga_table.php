<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabang_olahraga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('nama');
            $table->string('singkatan', 10);
            $table->string('warna', 7)->default('#C8102E');
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('durasi_default_menit')->default(60);
            $table->unsignedSmallInteger('jeda_antar_tanding_menit')->default(30);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['event_id', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cabang_olahraga');
    }
};
