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
use App\Traits\JadwalPelajaranTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\Templates\JadwalPelajaranTemplate;
use App\Imports\JadwalPelajaranImport;
use Maatwebsite\Excel\Facades\Excel;

class JadwalPelajaranController extends Controller
{
    use JadwalPelajaranTrait;
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

        // Mandatory filter by user's assigned cabang
        $userCabangId = auth()->user()->cabang_id;
        if (!$userCabangId) {
            return redirect()->back()->with('error', 'Akun Anda belum memiliki cabang yang ditetapkan. Hubungi administrator.');
        }
        $cabangId = $userCabangId; // Always use user's cabang, ignore request value

        // Get filter parameters (cabang is no longer user-selectable)
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

        // Mandatory cabang filter
        $query->whereHas('kelas', fn($q) => $q->where('cabang_id', $cabangId));

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

        $kelasList = Kelas::when($tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->where('cabang_id', $cabangId)
            ->when($jenjang, fn($q) => $q->where('jenjang', $jenjang))
            ->with('cabang')
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        // Get ALL kelas for the dropdown in Modal (filtered by user's cabang)
        $allKelasList = Kelas::where('tahun_ajaran_id', $tahunAjaranId)
            ->where('cabang_id', $cabangId)
            ->with('cabang')
            ->get()
            ->sortBy(function ($kelas) {
                // Parse grade number from nama_kelas (e.g. "10 IPA 1" -> 10)
                // Use regex to capture the leading number
                if (preg_match('/^(\d+)/', $kelas->nama_kelas, $matches)) {
                    $grade = (int)$matches[1];
                } else {
                    // Handle non-numeric classes (TK, KB) - assign low value
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

        // Statistics (filtered by user's cabang)
        $stats = [
            'totalJadwal' => JadwalPelajaran::byTahunAjaran($tahunAjaranId)->aktif()
                ->whereHas('kelas', fn($q) => $q->where('cabang_id', $cabangId))->count(),
            'jadwalKosong' => JadwalPelajaran::byTahunAjaran($tahunAjaranId)->where('status', 'kosong')
                ->whereHas('kelas', fn($q) => $q->where('cabang_id', $cabangId))->count(),
            'totalGuru' => TenagaPendidik::whereHas('user', fn($q) => $q->where('is_active', true))
                ->whereHas('jadwalMengajar', fn($q) => $q->byTahunAjaran($tahunAjaranId)
                    ->whereHas('kelas', fn($kq) => $kq->where('cabang_id', $cabangId)))
                ->count(),
            'totalKelas' => Kelas::where('tahun_ajaran_id', $tahunAjaranId)
                ->where('cabang_id', $cabangId)->count(),
        ];

        return view('waka.jadwal-pelajaran.index', compact(
            'jadwalList',
            'tahunAjarans',
            'currentTahunAjaran',
            'kelasList',
            'guruList',
            'stats',
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
            ->where('cabang_id', auth()->user()->cabang_id)
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
        // Multi-jenjang mode: delegate to trait
        if ($request->input('is_multi_jenjang')) {
            return $this->storeMultiJenjang($request, 'waka');
        }

        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'nullable|exists:tenaga_pendidik,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        // Security: pastikan semua kelas yang dipilih milik cabang waka
        $userCabangId = auth()->user()->cabang_id;
        $invalidKelas = Kelas::whereIn('id', $validated['kelas_ids'])
            ->where('cabang_id', '!=', $userCabangId)
            ->exists();
        if ($invalidKelas) {
            return back()->withInput()->with('error', 'Kelas yang dipilih tidak sesuai dengan cabang Anda.');
        }

        // Validasi Jenjang Compatibility
        $genreCheck = $this->validateJenjangCompatibility($validated['kelas_ids'], $validated['mata_pelajaran_id']);
        if (!$genreCheck['valid']) {
             return back()->withInput()->with('error', $genreCheck['message']);
        }

        // Validasi bentrok
        $conflicts = $this->checkConflicts(
            $validated['tahun_ajaran_id'],
            $validated['kelas_ids'],
            $validated['guru_id'],
            $validated['hari'],
            $validated['jam_mulai'],
            $validated['jam_selesai'],
            $validated['mata_pelajaran_id']
        );

        if ($conflicts['hasConflict']) {
            return back()->withInput()->with('error', $conflicts['message']);
        }

        $validated['siswa_ids'] = $validated['siswa_ids'] ?? null;
        $validated['status'] = $validated['guru_id'] ? 'aktif' : 'kosong';
        $validated['updated_by'] = Auth::id();

        // Backward compatibility for kelas_id (take first one)
        $validated['kelas_id'] = $validated['kelas_ids'][0] ?? null;

        $jadwal = JadwalPelajaran::create(\Illuminate\Support\Arr::except($validated, ['kelas_ids']));
        $jadwal->kelas()->attach($validated['kelas_ids']);

        // Auto-sync guru pengajar
        if ($validated['guru_id']) {
            $this->syncGuruPengajar($validated['guru_id'], $validated['kelas_ids'], $validated['mata_pelajaran_id']);
        }

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

        // Authorization: waka hanya boleh lihat jadwal kelas dari cabangnya
        if ($kelas->cabang_id != auth()->user()->cabang_id) {
            abort(403, 'Anda tidak berhak mengakses jadwal kelas ini.');
        }

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
            ->where('cabang_id', auth()->user()->cabang_id)
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
        // Multi-jenjang mode: delegate to trait
        if ($request->input('is_multi_jenjang')) {
            return $this->updateMultiJenjang($request, $jadwalPelajaran, 'waka');
        }

        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'nullable|exists:tenaga_pendidik,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        // Security: pastikan semua kelas yang dipilih milik cabang waka
        $userCabangId = auth()->user()->cabang_id;
        $invalidKelas = Kelas::whereIn('id', $validated['kelas_ids'])
            ->where('cabang_id', '!=', $userCabangId)
            ->exists();
        if ($invalidKelas) {
            return back()->withInput()->with('error', 'Kelas yang dipilih tidak sesuai dengan cabang Anda.');
        }

        // Validasi Jenjang Compatibility
        $genreCheck = $this->validateJenjangCompatibility($validated['kelas_ids'], $validated['mata_pelajaran_id']);
        if (!$genreCheck['valid']) {
             return back()->withInput()->with('error', $genreCheck['message']);
        }

        // Validasi bentrok (exclude current jadwal)
        $conflicts = $this->checkConflicts(
            $validated['tahun_ajaran_id'],
            $validated['kelas_ids'],
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

        $validated['status'] = $validated['guru_id'] ? 'aktif' : 'kosong';
        $validated['updated_by'] = Auth::id();

        // Backward compatibility for kelas_id
        $validated['kelas_id'] = $validated['kelas_ids'][0] ?? $jadwalPelajaran->kelas_id;

        $jadwalPelajaran->update(\Illuminate\Support\Arr::except($validated, ['kelas_ids']));
        $jadwalPelajaran->kelas()->sync($validated['kelas_ids']);

        // Auto-sync guru pengajar
        if ($validated['guru_id']) {
            $this->syncGuruPengajar($validated['guru_id'], $validated['kelas_ids'], $validated['mata_pelajaran_id']);
        }

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
        $guruId = $jadwalPelajaran->guru_id;
        $kelasIds = $jadwalPelajaran->kelas->pluck('id')->toArray();
        $mapelId = $jadwalPelajaran->mata_pelajaran_id;

        // Authorization: waka hanya boleh hapus jadwal milik cabangnya
        $belongsToUserCabang = $jadwalPelajaran->kelas->contains('cabang_id', auth()->user()->cabang_id);
        if (!$belongsToUserCabang) {
            abort(403, 'Anda tidak berhak menghapus jadwal ini.');
        }

        $jadwalPelajaran->kelas()->detach();
        $jadwalPelajaran->delete();

        // Cleanup guru_pengajar_kelas if no other jadwal references this combo
        if ($guruId) {
            $this->cleanupGuruPengajar($guruId, $kelasIds, $mapelId);
        }

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
        $oldGuruId = $jadwalPelajaran->guru_id;
        $guruBaru = $validated['guru_id_baru'] ? TenagaPendidik::find($validated['guru_id_baru']) : null;

        // Check conflict untuk guru baru
        if ($guruBaru) {
            $conflicts = $this->checkConflicts(
                $jadwalPelajaran->tahun_ajaran_id,
                $jadwalPelajaran->kelas->pluck('id')->toArray(),
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

        // Cleanup old guru assignment if no other jadwal references it
        if ($oldGuruId) {
            $this->cleanupGuruPengajar(
                $oldGuruId,
                $jadwalPelajaran->kelas->pluck('id')->toArray(),
                $jadwalPelajaran->mata_pelajaran_id
            );
        }

        // Auto-sync guru pengajar baru
        if ($validated['guru_id_baru']) {
            $this->syncGuruPengajar(
                $validated['guru_id_baru'],
                $jadwalPelajaran->kelas->pluck('id')->toArray(),
                $jadwalPelajaran->mata_pelajaran_id
            );
        }

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
                    $jadwal->kelas->pluck('id')->toArray(),
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
        // Handle multiple classes (comma separated)
        $kelasIds = explode(',', $kelasId);
        
        $students = \App\Models\Siswa::whereIn('kelas_id', $kelasIds)
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

    // checkConflicts, validateJenjangCompatibility, getFilteredSiswaIds, syncGuruPengajar
    // are provided by JadwalPelajaranTrait

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
            $import = new JadwalPelajaranImport($request->tahun_ajaran_id);
            Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $missingKelas = $import->getMissingKelas();
            $missingMapel = $import->getMissingMapel();
            $missingGuru = $import->getMissingGuru();

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
        return Excel::download(
            new JadwalPelajaranTemplate(),
            'template_jadwal_pelajaran.xlsx'
        );
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
        // $tahunAjaranBaru = TahunAjaran::findOrFail($validated['tahun_ajaran_id_baru']);

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
                
                // Assuming logic to find equivalent class in new year exists or is handled
                // Implementation simplified for brevity, copying robust logic from Admin
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
     * Export all jadwal to PDF.
     */
    public function exportPdfAll(Request $request)
    {
        // Get filter parameters
        $tahunAjaranId = $request->tahun_ajaran_id;
        $cabangId = auth()->user()->cabang_id; // Always use user's assigned cabang
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
     * Export jadwal to Excel.
     */
    public function exportExcel(Request $request)
    {
        // Reuse exportPdfAll logic for data
        // For brevity calling same logic or duplicating
        // Copying simplified logic
        
        $tahunAjaranId = $request->tahun_ajaran_id;
        $cabangId = auth()->user()->cabang_id; // Always use user's assigned cabang
        $jenjang = $request->jenjang;
        $kelasId = $request->kelas_id;
        $guruId = $request->guru_id;

        $query = JadwalPelajaran::with(['kelas.cabang', 'mataPelajaran', 'guru', 'tahunAjaran']);

        if ($tahunAjaranId) $query->byTahunAjaran($tahunAjaranId);
        if ($cabangId) $query->whereHas('kelas', fn($q) => $q->where('cabang_id', $cabangId));
        if ($jenjang) $query->whereHas('kelas', fn($q) => $q->where('jenjang', $jenjang));
        if ($kelasId) $query->byKelas($kelasId);
        if ($guruId) $query->byGuru($guruId);

        $jadwalList = $query->get()->sortBy(function ($jadwal) {
            $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            return [array_search($jadwal->hari, $hariOrder), $jadwal->jam_mulai];
        });

        $tahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : TahunAjaran::where('is_active', true)->first();
        $pengaturanIstirahat = PengaturanIstirahat::where('is_active', true)->orderBy('jenjang')->orderBy('jam_mulai')->get();

        $filterInfo = [
            'cabang' => $cabangId ? \App\Models\Cabang::find($cabangId)->nama_cabang : null,
            'jenjang' => $jenjang,
            'kelas' => $kelasId ? Kelas::find($kelasId)->nama_kelas : null,
            'guru' => $guruId ? TenagaPendidik::find($guruId)->nama_lengkap : null,
        ];

        $filename = 'Jadwal_Pelajaran_' . date('Ymd_His') . '.xls';

        return response(view('waka.jadwal-pelajaran.export-excel', compact('jadwalList', 'tahunAjaran', 'filterInfo', 'pengaturanIstirahat')))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Preview Print for specific class.
     */
    public function previewPrint($kelasId)
    {
        return $this->exportPdf(request(), $kelasId, true);
    }

    /**
     * Export/Print specific class schedule (PDF view).
     */
    public function exportPdf(Request $request, $kelasId, $preview = false)
    {
        $kelas = Kelas::with(['cabang', 'waliKelas', 'tahunAjaran'])->findOrFail($kelasId);
        $tahunAjaranId = $request->tahun_ajaran_id ?? $kelas->tahun_ajaran_id;
        
        $currentTahunAjaran = TahunAjaran::find($tahunAjaranId);

        $jadwalList = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->byTahunAjaran($tahunAjaranId)
            ->byKelas($kelasId)
            ->get();

        $pengaturanIstirahat = PengaturanIstirahat::where('jenjang', $kelas->jenjang)
            ->where('is_active', true)
            ->get();

        $scheduleGrid = $this->buildScheduleGrid($jadwalList, $pengaturanIstirahat);

        return view('waka.jadwal-pelajaran.print', compact('kelas', 'scheduleGrid', 'currentTahunAjaran', 'preview'));
    }

    /**
     * Export specific class schedule to Excel.
     */
    public function exportExcelClass(Request $request, $kelasId)
    {
        $kelas = Kelas::with(['cabang', 'waliKelas', 'tahunAjaran'])->findOrFail($kelasId);
        $tahunAjaranId = $request->tahun_ajaran_id ?? $kelas->tahun_ajaran_id;
        $currentTahunAjaran = TahunAjaran::find($tahunAjaranId);

        $jadwalList = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->byTahunAjaran($tahunAjaranId)
            ->byKelas($kelasId)
            ->get();

        $pengaturanIstirahat = PengaturanIstirahat::where('jenjang', $kelas->jenjang)
            ->where('is_active', true)
            ->get();

        $scheduleGrid = $this->buildScheduleGrid($jadwalList, $pengaturanIstirahat);
        $filename = 'Jadwal_Kelas_' . $kelas->nama_kelas . '_' . date('Ymd') . '.xls';

        return response(view('waka.jadwal-pelajaran.export-excel-class', compact('kelas', 'scheduleGrid', 'currentTahunAjaran')))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Helper to build grid for schedule view/print.
     */
    private function buildScheduleGrid($jadwalList, $istirahatList)
    {
        // 1. Collect all unique Start Times
        $startTimes = collect();
        
        foreach ($jadwalList as $jadwal) {
            $startTimes->push($jadwal->jam_mulai->format('H:i'));
        }
        
        foreach ($istirahatList as $ist) {
            $startTimes->push(substr($ist->jam_mulai, 0, 5));
        }

        $gridRows = $startTimes->unique()->sort()->values();

        // 2. Build the Grid structure
        $grid = [];
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        if ($jadwalList->where('hari', 'Sabtu')->count() > 0) {
            $hariList[] = 'Sabtu';
        }

        foreach ($gridRows as $index => $time) {
            $grid[$index] = [
                'time_start' => $time,
                'days' => []
            ];
            foreach ($hariList as $hari) {
                $grid[$index]['days'][$hari] = ['type' => 'empty'];
            }
        }

        $getGridIndex = function($time) use ($gridRows) {
            return $gridRows->search($time);
        };

        // 3a. Place Lessons
        foreach ($jadwalList as $jadwal) {
            $startTime = $jadwal->jam_mulai->format('H:i');
            $endTime = $jadwal->jam_selesai->format('H:i');
            $day = $jadwal->hari;
            
            if (!in_array($day, $hariList)) continue;

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

            if (isset($grid[$startIndex]['days'][$day]['type']) && $grid[$startIndex]['days'][$day]['type'] == 'taken') {
                 // Check if existing is lesson, append
                 $existing = $grid[$startIndex]['days'][$day];
                 if (isset($existing['type']) && $existing['type'] == 'lesson') {
                     $grid[$startIndex]['days'][$day]['data'][] = $jadwal;
                 } else {
                     // Overwrite or weird state, just set
                     $grid[$startIndex]['days'][$day] = ['type' => 'lesson', 'rowspan' => $span, 'data' => [$jadwal]];
                     for ($r = 1; $r < $span; $r++) {
                         if (isset($grid[$startIndex + $r])) $grid[$startIndex + $r]['days'][$day] = ['type' => 'taken'];
                     }
                 }
            } else if ($grid[$startIndex]['days'][$day]['type'] == 'empty') {
                 $grid[$startIndex]['days'][$day] = ['type' => 'lesson', 'rowspan' => $span, 'data' => [$jadwal]];
                 for ($r = 1; $r < $span; $r++) {
                     if (isset($grid[$startIndex + $r])) $grid[$startIndex + $r]['days'][$day] = ['type' => 'taken'];
                 }
            }
        }

        // 3b. Place Breaks
        foreach ($istirahatList as $ist) {
            $startTime = substr($ist->jam_mulai, 0, 5);
            $endTime = substr($ist->jam_selesai, 0, 5);
            $targetDays = is_array($ist->hari_aktif) ? $ist->hari_aktif : json_decode($ist->hari_aktif, true);
            
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
                
                $grid[$startIndex]['days'][$day] = [
                    'type' => 'break',
                    'rowspan' => $span,
                    'data' => $ist
                ];

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
}

