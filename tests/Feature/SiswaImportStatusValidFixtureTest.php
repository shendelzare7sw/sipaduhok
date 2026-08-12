<?php

namespace Tests\Feature;

use App\Imports\SiswaImport;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Controlled verification for SIT-RSK-005.
 *
 * The legacy SiswaImportStatusTest intentionally remains unchanged as evidence
 * of its stale fixture. This test supplies every field declared mandatory by
 * the official template and importer, then verifies the intended status rule.
 */
class SiswaImportStatusValidFixtureTest extends TestCase
{
    public function test_status_nonaktif_dan_lulus_terproses_dengan_fixture_valid(): void
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
        DB::connection('mysql')->beginTransaction();

        try {
            $tahunAjaran = TahunAjaran::where('is_active', true)->first();
            $this->assertNotNull($tahunAjaran, 'Butuh tahun ajaran aktif untuk import siswa.');

            $kelas = Kelas::with('cabang')
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->first();
            $this->assertNotNull($kelas, 'Butuh kelas pada tahun ajaran aktif.');
            $this->assertNotNull($kelas->cabang, 'Kelas aktif harus terhubung ke cabang.');

            $suffix = substr(md5(uniqid('', true)), 0, 8);
            $base = [
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kota',
                'tanggal_lahir' => '2010-01-01',
                'alamat' => 'Alamat pengujian',
                'tanggal_masuk' => '2023-07-15',
                'nama_kelas' => $kelas->nama_kelas,
                'nama_cabang' => $kelas->cabang->nama_cabang,
                'agama' => 'Islam',
            ];

            $rows = collect([
                collect($base + [
                    'nama_lengkap' => 'Retest Nonaktif '.$suffix,
                    'nisn' => '8'.substr(md5($suffix.'a'), 0, 8),
                    'status' => 'nonaktif',
                ]),
                collect($base + [
                    'nama_lengkap' => 'Retest Lulus '.$suffix,
                    'nisn' => '8'.substr(md5($suffix.'b'), 0, 8),
                    'status' => 'lulus',
                ]),
            ]);

            $import = new SiswaImport;
            $import->collection($rows);

            $this->assertSame(2, $import->getImportedCount(), json_encode($import->getWarnings()));
            $this->assertSame(0, $import->getSkippedCount());

            $nonaktif = Siswa::termasukNonaktif()
                ->with('user')
                ->where('nama_lengkap', 'Retest Nonaktif '.$suffix)
                ->first();
            $this->assertNotNull($nonaktif);
            $this->assertSame('aktif', $nonaktif->status);
            $this->assertFalse((bool) $nonaktif->user->is_active);

            $lulus = Siswa::with('user')
                ->where('nama_lengkap', 'Retest Lulus '.$suffix)
                ->first();
            $this->assertNotNull($lulus);
            $this->assertSame('lulus', $lulus->status);
            $this->assertTrue((bool) $lulus->user->is_active);
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }
}
