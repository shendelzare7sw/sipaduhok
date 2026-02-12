<?php

namespace App\Imports;

use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TenagaPendidik;
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
        $this->kelasList = Kelas::with('cabang')->get()->map(function($kelas) {
            return [
                'id' => $kelas->id,
                'nama_kelas' => strtolower(trim($kelas->nama_kelas)),
                'nama_cabang' => strtolower(trim($kelas->cabang->nama_cabang ?? '')),
            ];
        });

        $this->mapelList = MataPelajaran::pluck('id', 'nama_mapel')->toArray();
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
            $missingClassesInRow = [];

            foreach ($kelasNames as $kName) {
                $foundId = $this->findKelas($kName, $namaCabang);
                if ($foundId) {
                    $kelasIds[] = $foundId;
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

            // Lookup mata pelajaran
            $mapelId = $this->findMapel($row['nama_mapel']);
            if (!$mapelId) {
                $mapelName = trim($row['nama_mapel']);
                if (!in_array($mapelName, $this->missingMapel)) {
                    $this->missingMapel[] = $mapelName;
                }
                $this->warnings[] = "Baris {$rowNumber}: Mata Pelajaran '{$mapelName}' tidak ditemukan";
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
                    // Don't skip - just note the warning
                    $this->warnings[] = "Baris {$rowNumber}: Guru '{$guruName}' tidak ditemukan, jadwal dibuat dengan status kosong";
                }
            }

            // Check duplicate: check if ANY of the classes already has this schedule
            // Logic: Find schedules with same TA, Hari, Jam Mulai, that share ANY class from $kelasIds
            $exists = JadwalPelajaran::where('tahun_ajaran_id', $this->tahunAjaranId)
                ->where('hari', $hari)
                ->where('jam_mulai', $row['jam_mulai'])
                ->whereHas('kelas', function($q) use ($kelasIds) {
                    $q->whereIn('kelas.id', $kelasIds);
                })
                ->exists();

            if ($exists) {
                // Determine which class caused duplicate for better warning?
                // For now just skip as per original logic
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Jadwal duplikat untuk kelas/waktu tersebut.";
                continue;
            }

            try {
                // Create Jadwal (without single kelas_id)
                $jadwal = JadwalPelajaran::create([
                    'tahun_ajaran_id' => $this->tahunAjaranId,
                    'kelas_id' => $kelasIds[0] ?? null, // Backward compatibility
                    'mata_pelajaran_id' => $mapelId,
                    'guru_id' => $guruId,
                    'hari' => $hari,
                    'jam_mulai' => $row['jam_mulai'],
                    'jam_selesai' => $row['jam_selesai'],
                    'status' => $guruId ? 'aktif' : 'kosong',
                    'keterangan' => $row['keterangan'] ?? null,
                    'updated_by' => Auth::id(),
                ]);

                // Attach to pivot table
                $jadwal->kelas()->sync($kelasIds);

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Error - " . $e->getMessage();
            }
        }
    }

    private function findKelas($name, $cabangName = null)
    {
        $name = strtolower(trim($name));
        $cabangName = $cabangName ? strtolower(trim($cabangName)) : null;

        foreach ($this->kelasList as $kelas) {
            if ($kelas['nama_kelas'] === $name) {
                // If cabang specified, match it. If not, maybe verify if only 1 exists?
                // For now, if cabang specified, MUST match.
                if ($cabangName) {
                    if ($kelas['nama_cabang'] === $cabangName) {
                        return $kelas['id'];
                    }
                } else {
                    // If no branch specified, return first match (backward compatibility/risk of wrong branch)
                    // Ideal: User must specify branch if duplicates.
                    return $kelas['id'];
                }
            }
        }
        return null;
    }

    private function findMapel($name)
    {
        $name = trim($name);
        if (isset($this->mapelList[$name])) {
            return $this->mapelList[$name];
        }
        foreach ($this->mapelList as $n => $id) {
            if (strtolower(trim($n)) === strtolower($name)) {
                return $id;
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
