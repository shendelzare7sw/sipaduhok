<?php

namespace App\Imports;

use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TenagaPendidik;
use App\Models\GuruPengajarKelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JadwalPelajaranImport implements ToCollection, WithHeadingRow
{
    private $skippedCount = 0;
    private $importedCount = 0;
    private $kelasList;
    private $mapelList;
    private $mapelModels;
    private $guruList;
    private $tahunAjaranId;

    // Tracking missing entities
    private $missingKelas = [];
    private $missingMapel = [];
    private $missingGuru = [];
    private $warnings = [];

    public function __construct($tahunAjaranId = null)
    {
        $this->tahunAjaranId = $tahunAjaranId ?? TahunAjaran::where('is_active', true)->first()?->id;

        // Load classes with cabang info
        $this->kelasList = Kelas::with('cabang')->get()->map(function ($kelas) {
            return [
                'id' => $kelas->id,
                'nama_kelas' => strtolower(trim($kelas->nama_kelas)),
                'nama_cabang' => strtolower(trim($kelas->cabang->nama_cabang ?? '')),
                'jenjang' => $kelas->jenjang,
            ];
        });

        $this->mapelList = MataPelajaran::pluck('id', 'nama_mapel')->toArray();
        $this->mapelModels = MataPelajaran::all()->keyBy('id');
        $this->guruList = TenagaPendidik::pluck('id', 'nama_lengkap')->toArray();
    }

    public function collection(Collection $rows)
    {
        $rowNumber = 1; // Start after header

        foreach ($rows as $row) {
            $rowNumber++;
            $row = $row->toArray();

            // Skip empty rows
            if (empty($row['nama_kelas']) || empty($row['nama_mapel']) || empty($row['hari'])) {
                continue;
            }

            // Validate hari
            $hari = ucfirst(strtolower(trim($row['hari'])));
            if (!in_array($hari, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'])) {
                $this->warnings[] = "Baris {$rowNumber}: Hari '{$row['hari']}' tidak valid";
                $this->skippedCount++;
                continue;
            }

            // Lookup kelas (Multi-class support)
            $namaCabang = isset($row['nama_cabang']) ? trim($row['nama_cabang']) : null;
            $kelasNames = array_map('trim', explode(',', $row['nama_kelas']));
            $kelasIds = [];
            $kelasModels = [];
            $missingClassesInRow = [];

            foreach ($kelasNames as $kName) {
                $found = $this->findKelas($kName, $namaCabang);
                if ($found) {
                    $kelasIds[] = $found['id'];
                    $kelasModels[] = $found;
                } else {
                    $missingClassesInRow[] = $kName;
                }
            }

            if (!empty($missingClassesInRow)) {
                $cabangInfo = $namaCabang ? " (Cabang: {$namaCabang})" : "";
                foreach ($missingClassesInRow as $missing) {
                    $fullName = $missing . $cabangInfo;
                    if (!in_array($fullName, $this->missingKelas)) {
                        $this->missingKelas[] = $fullName;
                    }
                }
                $this->warnings[] = "Baris {$rowNumber}: Kelas tidak ditemukan: " . implode(', ', $missingClassesInRow) . $cabangInfo;
                $this->skippedCount++;
                continue;
            }

            if (empty($kelasIds)) {
                $this->skippedCount++;
                continue;
            }

            // Lookup guru (optional - jadwal tetap dibuat dengan status kosong)
            $guruId = null;
            if (!empty($row['nama_guru'])) {
                $guruId = $this->findGuru($row['nama_guru']);
                if (!$guruId) {
                    $guruName = trim($row['nama_guru']);
                    if (!in_array($guruName, $this->missingGuru)) {
                        $this->missingGuru[] = $guruName;
                    }
                    $this->warnings[] = "Baris {$rowNumber}: Guru '{$guruName}' tidak ditemukan, jadwal dibuat dengan status kosong";
                }
            }

            // Group kelas by jenjang for multi-jenjang support
            $kelasGrouped = collect($kelasModels)->groupBy('jenjang');

            foreach ($kelasGrouped as $jenjang => $kelasGroup) {
                $groupKelasIds = $kelasGroup->pluck('id')->toArray();

                // Find appropriate mapel for this jenjang group
                $mapelId = $this->findMapelForJenjang($row['nama_mapel'], $jenjang);
                if (!$mapelId) {
                    $mapelName = trim($row['nama_mapel']);
                    if (!in_array($mapelName, $this->missingMapel)) {
                        $this->missingMapel[] = $mapelName;
                    }
                    $this->warnings[] = "Baris {$rowNumber}: Mata Pelajaran '{$mapelName}' tidak ditemukan" .
                        (count($kelasGrouped) > 1 ? " untuk jenjang {$jenjang}" : "");
                    $this->skippedCount++;
                    continue;
                }

                // Check duplicate: check if ANY of the group classes already has this schedule
                $exists = JadwalPelajaran::where('tahun_ajaran_id', $this->tahunAjaranId)
                    ->where('hari', $hari)
                    ->where('jam_mulai', $row['jam_mulai'])
                    ->where('mata_pelajaran_id', $mapelId)
                    ->whereHas('kelas', function ($q) use ($groupKelasIds) {
                        $q->whereIn('kelas.id', $groupKelasIds);
                    })
                    ->exists();

                if ($exists) {
                    $this->skippedCount++;
                    $kelasNames = $kelasGroup->pluck('nama_kelas')->map(fn($n) => ucfirst($n))->join(', ');
                    $this->warnings[] = "Baris {$rowNumber}: Jadwal duplikat untuk kelas {$kelasNames} pada waktu tersebut.";
                    continue;
                }

                try {
                    $jadwal = JadwalPelajaran::create([
                        'tahun_ajaran_id' => $this->tahunAjaranId,
                        'kelas_id' => $groupKelasIds[0],
                        'mata_pelajaran_id' => $mapelId,
                        'guru_id' => $guruId,
                        'hari' => $hari,
                        'jam_mulai' => $row['jam_mulai'],
                        'jam_selesai' => $row['jam_selesai'],
                        'status' => $guruId ? 'aktif' : 'kosong',
                        'keterangan' => $row['keterangan'] ?? null,
                        'updated_by' => Auth::id(),
                    ]);

                    // Attach all classes in this jenjang group
                    $jadwal->kelas()->sync($groupKelasIds);

                    // Auto-sync guru pengajar
                    if ($guruId) {
                        foreach ($groupKelasIds as $kelasId) {
                            GuruPengajarKelas::firstOrCreate([
                                'tenaga_pendidik_id' => $guruId,
                                'kelas_id' => $kelasId,
                                'mata_pelajaran_id' => $mapelId,
                            ]);
                        }
                    }

                    $this->importedCount++;
                } catch (\Exception $e) {
                    $this->skippedCount++;
                    $this->warnings[] = "Baris {$rowNumber}: Error - " . $e->getMessage();
                }
            }
        }
    }

    /**
     * Find mapel that matches the name AND is compatible with the given jenjang.
     * Priority: exact jenjang match > universal (no jenjang) > any match
     */
    private function findMapelForJenjang($name, $jenjang)
    {
        $name = trim($name);
        $nameLower = strtolower($name);

        $exactJenjangMatch = null;
        $universalMatch = null;
        $anyMatch = null;

        foreach ($this->mapelModels as $mapel) {
            if (strtolower(trim($mapel->nama_mapel)) === $nameLower) {
                $anyMatch = $mapel->id;

                if (!$mapel->jenjang) {
                    $universalMatch = $mapel->id;
                } elseif (strcasecmp($mapel->jenjang, $jenjang) === 0) {
                    $exactJenjangMatch = $mapel->id;
                }
            }
        }

        return $exactJenjangMatch ?? $universalMatch ?? $anyMatch;
    }

    private function findKelas($name, $cabangName = null)
    {
        $name = strtolower(trim($name));
        $cabangName = $cabangName ? strtolower(trim($cabangName)) : null;

        foreach ($this->kelasList as $kelas) {
            if ($kelas['nama_kelas'] === $name) {
                if ($cabangName) {
                    if ($kelas['nama_cabang'] === $cabangName) {
                        return $kelas;
                    }
                } else {
                    return $kelas;
                }
            }
        }
        return null;
    }

    private function findGuru($name)
    {
        $name = trim($name);
        if (isset($this->guruList[$name])) {
            return $this->guruList[$name];
        }
        foreach ($this->guruList as $n => $id) {
            if (strtolower(trim($n)) === strtolower($name)) {
                return $id;
            }
        }
        return null;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
    public function getMissingKelas(): array
    {
        return $this->missingKelas;
    }
    public function getMissingMapel(): array
    {
        return $this->missingMapel;
    }
    public function getMissingGuru(): array
    {
        return $this->missingGuru;
    }
    public function getWarnings(): array
    {
        return $this->warnings;
    }
}
