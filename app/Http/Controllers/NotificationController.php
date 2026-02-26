<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display all notifications
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id());

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                    ->orWhere('pesan', 'like', '%' . $search . '%');
            });
        }

        // Read/unread filter
        if ($request->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($request->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();
        $unreadCount = Notification::where('user_id', Auth::id())->whereNull('read_at')->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Show/redirect a single notification - marks as read and navigates to target
     * Notifications are alerts, not messages; always redirect to the linked menu.
     */
    public function show($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->markAsRead();

        // Always redirect to the linked menu page if available
        if ($notification->link) {
            return redirect($this->resolveNotificationLink($notification->link));
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Bulk actions (delete, mark read, mark unread)
     */
    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada item dipilih']);
        }

        $query = Notification::where('user_id', Auth::id())->whereIn('id', $ids);
        $count = count($ids);

        switch ($action) {
            case 'delete':
                $query->delete();
                $message = $count . ' notifikasi dihapus';
                break;
            case 'read':
                $query->update(['read_at' => now()]);
                $message = $count . ' notifikasi ditandai dibaca';
                break;
            case 'unread':
                $query->update(['read_at' => null]);
                $message = $count . ' notifikasi ditandai belum dibaca';
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Aksi tidak dikenal']);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    /**
     * Get recent UNREAD notifications (for dropdown bell)
     */
    public function recent()
    {
        $notifications = $this->notificationService->getRecent(Auth::id(), 10);
        $unreadCount = $this->notificationService->getUnreadCount(Auth::id());

        // Add human-readable timestamp for display in bell dropdown
        $notifications = $notifications->map(function ($notif) {
            $notif->created_at_formatted = $notif->created_at->diffForHumans();
            return $notif;
        });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Get unread count (for badge)
     */
    public function unreadCount()
    {
        return response()->json([
            'count' => $this->notificationService->getUnreadCount(Auth::id()),
        ]);
    }

    /**
     * Get today's notifications
     */
    public function today()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->today()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Mark single notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        // Redirect to notification link if exists
        if ($notification->link) {
            return redirect($this->resolveNotificationLink($notification->link));
        }

        return back();
    }

    /**
     * Normalize a notification link to a relative path.
     * Strips the domain so old links stored with the wrong domain (e.g. sipaduhok.test)
     * still work correctly in production.
     */
    private function resolveNotificationLink(string $link): string
    {
        // If it's an absolute URL, extract only the path + query
        if (filter_var($link, FILTER_VALIDATE_URL)) {
            $path  = parse_url($link, PHP_URL_PATH) ?? '/';
            $query = parse_url($link, PHP_URL_QUERY);
            return $path . ($query ? '?' . $query : '');
        }

        // Already a relative path — return as-is
        return $link;
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(Auth::id());

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Filter notifications by type
     */
    public function byType($tipe)
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->where('tipe', $tipe)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications', 'tipe'));
    }
}
