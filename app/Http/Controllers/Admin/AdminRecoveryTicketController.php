<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\RecoveryTicket;
use App\Services\EmailRecoveryService;
use Illuminate\Http\Request;

class AdminRecoveryTicketController extends Controller
{
    protected $emailService;

    public function __construct(EmailRecoveryService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index()
    {
        $tickets = RecoveryTicket::with('user')
            ->whereIn('status', ['pending_admin', 'processing', 'failed', 'sent'])
            ->orderByRaw("FIELD(status, 'pending_admin', 'failed', 'processing', 'sent')")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $adminWa = AppSetting::where('key', 'admin_wa_number')->value('value') ?? '';

        return view('admin.recovery-tickets.index', compact('tickets', 'adminWa'));
    }

    public function resend(Request $request, RecoveryTicket $ticket)
    {
        $user = $ticket->user;

        if (empty($user->personal_email)) {
            return back()->with('error', 'Gagal mengirim. User tidak memiliki Email Pribadi. Harap tambahkan Email Pribadi terlebih dahulu melalui halaman edit user.');
        }

        // Refresh token and expiration for password reset tickets
        if (in_array($ticket->tipe_recovery, ['lupa_password', 'lupa_keduanya'])) {
            $ticket->update([
                'token_reset' => \Illuminate\Support\Str::random(64),
                'expires_at'  => \Carbon\Carbon::now()->addHours(24),
            ]);
        }

        $isSent = $this->emailService->sendTicketRecovery($ticket, $user);

        if ($isSent) {
            $ticket->update(['status' => 'sent']);
            return back()->with('success', 'Berhasil! Email pemulihan telah dikirim ulang ke ' . $user->personal_email);
        }

        $ticket->update(['status' => 'failed']);
        return back()->with('error', 'Gagal mengirim email otomatis. Silakan periksa konfigurasi mail server.');
    }

    public function resolve(RecoveryTicket $ticket)
    {
        $ticket->update(['status' => 'resolved']);
        app(\App\Services\NotificationService::class)->notifyUserTicketResolved($ticket);
        return back()->with('success', 'Tiket telah ditandai sebagai Selesai.');
    }

    public function reject(RecoveryTicket $ticket)
    {
        $ticket->update(['status' => 'rejected']);
        return back()->with('info', 'Permintaan pemulihan tiket ditolak.');
    }

    public function updateAdminWa(Request $request)
    {
        $request->validate(['admin_wa_number' => 'required|string|max:20']);

        $phone = preg_replace('/[^0-9]/', '', $request->admin_wa_number);
        
        // Correctly format to '628...'
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        AppSetting::updateOrCreate(
            ['key' => 'admin_wa_number'],
            ['value' => $phone]
        );

        return back()->with('success', 'Nomor WhatsApp Admin berhasil disimpan.');
    }

    public function bulkResolve(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Tidak ada tiket yang dipilih.');
        }

        $tickets = RecoveryTicket::whereIn('id', $ids)
            ->whereIn('status', ['pending_admin', 'processing', 'failed', 'sent'])
            ->get();

        $notifService = app(\App\Services\NotificationService::class);
        foreach ($tickets as $ticket) {
            $ticket->update(['status' => 'resolved']);
            $notifService->notifyUserTicketResolved($ticket);
        }

        return back()->with('success', $tickets->count() . ' tiket berhasil diselesaikan.');
    }

    public function bulkReject(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Tidak ada tiket yang dipilih.');
        }

        $count = RecoveryTicket::whereIn('id', $ids)
            ->whereIn('status', ['pending_admin', 'processing', 'failed', 'sent'])
            ->update(['status' => 'rejected']);

        return back()->with('info', $count . ' tiket berhasil ditolak.');
    }

    public function history()
    {
        $tickets = RecoveryTicket::with('user')
            ->whereIn('status', ['resolved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('admin.recovery-tickets.history', compact('tickets'));
    }

    public function bulkDeleteHistory(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Tidak ada riwayat tiket yang dipilih.');
        }

        $count = RecoveryTicket::whereIn('id', $ids)
            ->whereIn('status', ['resolved', 'rejected'])
            ->delete();

        return back()->with('success', $count . ' riwayat tiket berhasil dihapus permanen.');
    }
}
