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
        Schema::table('soal_ujian', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('narasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soal_ujian', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
