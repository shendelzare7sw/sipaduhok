<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('tipe', ['materi', 'tugas', 'ujian', 'forum', 'pengumuman', 'deadline', 'nilai']);
            $table->string('judul');
            $table->text('pesan');
            $table->string('link')->nullable();
            $table->json('data')->nullable(); // Additional data like mapel_id, tugas_id, etc.
            $table->string('icon')->nullable(); // Icon class for display
            $table->string('color')->nullable(); // Color class for display
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
