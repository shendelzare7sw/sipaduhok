<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Tagihan;
use App\Services\PromotionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PromotionSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure tables exist (depending on migration state in test env)
        // RefreshDatabase handles migration if configured correctly.
        
        $this->service = new PromotionService();
        
        // Seed Master Data
        $this->tahunAjaran = TahunAjaran::create([
            'tahun_ajaran' => '2024/2025',
            'semester' => 'Genap',
            'is_active' => true
        ]);
        
        // Settings
        DB::table('pengaturan_naik_kelas')->insert([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'persentase_minimal_tuntas' => 50, // Easify test
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Mapel
        MataPelajaran::create([
            'nama_mapel' => 'Matematika', 
            'jenjang' => 'SMP', 
            'kode_mapel' => 'MTK',
            'deskripsi' => 'Math'
        ]);

        DB::table('pengaturan_kkm')->insert([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'mata_pelajaran_id' => 1, 
            'jenjang' => 'SMP',
            'nilai_kkm' => 75
        ]);
    }

    public function test_student_eligible_for_promotion()
    {
        $kelas = Kelas::create(['nama_kelas' => '7A', 'jenjang' => 'SMP', 'tahun_ajaran_id' => $this->tahunAjaran->id]);
        
        // Manual Create Siswa (Factory not available)
        $siswa = new Siswa();
        $siswa->nama_lengkap = 'Test Student';
        $siswa->nis = '12345';
        $siswa->kelas_id = $kelas->id;
        $siswa->status = 'aktif';
        $siswa->save();
        
        // Lunas
        Tagihan::create([
            'siswa_id' => $siswa->id,
            'jenis_tagihan' => 'SPP',
            'jumlah' => 100000,
            'status' => 'sudah_bayar',
            'tahun_ajaran_id' => $this->tahunAjaran->id
        ]);

        // Academic Tuntas
        $mapel = MataPelajaran::first();
        Nilai::create([
            'siswa_id' => $siswa->id,
            'mata_pelajaran_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'uh_1' => 80,
            'pts' => 80,
            'pas' => 80,
            'nilai_akhir' => 80 // > 75
        ]);

        $result = $this->service->checkEligibility($siswa, $this->tahunAjaran->id);
        
        $this->assertTrue($result['eligible'], 'Student should be eligible');
        $this->assertEquals('LUNAS', $result['financial']['status']);
        $this->assertTrue($result['academic']['is_tuntas']);
    }

    public function test_student_not_eligible_financial()
    {
        $kelas = Kelas::create(['nama_kelas' => '7A', 'jenjang' => 'SMP', 'tahun_ajaran_id' => $this->tahunAjaran->id]);
        
        $siswa = new Siswa();
        $siswa->nama_lengkap = 'Debtor Student';
        $siswa->nis = '12346';
        $siswa->kelas_id = $kelas->id;
        $siswa->status = 'aktif';
        $siswa->save();
        
        // Belum Lunas
        Tagihan::create([
            'siswa_id' => $siswa->id,
            'jenis_tagihan' => 'SPP',
            'jumlah' => 100000,
            'status' => 'belum_bayar',
            'tahun_ajaran_id' => $this->tahunAjaran->id
        ]);
        
        // Academic OK
        $mapel = MataPelajaran::first();
        Nilai::create([
            'siswa_id' => $siswa->id,
            'mata_pelajaran_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'nilai_akhir' => 80
        ]);

        $result = $this->service->checkEligibility($siswa, $this->tahunAjaran->id);
        
        $this->assertFalse($result['eligible'], 'Student should NOT be eligible due to unpaid bills');
        $this->assertEquals('BELUM_LUNAS', $result['financial']['status']);
    }
}
