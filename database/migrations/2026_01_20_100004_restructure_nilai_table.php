<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            // Remove old simple columns if they exist
            if (Schema::hasColumn('nilai', 'nilai_tugas')) {
                $table->dropColumn('nilai_tugas');
            }

            // 5 Tugas
            $table->decimal('tugas_1', 5, 2)->nullable()->after('guru_id');
            $table->decimal('tugas_2', 5, 2)->nullable()->after('tugas_1');
            $table->decimal('tugas_3', 5, 2)->nullable()->after('tugas_2');
            $table->decimal('tugas_4', 5, 2)->nullable()->after('tugas_3');
            $table->decimal('tugas_5', 5, 2)->nullable()->after('tugas_4');
            $table->decimal('rata_tugas', 5, 2)->nullable()->after('tugas_5');

            // 5 Latihan
            $table->decimal('latihan_1', 5, 2)->nullable()->after('rata_tugas');
            $table->decimal('latihan_2', 5, 2)->nullable()->after('latihan_1');
            $table->decimal('latihan_3', 5, 2)->nullable()->after('latihan_2');
            $table->decimal('latihan_4', 5, 2)->nullable()->after('latihan_3');
            $table->decimal('latihan_5', 5, 2)->nullable()->after('latihan_4');
            $table->decimal('rata_latihan', 5, 2)->nullable()->after('latihan_5');

            // 5 Ulangan Harian
            $table->decimal('uh_1', 5, 2)->nullable()->after('rata_latihan');
            $table->decimal('uh_2', 5, 2)->nullable()->after('uh_1');
            $table->decimal('uh_3', 5, 2)->nullable()->after('uh_2');
            $table->decimal('uh_4', 5, 2)->nullable()->after('uh_3');
            $table->decimal('uh_5', 5, 2)->nullable()->after('uh_4');
            $table->decimal('rata_uh', 5, 2)->nullable()->after('uh_5');

            // PTS & PAS (rename from nilai_uts, nilai_uas)
            // These might already exist, so we rename them
        });

        // Rename columns if they exist
        if (Schema::hasColumn('nilai', 'nilai_uts')) {
            Schema::table('nilai', function (Blueprint $table) {
                $table->renameColumn('nilai_uts', 'pts');
            });
        } else {
            Schema::table('nilai', function (Blueprint $table) {
                $table->decimal('pts', 5, 2)->nullable()->after('rata_uh');
            });
        }

        if (Schema::hasColumn('nilai', 'nilai_uas')) {
            Schema::table('nilai', function (Blueprint $table) {
                $table->renameColumn('nilai_uas', 'pas');
            });
        } else {
            Schema::table('nilai', function (Blueprint $table) {
                $table->decimal('pas', 5, 2)->nullable()->after('pts');
            });
        }

        Schema::table('nilai', function (Blueprint $table) {
            // Keterampilan
            $table->decimal('keterampilan', 5, 2)->nullable()->after('nilai_akhir');

            // Khusus Kelas 9 & 12 (Tingkat Akhir)
            $table->decimal('to_1', 5, 2)->nullable()->after('keterampilan');
            $table->decimal('to_2', 5, 2)->nullable()->after('to_1');
            $table->decimal('to_3', 5, 2)->nullable()->after('to_2');
            $table->decimal('upk', 5, 2)->nullable()->after('to_3');
            $table->decimal('ujian_praktek', 5, 2)->nullable()->after('upk');
        });
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'tugas_1',
                'tugas_2',
                'tugas_3',
                'tugas_4',
                'tugas_5',
                'rata_tugas',
                'latihan_1',
                'latihan_2',
                'latihan_3',
                'latihan_4',
                'latihan_5',
                'rata_latihan',
                'uh_1',
                'uh_2',
                'uh_3',
                'uh_4',
                'uh_5',
                'rata_uh',
                'keterampilan',
                'to_1',
                'to_2',
                'to_3',
                'upk',
                'ujian_praktek'
            ]);
        });

        // Rename back
        if (Schema::hasColumn('nilai', 'pts')) {
            Schema::table('nilai', function (Blueprint $table) {
                $table->renameColumn('pts', 'nilai_uts');
            });
        }
        if (Schema::hasColumn('nilai', 'pas')) {
            Schema::table('nilai', function (Blueprint $table) {
                $table->renameColumn('pas', 'nilai_uas');
            });
        }

        // Re-add nilai_tugas
        Schema::table('nilai', function (Blueprint $table) {
            $table->decimal('nilai_tugas', 5, 2)->nullable()->after('guru_id');
        });
    }
};
