<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambahkan unique constraint untuk mencegah duplikasi kelas di masa depan
     */
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->unique(['nama_kelas', 'cabang_id'], 'unique_kelas_per_cabang');
        });

        echo "\n✓ Unique constraint ditambahkan pada (nama_kelas, cabang_id)\n";
        echo "✓ Duplikasi kelas tidak akan bisa terjadi lagi\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropUnique('unique_kelas_per_cabang');
        });
    }
};
