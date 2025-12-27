<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'admin',
                'ketua_pkbm',
                'sekretaris',
                'bendahara',
                'wali_kelas',
                'guru_pengajar',
                'siswa'
            ])->after('email');
            $table->foreignId('cabang_id')->nullable()->constrained('cabang')->onDelete('set null');
            $table->string('username')->unique()->nullable()->after('name');
            $table->boolean('is_active')->default(true)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn(['role', 'cabang_id', 'username', 'is_active']);
        });
    }
};