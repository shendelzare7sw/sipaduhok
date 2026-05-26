<?php

namespace App\Services;

use App\Models\GuruPengajarKelas;
use App\Models\Nilai;
use App\Models\TahunAjaran;
use App\Models\Tugas;
use App\Models\TugasSiswa;
use App\Models\Ujian;
use App\Models\UjianSiswa;

class NilaiSyncService
{
    private const UJIAN_TIPE_MAP = [
        Ujian::TIPE_PTS_GANJIL => ['field' => 'pts', 'semester' => 'ganjil'],
        Ujian::TIPE_PAS_GANJIL => ['field' => 'pas', 'semester' => 'ganjil'],
        Ujian::TIPE_PTS_GENAP => ['field' => 'pts', 'semester' => 'genap'],
        Ujian::TIPE_PAS_GENAP => ['field' => 'pas', 'semester' => 'genap'],
        Ujian::TIPE_UTS => ['field' => 'pts', 'semester' => null],
        Ujian::TIPE_UAS => ['field' => 'pas', 'semester' => null],
        Ujian::TIPE_TO_1 => ['field' => 'to_1', 'semester' => null],
        Ujian::TIPE_TO_2 => ['field' => 'to_2', 'semester' => null],
        Ujian::TIPE_TO_3 => ['field' => 'to_3', 'semester' => null],
        Ujian::TIPE_UPK => ['field' => 'upk', 'semester' => null],
        Ujian::TIPE_UJIAN_PRAKTEK => ['field' => 'ujian_praktek', 'semester' => null],
    ];

    public function syncForSiswaMapel(int $siswaId, int $mapelId, int $kelasId, string $semester, ?int $tahunAjaranId = null): ?Nilai
    {
        $tahunAjaranId = $tahunAjaranId ?? TahunAjaran::where('is_active', true)->value('id');
        if (!$tahunAjaranId) {
            return null;
        }

        $guruId = GuruPengajarKelas::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->value('tenaga_pendidik_id');

        $nilai = Nilai::firstOrNew([
            'siswa_id' => $siswaId,
            'mata_pelajaran_id' => $mapelId,
            'kelas_id' => $kelasId,
            'tahun_ajaran_id' => $tahunAjaranId,
            'semester' => $semester,
        ]);

        if (!$nilai->exists && $guruId) {
            $nilai->guru_id = $guruId;
        }
        if (!$nilai->exists) {
            $nilai->save();
        }

        $componentValues = $this->collectComponentValues($siswaId, $mapelId, $kelasId, $semester);
        if (empty($componentValues)) {
            return $nilai;
        }

        $waliPernahEdit = $nilai->wali_terakhir_edit_at !== null;
        $snapshotChanged = false;

        foreach ($componentValues as $field => $value) {
            $oldSnapshot = $nilai->{$field . '_guru'};
            $oldFloat = $oldSnapshot !== null ? (float) $oldSnapshot : null;
            if ($oldFloat !== $value) {
                $snapshotChanged = true;
            }
            $nilai->{$field . '_guru'} = $value;
            if (!$waliPernahEdit) {
                $nilai->{$field} = $value;
            }
        }

        if ($snapshotChanged) {
            $nilai->guru_terakhir_simpan_at = now();
        }
        $nilai->save();

        if (!$waliPernahEdit && $snapshotChanged) {
            $nilai->hitungNilaiAkhir();
        }

        return $nilai;
    }

    public function syncFromTugasSiswa(TugasSiswa $tugasSiswa): ?Nilai
    {
        if ($tugasSiswa->status !== 'dinilai' || $tugasSiswa->nilai === null) {
            return null;
        }

        $tugas = $tugasSiswa->tugas ?? Tugas::find($tugasSiswa->tugas_id);
        if (!$tugas) {
            return null;
        }

        return $this->syncForSiswaMapel(
            $tugasSiswa->siswa_id,
            $tugas->mata_pelajaran_id,
            $tugas->kelas_id,
            Nilai::getCurrentSemester()
        );
    }

    public function syncFromUjianSiswa(UjianSiswa $ujianSiswa): ?Nilai
    {
        if ($ujianSiswa->status !== 'selesai' || $ujianSiswa->nilai === null) {
            return null;
        }

        $ujian = $ujianSiswa->ujian ?? Ujian::find($ujianSiswa->ujian_id);
        if (!$ujian) {
            return null;
        }

        $semester = $this->inferSemester($ujian);

        return $this->syncForSiswaMapel(
            $ujianSiswa->siswa_id,
            $ujian->mata_pelajaran_id,
            $ujian->kelas_id,
            $semester
        );
    }

    private function collectComponentValues(int $siswaId, int $mapelId, int $kelasId, string $semester): array
    {
        $values = [];

        $tugasList = TugasSiswa::whereHas('tugas', function ($q) use ($mapelId, $kelasId) {
                $q->where('mata_pelajaran_id', $mapelId)
                  ->where('kelas_id', $kelasId);
            })
            ->with('tugas')
            ->where('siswa_id', $siswaId)
            ->where('status', 'dinilai')
            ->get();

        foreach ($tugasList as $ts) {
            $jenis = $ts->tugas->jenis_tugas ?? 'tugas';
            $urutan = $ts->tugas->urutan ?? 0;
            if ($urutan < 1 || $urutan > 5) continue;
            if (!in_array($jenis, ['tugas', 'latihan'], true)) continue;
            $values["{$jenis}_{$urutan}"] = (float) $ts->nilai;
        }

        $ujianList = UjianSiswa::whereHas('ujian', function ($q) use ($mapelId, $kelasId) {
                $q->where('mata_pelajaran_id', $mapelId)
                  ->where('kelas_id', $kelasId);
            })
            ->with('ujian')
            ->where('siswa_id', $siswaId)
            ->where('status', 'selesai')
            ->get();

        foreach ($ujianList as $us) {
            $ujian = $us->ujian;
            $tipe = $ujian->tipe_ujian;

            if (in_array($tipe, [Ujian::TIPE_ULANGAN_HARIAN], true)) {
                $urutan = (int) ($ujian->urutan ?? 0);
                if ($urutan >= 1 && $urutan <= 5) {
                    $values["uh_{$urutan}"] = (float) $us->nilai;
                }
                continue;
            }

            if (!isset(self::UJIAN_TIPE_MAP[$tipe])) {
                continue;
            }

            $map = self::UJIAN_TIPE_MAP[$tipe];
            if ($map['semester'] !== null && $map['semester'] !== $semester) {
                continue;
            }
            $values[$map['field']] = (float) $us->nilai;
        }

        return $values;
    }

    private function inferSemester(Ujian $ujian): string
    {
        $tipe = $ujian->tipe_ujian;
        if (isset(self::UJIAN_TIPE_MAP[$tipe]) && self::UJIAN_TIPE_MAP[$tipe]['semester'] !== null) {
            return self::UJIAN_TIPE_MAP[$tipe]['semester'];
        }
        return Nilai::getCurrentSemester();
    }
}
