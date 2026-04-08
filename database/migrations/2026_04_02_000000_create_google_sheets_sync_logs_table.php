<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('google_sheets_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('module'); // siswa, guru, kelas, jadwal, presensi, nilai, tagihan, pembayaran, dll
            $table->enum('direction', ['push', 'pull']);
            $table->string('spreadsheet_id');
            $table->string('sheet_name');
            $table->integer('rows_synced')->default(0);
            $table->enum('status', ['success', 'failed', 'partial'])->default('success');
            $table->longText('error_message')->nullable();
            $table->foreignId('synced_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('synced_at')->useCurrent();
            $table->timestamps();

            // Indexes for quick queries
            $table->index(['module', 'synced_at']);
            $table->index(['direction', 'synced_at']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_sheets_sync_logs');
    }
};
