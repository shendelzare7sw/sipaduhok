<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            foreach (range(1, 5) as $i) {
                $table->decimal("tugas_{$i}_guru", 5, 2)->nullable()->after('ujian_praktek');
                $table->decimal("latihan_{$i}_guru", 5, 2)->nullable()->after("tugas_{$i}_guru");
                $table->decimal("uh_{$i}_guru", 5, 2)->nullable()->after("latihan_{$i}_guru");
            }

            $table->decimal('pts_guru', 5, 2)->nullable();
            $table->decimal('pas_guru', 5, 2)->nullable();
            $table->decimal('keterampilan_guru', 5, 2)->nullable();
            $table->decimal('to_1_guru', 5, 2)->nullable();
            $table->decimal('to_2_guru', 5, 2)->nullable();
            $table->decimal('to_3_guru', 5, 2)->nullable();
            $table->decimal('upk_guru', 5, 2)->nullable();
            $table->decimal('ujian_praktek_guru', 5, 2)->nullable();

            $table->timestamp('guru_terakhir_simpan_at')->nullable();
            $table->timestamp('wali_terakhir_edit_at')->nullable();
            $table->foreignId('edited_by_wali_id')->nullable()->constrained('tenaga_pendidik')->nullOnDelete();
        });

        $snapshotFields = [];
        foreach (range(1, 5) as $i) {
            $snapshotFields[] = "tugas_{$i}_guru = tugas_{$i}";
            $snapshotFields[] = "latihan_{$i}_guru = latihan_{$i}";
            $snapshotFields[] = "uh_{$i}_guru = uh_{$i}";
        }
        $snapshotFields = array_merge($snapshotFields, [
            'pts_guru = pts',
            'pas_guru = pas',
            'keterampilan_guru = keterampilan',
            'to_1_guru = to_1',
            'to_2_guru = to_2',
            'to_3_guru = to_3',
            'upk_guru = upk',
            'ujian_praktek_guru = ujian_praktek',
            'guru_terakhir_simpan_at = updated_at',
        ]);

        DB::statement('UPDATE nilai SET ' . implode(', ', $snapshotFields));
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropForeign(['edited_by_wali_id']);
            $columns = ['guru_terakhir_simpan_at', 'wali_terakhir_edit_at', 'edited_by_wali_id',
                'pts_guru', 'pas_guru', 'keterampilan_guru',
                'to_1_guru', 'to_2_guru', 'to_3_guru', 'upk_guru', 'ujian_praktek_guru'];
            foreach (range(1, 5) as $i) {
                $columns[] = "tugas_{$i}_guru";
                $columns[] = "latihan_{$i}_guru";
                $columns[] = "uh_{$i}_guru";
            }
            $table->dropColumn($columns);
        });
    }
};
