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
        Schema::create('jadwal_pelajaran_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_pelajaran_id')->constrained('jadwal_pelajaran')->onDelete('cascade');
            $table->string('field_changed'); // guru_id, jam_mulai, jam_selesai, dll
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('keterangan')->nullable(); // alasan perubahan
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('changed_at');
            $table->timestamps();

            // Index untuk audit trail
            $table->index(['jadwal_pelajaran_id', 'changed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajaran_history');
    }
};
