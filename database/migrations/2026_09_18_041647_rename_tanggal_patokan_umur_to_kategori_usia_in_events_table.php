<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('tanggal_patokan_umur');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('kategori_usia')->default('senior')->after('tanggal_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('kategori_usia');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->date('tanggal_patokan_umur')->nullable()->after('tanggal_selesai');
        });
    }
};
