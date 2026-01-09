<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\TenagaPendidik;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalPelajaran::with(['kelas.tahunAjaran', 'tenagaPendidik', 'mataPelajaran']);

        // Filter by tahun ajaran
        if ($request->filled('tahun_ajaran_id')) {
            $query->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $request->tahun_ajaran_id));
        } else {
            $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
            if ($tahunAjaranAktif) {
                $query->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id));
            }
        }

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter by hari
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        // Filter by guru
        if ($request->filled('guru_id')) {
            $query->where('tenaga_pendidik_id', $request->guru_id);
        }

        $jadwalList = $query->orderBy('hari')
            ->orderBy('jam_mulai')
            ->paginate(20);

        // Data for filters
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $currentTahunAjaran = null;
        if ($request->filled('tahun_ajaran_id')) {
            $currentTahunAjaran = TahunAjaran::find($request->tahun_ajaran_id);
        } else {
            $currentTahunAjaran = TahunAjaran::where('is_active', true)->first();
        }

        $kelasList = Kelas::when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        $guruList = TenagaPendidik::whereHas('user', fn($q) => $q->whereIn('role', ['guru_pengajar', 'wali_kelas'])->where('is_active', true))
            ->orderBy('nama_lengkap')
            ->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $cabangList = \App\Models\Cabang::orderBy('nama_cabang')->get();

        // Statistics
        $totalKelas = Kelas::when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))->count();
        $kelasWithJadwal = JadwalPelajaran::when($currentTahunAjaran, fn($q) => $q->whereHas('kelas', fn($kq) => $kq->where('tahun_ajaran_id', $currentTahunAjaran->id)))
            ->distinct('kelas_id')
            ->count('kelas_id');

        $stats = [
            'totalJadwal' => JadwalPelajaran::when($currentTahunAjaran, fn($q) => $q->whereHas('kelas', fn($kq) => $kq->where('tahun_ajaran_id', $currentTahunAjaran->id)))->count(),
            'jadwalKosong' => $totalKelas - $kelasWithJadwal,
            'totalGuru' => TenagaPendidik::whereHas('user', fn($q) => $q->whereIn('role', ['guru_pengajar', 'wali_kelas']))->count(),
            'totalKelas' => $totalKelas,
        ];

        return view('waka.jadwal-pelajaran.index', compact(
            'jadwalList',
            'tahunAjarans',
            'currentTahunAjaran',
            'kelasList',
            'guruList',
            'hariList',
            'cabangList',
            'stats'
        ));
    }

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
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        $mataPelajaranList = MataPelajaran::orderBy('jenjang')->orderBy('nama_mapel')->get();

        $guruList = TenagaPendidik::whereHas('user', fn($q) => $q->whereIn('role', ['guru_pengajar', 'wali_kelas'])->where('is_active', true))
            ->orderBy('nama_lengkap')
            ->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('waka.jadwal-pelajaran.create', compact(
            'tahunAjarans',
            'currentTahunAjaran',
            'kelasList',
            'mataPelajaranList',
            'guruList',
            'hariList'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tenaga_pendidik_id' => 'required|exists:tenaga_pendidik,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        JadwalPelajaran::create($validated);

        return redirect()->route('waka.jadwal-pelajaran.index')
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan');
    }

    public function show(Request $request, $kelasId)
    {
        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        $kelas = Kelas::with('cabang', 'tahunAjaran', 'waliKelas')->findOrFail($kelasId);

        $jadwalList = JadwalPelajaran::with(['mataPelajaran', 'tenagaPendidik'])
            ->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->where('kelas_id', $kelasId)
            ->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalByHari = collect($hariList)->mapWithKeys(function($hari) use ($jadwalList) {
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

    public function edit(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->load(['kelas.tahunAjaran', 'mataPelajaran', 'tenagaPendidik']);

        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();

        $kelasList = Kelas::where('tahun_ajaran_id', $jadwalPelajaran->kelas->tahun_ajaran_id)
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        $mataPelajaranList = MataPelajaran::orderBy('jenjang')->orderBy('nama_mapel')->get();

        $guruList = TenagaPendidik::whereHas('user', fn($q) => $q->whereIn('role', ['guru_pengajar', 'wali_kelas'])->where('is_active', true))
            ->orderBy('nama_lengkap')
            ->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('waka.jadwal-pelajaran.edit', compact(
            'jadwalPelajaran',
            'tahunAjarans',
            'kelasList',
            'mataPelajaranList',
            'guruList',
            'hariList'
        ));
    }

    public function update(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tenaga_pendidik_id' => 'required|exists:tenaga_pendidik,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $jadwalPelajaran->update($validated);

        return redirect()->route('waka.jadwal-pelajaran.index')
            ->with('success', 'Jadwal pelajaran berhasil diperbarui');
    }

    public function destroy(JadwalPelajaran $jadwalPelajaran)
    {
        try {
            $jadwalPelajaran->delete();
            return redirect()->route('waka.jadwal-pelajaran.index')
                ->with('success', 'Jadwal pelajaran berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus jadwal pelajaran');
        }
    }

    public function print(Request $request)
    {
        $query = JadwalPelajaran::with(['kelas.tahunAjaran', 'tenagaPendidik', 'mataPelajaran']);

        // Apply same filters
        if ($request->filled('tahun_ajaran_id')) {
            $query->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $request->tahun_ajaran_id));
        } else {
            $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
            if ($tahunAjaranAktif) {
                $query->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id));
            }
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        if ($request->filled('guru_id')) {
            $query->where('tenaga_pendidik_id', $request->guru_id);
        }

        $jadwalList = $query->orderBy('hari')->orderBy('jam_mulai')->get();

        $currentTahunAjaran = null;
        if ($request->filled('tahun_ajaran_id')) {
            $currentTahunAjaran = TahunAjaran::find($request->tahun_ajaran_id);
        } else {
            $currentTahunAjaran = TahunAjaran::where('is_active', true)->first();
        }

        return view('waka.jadwal-pelajaran.print', compact('jadwalList', 'currentTahunAjaran'));
    }

    public function exportPdf(Request $request)
    {
        // Get filter parameters
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
            'tenagaPendidik',
            'kelas.tahunAjaran'
        ]);

        if ($tahunAjaranId) {
            $query->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId));
        } else {
            $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
            if ($tahunAjaranAktif) {
                $query->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id));
            }
        }

        if ($cabangId) {
            $query->whereHas('kelas', fn($q) => $q->where('cabang_id', $cabangId));
        }

        if ($jenjang) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $jenjang));
        }

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        if ($guruId) {
            $query->where('tenaga_pendidik_id', $guruId);
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

        return view('waka.jadwal-pelajaran.export-pdf', compact('jadwalList', 'tahunAjaran', 'filterInfo'));
    }

    public function exportExcel(Request $request)
    {
        // Get filter parameters
        $tahunAjaranId = $request->tahun_ajaran_id;
        $cabangId = $request->cabang_id;
        $jenjang = $request->jenjang;
        $kelasId = $request->kelas_id;
        $guruId = $request->guru_id;

        // Build query with all filters
        $query = JadwalPelajaran::with([
            'kelas.cabang',
            'mataPelajaran',
            'tenagaPendidik',
            'kelas.tahunAjaran'
        ]);

        if ($tahunAjaranId) {
            $query->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId));
        } else {
            $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
            if ($tahunAjaranAktif) {
                $query->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id));
            }
        }

        if ($cabangId) {
            $query->whereHas('kelas', fn($q) => $q->where('cabang_id', $cabangId));
        }

        if ($jenjang) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $jenjang));
        }

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        if ($guruId) {
            $query->where('tenaga_pendidik_id', $guruId);
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
        return response()->view('waka.jadwal-pelajaran.export-excel', compact('jadwalList', 'tahunAjaran', 'filterInfo'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

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

        $jadwalList = JadwalPelajaran::whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $validated['tahun_ajaran_id']))
            ->where('tenaga_pendidik_id', $validated['guru_id_lama'])
            ->get();

        if ($jadwalList->isEmpty()) {
            return back()->with('error', 'Tidak ada jadwal yang perlu diganti!');
        }

        \DB::beginTransaction();
        try {
            foreach ($jadwalList as $jadwal) {
                $jadwal->update([
                    'tenaga_pendidik_id' => $validated['guru_id_baru'],
                ]);
            }

            \DB::commit();

            $count = $jadwalList->count();
            $message = $guruBaru
                ? "Berhasil mengganti {$count} jadwal dari {$guruLama->nama_lengkap} ke {$guruBaru->nama_lengkap}"
                : "Berhasil mengosongkan {$count} jadwal dari {$guruLama->nama_lengkap}";

            return back()->with('success', $message);
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mengganti jadwal: ' . $e->getMessage());
        }
    }

    public function duplicate(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id_lama' => 'required|exists:tahun_ajaran,id',
            'tahun_ajaran_id_baru' => 'required|exists:tahun_ajaran,id',
        ]);

        $tahunAjaranLama = TahunAjaran::findOrFail($validated['tahun_ajaran_id_lama']);
        $tahunAjaranBaru = TahunAjaran::findOrFail($validated['tahun_ajaran_id_baru']);

        $jadwalLama = JadwalPelajaran::whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $validated['tahun_ajaran_id_lama']))->get();

        if ($jadwalLama->isEmpty()) {
            return back()->with('error', 'Tidak ada jadwal di tahun ajaran yang dipilih!');
        }

        \DB::beginTransaction();
        try {
            $duplicated = 0;
            $skipped = 0;

            foreach ($jadwalLama as $jadwal) {
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

                $exists = JadwalPelajaran::where('kelas_id', $kelasBaru->id)
                    ->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
                    ->where('hari', $jadwal->hari)
                    ->where('jam_mulai', $jadwal->jam_mulai)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                JadwalPelajaran::create([
                    'kelas_id' => $kelasBaru->id,
                    'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
                    'tenaga_pendidik_id' => $jadwal->tenaga_pendidik_id,
                    'hari' => $jadwal->hari,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                ]);

                $duplicated++;
            }

            \DB::commit();

            $message = "Berhasil menduplikasi {$duplicated} jadwal.";
            if ($skipped > 0) {
                $message .= " {$skipped} jadwal dilewati (kelas tidak ditemukan atau sudah ada).";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

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

            \DB::beginTransaction();

            $deleted = JadwalPelajaran::whereIn('id', $jadwalIds)->delete();

            \DB::commit();

            return back()->with('success', "Berhasil menghapus {$deleted} jadwal pelajaran");
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
