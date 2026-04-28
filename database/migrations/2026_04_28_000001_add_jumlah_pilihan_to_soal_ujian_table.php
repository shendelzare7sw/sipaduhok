<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soal_ujian', function (Blueprint $table) {
            // Tambahkan kolom jumlah_pilihan dengan default 5 untuk backward compatibility
            $table->integer('jumlah_pilihan')->default(5)->after('tipe_soal');
        });
    }

    public function down(): void
    {
        Schema::table('soal_ujian', function (Blueprint $table) {
            $table->dropColumn('jumlah_pilihan');
        });
    }
};
