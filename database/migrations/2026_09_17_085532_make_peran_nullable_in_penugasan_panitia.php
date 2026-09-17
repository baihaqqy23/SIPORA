<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penugasan_panitia', function (Blueprint $table) {
            $table->enum('peran', [
                'ketua_pelaksana', 'pj_cabor', 'wasit', 'juri',
                'pencatat_skor', 'lo_kontingen', 'medis', 'keamanan', 'dokumentasi',
            ])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('penugasan_panitia', function (Blueprint $table) {
            $table->enum('peran', [
                'ketua_pelaksana', 'pj_cabor', 'wasit', 'juri',
                'pencatat_skor', 'lo_kontingen', 'medis', 'keamanan', 'dokumentasi',
            ])->nullable(false)->change();
        });
    }
};
