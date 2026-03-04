<?php

namespace App\Services;

use App\Models\RecoveryTicket;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailRecoveryService
{
    /**
     * Send a recovery email to the user's personal email.
     *
     * @param string $targetEmail  The personal email address
     * @param string $subject      Email subject
     * @param string $body         Plain-text message body
     * @return bool
     */
    public function sendRecoveryEmail(string $targetEmail, string $subject, string $body): bool
    {
        try {
            Mail::raw($body, function ($message) use ($targetEmail, $subject) {
                $message->to($targetEmail)
                        ->subject($subject);
            });

            Log::info("Recovery email sent", [
                'to' => $targetEmail,
                'subject' => $subject,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send recovery email", [
                'to' => $targetEmail,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Build and send a recovery notification based on ticket type.
     */
    public function sendTicketRecovery(RecoveryTicket $ticket, User $user): bool
    {
        $targetEmail = $user->personal_email;

        if (empty($targetEmail)) {
            return false;
        }

        if ($ticket->tipe_recovery === 'lupa_username') {
            $rawRole = $user->roleRelation->name ?? $user->role ?? 'User';
            $roleName = ucwords(str_replace('_', ' ', $rawRole));

            $subject = "Pemulihan Username ($roleName) – PKBM House of Knowledge";
            $body  = "LAYANAN IT OTOMATIS PKBM HOUSE OF KNOWLEDGE\n\n";
            $body .= "Halo,\nKami menerima permintaan pemulihan Username Anda.\n\n";
            $body .= "Nama: {$user->name}\n";
            $body .= "Tipe Akun (Role): {$roleName}\n";
            $body .= "Username Anda: {$user->username}\n\n";
            $body .= "Silakan kembali ke aplikasi dan login menggunakan username tersebut.";

            return $this->sendRecoveryEmail($targetEmail, $subject, $body);
        }

        if (in_array($ticket->tipe_recovery, ['lupa_password', 'lupa_keduanya'])) {
            $rawRole = $user->roleRelation->name ?? $user->role ?? 'User';
            $roleName = ucwords(str_replace('_', ' ', $rawRole));

            $resetUrl = route('password.reset.ticket', ['token' => $ticket->token_reset]);

            $subject = "Reset Password ($roleName) – PKBM House of Knowledge";
            $body  = "LAYANAN IT OTOMATIS PKBM HOUSE OF KNOWLEDGE\n\n";
            $body .= "Halo,\nKami menerima permintaan reset Password Anda.\n\n";
            $body .= "Nama: {$user->name}\n";
            $body .= "Tipe Akun (Role): {$roleName}\n";

            if ($ticket->tipe_recovery === 'lupa_keduanya') {
                $body .= "Username Anda: {$user->username}\n";
                $body .= "Email Login Anda: {$user->email}\n";
            }

            $body .= "\nKlik link aman di bawah ini untuk membuat Password Baru:\n";
            $body .= $resetUrl . "\n\n";
            $body .= "(Link ini berlaku maksimal 24 jam sejak dikirim)";

            return $this->sendRecoveryEmail($targetEmail, $subject, $body);
        }

        return false;
    }

    /**
     * Send a notification when an admin updates their security settings (PIN/Question).
     */
    public function sendSecurityUpdateNotification(User $user, string $newQuestion): bool
    {
        $targetEmail = $user->personal_email;

        if (empty($targetEmail)) {
            return false;
        }

        $rawRole = $user->roleRelation->name ?? $user->role ?? 'Admin';
        $roleName = ucwords(str_replace('_', ' ', $rawRole));

        $subject = "Perubahan Pengaturan Keamanan ($roleName) – PKBM House of Knowledge";
        
        $body  = "LAYANAN IT OTOMATIS PKBM HOUSE OF KNOWLEDGE\n\n";
        $body .= "Halo,\nSistem mencatat adanya pembaruan pada Pengaturan Keamanan Khusus (PIN & Pertanyaan Keamanan) untuk akun Anda.\n\n";
        $body .= "Nama: {$user->name}\n";
        $body .= "Tipe Akun (Role): {$roleName}\n";
        $body .= "Username: {$user->username}\n\n";
        $body .= "Pertanyaan Keamanan Baru Anda adalah:\n\"{$newQuestion}\"\n\n";
        $body .= "Jika Anda merasa tidak melakukan perubahan ini, harap SEGERA hubungi Developer Utama (Sysadmin) untuk mengamankan akun Anda.\n\n";
        $body .= "Pesan ini otomatis dan tidak perlu dibalas.";

        return $this->sendRecoveryEmail($targetEmail, $subject, $body);
    }
}
