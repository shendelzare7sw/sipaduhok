<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->enum('jenis_tugas', ['tugas', 'latihan'])->default('tugas')->after('guru_id');
            $table->integer('urutan')->nullable()->after('jenis_tugas'); // Auto-numbering (Tugas 1, 2, 3...)
            $table->string('judul_bab')->nullable()->after('judul_tugas');
            $table->string('nama_materi')->nullable()->after('judul_bab');
        });
    }

    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->dropColumn(['jenis_tugas', 'urutan', 'judul_bab', 'nama_materi']);
        });
    }
};
