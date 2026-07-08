<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mapel')->unique();
            $table->string('nama_mapel');
            $table->enum('jenjang', ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA']);
            $table->enum('kelompok', ['A', 'B'])->nullable()
                ->comment('Subject category: A (Core subjects) or B (Elective/Local content)');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran');
    }
};
