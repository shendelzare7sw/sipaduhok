<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\TenagaPendidik;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\LmsMeeting;

class GuruLmsMeetingController extends Controller
{
    public function index($kelas, $mapel): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelas, $mapel);

        $kelasModel = Kelas::findOrFail($kelas);
        $mataPelajaran = MataPelajaran::findOrFail($mapel);

        $meetings = LmsMeeting::where('kelas_id', $kelas)
            ->where('mata_pelajaran_id', $mapel)
            ->where('guru_id', $tenagaPendidik->id)
            ->orderBy('waktu_mulai', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('guru.lms.meeting.index', [
            'kelas' => $kelasModel,
            'mapel' => $mataPelajaran,
            'meetings' => $meetings,
            'guru' => $tenagaPendidik,
        ]);
    }

    public function create($kelas, $mapel): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelas, $mapel);

        $kelasModel = Kelas::findOrFail($kelas);
        $mataPelajaran = MataPelajaran::findOrFail($mapel);

        $kelasLain = GuruPengajarKelas::where('tenaga_pendidik_id', $tenagaPendidik->id)
            ->where('mata_pelajaran_id', $mapel)
            ->where('kelas_id', '!=', $kelas)
            ->with('kelas')
            ->get();

        return view('guru.lms.meeting.create', [
            'kelas' => $kelasModel,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'kelasLain' => $kelasLain,
        ]);
    }

    public function store(Request $request, $kelas, $mapel): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelas, $mapel);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'platform' => 'required|in:zoom,google_meet,lainnya',
            'link_meeting' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'nullable|date|after:waktu_mulai',
            'deskripsi' => 'nullable|string',
        ]);

        $meetingData = [
            'mata_pelajaran_id' => $mapel,
            'guru_id' => $tenagaPendidik->id,
            'judul' => $validated['judul'],
            'platform' => $validated['platform'],
            'link_meeting' => $validated['link_meeting'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'deskripsi' => $validated['deskripsi'],
        ];

        $notif = app(\App\Services\NotificationService::class);

        // Buat untuk kelas utama + notif siswa sebagai pengingat
        $meetingUtama = LmsMeeting::create(array_merge($meetingData, ['kelas_id' => $kelas]));
        $notif->notifyKelasVirtualBaru($meetingUtama);

        // Duplikasi ke kelas tambahan
        $kelasTambahan = $request->input('kelas_tambahan', []);
        $jumlahDuplikasi = 0;
        foreach ($kelasTambahan as $kelasLainId) {
            if ($this->hasAccess($tenagaPendidik->id, $kelasLainId, $mapel)) {
                $meetingLain = LmsMeeting::create(array_merge($meetingData, ['kelas_id' => $kelasLainId]));
                $notif->notifyKelasVirtualBaru($meetingLain);
                $jumlahDuplikasi++;
            }
        }

        $msg = 'Meeting berhasil dijadwalkan';
        if ($jumlahDuplikasi > 0) {
            $msg .= " dan diduplikasi ke {$jumlahDuplikasi} kelas lain";
        }

        return redirect()
            ->route('guru.lms.meeting.index', [$kelas, $mapel])
            ->with('success', $msg);
    }

    public function edit($kelas, $mapel, $meeting): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelas, $mapel);

        $meetingModel = LmsMeeting::where('id', $meeting)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $kelasModel = Kelas::findOrFail($kelas);
        $mataPelajaran = MataPelajaran::findOrFail($mapel);

        $kelasLain = GuruPengajarKelas::where('tenaga_pendidik_id', $tenagaPendidik->id)
            ->where('mata_pelajaran_id', $mapel)
            ->where('kelas_id', '!=', $kelas)
            ->with('kelas')
            ->get();

        return view('guru.lms.meeting.edit', [
            'kelas' => $kelasModel,
            'mapel' => $mataPelajaran,
            'meeting' => $meetingModel,
            'guru' => $tenagaPendidik,
            'kelasLain' => $kelasLain,
            'relatedClassIds' => LmsMeeting::where('guru_id', $tenagaPendidik->id)
                ->where('mata_pelajaran_id', $mapel)
                ->where('judul', $meetingModel->judul)
                ->where('id', '!=', $meetingModel->id)
                ->pluck('kelas_id')
                ->toArray(),
        ]);
    }

    public function update(Request $request, $kelas, $mapel, $meeting): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelas, $mapel);

        $meetingModel = LmsMeeting::where('id', $meeting)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        // Capture original state for matching in other classes
        $originalTitle = $meetingModel->judul;

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'platform' => 'required|in:zoom,google_meet,lainnya',
            'link_meeting' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'nullable|date|after:waktu_mulai',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $meetingModel->update($validated);

        // SYNC LOGIC (Update or Create to linked classes)
        $kelasTambahan = $request->input('kelas_tambahan', []);
        $jumlahDuplikasi = 0;
        $jumlahUpdate = 0;

        if (!empty($kelasTambahan)) {
            $syncData = [
                'mata_pelajaran_id' => $mapel,
                'guru_id' => $tenagaPendidik->id,
                'judul' => $meetingModel->judul,
                'platform' => $meetingModel->platform,
                'link_meeting' => $meetingModel->link_meeting,
                'waktu_mulai' => $meetingModel->waktu_mulai,
                'waktu_selesai' => $meetingModel->waktu_selesai,
                'deskripsi' => $meetingModel->deskripsi,
            ];

            foreach ($kelasTambahan as $kelasLainId) {
                if ($this->hasAccess($tenagaPendidik->id, $kelasLainId, $mapel)) {
                    $existing = LmsMeeting::where('kelas_id', $kelasLainId)
                        ->where('guru_id', $tenagaPendidik->id)
                        ->where('mata_pelajaran_id', $mapel)
                        ->where('judul', $originalTitle)
                        ->first();

                    if ($existing) {
                        $existing->update($syncData);
                        $jumlahUpdate++;
                    } else {
                        LmsMeeting::create(array_merge($syncData, ['kelas_id' => $kelasLainId]));
                        $jumlahDuplikasi++;
                    }
                }
            }
        }

        $msg = 'Meeting berhasil diperbarui';
        if ($jumlahDuplikasi > 0 || $jumlahUpdate > 0) {
            $msg .= " (Disinkronisasi ke " . ($jumlahDuplikasi + $jumlahUpdate) . " kelas lain)";
        }

        return redirect()
            ->route('guru.lms.meeting.index', [$kelas, $mapel])
            ->with('success', $msg);
    }

    public function destroy(Request $request, $kelas, $mapel, $meeting): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelas, $mapel);

        $meetingModel = LmsMeeting::where('id', $meeting)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $idsToDelete = [$meetingModel->id];

        // BULK DELETE LOGIC
        if ($request->has('hapus_terkait')) {
            $relatedMeetings = LmsMeeting::where('guru_id', $tenagaPendidik->id)
                ->where('mata_pelajaran_id', $mapel)
                ->where('judul', $meetingModel->judul)
                ->where('id', '!=', $meetingModel->id)
                ->get();

            foreach ($relatedMeetings as $rel) {
                $idsToDelete[] = $rel->id;
            }
        }

        LmsMeeting::whereIn('id', $idsToDelete)->delete();

        $msg = 'Meeting berhasil dihapus';
        if (count($idsToDelete) > 1) {
            $countLain = count($idsToDelete) - 1;
            $msg .= " (termasuk {$countLain} meeting terkait di kelas lain)";
        }

        return redirect()
            ->route('guru.lms.meeting.index', [$kelas, $mapel])
            ->with('success', $msg);
    }

    private function verifyAccess($guruId, $kelasId, $mapelId)
    {
        if (!$this->hasAccess($guruId, $kelasId, $mapelId)) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini');
        }
    }

    private function hasAccess($guruId, $kelasId, $mapelId): bool
    {
        return GuruPengajarKelas::where('tenaga_pendidik_id', $guruId)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->exists();
    }
}
