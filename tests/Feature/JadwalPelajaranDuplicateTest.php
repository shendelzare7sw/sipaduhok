<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalPelajaranDuplicateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_duplicate_preserves_all_class_assignments(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $cabang = $this->createCabang('ADM');
        [$sourceYear, $targetYear] = $this->createYears();
        $sourceA = $this->createKelas($cabang, $sourceYear, '7A');
        $sourceB = $this->createKelas($cabang, $sourceYear, '7B');
        $targetA = $this->createKelas($cabang, $targetYear, '7A');
        $targetB = $this->createKelas($cabang, $targetYear, '7B');
        $mapel = $this->createMapel('ADM');
        $this->createJadwal($sourceYear, $mapel, [$sourceA, $sourceB]);

        $this->actingAs($admin)->withoutMiddleware()
            ->post(route('admin.jadwal-pelajaran.duplicate'), [
                'tahun_ajaran_id_lama' => $sourceYear->id,
                'tahun_ajaran_id_baru' => $targetYear->id,
            ])->assertRedirect();

        $copy = JadwalPelajaran::byTahunAjaran($targetYear->id)->firstOrFail();
        $this->assertEqualsCanonicalizing(
            [$targetA->id, $targetB->id],
            $copy->kelas()->pluck('kelas.id')->all()
        );
    }

    public function test_waka_duplicate_only_copies_its_own_branch(): void
    {
        $cabangWaka = $this->createCabang('WKA');
        $cabangLain = $this->createCabang('OTH');
        [$sourceYear, $targetYear] = $this->createYears();
        $sourceOwn = $this->createKelas($cabangWaka, $sourceYear, '8A');
        $sourceOther = $this->createKelas($cabangLain, $sourceYear, '8A');
        $targetOwn = $this->createKelas($cabangWaka, $targetYear, '8A');
        $targetOther = $this->createKelas($cabangLain, $targetYear, '8A');
        $mapel = $this->createMapel('WKA');
        $this->createJadwal($sourceYear, $mapel, [$sourceOwn]);
        $this->createJadwal($sourceYear, $mapel, [$sourceOther], 'Selasa');
        $waka = User::factory()->create([
            'role' => 'wakil_kepala_sekolah',
            'cabang_id' => $cabangWaka->id,
            'is_active' => true,
        ]);

        $this->actingAs($waka)->withoutMiddleware()
            ->post(route('waka.jadwal-pelajaran.duplicate'), [
                'tahun_ajaran_id_lama' => $sourceYear->id,
                'tahun_ajaran_id_baru' => $targetYear->id,
            ])->assertRedirect();

        $this->assertDatabaseHas('jadwal_kelas', ['kelas_id' => $targetOwn->id]);
        $this->assertDatabaseMissing('jadwal_kelas', ['kelas_id' => $targetOther->id]);
    }

    private function createCabang(string $code): Cabang
    {
        return Cabang::create([
            'kode_cabang' => $code,
            'nama_cabang' => "Cabang {$code}",
            'alamat' => 'Alamat pengujian',
            'is_active' => true,
        ]);
    }

    private function createYears(): array
    {
        return [
            TahunAjaran::create([
                'nama_tahun_ajaran' => '2026/2027',
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2027-06-30',
                'is_active' => true,
            ]),
            TahunAjaran::create([
                'nama_tahun_ajaran' => '2027/2028',
                'tanggal_mulai' => '2027-07-01',
                'tanggal_selesai' => '2028-06-30',
                'is_active' => false,
            ]),
        ];
    }

    private function createKelas(Cabang $cabang, TahunAjaran $tahun, string $name): Kelas
    {
        return Kelas::create([
            'cabang_id' => $cabang->id,
            'tahun_ajaran_id' => $tahun->id,
            'nama_kelas' => $name,
            'jenjang' => 'SMP',
            'kode_kelas' => "{$cabang->kode_cabang}-{$tahun->id}-{$name}",
            'kuota_siswa' => 30,
        ]);
    }

    private function createMapel(string $code): MataPelajaran
    {
        return MataPelajaran::create([
            'kode_mapel' => "{$code}-001",
            'nama_mapel' => "Matematika {$code}",
            'jenjang' => 'SMP',
        ]);
    }

    private function createJadwal(
        TahunAjaran $tahun,
        MataPelajaran $mapel,
        array $kelas,
        string $hari = 'Senin'
    ): JadwalPelajaran {
        $jadwal = JadwalPelajaran::create([
            'tahun_ajaran_id' => $tahun->id,
            'kelas_id' => $kelas[0]->id,
            'mata_pelajaran_id' => $mapel->id,
            'hari' => $hari,
            'jam_mulai' => '07:00',
            'jam_selesai' => '08:00',
            'status' => 'kosong',
        ]);
        $jadwal->kelas()->sync(collect($kelas)->pluck('id'));

        return $jadwal;
    }
}
