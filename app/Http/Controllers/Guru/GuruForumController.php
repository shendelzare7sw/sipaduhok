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
use App\Models\ForumDiskusi;
use App\Models\ForumReply;
use App\Models\Siswa;
use Illuminate\Support\Facades\Storage;
// use App\Services\NotificationService;

class GuruForumController extends Controller
{
    // private $notificationService;

    // public function __construct(NotificationService $notificationService)
    // {
    //     $this->notificationService = $notificationService;
    // }

    /**
     * Tampilkan daftar diskusi forum
     */
    public function index($kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $forums = ForumDiskusi::with(['user', 'replies'])
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('guru.lms.forum.index', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'forums' => $forums,
            'guru' => $tenagaPendidik,
        ]);
    }

    /**
     * Form tambah diskusi baru
     */
    public function create(Request $request, $kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $kelasLain = GuruPengajarKelas::where('tenaga_pendidik_id', $tenagaPendidik->id)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('kelas_id', '!=', $kelasId)
            ->with('kelas')
            ->get();

        return view('guru.lms.forum.create', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'kelasLain' => $kelasLain,
        ]);
    }

    /**
     * Simpan diskusi baru
     */
    public function store(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'is_pinned' => 'nullable|boolean',
            'lampiran' => 'nullable|array',
            'lampiran.*' => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,avi,mov|max:10240',
        ]);

        $lampiranPaths = [];
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $lampiranPaths[] = $file->store('forum-attachments', 'public');
            }
        }

        $forumData = [
            'mata_pelajaran_id' => $mapelId,
            'user_id' => auth()->id(),
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'is_pinned' => $request->has('is_pinned'),
            'is_closed' => false,
            'lampiran' => !empty($lampiranPaths) ? $lampiranPaths : null,
        ];

        // Buat untuk kelas utama
        ForumDiskusi::create(array_merge($forumData, ['kelas_id' => $kelasId]));

        // Duplikasi ke kelas tambahan
        $kelasTambahan = $request->input('kelas_tambahan', []);
        $jumlahDuplikasi = 0;
        foreach ($kelasTambahan as $kelasLainId) {
            if ($this->hasAccess($tenagaPendidik->id, $kelasLainId, $mapelId)) {
                ForumDiskusi::create(array_merge($forumData, ['kelas_id' => $kelasLainId]));
                $jumlahDuplikasi++;
            }
        }

        $msg = 'Diskusi baru berhasil dibuat';
        if ($jumlahDuplikasi > 0) {
            $msg .= " dan diduplikasi ke {$jumlahDuplikasi} kelas lain";
        }

        return redirect()
            ->route('guru.lms.forum.index', [$kelasId, $mapelId])
            ->with('success', $msg);
    }

    /**
     * Tampilkan detail diskusi
     */
    public function show($kelasId, $mapelId, $forumId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $forum = ForumDiskusi::with(['user', 'replies.user', 'replies.children.user'])
            ->where('id', $forumId)
            ->firstOrFail();

        return view('guru.lms.forum.show', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'forum' => $forum,
            'guru' => $tenagaPendidik,
        ]);
    }

    /**
     * Simpan balasan dari guru
     */
    public function reply(Request $request, $kelasId, $mapelId, $forumId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $request->validate([
            'isi' => 'required|string',
            'parent_id' => 'nullable|exists:forum_replies,id',
            'attachment' => 'nullable|array',
            'attachment.*' => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,avi,mov|max:10240',
        ]);

        $forum = ForumDiskusi::findOrFail($forumId);

        if ($forum->is_closed) {
            return back()->with('error', 'Diskusi ini sudah ditutup.');
        }

        // Process multiple attachments
        $attachmentPaths = [];
        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $attachmentPaths[] = $file->store('forum-attachments', 'public');
            }
        }

        $reply = ForumReply::create([
            'forum_diskusi_id' => $forumId,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'isi' => $request->isi,
            'attachment' => !empty($attachmentPaths) ? $attachmentPaths : null,
        ]);

        // Notification logic placeholder

        return back()->with('success', 'Balasan berhasil dikirim');
    }

    /**
     * Update balasan guru
     */
    public function updateReply(Request $request, $kelasId, $mapelId, $forumId, $replyId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $request->validate([
            'isi' => 'required|string',
            'attachment' => 'nullable|array',
            'attachment.*' => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,avi,mov|max:10240',
        ]);

        $reply = ForumReply::where('id', $replyId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Process new attachments
        $newAttachmentPaths = [];
        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $newAttachmentPaths[] = $file->store('forum-attachments', 'public');
            }
        }

        // Merge with existing attachments
        $existingAttachments = $reply->attachment ?? [];
        if (!is_array($existingAttachments)) {
            $existingAttachments = [];
        }
        $allAttachments = array_merge($existingAttachments, $newAttachmentPaths);

        $reply->update([
            'isi' => $request->isi,
            'attachment' => !empty($allAttachments) ? $allAttachments : null,
        ]);

        return back()->with('success', 'Balasan berhasil diperbarui');
    }

    /**
     * Hapus balasan
     */
    public function destroyReply($kelasId, $mapelId, $forumId, $replyId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $reply = ForumReply::findOrFail($replyId);

        // Allow teacher to delete any reply in their class
        $reply->delete();

        return back()->with('success', 'Balasan berhasil dihapus');
    }

    /**
     * Toggle status pinned
     */
    public function togglePin(Request $request, $kelasId, $mapelId, $forumId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $forum = ForumDiskusi::findOrFail($forumId);
        $newState = !$forum->is_pinned;
        $forum->update(['is_pinned' => $newState]);

        $syncedCount = 0;
        if ($request->has('sync_kelas') && $request->sync_kelas == '1') {
            $relatedForums = ForumDiskusi::where('user_id', auth()->id())
                ->where('mata_pelajaran_id', $mapelId)
                ->where('judul', $forum->judul)
                ->where('id', '!=', $forum->id)
                ->get();

            foreach ($relatedForums as $rel) {
                // Ensure the teacher has access to the related class (safety check)
                if ($this->hasAccess($tenagaPendidik->id, $rel->kelas_id, $mapelId)) {
                    $rel->update(['is_pinned' => $newState]);
                    $syncedCount++;
                }
            }
        }

        $msg = 'Status pin berhasil diubah';
        if ($syncedCount > 0) {
            $msg .= " (Disinkronisasi ke $syncedCount kelas lain)";
        }

        return back()->with('success', $msg);
    }

    /**
     * Toggle status closed
     */
    public function toggleClose(Request $request, $kelasId, $mapelId, $forumId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $forum = ForumDiskusi::findOrFail($forumId);
        $newState = !$forum->is_closed;
        $forum->update(['is_closed' => $newState]);

        $syncedCount = 0;
        if ($request->has('sync_kelas') && $request->sync_kelas == '1') {
             $relatedForums = ForumDiskusi::where('user_id', auth()->id())
                ->where('mata_pelajaran_id', $mapelId)
                ->where('judul', $forum->judul)
                ->where('id', '!=', $forum->id)
                ->get();

            foreach ($relatedForums as $rel) {
                 if ($this->hasAccess($tenagaPendidik->id, $rel->kelas_id, $mapelId)) {
                    $rel->update(['is_closed' => $newState]);
                    $syncedCount++;
                 }
            }
        }

        $msg = 'Status diskusi berhasil diubah';
        if ($syncedCount > 0) {
            $msg .= " (Disinkronisasi ke $syncedCount kelas lain)";
        }

        return back()->with('success', $msg);
    }

    /**
     * Hapus diskusi
     */
    public function destroy(Request $request, $kelasId, $mapelId, $forumId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $forum = ForumDiskusi::where('id', $forumId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $idsToDelete = [$forum->id];

        // BULK DELETE LOGIC
        if ($request->has('hapus_terkait')) {
            $relatedForums = ForumDiskusi::where('user_id', auth()->id())
                ->where('mata_pelajaran_id', $mapelId)
                ->where('judul', $forum->judul)
                ->where('id', '!=', $forum->id)
                ->get();

            foreach ($relatedForums as $rel) {
                $idsToDelete[] = $rel->id;
            }
        }

        // Process Deletion with safe lampiran delete
        $filesToDelete = [];
        $forumsToDelete = ForumDiskusi::whereIn('id', $idsToDelete)->get();

        foreach ($forumsToDelete as $f) {
            if ($f->lampiran && is_array($f->lampiran)) {
                foreach ($f->lampiran as $file) {
                    // Check if lampiran used by forum NOT being deleted
                    $usedElsewhere = ForumDiskusi::whereNotIn('id', $idsToDelete)
                        ->whereJsonContains('lampiran', $file)
                        ->exists();

                    if (!$usedElsewhere) {
                        $filesToDelete[] = $file;
                    }
                }
            }
            $f->delete();
        }

        // Delete physical files
        $filesToDelete = array_unique($filesToDelete);
        foreach ($filesToDelete as $file) {
            Storage::disk('public')->delete($file);
        }

        $msg = 'Diskusi berhasil dihapus';
        if (count($idsToDelete) > 1) {
            $countLain = count($idsToDelete) - 1;
            $msg .= " (termasuk {$countLain} diskusi terkait di kelas lain)";
        }

        return redirect()
            ->route('guru.lms.forum.index', [$kelasId, $mapelId])
            ->with('success', $msg);
    }

    /**
     * Verifikasi akses guru
     */
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
