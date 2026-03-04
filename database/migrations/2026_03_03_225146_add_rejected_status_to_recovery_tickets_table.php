<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE recovery_tickets MODIFY COLUMN status ENUM('processing', 'sent', 'pending_admin', 'resolved', 'rejected', 'expired', 'failed') DEFAULT 'processing'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE recovery_tickets MODIFY COLUMN status ENUM('processing', 'sent', 'pending_admin', 'resolved', 'expired', 'failed') DEFAULT 'processing'");
    }
};
