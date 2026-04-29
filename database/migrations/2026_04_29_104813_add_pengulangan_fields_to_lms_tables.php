<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ujian', function (Blueprint $table) {
            $table->integer('batas_pengulangan')->nullable()->after('bisa_diulang');
        });

        Schema::table('tugas', function (Blueprint $table) {
            $table->boolean('tampilkan_nilai')->default(true)->after('file_tugas');
            $table->boolean('bisa_diulang')->default(false)->after('tampilkan_nilai');
            $table->integer('batas_pengulangan')->nullable()->after('bisa_diulang');
        });

        Schema::table('ujian_siswa', function (Blueprint $table) {
            $table->integer('pengulangan_ke')->default(1)->after('nilai');
            $table->decimal('nilai_terbaik', 5, 2)->nullable()->after('pengulangan_ke');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ujian', function (Blueprint $table) {
            $table->dropColumn('batas_pengulangan');
        });

        Schema::table('tugas', function (Blueprint $table) {
            $table->dropColumn(['tampilkan_nilai', 'bisa_diulang', 'batas_pengulangan']);
        });

        Schema::table('ujian_siswa', function (Blueprint $table) {
            $table->dropColumn(['pengulangan_ke', 'nilai_terbaik']);
        });
    }
};
