<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->foreignId('pertemuan_id')->nullable()->after('mata_pelajaran_id')->constrained('pertemuans')->onDelete('set null');
        });

        Schema::table('tugas', function (Blueprint $table) {
            $table->foreignId('pertemuan_id')->nullable()->after('mata_pelajaran_id')->constrained('pertemuans')->onDelete('set null');
        });

        Schema::table('ujian', function (Blueprint $table) {
            $table->foreignId('pertemuan_id')->nullable()->after('mata_pelajaran_id')->constrained('pertemuans')->onDelete('set null');
        });

        Schema::table('forum_diskusi', function (Blueprint $table) {
            $table->foreignId('pertemuan_id')->nullable()->after('mata_pelajaran_id')->constrained('pertemuans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->dropForeign(['pertemuan_id']);
            $table->dropColumn('pertemuan_id');
        });

        Schema::table('tugas', function (Blueprint $table) {
            $table->dropForeign(['pertemuan_id']);
            $table->dropColumn('pertemuan_id');
        });

        Schema::table('ujian', function (Blueprint $table) {
            $table->dropForeign(['pertemuan_id']);
            $table->dropColumn('pertemuan_id');
        });

        Schema::table('forum_diskusi', function (Blueprint $table) {
            $table->dropForeign(['pertemuan_id']);
            $table->dropColumn('pertemuan_id');
        });
    }
};
