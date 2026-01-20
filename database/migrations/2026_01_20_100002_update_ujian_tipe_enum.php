<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // MySQL doesn't allow direct enum modification, so we use raw SQL
        DB::statement("ALTER TABLE ujian MODIFY COLUMN tipe_ujian ENUM(
            'harian', 'uts', 'uas',
            'ulangan_harian', 
            'pts_ganjil', 'pas_ganjil', 
            'pts_genap', 'pas_genap',
            'to_1', 'to_2', 'to_3',
            'upk', 'ujian_praktek'
        ) NOT NULL DEFAULT 'ulangan_harian'");

        Schema::table('ujian', function (Blueprint $table) {
            $table->integer('urutan')->nullable()->after('tipe_ujian'); // Auto-numbering (UH1, UH2...)
            $table->boolean('is_active')->default(true)->after('durasi_menit');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE ujian MODIFY COLUMN tipe_ujian ENUM('harian', 'uts', 'uas') NOT NULL DEFAULT 'harian'");

        Schema::table('ujian', function (Blueprint $table) {
            $table->dropColumn(['urutan', 'is_active']);
        });
    }
};
