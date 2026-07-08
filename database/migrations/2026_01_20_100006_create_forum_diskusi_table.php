<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_diskusi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('pertemuan_id')->nullable(); // ID pertemuan LMS (opsional)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('topik', ['materi', 'tugas', 'ujian', 'umum'])->default('umum');
            $table->string('judul');
            $table->text('isi');
            $table->foreignId('reference_id')->nullable(); // ID materi/tugas/ujian yang didiskusikan
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->json('lampiran')->nullable();
            $table->timestamps();

            $table->index(['mata_pelajaran_id', 'created_at']);
        });

        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_diskusi_id')->constrained('forum_diskusi')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('forum_replies')->onDelete('cascade'); // balasan bertingkat
            $table->text('isi');
            $table->boolean('is_answer')->default(false); // jawaban resmi
            $table->boolean('is_teacher_reply')->default(false);
            $table->text('attachment')->nullable();
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
