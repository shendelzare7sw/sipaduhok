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
            $table->text('narasi')->nullable()->after('ujian_id');
        });
    }

    public function down(): void
    {
        Schema::table('soal_ujian', function (Blueprint $table) {
            $table->dropColumn('narasi');
        });
    }
};
