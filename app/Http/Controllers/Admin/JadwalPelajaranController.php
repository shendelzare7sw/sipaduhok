<?php

namespace App\Http\Controllers\Admin;

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
        $jadwalList = $query->get()->sortBy(function($jadwal) use ($hariOrder) {
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
            ->get();

        // Hanya ambil guru dengan role 'guru_pengajar' (bukan wali_kelas)
        $guruList = TenagaPendidik::whereHas('user', function($q) {
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

        return view('admin.jadwal-pelajaran.index', compact(
            'jadwalList',
            'tahunAjarans',
            'currentTahunAjaran',
            'cabangList',
            'kelasList',
            'guruList',
            'stats',
            'cabangId',
            'jenjang',
            'kelasId',
            'guruId'
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
        $guruList = TenagaPendidik::whereHas('user', function($q) {
            $q->where('is_active', true)->where('role', 'guru_pengajar');
        })->orderBy('nama_lengkap')->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Ambil semua pengaturan istirahat yang aktif, dikelompokkan per jenjang
        $pengaturanIstirahat = PengaturanIstirahat::where('is_active', true)
            ->orderBy('jenjang')
            ->orderBy('urutan')
            ->get()
            ->groupBy('jenjang');

        return view('admin.jadwal-pelajaran.create', compact(
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
        ]);

        // Validasi bentrok
        $conflicts = $this->checkConflicts(
            $validated['tahun_ajaran_id'],
            $validated['kelas_id'],
            $validated['guru_id'],
            $validated['hari'],
            $validated['jam_mulai'],
            $validated['jam_selesai']
        );

        if ($conflicts['hasConflict']) {
            return back()->withInput()->with('error', $conflicts['message']);
        }

        $validated['status'] = $validated['guru_id'] ? 'aktif' : 'kosong';
        $validated['updated_by'] = Auth::id();

        JadwalPelajaran::create($validated);

        return redirect()
            ->route('admin.jadwal-pelajaran.index', ['tahun_ajaran_id' => $validated['tahun_ajaran_id']])
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
        $jadwalByHari = collect($hariList)->mapWithKeys(function($hari) use ($jadwalList) {
            return [
                $hari => $jadwalList->where('hari', $hari)->sortBy('jam_mulai')->values()
            ];
        });

        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        return view('admin.jadwal-pelajaran.show', compact(
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
        $guruList = TenagaPendidik::whereHas('user', function($q) {
            $q->where('is_active', true)->where('role', 'guru_pengajar');
        })->orderBy('nama_lengkap')->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Ambil semua pengaturan istirahat yang aktif, dikelompokkan per jenjang
        $pengaturanIstirahat = PengaturanIstirahat::where('is_active', true)
            ->orderBy('jenjang')
            ->orderBy('urutan')
            ->get()
            ->groupBy('jenjang');

        return view('admin.jadwal-pelajaran.edit', compact(
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
        ]);

        // Validasi bentrok (exclude current jadwal)
        $conflicts = $this->checkConflicts(
            $validated['tahun_ajaran_id'],
            $validated['kelas_id'],
            $validated['guru_id'],
            $validated['hari'],
            $validated['jam_mulai'],
            $validated['jam_selesai'],
            $jadwalPelajaran->id
        );

        if ($conflicts['hasConflict']) {
            return back()->withInput()->with('error', $conflicts['message']);
        }

        // Track changes untuk history
        $this->trackChanges($jadwalPelajaran, $validated);

        $validated['status'] = $validated['guru_id'] ? 'aktif' : 'kosong';
        $validated['updated_by'] = Auth::id();

        $jadwalPelajaran->update($validated);

        return redirect()
            ->route('admin.jadwal-pelajaran.index', ['tahun_ajaran_id' => $validated['tahun_ajaran_id']])
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
            ->route('admin.jadwal-pelajaran.index', ['tahun_ajaran_id' => $tahunAjaranId])
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
    private function checkConflicts($tahunAjaranId, $kelasId, $guruId, $hari, $jamMulai, $jamSelesai, $excludeId = null)
    {
        $result = ['hasConflict' => false, 'message' => ''];

        // Check bentrok dengan waktu istirahat
        $kelas = Kelas::find($kelasId);
        if ($kelas) {
            $istirahatConflict = PengaturanIstirahat::jenjang($kelas->jenjang)
                ->aktif()
                ->untukHari($hari)
                ->get()
                ->first(function($istirahat) use ($jamMulai, $jamSelesai) {
                    // Check if time overlaps
                    // Menggunakan <= dan >= agar jadwal yang berakhir/mulai TEPAT pada boundary
                    // istirahat TIDAK dianggap bentrok
                    // Contoh: Jadwal 07:00-08:30 dengan Istirahat 08:30-09:00 = TIDAK bentrok ✅
                    return !($jamSelesai <= $istirahat->jam_mulai || $jamMulai >= $istirahat->jam_selesai);
                });

            if ($istirahatConflict) {
                $result['hasConflict'] = true;
                $result['message'] = "Bentrok dengan waktu istirahat '{$istirahatConflict->nama_istirahat}' pada {$hari} jam " . substr($istirahatConflict->jam_mulai, 0, 5) . " - " . substr($istirahatConflict->jam_selesai, 0, 5);
                return $result;
            }
        }

        // Check bentrok kelas
        $kelasConflict = JadwalPelajaran::byTahunAjaran($tahunAjaranId)
            ->byKelas($kelasId)
            ->byHari($hari)
            ->where(function($q) use ($jamMulai, $jamSelesai) {
                $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                  ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                  ->orWhere(function($q2) use ($jamMulai, $jamSelesai) {
                      $q2->where('jam_mulai', '<=', $jamMulai)
                         ->where('jam_selesai', '>=', $jamSelesai);
                  });
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->first();

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
                ->where(function($q) use ($jamMulai, $jamSelesai) {
                    $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                      ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                      ->orWhere(function($q2) use ($jamMulai, $jamSelesai) {
                          $q2->where('jam_mulai', '<=', $jamMulai)
                             ->where('jam_selesai', '>=', $jamSelesai);
                      });
                })
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->with('kelas')
                ->first();

            if ($guruConflict) {
                $result['hasConflict'] = true;
                $result['message'] = "Guru bentrok pada {$hari} jam {$guruConflict->jam_mulai} - {$guruConflict->jam_selesai} di kelas {$guruConflict->kelas->nama_kelas}";
                return $result;
            }
        }

        return $result;
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
        if (is_null($value)) return 'Kosong';

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

        // Group by hari
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalByHari = collect($hariList)->mapWithKeys(function($hari) use ($jadwalList) {
            return [
                $hari => $jadwalList->where('hari', $hari)->sortBy('jam_mulai')->values()
            ];
        });

        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        return view('admin.jadwal-pelajaran.print', compact(
            'kelas',
            'jadwalByHari',
            'hariList',
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

        // Group by hari
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalByHari = collect($hariList)->mapWithKeys(function($hari) use ($jadwalList) {
            return [
                $hari => $jadwalList->where('hari', $hari)->sortBy('jam_mulai')->values()
            ];
        });

        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        return view('admin.jadwal-pelajaran.print', compact(
            'kelas',
            'jadwalByHari',
            'hariList',
            'currentTahunAjaran'
        ))->with('preview', false);
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

        $jadwalList = $query->get()->sortBy(function($jadwal) {
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

        // Generate filename
        $filename = 'Jadwal_Pelajaran_' . ($tahunAjaran ? str_replace(' ', '_', $tahunAjaran->nama_tahun_ajaran) : 'Export') . '.xls';

        // Set proper headers for Excel download
        return response()->view('admin.jadwal-pelajaran.export-excel', compact('jadwalList', 'tahunAjaran', 'filterInfo'))
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

        $jadwalList = $query->get()->sortBy(function($jadwal) {
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

        return view('admin.jadwal-pelajaran.export-pdf', compact('jadwalList', 'tahunAjaran', 'filterInfo'));
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
}
