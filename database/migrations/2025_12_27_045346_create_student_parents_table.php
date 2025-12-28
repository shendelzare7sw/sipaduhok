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
        Schema::create('student_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->enum('relationship', [
                'ayah_kandung',
                'ibu_kandung',
                'ayah_tiri',
                'ibu_tiri',
                'kakek',
                'nenek',
                'paman',
                'bibi',
                'wali',
                'lainnya'
            ]);
            $table->boolean('is_primary')->default(false); // penanggung jawab utama
            $table->boolean('is_financial_responsible')->default(true); // yang handle pembayaran
            $table->boolean('can_access_academic')->default(true); // bisa lihat nilai/absensi
            $table->timestamps();

            // Unique constraint: satu siswa + satu parent = satu relasi
            $table->unique(['siswa_id', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_parents');
    }
};
