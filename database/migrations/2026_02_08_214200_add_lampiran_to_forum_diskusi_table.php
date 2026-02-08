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
        Schema::table('forum_diskusi', function (Blueprint $table) {
            if (!Schema::hasColumn('forum_diskusi', 'lampiran')) {
                $table->json('lampiran')->nullable()->after('is_closed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_diskusi', function (Blueprint $table) {
            if (Schema::hasColumn('forum_diskusi', 'lampiran')) {
                $table->dropColumn('lampiran');
            }
        });
    }
};
