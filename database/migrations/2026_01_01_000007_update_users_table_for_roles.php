<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'pj_cabor', 'kontingen'])->default('kontingen')->after('password');
            $table->foreignId('kontingen_id')->nullable()->after('role')->constrained('kontingen')->nullOnDelete();
            $table->foreignId('panitia_id')->nullable()->after('kontingen_id')->constrained('panitia')->nullOnDelete();
            $table->boolean('harus_ganti_password')->default(false)->after('panitia_id');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('harus_ganti_password');
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['kontingen_id']);
            $table->dropForeign(['panitia_id']);
            $table->dropColumn(['role', 'kontingen_id', 'panitia_id', 'harus_ganti_password', 'status', 'deleted_at']);
        });
    }
};
