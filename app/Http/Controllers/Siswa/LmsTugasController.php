<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Tugas;
use App\Models\TugasSiswa;

class LmsTugasController extends Controller
{
    /**
     * Tampilkan detail tugas
     */
    public function show($mapelId, $tugasId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        $tugas = Tugas::where('id', $tugasId)
            ->where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->with(['mataPelajaran', 'guru'])
            ->firstOrFail();

        // Cek apakah sudah submit
        $tugasSiswa = TugasSiswa::where('tugas_id', $tugasId)
            ->where('siswa_id', $siswa->id)
            ->first();

        // Cek apakah sudah deadline
        $isDeadline = now()->gt($tugas->tanggal_deadline);

        // Cek apakah siswa berhak mengakses mapel ini (Filter Agama)
        if (!$siswa->canAccessMapel($tugas->mataPelajaran)) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Akses ditolak: Mata pelajaran ini tidak sesuai dengan agama Anda.');
        }

        $mataPelajaran = $tugas->mataPelajaran;
        $existingSubmission = $tugasSiswa;

        return view('siswa.lms.mata-pelajaran.tugas.show', compact(
            'siswa',
            'mataPelajaran',
            'tugas',
            'tugasSiswa',
            'existingSubmission',
            'tugas',
            'tugasSiswa',
            'existingSubmission',
            'isDeadline'
        ));
    }

    /**
     * Submit tugas
     */
    public function submit(Request $request, $mapelId, $tugasId)
    {
        $request->validate([
            'jawaban_text' => 'nullable|string',
            'file_jawaban' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,mp4|max:10240',
        ]);

        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan');
        }

        $tugas = Tugas::where('id', $tugasId)
            ->where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->firstOrFail();

        // Cek deadline
        if (now()->gt($tugas->tanggal_deadline)) {
            return back()->with('error', 'Waktu pengumpulan tugas sudah habis');
        }

        // Cek batas pengulangan jika tugas ini membatasi pengulangan
        $tugasSiswa = TugasSiswa::where('tugas_id', $tugasId)
            ->where('siswa_id', $siswa->id)
            ->first();

        if ($tugasSiswa && $tugas->bisa_diulang && $tugas->batas_pengulangan > 0) {
            if ($tugasSiswa->pengulangan_ke > $tugas->batas_pengulangan) {
                return back()->with('error', 'Anda sudah mencapai batas maksimal edit jawaban (' . $tugas->batas_pengulangan . ' kali).');
            }
        }

        // Upload file jika ada
        $filePath = $tugasSiswa ? $tugasSiswa->file_jawaban : null;
        if ($request->hasFile('file_jawaban')) {
            $filePath = $request->file('file_jawaban')->store('tugas/jawaban', 'public');
        }

        // Simpan atau update jawaban
        if ($tugasSiswa) {
            $tugasSiswa->update([
                'jawaban_text' => $request->jawaban_text,
                'file_jawaban' => $filePath,
                'tanggal_submit' => now(),
                'status' => now()->gt($tugas->tanggal_deadline) ? 'terlambat' : 'dikerjakan',
                'pengulangan_ke' => $tugasSiswa->pengulangan_ke + 1,
            ]);
        } else {
            $tugasSiswa = TugasSiswa::create([
                'tugas_id' => $tugasId,
                'siswa_id' => $siswa->id,
                'jawaban_text' => $request->jawaban_text,
                'file_jawaban' => $filePath,
                'tanggal_submit' => now(),
                'status' => now()->gt($tugas->tanggal_deadline) ? 'terlambat' : 'dikerjakan',
                'pengulangan_ke' => 1,
            ]);
        }

        // Notify guru about tugas submission
        $tugasSiswa->load(['tugas', 'siswa']);
        app(\App\Services\NotificationService::class)->notifyTugasDikumpulkan($tugasSiswa);

        return back()->with('success', 'Tugas berhasil dikumpulkan!');
    }
    /**
     * Tampilkan semua tugas (Lists)
     */
    public function indexAll(Request $request)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Get mata pelajaran for filter (from Jadwal Pelajaran)
        $mataPelajaranList = \App\Models\JadwalPelajaran::whereHas('kelas', function($q) use ($siswa) {
                $q->where('kelas.id', $siswa->kelas_id);
            })
            ->with('mataPelajaran')
            ->get()
            ->filter(fn($j) => $siswa->canAccessMapel($j->mataPelajaran))
            ->map(function ($jadwal) {
                return $jadwal->mataPelajaran;
            })
            ->filter() // Remove nulls
            ->unique('id')
            ->values();

        // Base query
        $query = Tugas::where('kelas_id', $siswa->kelas_id)
            ->with(['mataPelajaran', 'guru', 'tugasSiswa' => function($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id);
            }]);

        // Filter by Mapel
        if ($request->has('mapel') && $request->mapel != '') {
            $query->where('mata_pelajaran_id', $request->mapel);
        }

        // Get all tasks to calculate stats (before status filter)
        // Filter collection by religion access
        $allTasks = (clone $query)->get()->filter(fn($t) => $siswa->canAccessMapel($t->mataPelajaran));

        $tugasBelum = 0;
        $tugasProses = 0;
        $tugasSelesai = 0;
        $tugasTerlambat = 0;

        foreach ($allTasks as $t) {
            $submission = $t->tugasSiswa->first();
            $status = $submission ? $submission->status : 'belum_dikerjakan';
            
            // Check urgency/late for stats
            if ($status == 'belum_dikerjakan') {
                if (now()->gt($t->tanggal_deadline)) {
                    $tugasTerlambat++; // Overdue but not submitted
                } else {
                    $tugasBelum++;
                }
            } elseif ($status == 'dikerjakan') {
                $tugasProses++;
            } elseif ($status == 'dinilai') {
                $tugasSelesai++;
            } elseif ($status == 'terlambat') {
                $tugasTerlambat++;
            }
        }

        // Filter by Status (Custom Logic because status is in relation or calculated)
        if ($request->has('status') && $request->status != '') {
            $statusFilter = $request->status;
            
            // We need to filter collection after retrieval or use whereHas for complex logic
            // Converting to collection filter for simplicity as dataset per filtered mapel shouldn't be huge for a single class
            // But efficient way is to use subqueries or simple post-filtering
        }

        // Filter main query results as well?
        // Pagination makes filtering collection hard. We should filter query if possible.
        // But mapel relation check is hard in SQL without JOINs and standardized naming.
        // Given dataset size per student, collection filter is safer for logic correctness.
        // However, $query->paginate() runs SQL.
        // We must perform filtering on retrieved items or modify query.
        
        // MODIFIED: Since we can't easily filter pagination SQL for dynamic text check,
        // we will fetch GET first then slice manually OR just accept that some pages might have gaps if we filter output.
        // Better approach: Filter $mataPelajaranList IDs first, then whereIn('mata_pelajaran_id', $allowedMapelIds).
        
        // Get Allowed Mapel IDs
        $allowedMapelIds = \App\Models\JadwalPelajaran::whereHas('kelas', function($q) use ($siswa) {
                $q->where('kelas.id', $siswa->kelas_id);
            })
            ->with('mataPelajaran')
            ->get()
            ->filter(fn($j) => $siswa->canAccessMapel($j->mataPelajaran))
            ->pluck('mata_pelajaran_id')
            ->unique()
            ->toArray();
            
        $query->whereIn('mata_pelajaran_id', $allowedMapelIds);
        
        $tugasList = $query->orderBy('tanggal_deadline', 'asc')->paginate(10);
        
        // Custom Collection Filter for Pagination (Workaround)
        // Ideally we should filter in SQL. Let's do a basic SQL filter where possible.
        // For 'belum_dikerjakan', it means no record in tugas_siswa OR record exists but status is 'belum_dikerjakan'
        
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'belum_dikerjakan') {
                $query->whereDoesntHave('tugasSiswa', function($q) use ($siswa) {
                    $q->where('siswa_id', $siswa->id);
                });
            } else {
                $query->whereHas('tugasSiswa', function($q) use ($siswa, $request) {
                    $q->where('siswa_id', $siswa->id)
                      ->where('status', $request->status);
                });
            }
            $tugasList = $query->orderBy('tanggal_deadline', 'asc')->paginate(10);
        }

        return view('siswa.lms.mata-pelajaran.tugas.index', compact(
            'siswa',
            'mataPelajaranList',
            'tugasList',
            'tugasBelum',
            'tugasProses',
            'tugasSelesai',
            'tugasTerlambat'
        ));
    }
}