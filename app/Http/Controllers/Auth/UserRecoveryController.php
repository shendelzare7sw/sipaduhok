<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RecoveryTicket;
use App\Models\User;
use App\Models\Siswa;
use App\Services\EmailRecoveryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserRecoveryController extends Controller
{
    protected $emailService;

    public function __construct(EmailRecoveryService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index()
    {
        return view('auth.user-recovery');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_recovery' => 'required|in:lupa_username,lupa_password,lupa_keduanya',
            'identifier'    => 'required|string|max:255',
        ]);

        $tipe = $request->tipe_recovery;
        $identifier = $request->identifier;

        $user = $this->findUserByIdentifier($identifier);

        if (!$user) {
            return back()->with('error', 'Identitas tidak ditemukan dalam sistem. Coba periksa kembali NISN, NIP, No. HP, Email Pribadi, atau Username Anda.');
        }

        if ($user->isAdmin() || $user->isKetuaPKBM()) {
            return back()->with('error', 'Akses ditolak. Administrator / Ketua PKBM tidak diizinkan menggunakan form pemulihan umum.');
        }

        $targetEmail = $user->personal_email;

        $token = null;
        if (in_array($tipe, ['lupa_password', 'lupa_keduanya'])) {
            $token = Str::random(64);
            // Delete old pending tickets for this user to avoid clutter
            RecoveryTicket::where('user_id', $user->id)->whereIn('status', ['pending_admin', 'processing'])->delete();
        }

        $ticket = RecoveryTicket::create([
            'user_id'       => $user->id,
            'tipe_recovery' => $tipe,
            'status'        => $targetEmail ? 'processing' : 'pending_admin',
            'token_reset'   => $token,
            'target_phone'  => $targetEmail, // Re-using column for target email
            'requested_ip'  => $request->ip(),
            'user_agent'    => $request->userAgent(),
            'expires_at'    => Carbon::now()->addHours(24),
        ]);

        // Log for debugging
        \Log::info("Recovery Ticket Created", [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'user_role' => $user->attributes['role'] ?? $user->role_id,
            'personal_email' => $targetEmail,
            'status' => $ticket->status,
        ]);

        if (empty($targetEmail)) {
            app(\App\Services\NotificationService::class)->notifyAdminTicketPemulihan($ticket);
            return redirect()->route('login')->with('info', "Email pribadi Anda tidak terdaftar. Permintaan Anda (#{$ticket->id}) telah ditangguhkan dan akan dibantu secara manual oleh Administrator sekolah.");
        }

        $isSent = $this->emailService->sendTicketRecovery($ticket, $user);

        if ($isSent) {
            $ticket->update(['status' => 'sent']);
            $redactedEmail = Str::mask($targetEmail, '*', 3, strpos($targetEmail, '@') - 3);
            return redirect()->route('login')->with('success', "Proses otomatis berhasil! Kami telah mengirimkan instruksi pemulihan ke email $redactedEmail. Silakan periksa kotak masuk Anda.");
        } else {
            $ticket->update(['status' => 'pending_admin']);
            app(\App\Services\NotificationService::class)->notifyAdminTicketPemulihan($ticket);
            return redirect()->route('login')->with('warning', "Terdapat masalah pada sistem pengiriman email otomatis. Tiket Anda (#{$ticket->id}) telah diteruskan ke Administrator untuk diproses secara manual.");
        }
    }

    private function findUserByIdentifier(string $ident): ?User
    {
        $user = User::where('username', $ident)->orWhere('email', $ident)->orWhere('personal_email', $ident)->orWhere('phone', $ident)->first();
        if ($user) return $user;

        $siswa = Siswa::where('nisn', $ident)->orWhere('nis', $ident)->first();
        if ($siswa && $siswa->user_id) {
            return User::find($siswa->user_id);
        }

        $tenaga = \App\Models\TenagaPendidik::where('nip', $ident)->orWhere('telepon', $ident)->first();
        if ($tenaga && $tenaga->user_id) {
            return User::find($tenaga->user_id);
        }

        return null;
    }
}
