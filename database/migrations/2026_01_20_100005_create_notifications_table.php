<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tipe', 50); // materi, tugas, ujian, forum, pengumuman, deadline, nilai, dll
            $table->string('judul');
            $table->text('pesan');
            $table->string('link')->nullable();
            $table->json('data')->nullable(); // Data tambahan: mapel_id, tugas_id, dsb.
            $table->string('icon')->nullable(); // Class icon untuk tampilan
            $table->string('color')->nullable(); // Class warna untuk tampilan
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
