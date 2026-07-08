<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Status per soal untuk tiap sesi ujian siswa (visited/answered/doubt)
        Schema::create('ujian_siswa_soal_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_siswa_id')->constrained('ujian_siswa')->cascadeOnDelete();
            $table->foreignId('soal_ujian_id')->constrained('soal_ujian')->cascadeOnDelete();
            $table->unsignedInteger('nomor_soal')->nullable();
            $table->boolean('is_visited')->default(false);
            $table->boolean('is_answered')->default(false);
            $table->boolean('is_doubt')->default(false);
            $table->timestamp('last_visited_at')->nullable();
            $table->timestamp('last_answered_at')->nullable();
            $table->timestamp('last_doubt_at')->nullable();
            $table->timestamps();

            $table->unique(['ujian_siswa_id', 'soal_ujian_id'], 'usss_ujian_siswa_soal_unique');
            $table->index(['ujian_siswa_id', 'is_answered']);
            $table->index(['ujian_siswa_id', 'is_doubt']);
        });

        // Log kejadian pengawasan ujian (focus lost, dsb.)
        Schema::create('ujian_pengawasan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_siswa_id')->constrained('ujian_siswa')->cascadeOnDelete();
            $table->string('event_type', 50);
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index(['ujian_siswa_id', 'event_type']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_pengawasan_logs');
        Schema::dropIfExists('ujian_siswa_soal_statuses');
    }
};
