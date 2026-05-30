<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujian_siswa', function (Blueprint $table) {
            $table->foreignId('current_soal_ujian_id')
                ->nullable()
                ->after('siswa_id')
                ->constrained('soal_ujian')
                ->nullOnDelete();
            $table->unsignedInteger('current_nomor_soal')->nullable()->after('current_soal_ujian_id');
            $table->timestamp('last_activity_at')->nullable()->after('waktu_selesai');
            $table->timestamp('last_heartbeat_at')->nullable()->after('last_activity_at');
            $table->unsignedInteger('answered_count')->default(0)->after('nilai_terbaik');
            $table->unsignedInteger('doubt_count')->default(0)->after('answered_count');
            $table->unsignedInteger('visited_count')->default(0)->after('doubt_count');
            $table->unsignedInteger('focus_lost_count')->default(0)->after('visited_count');
            $table->unsignedInteger('focus_lost_total_seconds')->default(0)->after('focus_lost_count');
            $table->timestamp('active_focus_lost_at')->nullable()->after('focus_lost_total_seconds');
        });

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

        Schema::table('ujian_siswa', function (Blueprint $table) {
            $table->dropForeign(['current_soal_ujian_id']);
            $table->dropColumn([
                'current_soal_ujian_id',
                'current_nomor_soal',
                'last_activity_at',
                'last_heartbeat_at',
                'answered_count',
                'doubt_count',
                'visited_count',
                'focus_lost_count',
                'focus_lost_total_seconds',
                'active_focus_lost_at',
            ]);
        });
    }
};
