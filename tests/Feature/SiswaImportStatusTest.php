<?php

namespace Tests\Feature;

use App\Imports\SiswaImport;
use App\Models\Cabang;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi bug import siswa: kolom "status".
 *
 * Template menginstruksikan "aktif/nonaktif", tapi enum siswa.status =
 * aktif|lulus|pindah|keluar (tanpa 'nonaktif'). Dulu baris ber-status "nonaktif"
 * menulis enum invalid -> baris GAGAL/di-skip (broken data), dan status
 * lulus/pindah/keluar dipaksa jadi 'aktif'. Sekarang:
 *  - "nonaktif" -> siswa dibuat, status='aktif', akun is_active=false.
 *  - "lulus" (dan enum asli lain) -> terimport apa adanya.
 *
 * Menjalankan: php artisan test --filter=SiswaImportStatusTest
 */
class SiswaImportStatusTest extends TestCase
{
    private function useMysql(): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.database' => 'db_sipaduhok',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
        DB::purge('mysql');
    }

    public function test_status_nonaktif_tidak_gagal_dan_akun_dinonaktifkan(): void
    {
        $this->useMysql();
        DB::connection('mysql')->beginTransaction();
        try {
            $cabang = Cabang::first();
            $this->assertNotNull($cabang, 'Butuh data Cabang (seed).');
            $sfx = substr(md5(uniqid('', true)), 0, 8);

            $rows = collect([
                collect([
                    'nama_lengkap' => 'Nonaktif ' . $sfx,
                    'nisn' => '9' . substr(md5($sfx . 'a'), 0, 8),
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Kota',
                    'tanggal_lahir' => '2010-01-01',
                    'alamat' => 'Alamat',
                    'tanggal_masuk' => '2023-07-15',
                    'nama_cabang' => $cabang->nama_cabang,
                    'status' => 'nonaktif',
                ]),
                collect([
                    'nama_lengkap' => 'Lulus ' . $sfx,
                    'nisn' => '9' . substr(md5($sfx . 'b'), 0, 8),
                    'jenis_kelamin' => 'P',
                    'tempat_lahir' => 'Kota',
                    'tanggal_lahir' => '2011-01-01',
                    'alamat' => 'Alamat',
                    'tanggal_masuk' => '2023-07-15',
                    'nama_cabang' => $cabang->nama_cabang,
                    'status' => 'lulus',
                ]),
            ]);

            $import = new SiswaImport();
            $import->collection($rows);

            // Dulu baris "nonaktif" masuk skippedCount karena enum invalid. Sekarang 2 sukses.
            $this->assertEquals(2, $import->getImportedCount(), 'Kedua baris harus terimport: ' . json_encode($import->getWarnings()));
            $this->assertEquals(0, $import->getSkippedCount());

            $nonaktif = Siswa::where('nama_lengkap', 'Nonaktif ' . $sfx)->with('user')->first();
            $this->assertNotNull($nonaktif, 'Siswa status nonaktif harus tetap dibuat');
            $this->assertEquals('aktif', $nonaktif->status, 'status akademik tetap aktif (enum valid)');
            $this->assertNotNull($nonaktif->user);
            $this->assertEquals(0, (int) $nonaktif->user->is_active, 'akun harus dinonaktifkan');

            $lulus = Siswa::where('nama_lengkap', 'Lulus ' . $sfx)->first();
            $this->assertNotNull($lulus);
            $this->assertEquals('lulus', $lulus->status, 'status lulus harus terimport apa adanya');
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }
}
