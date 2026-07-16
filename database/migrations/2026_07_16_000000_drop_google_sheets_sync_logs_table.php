<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fitur Google Sheets Sync dihapus (tidak dipakai lagi). Drop tabel log-nya.
 * Migration create lama sudah dihapus, jadi pada DB fresh tabel tak pernah dibuat
 * dan dropIfExists aman no-op; pada DB lama tabel benar-benar dihapus.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('google_sheets_sync_logs');
    }

    public function down(): void
    {
        // Fitur sudah dihapus permanen; tidak ada rollback tabel.
    }
};
