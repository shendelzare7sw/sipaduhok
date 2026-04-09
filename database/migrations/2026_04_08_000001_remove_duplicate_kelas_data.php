<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menghapus duplikasi kelas dengan strategi keep MIN(id), hapus yang lebih besar
     */
    public function up(): void
    {
        // Backup duplikasi data sebelum dihapus
        DB::statement(<<<SQL
            CREATE TABLE IF NOT EXISTS kelas_duplicate_backup AS
            SELECT k1.* FROM kelas k1
            WHERE k1.id NOT IN (
                SELECT MIN(id) 
                FROM kelas
                GROUP BY nama_kelas, cabang_id
            );
        SQL);

        // Hapus duplikasi - keep yang ID-nya paling kecil
        DB::statement(<<<SQL
            DELETE FROM kelas
            WHERE id NOT IN (
                SELECT id FROM (
                    SELECT MIN(id) as id
                    FROM kelas
                    GROUP BY nama_kelas, cabang_id
                ) as temp_table
            );
        SQL);

        // Log result
        $totalKelas = DB::table('kelas')->count();
        echo "\n✓ Duplikasi kelas berhasil dihapus!\n";
        echo "✓ Total kelas sekarang: {$totalKelas}\n";
        echo "✓ Backup duplikasi disimpan di table kelas_duplicate_backup\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore dari backup jika diperlukan
        if (DB::getSchemaBuilder()->hasTable('kelas_duplicate_backup')) {
            // Insert data kembali
            DB::statement(<<<SQL
                INSERT INTO kelas SELECT * FROM kelas_duplicate_backup;
            SQL);
            
            // Drop backup table
            DB::statement(<<<SQL
                DROP TABLE kelas_duplicate_backup;
            SQL);

            echo "\n⚠ Duplikasi kelas telah di-restore dari backup\n";
        }
    }
};
