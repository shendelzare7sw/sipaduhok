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
        Schema::create('rapor_kegiatan_ekstra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapor_id')
                  ->constrained('rapor')->onDelete('cascade')
                  ->comment('FK to rapor table');
            $table->string('kegiatan_nama', 100)
                  ->comment('Activity name: Life Skill, Seni Musik, Menggambar, etc.');
            $table->enum('predikat', ['A', 'B', 'C'])->nullable()
                  ->comment('Grade/Predicate for the activity');
            $table->text('keterangan')->nullable()
                  ->comment('Optional notes about the activity');
            $table->timestamps();

            // Index for faster lookups
            $table->index(['rapor_id', 'kegiatan_nama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapor_kegiatan_ekstra');
    }
};
