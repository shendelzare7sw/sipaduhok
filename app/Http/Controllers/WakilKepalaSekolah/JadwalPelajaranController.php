<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\JadwalPelajaranHistory;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TenagaPendidik;
use App\Models\PengaturanIstirahat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class JadwalPelajaranController extends Controller
{
    /**
     * Display a listing of jadwal pelajaran.
     */
    public function index(Request $request)
    {
        // Get current or selected tahun ajaran
        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        // Get filter parameters
        $cabangId = $request->cabang_id;
        $jenjang = $request->jenjang;
        $kelasId = $request->kelas_id;
        $guruId = $request->guru_id;

        // Build query
        $query = JadwalPelajaran::with([
            'kelas.cabang',
            'mataPelajaran',
            'guru',
            'tahunAjaran'
        ])->byTahunAjaran($tahunAjaranId);

        // Apply filters
        if ($cabangId) {
            $query->whereHas('kelas', fn($q) => $q->where('cabang_id', $cabangId));
        }

        if ($jenjang) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $jenjang));
        }

        if ($kelasId) {
            $query->byKelas($kelasId);
        }

        if ($guruId) {
            $query->byGuru($guruId);
        }

        // Order by hari and jam
        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalList = $query->get()->sortBy(function ($jadwal) use ($hariOrder) {
            return [
                array_search($jadwal->hari, $hariOrder),
                $jadwal->jam_mulai
            ];
        });

        // Data untuk filter
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        $cabangList = \App\Models\Cabang::orderBy('nama_cabang')->get();

        $kelasList = Kelas::when($tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->when($cabangId, fn($q) => $q->where('cabang_id', $cabangId))
            ->when($jenjang, fn($q) => $q->where('jenjang', $jenjang))
            ->with('cabang')
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->orderBy('nama_kelas')
            ->get();

        // Get ALL kelas for the dropdown in Modal (ignoring filters)
        $allKelasList = Kelas::where('tahun_ajaran_id', $tahunAjaranId)
            ->with('cabang')
            ->get()
            ->sortBy(function ($kelas) {
                // Parse grade number from nama_kelas (e.g. "10 IPA 1" -> 10)
                // Use regex to capture the leading number
                if (preg_match('/^(\d+)/', $kelas->nama_kelas, $matches)) {
                    $grade = (int)$matches[1];
                } else {
                    // Handle non-numeric classes (TK, KB) - assign low value
                    // TK B > TK A > KB if needed, or just grouping
                    $grade = 0; 
                }
                
                return [
                    $kelas->cabang->nama_cabang, // Sort by Cabang first
                    -$grade,                    // Sort by Grade DESC (negative for asc sort)
                    $kelas->nama_kelas          // Then by Name (e.g., 10 IPA 1 vs 10 IPA 2)
                ];
            });

        // Hanya ambil guru dengan role 'guru_pengajar' (bukan wali_kelas)
        $guruList = TenagaPendidik::whereHas('user', function ($q) {
            $q->where('is_active', true)->where('role', 'guru_pengajar');
        })->orderBy('nama_lengkap')->get();

        // Statistics
        $stats = [
            'totalJadwal' => JadwalPelajaran::byTahunAjaran($tahunAjaranId)->aktif()->count(),
            'jadwalKosong' => JadwalPelajaran::byTahunAjaran($tahunAjaranId)->where('status', 'kosong')->count(),
            'totalGuru' => TenagaPendidik::whereHas('user', fn($q) => $q->where('is_active', true))
                ->whereHas('jadwalMengajar', fn($q) => $q->byTahunAjaran($tahunAjaranId))
                ->count(),
            'totalKelas' => Kelas::where('tahun_ajaran_id', $tahunAjaranId)->count(),
        ];

        return view('waka.jadwal-pelajaran.index', compact(
            'jadwalList',
            'tahunAjarans',
            'currentTahunAjaran',
            'cabangList',
            'kelasList',
            'guruList',
            'stats',
            'cabangId',
            'jenjang',
            'guruId',
            'allKelasList'
        ));
    }

    /**
     * Show the form for creating a new jadwal.
     */
    public function create(Request $request)
    {
        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        $kelasList = Kelas::where('tahun_ajaran_id', $tahunAjaranId)
            ->with('cabang')
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        $mataPelajaranList = MataPelajaran::orderBy('jenjang')->orderBy('nama_mapel')->get();

        // Hanya ambil guru dengan role 'guru_pengajar' (bukan wali_kelas)
        $guruList = TenagaPendidik::whereHas('user', function ($q) {
            $q->where('is_active', true)->where('role', 'guru_pengajar');
        })->with('user')->orderBy('nama_lengkap')->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Ambil semua pengaturan istirahat yang aktif, dikelompokkan per jenjang
        $pengaturanIstirahat = PengaturanIstirahat::where('is_active', true)
            ->orderBy('jenjang')
            ->orderBy('urutan')
            ->get()
            ->groupBy('jenjang');

        return view('waka.jadwal-pelajaran.create', compact(
            'tahunAjarans',
            'currentTahunAjaran',
            'kelasList',
            'mataPelajaranList',
            'guruList',
            'hariList',
            'pengaturanIstirahat'
        ));
    }

    /**
     * Store a newly created jadwal in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'nullable|exists:tenaga_pendidik,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        // Validasi bentrok
        $conflicts = $this->checkConflicts(
            $validated['tahun_ajaran_id'],
            $validated['kelas_id'],
            $validated['guru_id'],
            $validated['hari'],
            $validated['jam_mulai'],
            $validated['jam_selesai'],
            $validated['mata_pelajaran_id']
        );

        if ($conflicts['hasConflict']) {
            return back()->withInput()->with('error', $conflicts['message']);
        }

        // Auto-filter students for Religion subjects
        $filteredSiswaIds = $this->getFilteredSiswaIds($validated['kelas_id'], $validated['mataPelajaran_id'] ?? $validated['mata_pelajaran_id']);
        if ($filteredSiswaIds !== null) {
            $validated['siswa_ids'] = $filteredSiswaIds;
        }

        $validated['status'] = $validated['guru_id'] ? 'aktif' : 'kosong';
        $validated['updated_by'] = Auth::id();

        JadwalPelajaran::create($validated);

        return redirect()
            ->route('waka.jadwal-pelajaran.index', ['tahun_ajaran_id' => $validated['tahun_ajaran_id']])
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan!');
    }

    /**
     * Display the specified jadwal (show weekly grid).
     */
    public function show(Request $request, $kelasId)
    {
        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        $kelas = Kelas::with('cabang', 'tahunAjaran')->findOrFail($kelasId);

        $jadwalList = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->byTahunAjaran($tahunAjaranId)
            ->byKelas($kelasId)
            ->get();

        // Group by hari
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalByHari = collect($hariList)->mapWithKeys(function ($hari) use ($jadwalList) {
            return [
                $hari => $jadwalList->where('hari', $hari)->sortBy('jam_mulai')->values()
            ];
        });

        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        return view('waka.jadwal-pelajaran.show', compact(
            'kelas',
            'jadwalByHari',
            'hariList',
            'tahunAjarans',
            'currentTahunAjaran'
        ));
    }

    /**
     * Show the form for editing the specified jadwal.
     */
    public function edit(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->load(['tahunAjaran', 'kelas', 'mataPelajaran', 'guru']);

        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();

        $kelasList = Kelas::where('tahun_ajaran_id', $jadwalPelajaran->tahun_ajaran_id)
            ->with('cabang')
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        $mataPelajaranList = MataPelajaran::orderBy('jenjang')->orderBy('nama_mapel')->get();

        // Hanya ambil guru dengan role 'guru_pengajar' (bukan wali_kelas)
        $guruList = TenagaPendidik::whereHas('user', function ($q) {
            $q->where('is_active', true)->where('role', 'guru_pengajar');
        })->with('user')->orderBy('nama_lengkap')->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Ambil semua pengaturan istirahat yang aktif, dikelompokkan per jenjang
        $pengaturanIstirahat = PengaturanIstirahat::where('is_active', true)
            ->orderBy('jenjang')
            ->orderBy('urutan')
            ->get()
            ->groupBy('jenjang');

        return view('waka.jadwal-pelajaran.edit', compact(
            'jadwalPelajaran',
            'tahunAjarans',
            'kelasList',
            'mataPelajaranList',
            'guruList',
            'hariList',
            'pengaturanIstirahat'
        ));
    }

    /**
     * Update the specified jadwal in storage.
     */
    public function update(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'nullable|exists:tenaga_pendidik,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        // Validasi bentrok (exclude current jadwal)
        $conflicts = $this->checkConflicts(
            $validated['tahun_ajaran_id'],
            $validated['kelas_id'],
            $validated['guru_id'],
            $validated['hari'],
            $validated['jam_mulai'],
            $validated['jam_selesai'],
            $validated['mata_pelajaran_id'],
            $jadwalPelajaran->id
        );

        if ($conflicts['hasConflict']) {
            return back()->withInput()->with('error', $conflicts['message']);
        }

        // Track changes untuk history
        $this->trackChanges($jadwalPelajaran, $validated);

        // Auto-filter students for Religion subjects
        $filteredSiswaIds = $this->getFilteredSiswaIds($validated['kelas_id'], $validated['mataPelajaran_id'] ?? $validated['mata_pelajaran_id']);
        if ($filteredSiswaIds !== null) {
            $validated['siswa_ids'] = $filteredSiswaIds;
        }

        $validated['status'] = $validated['guru_id'] ? 'aktif' : 'kosong';
        $validated['updated_by'] = Auth::id();

        $jadwalPelajaran->update($validated);

        return redirect()
            ->route('waka.jadwal-pelajaran.index', ['tahun_ajaran_id' => $validated['tahun_ajaran_id']])
            ->with('success', 'Jadwal pelajaran berhasil diperbarui!');
    }

    /**
     * Remove the specified jadwal from storage.
     */
    public function destroy(JadwalPelajaran $jadwalPelajaran)
    {
        $tahunAjaranId = $jadwalPelajaran->tahun_ajaran_id;
        $jadwalPelajaran->delete();

        return redirect()
            ->route('waka.jadwal-pelajaran.index', ['tahun_ajaran_id' => $tahunAjaranId])
            ->with('success', 'Jadwal pelajaran berhasil dihapus!');
    }

    /**
     * Ganti guru pada jadwal tertentu.
     */
    public function gantiGuru(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $validated = $request->validate([
            'guru_id_baru' => 'nullable|exists:tenaga_pendidik,id',
            'alasan' => 'nullable|string',
        ]);

        $guruLama = $jadwalPelajaran->guru;
        $guruBaru = $validated['guru_id_baru'] ? TenagaPendidik::find($validated['guru_id_baru']) : null;

        // Check conflict untuk guru baru
        if ($guruBaru) {
            $conflicts = $this->checkConflicts(
                $jadwalPelajaran->tahun_ajaran_id,
                $jadwalPelajaran->kelas_id,
                $validated['guru_id_baru'],
                $jadwalPelajaran->hari,
                $jadwalPelajaran->jam_mulai,
                $jadwalPelajaran->jam_selesai,
                $jadwalPelajaran->mata_pelajaran_id,
                $jadwalPelajaran->id
            );

            if ($conflicts['hasConflict']) {
                return back()->with('error', $conflicts['message']);
            }
        }

        // Catat ke history
        JadwalPelajaranHistory::create([
            'jadwal_pelajaran_id' => $jadwalPelajaran->id,
            'field_changed' => 'guru_id',
            'old_value' => $guruLama ? $guruLama->nama_lengkap : 'Kosong',
            'new_value' => $guruBaru ? $guruBaru->nama_lengkap : 'Kosong',
            'keterangan' => $validated['alasan'] ?? 'Penggantian guru',
            'changed_by' => Auth::id(),
            'changed_at' => now(),
        ]);

        $jadwalPelajaran->update([
            'guru_id' => $validated['guru_id_baru'],
            'status' => $validated['guru_id_baru'] ? 'aktif' : 'kosong',
            'keterangan' => $validated['alasan'],
            'updated_by' => Auth::id(),
        ]);

        $message = $guruBaru
            ? "Guru berhasil diganti dari {$guruLama->nama_lengkap} ke {$guruBaru->nama_lengkap}"
            : "Jadwal diset menjadi kosong (menunggu guru pengganti)";

        return back()->with('success', $message);
    }

    /**
     * Bulk replace guru - ganti semua jadwal guru lama ke guru baru.
     */
    public function bulkReplaceGuru(Request $request)
    {
        $validated = $request->validate([
            'guru_id_lama' => 'required|exists:tenaga_pendidik,id',
            'guru_id_baru' => 'nullable|exists:tenaga_pendidik,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'alasan' => 'nullable|string',
        ]);

        $guruLama = TenagaPendidik::findOrFail($validated['guru_id_lama']);
        $guruBaru = $validated['guru_id_baru'] ? TenagaPendidik::find($validated['guru_id_baru']) : null;

        $jadwalList = JadwalPelajaran::byTahunAjaran($validated['tahun_ajaran_id'])
            ->byGuru($validated['guru_id_lama'])
            ->get();

        if ($jadwalList->isEmpty()) {
            return back()->with('error', 'Tidak ada jadwal yang perlu diganti!');
        }

        // Check conflicts untuk guru baru
        if ($guruBaru) {
            foreach ($jadwalList as $jadwal) {
                $conflicts = $this->checkConflicts(
                    $jadwal->tahun_ajaran_id,
                    $jadwal->kelas_id,
                    $validated['guru_id_baru'],
                    $jadwal->hari,
                    $jadwal->jam_mulai,
                    $jadwal->jam_selesai,
                    $jadwal->mata_pelajaran_id,
                    $jadwal->id
                );

                if ($conflicts['hasConflict']) {
                    return back()->with('error', "Bentrok pada jadwal: {$conflicts['message']}");
                }
            }
        }

        DB::beginTransaction();
        try {
            foreach ($jadwalList as $jadwal) {
                // Catat history
                JadwalPelajaranHistory::create([
                    'jadwal_pelajaran_id' => $jadwal->id,
                    'field_changed' => 'guru_id',
                    'old_value' => $guruLama->nama_lengkap,
                    'new_value' => $guruBaru ? $guruBaru->nama_lengkap : 'Kosong',
                    'keterangan' => $validated['alasan'] ?? 'Bulk replacement guru',
                    'changed_by' => Auth::id(),
                    'changed_at' => now(),
                ]);

                // Update jadwal
                $jadwal->update([
                    'guru_id' => $validated['guru_id_baru'],
                    'status' => $validated['guru_id_baru'] ? 'aktif' : 'kosong',
                    'keterangan' => $validated['alasan'],
                    'updated_by' => Auth::id(),
                ]);
            }

            DB::commit();

            $count = $jadwalList->count();
            $message = $guruBaru
                ? "Berhasil mengganti {$count} jadwal dari {$guruLama->nama_lengkap} ke {$guruBaru->nama_lengkap}"
                : "Berhasil mengosongkan {$count} jadwal dari {$guruLama->nama_lengkap}";

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mengganti jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Get jadwal by kelas (AJAX).
     */
    public function getByKelas(Request $request, $kelasId)
    {
        $tahunAjaranId = $request->tahun_ajaran_id;

        $jadwalList = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->byTahunAjaran($tahunAjaranId)
            ->byKelas($kelasId)
            ->get();

        return response()->json($jadwalList);
    }

    /**
     * Get students by kelas (AJAX).
     */
    public function getStudents(Request $request, $kelasId)
    {
        $students = \App\Models\Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->select('id', 'nama_lengkap', 'nis', 'agama')
            ->get();

        return response()->json($students);
    }

    /**
     * Get jadwal by guru (AJAX).
     */
    public function getByGuru(Request $request, $guruId)
    {
        $tahunAjaranId = $request->tahun_ajaran_id;

        $jadwalList = JadwalPelajaran::with(['kelas', 'mataPelajaran'])
            ->byTahunAjaran($tahunAjaranId)
            ->byGuru($guruId)
            ->get();

        return response()->json($jadwalList);
    }

    /**
     * Check for schedule conflicts.
     */
    private function checkConflicts($tahunAjaranId, $kelasId, $guruId, $hari, $jamMulai, $jamSelesai, $mataPelajaranId = null, $excludeId = null)
    {
        $result = ['hasConflict' => false, 'message' => ''];

        // Get Current Class Data
        $kelas = Kelas::with('cabang')->find($kelasId);
        if (!$kelas) {
            return ['hasConflict' => true, 'message' => 'Kelas tidak ditemukan'];
        }

        // Check bentrok dengan waktu istirahat
        $istirahatConflict = PengaturanIstirahat::jenjang($kelas->jenjang)
            ->aktif()
            ->untukHari($hari)
            ->get()
            ->first(function ($istirahat) use ($jamMulai, $jamSelesai) {
                return !($jamSelesai <= $istirahat->jam_mulai || $jamMulai >= $istirahat->jam_selesai);
            });

        if ($istirahatConflict) {
            $result['hasConflict'] = true;
            $result['message'] = "Bentrok dengan waktu istirahat '{$istirahatConflict->nama_istirahat}' pada {$hari} jam " . substr($istirahatConflict->jam_mulai, 0, 5) . " - " . substr($istirahatConflict->jam_selesai, 0, 5);
            return $result;
        }

        // Check if subject is Religion
        $isAgama = false;
        if ($mataPelajaranId) {
            $mapel = MataPelajaran::find($mataPelajaranId);
            if ($mapel && (stripos($mapel->nama_mapel, 'Agama') !== false || stripos($mapel->nama_mapel, 'Religi') !== false)) {
                $isAgama = true;
            }
        }

        // Check bentrok kelas
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
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId));

        // If Agama, allow overlapping with OTHER Agama subjects
        if ($isAgama) {
            $kelasConflictQuery->whereHas('mataPelajaran', function($q) {
                 $q->where('nama_mapel', 'not like', '%Agama%')
                   ->where('nama_mapel', 'not like', '%Religi%');
            });
        }

        $kelasConflict = $kelasConflictQuery->first();

        if ($kelasConflict) {
            $result['hasConflict'] = true;
            $result['message'] = "Bentrok dengan jadwal kelas pada {$hari} jam {$kelasConflict->jam_mulai} - {$kelasConflict->jam_selesai}";
            return $result;
        }

        // Check bentrok guru (jika ada)
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
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->with(['kelas.cabang', 'mataPelajaran'])
                ->first();

            if ($guruConflict) {
                // Check if Merge Exception applies:
                // Same Subject AND Same Branch (Teacher teaches same subject in same branch at same time)
                $sameSubject = $mataPelajaranId && $guruConflict->mata_pelajaran_id == $mataPelajaranId;
                
                // Check branch match
                $conflictingClassBranchId = $guruConflict->kelas->cabang_id ?? null;
                $currentClassBranchId = $kelas->cabang_id ?? null;
                
                $sameBranch = $conflictingClassBranchId && $currentClassBranchId && $conflictingClassBranchId == $currentClassBranchId;

                // Also we can check Jenjang match if needed, but "Same Branch" is the user requirement "cabang sama".
                // User said "misalnya SMP berada pada 7A,8A,9A". This implies Same Jenjang too, but Same Branch is the hard constraint mentioned.
                
                if ($sameSubject && $sameBranch) {
                    // It is a valid merge. No conflict.
                } else {
                    $result['hasConflict'] = true;
                    $result['message'] = "Guru bentrok pada {$hari} jam {$guruConflict->jam_mulai} - {$guruConflict->jam_selesai} di kelas {$guruConflict->kelas->nama_kelas}";
                    return $result;
                }
            }
        }

        return $result;
    }

    /**
     * Get filtered student IDs for religion subjects.
     * Returns array of IDs if filtering is needed, or null if no filtering (all students).
     */
    private function getFilteredSiswaIds($kelasId, $mataPelajaranId)
    {
        $mapel = MataPelajaran::find($mataPelajaranId);
        if (!$mapel) {
            return null;
        }

        $namaMapel = strtolower($mapel->nama_mapel);

        // Check for Religion keywords
        $agamaFilter = null;
        if (str_contains($namaMapel, 'agama kristen') || str_contains($namaMapel, 'religi kristen')) {
            $agamaFilter = 'Kristen';
        } elseif (str_contains($namaMapel, 'agama islam') || str_contains($namaMapel, 'religi islam')) {
            $agamaFilter = 'Islam';
        }

        // If it's a religion subject that requires filtering
        if ($agamaFilter) {
            // Fetch students in the class with matching religion
            // Case insensitive comparison for religion might be needed depending on DB collation, 
            // but strict matching is usually safer for standardized inputs.
            // Using 'like' for bit of flexibility if needed, or simple where.
            $siswaIds = \App\Models\Siswa::where('kelas_id', $kelasId)
                ->where('status', 'aktif')
                ->where('agama', 'LIKE', "%{$agamaFilter}%") 
                ->pluck('id')
                ->toArray();
            
            return $siswaIds;
        }

        // Return null means "All Students" (no specific list stored, or handled as null in DB)
        // If your DB requires explicit list for "All", fetch all IDs. 
        // Based on previous code `siswa_ids` => 'nullable|array', if null it likely means all.
        return null;
    }

    /**
     * Track changes untuk history.
     */
    private function trackChanges(JadwalPelajaran $jadwal, array $newData)
    {
        $fieldsToTrack = [
            'guru_id' => 'Guru',
            'kelas_id' => 'Kelas',
            'mata_pelajaran_id' => 'Mata Pelajaran',
            'hari' => 'Hari',
            'jam_mulai' => 'Jam Mulai',
            'jam_selesai' => 'Jam Selesai',
            'siswa_ids' => 'Siswa Khusus',
        ];

        foreach ($fieldsToTrack as $field => $label) {
            if (isset($newData[$field]) && $jadwal->$field != $newData[$field]) {
                $oldValue = $this->getReadableValue($field, $jadwal->$field);
                $newValue = $this->getReadableValue($field, $newData[$field]);

                JadwalPelajaranHistory::create([
                    'jadwal_pelajaran_id' => $jadwal->id,
                    'field_changed' => $field,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'keterangan' => "Perubahan {$label}",
                    'changed_by' => Auth::id(),
                    'changed_at' => now(),
                ]);
            }
        }
    }

    /**
     * Get readable value for history.
     */
    private function getReadableValue($field, $value)
    {
        if (is_null($value))
            return 'Kosong';

        switch ($field) {
            case 'guru_id':
                $guru = TenagaPendidik::find($value);
                return $guru ? $guru->nama_lengkap : 'Kosong';
            case 'kelas_id':
                $kelas = Kelas::find($value);
                return $kelas ? $kelas->nama_kelas : '-';
            case 'mata_pelajaran_id':
                $mapel = MataPelajaran::find($value);
                return $mapel ? $mapel->nama_mapel : '-';
            case 'siswa_ids':
                if (empty($value)) return 'Semua Siswa';
                return count($value) . ' Siswa Dipilih';
            default:
                return $value;
        }
    }

    /**
     * Preview print jadwal (per kelas).
     */
    public function previewPrint(Request $request, $kelasId)
    {
        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        $kelas = Kelas::with('cabang', 'tahunAjaran', 'waliKelas')->findOrFail($kelasId);

        $jadwalList = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->byTahunAjaran($tahunAjaranId)
            ->byKelas($kelasId)
            ->get();

        // Get Breaks for this Jenjang
        $istirahatList = PengaturanIstirahat::where('is_active', true)
            ->where('jenjang', $kelas->jenjang)
            ->get();

        // Build Grid
        $scheduleGrid = $this->buildScheduleGrid($jadwalList, $istirahatList);

        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        return view('waka.jadwal-pelajaran.print', compact(
            'kelas',
            'scheduleGrid',
            'currentTahunAjaran'
        ))->with('preview', true);
    }

    /**
     * Export jadwal to PDF (per kelas).
     */
    public function exportPdf(Request $request, $kelasId)
    {
        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        $kelas = Kelas::with('cabang', 'tahunAjaran', 'waliKelas')->findOrFail($kelasId);

        $jadwalList = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->byTahunAjaran($tahunAjaranId)
            ->byKelas($kelasId)
            ->get();

        // Get Breaks for this Jenjang
        $istirahatList = PengaturanIstirahat::where('is_active', true)
            ->where('jenjang', $kelas->jenjang)
            ->get();

        // Build Grid
        $scheduleGrid = $this->buildScheduleGrid($jadwalList, $istirahatList);

        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        return view('waka.jadwal-pelajaran.print', compact(
            'kelas',
            'scheduleGrid',
            'currentTahunAjaran'
        ))->with('preview', false);
    }

    /**
     * Export jadwal to Excel (per kelas).
     */
    public function exportExcelClass(Request $request, $kelasId)
    {
        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        $kelas = Kelas::with('cabang', 'tahunAjaran', 'waliKelas')->findOrFail($kelasId);

        $jadwalList = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->byTahunAjaran($tahunAjaranId)
            ->byKelas($kelasId)
            ->get();

        // Get Breaks for this Jenjang
        $istirahatList = PengaturanIstirahat::where('is_active', true)
            ->where('jenjang', $kelas->jenjang)
            ->get();

        // Build Grid
        $scheduleGrid = $this->buildScheduleGrid($jadwalList, $istirahatList);

        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;
        
        // Generate filename with .xls extension
        $filename = 'Jadwal_' . str_replace(' ', '_', $kelas->nama_kelas) . '_' . ($currentTahunAjaran ? str_replace(' ', '_', $currentTahunAjaran->nama_tahun_ajaran) : '') . '.xls';

        return response()->view('waka.jadwal-pelajaran.export-excel-class', compact(
            'kelas',
            'scheduleGrid',
            'currentTahunAjaran'
        ))
        ->header('Content-Type', 'application/vnd.ms-excel')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
    }

    /**
     * Export jadwal to Excel.
     */
    public function exportExcel(Request $request)
    {
        // Get filter parameters - same as index method
        $tahunAjaranId = $request->tahun_ajaran_id;
        $cabangId = $request->cabang_id;
        $jenjang = $request->jenjang;
        $kelasId = $request->kelas_id;
        $guruId = $request->guru_id;

        // Build query with all filters
        $query = JadwalPelajaran::with([
            'kelas.cabang',
            'mataPelajaran',
            'guru',
            'tahunAjaran'
        ]);

        if ($tahunAjaranId) {
            $query->byTahunAjaran($tahunAjaranId);
        }

        if ($cabangId) {
            $query->whereHas('kelas', fn($q) => $q->where('cabang_id', $cabangId));
        }

        if ($jenjang) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $jenjang));
        }

        if ($kelasId) {
            $query->byKelas($kelasId);
        }

        if ($guruId) {
            $query->byGuru($guruId);
        }

        // Get sorted list
        $jadwalList = $query->get()->sortBy(function ($jadwal) {
            $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            return [
                array_search($jadwal->hari, $hariOrder),
                $jadwal->jam_mulai
            ];
        });

        $tahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : TahunAjaran::where('is_active', true)->first();

        // Get filter info for display
        $filterInfo = [
            'cabang' => $cabangId ? \App\Models\Cabang::find($cabangId)->nama_cabang : null,
            'jenjang' => $jenjang,
            'kelas' => $kelasId ? Kelas::find($kelasId)->nama_kelas : null,
            'guru' => $guruId ? TenagaPendidik::find($guruId)->nama_lengkap : null,
        ];

        // Get pengaturan istirahat aktif
        $pengaturanIstirahat = PengaturanIstirahat::where('is_active', true)
            ->orderBy('jenjang')
            ->orderBy('jam_mulai')
            ->get();

        // Generate filename with .xls extension
        $filename = 'Jadwal_Pelajaran_' . ($tahunAjaran ? str_replace(' ', '_', $tahunAjaran->nama_tahun_ajaran) : 'Export') . '.xls';

        return response()->view('waka.jadwal-pelajaran.export-excel', compact('jadwalList', 'tahunAjaran', 'filterInfo', 'pengaturanIstirahat'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Export all jadwal to PDF.
     */
    public function exportPdfAll(Request $request)
    {
        // Get filter parameters - same as index method
        $tahunAjaranId = $request->tahun_ajaran_id;
        $cabangId = $request->cabang_id;
        $jenjang = $request->jenjang;
        $kelasId = $request->kelas_id;
        $guruId = $request->guru_id;

        // Build query with all filters
        $query = JadwalPelajaran::with([
            'kelas.cabang',
            'kelas.waliKelas',
            'mataPelajaran',
            'guru',
            'tahunAjaran'
        ]);

        if ($tahunAjaranId) {
            $query->byTahunAjaran($tahunAjaranId);
        }

        if ($cabangId) {
            $query->whereHas('kelas', fn($q) => $q->where('cabang_id', $cabangId));
        }

        if ($jenjang) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $jenjang));
        }

        if ($kelasId) {
            $query->byKelas($kelasId);
        }

        if ($guruId) {
            $query->byGuru($guruId);
        }

        // Get sorted list
        $jadwalList = $query->get()->sortBy(function ($jadwal) {
            $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            return [
                array_search($jadwal->hari, $hariOrder),
                $jadwal->jam_mulai
            ];
        });

        $tahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : TahunAjaran::where('is_active', true)->first();

        // Get filter info for display
        $filterInfo = [
            'cabang' => $cabangId ? \App\Models\Cabang::find($cabangId)->nama_cabang : null,
            'jenjang' => $jenjang,
            'kelas' => $kelasId ? Kelas::find($kelasId)->nama_kelas : null,
            'guru' => $guruId ? TenagaPendidik::find($guruId)->nama_lengkap : null,
        ];

        // Get pengaturan istirahat aktif
        $pengaturanIstirahat = PengaturanIstirahat::where('is_active', true)
            ->orderBy('jenjang')
            ->orderBy('jam_mulai')
            ->get();

        return view('waka.jadwal-pelajaran.export-pdf', compact('jadwalList', 'tahunAjaran', 'filterInfo', 'pengaturanIstirahat'));
    }

    /**
     * Build grid structure for schedule PDF
     */
    private function buildScheduleGrid($jadwalList, $istirahatList)
    {
        // 1. Collect all unique Start Times to define Grid Rows
        $startTimes = collect();
        
        foreach ($jadwalList as $jadwal) {
            $startTimes->push($jadwal->jam_mulai->format('H:i'));
        }
        
        foreach ($istirahatList as $ist) {
            $startTimes->push(substr($ist->jam_mulai, 0, 5));
        }

        $gridRows = $startTimes->unique()->sort()->values(); // e.g. ['07:00', '07:40', '08:20', ...]

        // 2. Build the Grid
        // Structure: $grid[time_index]['time'] = '07:00'
        //            $grid[time_index]['days'][Senin] = Item
        
        $grid = [];
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; // Sabtu usually not in PDF report matrix unless needed? User image shows until Jumat. I will include Sabtu if data exists.
        
        // Check if we have Sabtu data
        if ($jadwalList->where('hari', 'Sabtu')->count() > 0) {
            $hariList[] = 'Sabtu';
        }

        // Initialize Grid
        foreach ($gridRows as $index => $time) {
            $grid[$index] = [
                'time_start' => $time,
                'days' => []
            ];
            foreach ($hariList as $hari) {
                $grid[$index]['days'][$hari] = ['type' => 'empty'];
            }
        }

        // Helper to find grid index for a given time
        $getGridIndex = function($time) use ($gridRows) {
            return $gridRows->search($time);
        };

        // 3. Place Items into Grid
        
        // A. Place Lessons
        foreach ($jadwalList as $jadwal) {
            $startTime = $jadwal->jam_mulai->format('H:i');
            $endTime = $jadwal->jam_selesai->format('H:i');
            $day = $jadwal->hari;
            
            if (!in_array($day, $hariList)) continue;

            $startIndex = $getGridIndex($startTime);
            if ($startIndex === false) continue; // Should not happen

            // Calculate Rowspan
            // Count how many grid rows this item covers
            // It covers from startIndex UP TO (but not including) the grid row that matches endTime
            // OR if endTime is not a startTme, find the next one? 
            // Simplification: Count how many startTimes are < endTime and >= startTime
            
            $span = 0;
            for ($i = $startIndex; $i < count($gridRows); $i++) {
                if ($gridRows[$i] < $endTime) {
                    $span++;
                } else {
                    break;
                }
            }
            if ($span < 1) $span = 1;

            // Mark cells
            if (isset($grid[$startIndex]['days'][$day]['type']) && $grid[$startIndex]['days'][$day]['type'] == 'taken') {
                // Conflict or merge? Append text?
                // For now, simplify: if 7A and 7B both have Math at same time, just combine text
                // But here we are iterating items.
                // We need to check if cell is already 'lesson'.
                $existing = $grid[$startIndex]['days'][$day];
                 if ($existing['type'] == 'lesson') {
                     // Append content
                     $grid[$startIndex]['days'][$day]['data'][] = $jadwal;
                 } else {
                     // Create new
                     $grid[$startIndex]['days'][$day] = [
                         'type' => 'lesson',
                         'rowspan' => $span,
                         'data' => [$jadwal]
                     ];
                     
                     // Mark covered cells as 'taken'
                     for ($r = 1; $r < $span; $r++) {
                         if (isset($grid[$startIndex + $r])) {
                            $grid[$startIndex + $r]['days'][$day] = ['type' => 'taken'];
                         }
                     }
                 }
            } else if ($grid[$startIndex]['days'][$day]['type'] == 'empty') {
                 $grid[$startIndex]['days'][$day] = [
                     'type' => 'lesson',
                     'rowspan' => $span,
                     'data' => [$jadwal]
                 ];
                 // Mark covered
                 for ($r = 1; $r < $span; $r++) {
                     if (isset($grid[$startIndex + $r])) {
                        $grid[$startIndex + $r]['days'][$day] = ['type' => 'taken'];
                     }
                 }
            }
        }

        // B. Place Breaks
        foreach ($istirahatList as $ist) {
            $startTime = substr($ist->jam_mulai, 0, 5);
            $endTime = substr($ist->jam_selesai, 0, 5);
            $targetDays = is_array($ist->hari_aktif) ? $ist->hari_aktif : json_decode($ist->hari_aktif, true);
            
            if (!in_array($ist->jenjang, ['SMA', 'SMP', 'SD', 'TK', 'KB'])) {
                 // Fallback if needed
            }
            
            if (!$targetDays) $targetDays = $hariList;

            $startIndex = $getGridIndex($startTime);
            if ($startIndex === false) continue;

             $span = 0;
            for ($i = $startIndex; $i < count($gridRows); $i++) {
                if ($gridRows[$i] < $endTime) {
                    $span++;
                } else {
                    break;
                }
            }
            if ($span < 1) $span = 1;

            foreach ($targetDays as $day) {
                if (!in_array($day, $hariList)) continue;
                
                // Check if cell is available (lesson takes precedence? or break?)
                // Usually break is absolute.
                
                $grid[$startIndex]['days'][$day] = [
                    'type' => 'break',
                    'rowspan' => $span,
                    'data' => $ist
                ];

                 // Mark covered
                 for ($r = 1; $r < $span; $r++) {
                     if (isset($grid[$startIndex + $r])) {
                        $grid[$startIndex + $r]['days'][$day] = ['type' => 'taken'];
                     }
                 }
            }
        }
        
        return [
            'rows' => $grid,
            'days' => $hariList
        ];
    }

    /**
     * Duplicate jadwal from previous year.
     */
    public function duplicate(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id_lama' => 'required|exists:tahun_ajaran,id',
            'tahun_ajaran_id_baru' => 'required|exists:tahun_ajaran,id',
        ]);

        $tahunAjaranLama = TahunAjaran::findOrFail($validated['tahun_ajaran_id_lama']);
        $tahunAjaranBaru = TahunAjaran::findOrFail($validated['tahun_ajaran_id_baru']);

        // Get all jadwal from old year
        $jadwalLama = JadwalPelajaran::byTahunAjaran($validated['tahun_ajaran_id_lama'])->get();

        if ($jadwalLama->isEmpty()) {
            return back()->with('error', 'Tidak ada jadwal di tahun ajaran yang dipilih!');
        }

        DB::beginTransaction();
        try {
            $duplicated = 0;
            $skipped = 0;

            foreach ($jadwalLama as $jadwal) {
                // Find corresponding kelas in new year
                $kelasLama = $jadwal->kelas;
                $kelasBaru = Kelas::where('tahun_ajaran_id', $validated['tahun_ajaran_id_baru'])
                    ->where('nama_kelas', $kelasLama->nama_kelas)
                    ->where('jenjang', $kelasLama->jenjang)
                    ->where('cabang_id', $kelasLama->cabang_id)
                    ->first();

                if (!$kelasBaru) {
                    $skipped++;
                    continue;
                }

                // Check if jadwal already exists
                $exists = JadwalPelajaran::where('tahun_ajaran_id', $validated['tahun_ajaran_id_baru'])
                    ->where('kelas_id', $kelasBaru->id)
                    ->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
                    ->where('hari', $jadwal->hari)
                    ->where('jam_mulai', $jadwal->jam_mulai)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Create duplicate
                JadwalPelajaran::create([
                    'tahun_ajaran_id' => $validated['tahun_ajaran_id_baru'],
                    'kelas_id' => $kelasBaru->id,
                    'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
                    'guru_id' => $jadwal->guru_id,
                    'hari' => $jadwal->hari,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'status' => $jadwal->status,
                    'keterangan' => 'Duplikasi dari ' . $tahunAjaranLama->nama_tahun_ajaran,
                    'updated_by' => Auth::id(),
                ]);

                $duplicated++;
            }

            DB::commit();

            $message = "Berhasil menduplikasi {$duplicated} jadwal.";
            if ($skipped > 0) {
                $message .= " {$skipped} jadwal dilewati (kelas tidak ditemukan atau sudah ada).";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete jadwal pelajaran.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'jadwal_ids' => 'required|string',
        ]);

        try {
            $jadwalIds = json_decode($request->jadwal_ids, true);

            if (!is_array($jadwalIds) || empty($jadwalIds)) {
                return back()->with('error', 'Data jadwal tidak valid');
            }

            DB::beginTransaction();

            // Delete jadwal
            $deleted = JadwalPelajaran::whereIn('id', $jadwalIds)->delete();

            DB::commit();

            return back()->with('success', "Berhasil menghapus {$deleted} jadwal pelajaran");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update status jadwal pelajaran.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'jadwal_ids' => 'required|string',
            'status' => 'required|in:aktif,kosong',
        ]);

        try {
            $jadwalIds = json_decode($request->jadwal_ids, true);

            if (!is_array($jadwalIds) || empty($jadwalIds)) {
                return back()->with('error', 'Data jadwal tidak valid');
            }

            DB::beginTransaction();

            // Update status
            $updated = JadwalPelajaran::whereIn('id', $jadwalIds)
                ->update([
                        'status' => $request->status,
                        'updated_at' => now(),
                        'updated_by' => Auth::id(),
                    ]);

            DB::commit();

            $statusLabel = $request->status == 'aktif' ? 'AKTIF' : 'KOSONG';
            return back()->with('success', "Berhasil mengubah status {$updated} jadwal menjadi {$statusLabel}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show import form.
     */
    public function importForm()
    {
        return view('waka.jadwal-pelajaran.import');
    }

    /**
     * Process import from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        try {
            $import = new \App\Imports\JadwalPelajaranImport($request->tahun_ajaran_id);
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $missingKelas = $import->getMissingKelas();
            $missingMapel = $import->getMissingMapel();
            $missingGuru = $import->getMissingGuru();
            $warnings = $import->getWarnings();

            $message = "Berhasil mengimport {$imported} jadwal pelajaran.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati.";
            }

            // Build warning message for missing entities
            $warningMessage = '';
            if (!empty($missingKelas)) {
                $warningMessage .= "Kelas tidak ditemukan: " . implode(', ', $missingKelas) . ". ";
            }
            if (!empty($missingMapel)) {
                $warningMessage .= "Mata Pelajaran tidak ditemukan: " . implode(', ', $missingMapel) . ". ";
            }
            if (!empty($missingGuru)) {
                $warningMessage .= "Guru tidak ditemukan (jadwal dibuat dengan status kosong): " . implode(', ', $missingGuru) . ". ";
            }

            if (!empty($warningMessage)) {
                return redirect()->route('waka.jadwal-pelajaran.index', ['tahun_ajaran_id' => $request->tahun_ajaran_id])
                    ->with('success', $message)
                    ->with('warning', $warningMessage);
            }

            return redirect()->route('waka.jadwal-pelajaran.index', ['tahun_ajaran_id' => $request->tahun_ajaran_id])
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport: ' . $e->getMessage());
        }
    }

    /**
     * Download import template.
     */
    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Templates\JadwalPelajaranTemplate(),
            'template_jadwal_pelajaran.xlsx'
        );
    }
}
