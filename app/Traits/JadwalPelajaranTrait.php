<?php

namespace App\Traits;

use App\Models\GuruPengajarKelas;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\PengaturanIstirahat;
use App\Models\TenagaPendidik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait JadwalPelajaranTrait
{
    /**
     * Store jadwal for multi-jenjang scenario.
     * Creates separate jadwal per jenjang group, each with its own mata pelajaran.
     */
    protected function storeMultiJenjang(Request $request, string $routePrefix)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,id',
            'mapel_per_jenjang' => 'required|array',
            'mapel_per_jenjang.*' => 'exists:mata_pelajaran,id',
            'guru_id' => 'nullable|exists:tenaga_pendidik,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        $this->ensureEligibleTeacher($validated['guru_id'] ?? null);

        // Group kelas by jenjang
        $kelasList = Kelas::whereIn('id', $validated['kelas_ids'])->get();
        $kelasGrouped = $kelasList->groupBy('jenjang');

        // Validate each jenjang group has a mapel assigned
        foreach ($kelasGrouped as $jenjang => $kelasGroup) {
            if (! isset($validated['mapel_per_jenjang'][$jenjang])) {
                return back()->withInput()->with('error', "Mata pelajaran untuk jenjang {$jenjang} belum dipilih.");
            }

            $mapel = MataPelajaran::find($validated['mapel_per_jenjang'][$jenjang]);
            if ($mapel && $mapel->jenjang && strcasecmp($mapel->jenjang, $jenjang) !== 0) {
                return back()->withInput()->with('error', "Mata pelajaran '{$mapel->nama_mapel}' ({$mapel->jenjang}) tidak sesuai dengan jenjang {$jenjang}.");
            }
        }

        // Check conflicts per group
        $excludeIds = [];
        foreach ($kelasGrouped as $jenjang => $kelasGroup) {
            $groupKelasIds = $kelasGroup->pluck('id')->toArray();
            $mapelId = $validated['mapel_per_jenjang'][$jenjang];

            $conflicts = $this->checkConflicts(
                $validated['tahun_ajaran_id'],
                $groupKelasIds,
                $validated['guru_id'],
                $validated['hari'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $mapelId,
                null,
                $excludeIds
            );

            if ($conflicts['hasConflict']) {
                return back()->withInput()->with('error', "[{$jenjang}] ".$conflicts['message']);
            }
        }

        DB::beginTransaction();
        try {
            $createdCount = 0;
            $jenjangNames = [];

            foreach ($kelasGrouped as $jenjang => $kelasGroup) {
                $groupKelasIds = $kelasGroup->pluck('id')->toArray();
                $mapelId = $validated['mapel_per_jenjang'][$jenjang];

                $jadwal = JadwalPelajaran::create([
                    'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                    'kelas_id' => $groupKelasIds[0],
                    'mata_pelajaran_id' => $mapelId,
                    'guru_id' => $validated['guru_id'],
                    'hari' => $validated['hari'],
                    'jam_mulai' => $validated['jam_mulai'],
                    'jam_selesai' => $validated['jam_selesai'],
                    'keterangan' => $validated['keterangan'],
                    'siswa_ids' => $validated['siswa_ids'] ?? null,
                    'status' => $validated['guru_id'] ? 'aktif' : 'kosong',
                    'updated_by' => Auth::id(),
                ]);

                $jadwal->kelas()->attach($groupKelasIds);
                $excludeIds[] = $jadwal->id;

                // Auto-sync guru pengajar
                if ($validated['guru_id']) {
                    $this->syncGuruPengajar($validated['guru_id'], $groupKelasIds, $mapelId);
                }

                $createdCount++;
                $jenjangNames[] = $jenjang;
            }

            DB::commit();

            $jenjangStr = implode(', ', $jenjangNames);

            return redirect()
                ->route("{$routePrefix}.jadwal-pelajaran.index", ['tahun_ajaran_id' => $validated['tahun_ajaran_id']])
                ->with('success', "Berhasil membuat {$createdCount} jadwal untuk jenjang: {$jenjangStr}");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Update jadwal for multi-jenjang scenario.
     * Updates existing jadwal for first group, creates new for additional groups.
     */
    protected function updateMultiJenjang(Request $request, JadwalPelajaran $jadwalPelajaran, string $routePrefix)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,id',
            'mapel_per_jenjang' => 'required|array',
            'mapel_per_jenjang.*' => 'exists:mata_pelajaran,id',
            'guru_id' => 'nullable|exists:tenaga_pendidik,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        $this->ensureEligibleTeacher($validated['guru_id'] ?? null);

        // Group kelas by jenjang
        $kelasList = Kelas::whereIn('id', $validated['kelas_ids'])->get();
        $kelasGrouped = $kelasList->groupBy('jenjang');

        // Validate each jenjang group has a mapel assigned
        foreach ($kelasGrouped as $jenjang => $kelasGroup) {
            if (! isset($validated['mapel_per_jenjang'][$jenjang])) {
                return back()->withInput()->with('error', "Mata pelajaran untuk jenjang {$jenjang} belum dipilih.");
            }

            $mapel = MataPelajaran::find($validated['mapel_per_jenjang'][$jenjang]);
            if ($mapel && $mapel->jenjang && strcasecmp($mapel->jenjang, $jenjang) !== 0) {
                return back()->withInput()->with('error', "Mata pelajaran '{$mapel->nama_mapel}' ({$mapel->jenjang}) tidak sesuai dengan jenjang {$jenjang}.");
            }
        }

        // Check conflicts per group (exclude the jadwal being edited)
        $excludeIds = [$jadwalPelajaran->id];
        foreach ($kelasGrouped as $jenjang => $kelasGroup) {
            $groupKelasIds = $kelasGroup->pluck('id')->toArray();
            $mapelId = $validated['mapel_per_jenjang'][$jenjang];

            $conflicts = $this->checkConflicts(
                $validated['tahun_ajaran_id'],
                $groupKelasIds,
                $validated['guru_id'],
                $validated['hari'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $mapelId,
                null,
                $excludeIds
            );

            if ($conflicts['hasConflict']) {
                return back()->withInput()->with('error', "[{$jenjang}] ".$conflicts['message']);
            }
        }

        DB::beginTransaction();
        try {
            $isFirst = true;

            foreach ($kelasGrouped as $jenjang => $kelasGroup) {
                $groupKelasIds = $kelasGroup->pluck('id')->toArray();
                $mapelId = $validated['mapel_per_jenjang'][$jenjang];

                if ($isFirst) {
                    // Update existing jadwal with first group
                    $jadwalPelajaran->update([
                        'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                        'kelas_id' => $groupKelasIds[0],
                        'mata_pelajaran_id' => $mapelId,
                        'guru_id' => $validated['guru_id'],
                        'hari' => $validated['hari'],
                        'jam_mulai' => $validated['jam_mulai'],
                        'jam_selesai' => $validated['jam_selesai'],
                        'keterangan' => $validated['keterangan'],
                        'siswa_ids' => $validated['siswa_ids'] ?? null,
                        'status' => $validated['guru_id'] ? 'aktif' : 'kosong',
                        'updated_by' => Auth::id(),
                    ]);
                    $jadwalPelajaran->kelas()->sync($groupKelasIds);
                    $isFirst = false;
                } else {
                    // Create new jadwal for additional jenjang groups
                    $newJadwal = JadwalPelajaran::create([
                        'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                        'kelas_id' => $groupKelasIds[0],
                        'mata_pelajaran_id' => $mapelId,
                        'guru_id' => $validated['guru_id'],
                        'hari' => $validated['hari'],
                        'jam_mulai' => $validated['jam_mulai'],
                        'jam_selesai' => $validated['jam_selesai'],
                        'keterangan' => $validated['keterangan'],
                        'siswa_ids' => $validated['siswa_ids'] ?? null,
                        'status' => $validated['guru_id'] ? 'aktif' : 'kosong',
                        'updated_by' => Auth::id(),
                    ]);
                    $newJadwal->kelas()->attach($groupKelasIds);
                    $excludeIds[] = $newJadwal->id;
                }

                // Auto-sync guru pengajar
                if ($validated['guru_id']) {
                    $this->syncGuruPengajar($validated['guru_id'], $groupKelasIds, $mapelId);
                }
            }

            DB::commit();

            return redirect()
                ->route("{$routePrefix}.jadwal-pelajaran.index", ['tahun_ajaran_id' => $validated['tahun_ajaran_id']])
                ->with('success', 'Jadwal pelajaran berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Check for schedule conflicts.
     * Supports both single excludeId and multiple excludeIds for multi-jenjang batch creation.
     */
    protected function checkConflicts($tahunAjaranId, $kelasIds, $guruId, $hari, $jamMulai, $jamSelesai, $mataPelajaranId = null, $excludeId = null, $excludeIds = [])
    {
        $result = ['hasConflict' => false, 'message' => ''];

        // Merge excludeId into excludeIds
        $allExcludes = $excludeIds;
        if ($excludeId) {
            $allExcludes[] = $excludeId;
        }

        // Convert to array if single value
        if (! is_array($kelasIds)) {
            $kelasIds = [$kelasIds];
        }

        $targetMapel = $mataPelajaranId ? MataPelajaran::find($mataPelajaranId) : null;
        $targetAgamaFilter = $targetMapel?->filter_agama;
        $isAgama = ! empty($targetAgamaFilter);

        // 1. Conflict Check: Classes
        foreach ($kelasIds as $kelasId) {
            $kelas = Kelas::find($kelasId);
            if (! $kelas) {
                continue;
            }

            // Istirahat Conflict Check
            $istirahatConflict = PengaturanIstirahat::jenjang($kelas->jenjang)
                ->aktif()
                ->untukHari($hari)
                ->get()
                ->first(function ($istirahat) use ($jamMulai, $jamSelesai) {
                    return ! ($jamSelesai <= $istirahat->jam_mulai || $jamMulai >= $istirahat->jam_selesai);
                });

            if ($istirahatConflict) {
                return [
                    'hasConflict' => true,
                    'message' => "Kelas {$kelas->nama_kelas} bentrok dengan istirahat '{$istirahatConflict->nama_istirahat}' ({$istirahatConflict->jam_mulai}-{$istirahatConflict->jam_selesai})",
                ];
            }

            // Other Jadwal Check

            $kelasConflictQuery = JadwalPelajaran::byTahunAjaran($tahunAjaranId)
                ->byKelas($kelasId)
                ->byHari($hari)
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                        ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                        ->orWhere(function ($q2) use ($jamMulai, $jamSelesai) {
                            $q2->where('jam_mulai', '<=', $jamMulai)
                                ->where('jam_selesai', '>=', $jamSelesai);
                        });
                })
                ->when(! empty($allExcludes), fn ($q) => $q->whereNotIn('id', $allExcludes));

            // Agama Exception Logic: different agama subjects may share the same slot.
            if ($isAgama) {
                $kelasConflictQuery->where(function ($q) use ($targetAgamaFilter) {
                    $q->whereDoesntHave('mataPelajaran')
                        ->orWhereHas('mataPelajaran', function ($mapelQuery) use ($targetAgamaFilter) {
                            $mapelQuery->whereNull('filter_agama')
                                ->orWhere('filter_agama', $targetAgamaFilter);
                        });
                });
            }

            $kelasConflict = $kelasConflictQuery->first();

            if ($kelasConflict) {
                return [
                    'hasConflict' => true,
                    'message' => "Kelas {$kelas->nama_kelas} sudah ada jadwal ({$kelasConflict->mataPelajaran->nama_mapel}) pada jam tersebut",
                ];
            }
        }

        // 2. Conflict Check: Teacher
        if ($guruId) {
            $guruConflict = JadwalPelajaran::byTahunAjaran($tahunAjaranId)
                ->byGuru($guruId)
                ->byHari($hari)
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                        ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                        ->orWhere(function ($q2) use ($jamMulai, $jamSelesai) {
                            $q2->where('jam_mulai', '<=', $jamMulai)
                                ->where('jam_selesai', '>=', $jamSelesai);
                        });
                })
                ->when(! empty($allExcludes), fn ($q) => $q->whereNotIn('id', $allExcludes))
                ->with(['kelas.cabang', 'mataPelajaran'])
                ->first();

            if ($guruConflict) {
                $sameSubject = $mataPelajaranId && $guruConflict->mata_pelajaran_id == $mataPelajaranId;

                // Check if the conflicting schedule is also a religion subject
                $existingMapel = $guruConflict->mataPelajaran;
                $existingAgamaFilter = $existingMapel?->filter_agama;
                $existingIsAgama = ! empty($existingAgamaFilter);

                // Allow: same teacher teaches different religion denominations at same time
                // (students are physically in separate groups)
                if ($isAgama && $existingIsAgama && $existingAgamaFilter !== $targetAgamaFilter && ! $sameSubject) {
                    // Different agama subjects: skip guru conflict
                } else {
                    $newBranchIds = Kelas::whereIn('id', $kelasIds)->pluck('cabang_id')->unique()->filter();

                    // Fallback: if kelas pivot not populated, use kelas_id column
                    $fallbackKelas = null;
                    if ($guruConflict->kelas->isNotEmpty()) {
                        $existingBranchIds = $guruConflict->kelas->pluck('cabang_id')->unique()->filter();
                    } else {
                        $fallbackKelas = Kelas::find($guruConflict->kelas_id);
                        $existingBranchIds = collect($fallbackKelas ? [$fallbackKelas->cabang_id] : [])->filter();
                    }

                    // sameBranch: new classes and existing classes share at least one common branch
                    $sameBranch = $newBranchIds->intersect($existingBranchIds)->isNotEmpty();

                    if ($sameSubject && $sameBranch) {
                        // Allowed (Merge / Combined Class — same subject, same branch)
                    } else {
                        $conflictKelasNames = $guruConflict->kelas->isNotEmpty()
                            ? $guruConflict->kelas->pluck('nama_kelas')->join(', ')
                            : ($fallbackKelas ? $fallbackKelas->nama_kelas : 'kelas lain');

                        return [
                            'hasConflict' => true,
                            'message' => "Guru sedang mengajar di kelas {$conflictKelasNames} pada jam tersebut",
                        ];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Validate Jenjang Compatibility between Classes and Subject.
     */
    protected function validateJenjangCompatibility($kelasIds, $mataPelajaranId)
    {
        $mapel = MataPelajaran::findOrFail($mataPelajaranId);

        // Skip validation if mapel has no specific jenjang
        if (! $mapel->jenjang) {
            return ['valid' => true];
        }

        $kelasList = Kelas::with('cabang')->whereIn('id', $kelasIds)->get();

        foreach ($kelasList as $kelas) {
            if (strcasecmp($kelas->jenjang, $mapel->jenjang) !== 0) {
                return [
                    'valid' => false,
                    'message' => "Jenjang Mata Pelajaran '{$mapel->nama_mapel}' ({$mapel->jenjang}) tidak sesuai dengan Jenjang Kelas '{$kelas->nama_kelas}' ({$kelas->jenjang}).",
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Get filtered student IDs for religion subjects.
     */
    protected function getFilteredSiswaIds($kelasId, $mataPelajaranId)
    {
        $mapel = MataPelajaran::find($mataPelajaranId);
        if (! $mapel || empty($mapel->filter_agama)) {
            return null;
        }

        return \App\Models\Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->where('agama', $mapel->filter_agama)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Auto-sync guru_pengajar_kelas table.
     * Adds entries that don't exist yet (does NOT remove old entries).
     */
    protected function syncGuruPengajar($guruId, $kelasIds, $mapelId)
    {
        if (! $guruId || ! $mapelId) {
            return;
        }

        $this->ensureEligibleTeacher($guruId);

        foreach ($kelasIds as $kelasId) {
            GuruPengajarKelas::firstOrCreate([
                'tenaga_pendidik_id' => $guruId,
                'kelas_id' => $kelasId,
                'mata_pelajaran_id' => $mapelId,
            ]);
        }
    }

    /**
     * Pertahanan server-side: ID tenaga pendidik saja belum membuktikan bahwa
     * akun tersebut berwenang mengajar.
     */
    protected function ensureEligibleTeacher($guruId, string $attribute = 'guru_id'): void
    {
        if (blank($guruId)) {
            return;
        }

        $eligible = TenagaPendidik::eligibleToTeach()->whereKey($guruId)->exists();

        if (! $eligible) {
            throw ValidationException::withMessages([
                $attribute => 'Guru yang dipilih harus memakai akun Guru Pengajar yang aktif.',
            ]);
        }
    }

    /**
     * Cleanup guru_pengajar_kelas entries that are no longer referenced by any jadwal.
     * Only deletes if no other jadwal references the same guru-kelas-mapel combination.
     */
    protected function cleanupGuruPengajar($guruId, $kelasIds, $mapelId)
    {
        if (! $guruId || ! $mapelId) {
            return;
        }

        foreach ($kelasIds as $kelasId) {
            $otherJadwalExists = JadwalPelajaran::where('guru_id', $guruId)
                ->where('mata_pelajaran_id', $mapelId)
                ->whereHas('kelas', fn ($q) => $q->where('kelas.id', $kelasId))
                ->exists();

            if (! $otherJadwalExists) {
                GuruPengajarKelas::where('tenaga_pendidik_id', $guruId)
                    ->where('kelas_id', $kelasId)
                    ->where('mata_pelajaran_id', $mapelId)
                    ->delete();
            }
        }
    }
}
