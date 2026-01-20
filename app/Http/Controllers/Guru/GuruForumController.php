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

        $forums = ForumDiskusi::with(['siswa', 'replies'])
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
        $pertemuanId = $request->get('pertemuan_id');

        return view('guru.lms.forum.create', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'pertemuanId' => $pertemuanId,
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
            'content' => 'required|string',
            'is_pinned' => 'nullable|boolean',
            'pertemuan_id' => 'nullable|exists:pertemuans,id',
        ]);

        $forum = ForumDiskusi::create([
            'kelas_id' => $kelasId,
            'mata_pelajaran_id' => $mapelId,
            'pertemuan_id' => $validated['pertemuan_id'] ?? null,
            'user_id' => auth()->id(), // Guru as the creator
            'judul' => $validated['judul'],
            'content' => $validated['content'],
            'is_pinned' => $request->has('is_pinned'),
            'is_closed' => false,
        ]);

        if (!empty($validated['pertemuan_id'])) {
            return redirect()
                ->route('guru.lms.pertemuan.show', [$kelasId, $mapelId, $validated['pertemuan_id']])
                ->with('success', 'Diskusi berhasil ditambahkan ke pertemuan');
        }

        return redirect()
            ->route('guru.lms.forum.index', [$kelasId, $mapelId])
            ->with('success', 'Diskusi baru berhasil dibuat');
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

        $forum = ForumDiskusi::with(['siswa', 'replies.user', 'replies.replies.user'])
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
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:forum_replies,id'
        ]);

        $forum = ForumDiskusi::findOrFail($forumId);

        if ($forum->is_closed) {
            return back()->with('error', 'Diskusi ini sudah ditutup.');
        }

        $reply = ForumReply::create([
            'forum_diskusi_id' => $forumId,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'is_teacher_reply' => true
        ]);

        // Notification logic placeholder

        return back()->with('success', 'Balasan berhasil dikirim');
    }

    /**
     * Toggle status pinned
     */
    public function togglePin($kelasId, $mapelId, $forumId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $forum = ForumDiskusi::findOrFail($forumId);
        $forum->update(['is_pinned' => !$forum->is_pinned]);

        return back()->with('success', 'Status pin berhasil diubah');
    }

    /**
     * Toggle status closed
     */
    public function toggleClose($kelasId, $mapelId, $forumId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $forum = ForumDiskusi::findOrFail($forumId);
        $forum->update(['is_closed' => !$forum->is_closed]);

        return back()->with('success', 'Status diskusi berhasil diubah');
    }

    /**
     * Hapus diskusi
     */
    public function destroy($kelasId, $mapelId, $forumId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $forum = ForumDiskusi::findOrFail($forumId);
        $forum->delete();

        return redirect()
            ->route('guru.lms.forum.index', [$kelasId, $mapelId])
            ->with('success', 'Diskusi berhasil dihapus');
    }

    /**
     * Verifikasi akses guru
     */
    private function verifyAccess($guruId, $kelasId, $mapelId)
    {
        $access = GuruPengajarKelas::where('tenaga_pendidik_id', $guruId)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->exists();

        if (!$access) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini');
        }
    }
}