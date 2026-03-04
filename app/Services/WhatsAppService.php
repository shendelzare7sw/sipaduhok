<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message using a Gateway (e.g. Fonnte)
     *
     * @param string $targetPhone The destination phone number (e.g., '08123456789' or '628123456789')
     * @param string $message The text message to send
     * @return bool True if successful, False otherwise
     */
    public function sendMessage(string $targetPhone, string $message): bool
    {
        // TODO: Replace with new WhatsApp/Notification Provider
        // Temporarily, we just log the message and return false
        // This forces tickets to go to 'pending_admin' status,
        // allowing the admin to handle them manually.
        
        Log::info("WA_MOCK_SENT to {$targetPhone}: {$message}");
        
        // Return false so the system interprets it as a failed auto-send
        // and safely falls back to admin manual handling.
        return false;
    }
}
