<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\ForumDiskusi;
use App\Models\ForumReply;
use App\Services\NotificationService;

class LmsForumController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display forum untuk mata pelajaran
     */
    public function index($mapelId)
    {
        $siswa = Siswa::where('user_id', Auth::id())->with('kelas')->first();
        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $diskusi = ForumDiskusi::where('mata_pelajaran_id', $mapelId)
            ->where('kelas_id', $siswa->kelas_id)
            ->with([
                'user',
                'replies' => function ($q) {
                    $q->orderBy('created_at', 'desc')->take(3);
                }
            ])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('siswa.lms.mata-pelajaran.forum.index', compact(
            'siswa',
            'mataPelajaran',
            'diskusi'
        ));
    }

    /**
     * Show form to create new diskusi
     */
    /**
     * Show form to create new diskusi
     */
    public function create($mapelId)
    {
        return redirect()->back()->with('error', 'Hanya guru yang dapat membuat topik diskusi baru.');
    }

    /**
     * Store new diskusi
     */
    public function store(Request $request, $mapelId)
    {
        return redirect()->back()->with('error', 'Hanya guru yang dapat membuat topik diskusi baru.');
    }

    /**
     * Show single diskusi with replies
     */
    public function show($mapelId, $diskusiId)
    {
        $siswa = Siswa::where('user_id', Auth::id())->with('kelas')->first();
        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $diskusi = ForumDiskusi::where('id', $diskusiId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('kelas_id', $siswa->kelas_id)
            ->with(['user', 'replies.user', 'replies.children.user'])
            ->firstOrFail();

        // Get all replies with nested children
        $replies = ForumReply::where('forum_diskusi_id', $diskusiId)
            ->whereNull('parent_id')
            ->with(['user', 'children.user'])
            ->orderBy('is_answer', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('siswa.lms.mata-pelajaran.forum.show', compact(
            'siswa',
            'mataPelajaran',
            'diskusi',
            'replies'
        ));
    }

    /**
     * Store reply to diskusi
     */
    public function reply(Request $request, $mapelId, $diskusiId)
    {
        $validated = $request->validate([
            'isi' => 'required|string|min:3',
            'parent_id' => 'nullable|exists:forum_replies,id',
            'attachment' => 'nullable|array',
            'attachment.*' => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,avi,mov|max:10240',
        ]);

        $siswa = Siswa::where('user_id', Auth::id())->first();
        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        $diskusi = ForumDiskusi::where('id', $diskusiId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('kelas_id', $siswa->kelas_id)
            ->firstOrFail();

        if ($diskusi->is_closed) {
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
            'forum_diskusi_id' => $diskusiId,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'isi' => $validated['isi'],
            'attachment' => !empty($attachmentPaths) ? $attachmentPaths : null,
        ]);

        // Notify if replying to someone else's post
        if ($diskusi->user_id !== Auth::id()) {
            $this->notificationService->notifyForumReply($reply);
        }

        return back()->with('success', 'Balasan berhasil diposting!');
    }

    /**
     * Update reply
     */
    public function updateReply(Request $request, $mapelId, $diskusiId, $replyId)
    {
        $validated = $request->validate([
            'isi' => 'required|string|min:3',
            'attachment' => 'nullable|array',
            'attachment.*' => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,avi,mov|max:10240',
        ]);

        $reply = ForumReply::where('id', $replyId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$reply->can_edit) {
            return back()->with('error', 'Waktu edit (1 jam) telah habis atau Anda tidak memiliki izin.');
        }

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
            'isi' => $validated['isi'],
            'attachment' => !empty($allAttachments) ? $allAttachments : null,
        ]);

        return back()->with('success', 'Balasan berhasil diperbarui');
    }

    /**
     * Delete reply
     */
    public function destroyReply($mapelId, $diskusiId, $replyId)
    {
        $reply = ForumReply::where('id', $replyId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$reply->can_edit) {
            return back()->with('error', 'Waktu hapus (1 jam) telah habis atau Anda tidak memiliki izin.');
        }

        $reply->delete();

        return back()->with('success', 'Balasan berhasil dihapus');
    }
}
