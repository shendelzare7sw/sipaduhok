<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forum_diskusi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('topik', ['materi', 'tugas', 'ujian', 'umum'])->default('umum');
            $table->string('judul');
            $table->text('isi');
            $table->foreignId('reference_id')->nullable(); // ID of materi/tugas/ujian being discussed
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            $table->index(['mata_pelajaran_id', 'created_at']);
        });

        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_diskusi_id')->constrained('forum_diskusi')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('forum_replies')->onDelete('cascade'); // For nested replies
            $table->text('isi');
            $table->boolean('is_answer')->default(false); // Mark as official answer from teacher
            $table->timestamps();

            $table->index(['forum_diskusi_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_diskusi');
    }
};
