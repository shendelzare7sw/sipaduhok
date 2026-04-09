<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\TahunAjaran;

class SyncKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $activeTahunAjaranId = TahunAjaran::where('is_active', true)->value('id');

        if (!$activeTahunAjaranId) {
            $this->command->error("Tidak ada Tahun Ajaran aktif!");
            return;
        }

        $kelasToSync = [
            ["nama_kelas"=>"KB1","jenjang"=>"KB","cabang_id"=>1,"kode_kelas"=>"RUKO-KB1-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"KB2","jenjang"=>"KB","cabang_id"=>1,"kode_kelas"=>"RUKO-KB2-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"TKA1","jenjang"=>"TKA","cabang_id"=>1,"kode_kelas"=>"RUKO-TKA1-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"TKA2","jenjang"=>"TKA","cabang_id"=>1,"kode_kelas"=>"RUKO-TKA2-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"TKB1","jenjang"=>"TKB","cabang_id"=>1,"kode_kelas"=>"RUKO-TKB1-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"TKB2","jenjang"=>"TKB","cabang_id"=>1,"kode_kelas"=>"RUKO-TKB2-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"1A","jenjang"=>"SD","cabang_id"=>1,"kode_kelas"=>"RUKO-1A-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"1B","jenjang"=>"SD","cabang_id"=>1,"kode_kelas"=>"RUKO-1B-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"2A","jenjang"=>"SD","cabang_id"=>1,"kode_kelas"=>"RUKO-2A-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"2B","jenjang"=>"SD","cabang_id"=>1,"kode_kelas"=>"RUKO-2B-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"3A","jenjang"=>"SD","cabang_id"=>1,"kode_kelas"=>"RUKO-3A-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"3B","jenjang"=>"SD","cabang_id"=>1,"kode_kelas"=>"RUKO-3B-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"4A","jenjang"=>"SD","cabang_id"=>1,"kode_kelas"=>"RUKO-4A-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"4B","jenjang"=>"SD","cabang_id"=>1,"kode_kelas"=>"RUKO-4B-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"5A","jenjang"=>"SD","cabang_id"=>1,"kode_kelas"=>"RUKO-5A-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"5B","jenjang"=>"SD","cabang_id"=>1,"kode_kelas"=>"RUKO-5B-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"6A","jenjang"=>"SD","cabang_id"=>1,"kode_kelas"=>"RUKO-6A-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"6B","jenjang"=>"SD","cabang_id"=>1,"kode_kelas"=>"RUKO-6B-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"7A","jenjang"=>"SMP","cabang_id"=>1,"kode_kelas"=>"RUKO-7A-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"7B","jenjang"=>"SMP","cabang_id"=>1,"kode_kelas"=>"RUKO-7B-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"8A","jenjang"=>"SMP","cabang_id"=>1,"kode_kelas"=>"RUKO-8A-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"8B","jenjang"=>"SMP","cabang_id"=>1,"kode_kelas"=>"RUKO-8B-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"9A","jenjang"=>"SMP","cabang_id"=>1,"kode_kelas"=>"RUKO-9A-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"9B","jenjang"=>"SMP","cabang_id"=>1,"kode_kelas"=>"RUKO-9B-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"10A","jenjang"=>"SMA","cabang_id"=>1,"kode_kelas"=>"RUKO-10A-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"10B","jenjang"=>"SMA","cabang_id"=>1,"kode_kelas"=>"RUKO-10B-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"11A","jenjang"=>"SMA","cabang_id"=>1,"kode_kelas"=>"RUKO-11A-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"11B","jenjang"=>"SMA","cabang_id"=>1,"kode_kelas"=>"RUKO-11B-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"12A","jenjang"=>"SMA","cabang_id"=>1,"kode_kelas"=>"RUKO-12A-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"12B","jenjang"=>"SMA","cabang_id"=>1,"kode_kelas"=>"RUKO-12B-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"KB1","jenjang"=>"KB","cabang_id"=>2,"kode_kelas"=>"PAUD-KB1-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"KB2","jenjang"=>"KB","cabang_id"=>2,"kode_kelas"=>"PAUD-KB2-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"TKA1","jenjang"=>"TKA","cabang_id"=>2,"kode_kelas"=>"PAUD-TKA1-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"TKA2","jenjang"=>"TKA","cabang_id"=>2,"kode_kelas"=>"PAUD-TKA2-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"TKB1","jenjang"=>"TKB","cabang_id"=>2,"kode_kelas"=>"PAUD-TKB1-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"TKB2","jenjang"=>"TKB","cabang_id"=>2,"kode_kelas"=>"PAUD-TKB2-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"1A","jenjang"=>"SD","cabang_id"=>3,"kode_kelas"=>"CMNGS-1A-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"1B","jenjang"=>"SD","cabang_id"=>3,"kode_kelas"=>"CMNGS-1B-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"2A","jenjang"=>"SD","cabang_id"=>3,"kode_kelas"=>"CMNGS-2A-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"2B","jenjang"=>"SD","cabang_id"=>3,"kode_kelas"=>"CMNGS-2B-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"3A","jenjang"=>"SD","cabang_id"=>3,"kode_kelas"=>"CMNGS-3A-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"3B","jenjang"=>"SD","cabang_id"=>3,"kode_kelas"=>"CMNGS-3B-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"4A","jenjang"=>"SD","cabang_id"=>3,"kode_kelas"=>"CMNGS-4A-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"4B","jenjang"=>"SD","cabang_id"=>3,"kode_kelas"=>"CMNGS-4B-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"5A","jenjang"=>"SD","cabang_id"=>3,"kode_kelas"=>"CMNGS-5A-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"5B","jenjang"=>"SD","cabang_id"=>3,"kode_kelas"=>"CMNGS-5B-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"6A","jenjang"=>"SD","cabang_id"=>3,"kode_kelas"=>"CMNGS-6A-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"6B","jenjang"=>"SD","cabang_id"=>3,"kode_kelas"=>"CMNGS-6B-2025","kuota_siswa"=>25],
            ["nama_kelas"=>"KB1","jenjang"=>"KB","cabang_id"=>3,"kode_kelas"=>"CMNGS-KB1-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"KB2","jenjang"=>"KB","cabang_id"=>3,"kode_kelas"=>"CMNGS-KB2-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"TKA1","jenjang"=>"TKA","cabang_id"=>3,"kode_kelas"=>"CMNGS-TKA1-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"TKA2","jenjang"=>"TKA","cabang_id"=>3,"kode_kelas"=>"CMNGS-TKA2-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"TKB1","jenjang"=>"TKB","cabang_id"=>3,"kode_kelas"=>"CMNGS-TKB1-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"TKB2","jenjang"=>"TKB","cabang_id"=>3,"kode_kelas"=>"CMNGS-TKB2-2025","kuota_siswa"=>20],
            ["nama_kelas"=>"7A","jenjang"=>"SMP","cabang_id"=>3,"kode_kelas"=>"CMNGS-7A-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"7B","jenjang"=>"SMP","cabang_id"=>3,"kode_kelas"=>"CMNGS-7B-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"8A","jenjang"=>"SMP","cabang_id"=>3,"kode_kelas"=>"CMNGS-8A-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"8B","jenjang"=>"SMP","cabang_id"=>3,"kode_kelas"=>"CMNGS-8B-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"9A","jenjang"=>"SMP","cabang_id"=>3,"kode_kelas"=>"CMNGS-9A-2025","kuota_siswa"=>30],
            ["nama_kelas"=>"9B","jenjang"=>"SMP","cabang_id"=>3,"kode_kelas"=>"CMNGS-9B-2025","kuota_siswa"=>30]
        ];

        $inserted = 0;
        foreach ($kelasToSync as $kelasData) {
            $exists = Kelas::where('nama_kelas', $kelasData['nama_kelas'])
                ->where('jenjang', $kelasData['jenjang'])
                ->where('cabang_id', $kelasData['cabang_id'])
                ->where('tahun_ajaran_id', $activeTahunAjaranId)
                ->exists();

            if (!$exists) {
                // If a completely different kode_kelas exists across all years, 
                // we should generate a valid one. But the exported ones are fine.
                Kelas::create([
                    'cabang_id' => $kelasData['cabang_id'],
                    'tahun_ajaran_id' => $activeTahunAjaranId,
                    'wali_kelas_id' => null, // Safety reset
                    'nama_kelas' => $kelasData['nama_kelas'],
                    'jenjang' => $kelasData['jenjang'],
                    'kode_kelas' => $kelasData['kode_kelas'],
                    'kuota_siswa' => $kelasData['kuota_siswa'],
                ]);
                $inserted++;
            }
        }

        $this->command->info("Berhasil menambahkan {$inserted} kelas baru yang hilang di tahun ajaran aktif.");
    }
}
