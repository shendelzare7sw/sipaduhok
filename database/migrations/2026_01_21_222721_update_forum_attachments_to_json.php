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
        // Only modify if column exists
        if (Schema::hasColumn('forum_diskusi', 'lampiran')) {
            Schema::table('forum_diskusi', function (Blueprint $table) {
                $table->text('lampiran')->nullable()->change();
            });
        }

        if (Schema::hasColumn('forum_replies', 'attachment')) {
            Schema::table('forum_replies', function (Blueprint $table) {
                $table->text('attachment')->nullable()->change();
            });
        }

        // Separate schema call for dropping column
        if (Schema::hasColumn('forum_replies', 'attachment_type')) {
            Schema::table('forum_replies', function (Blueprint $table) {
                $table->dropColumn('attachment_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('forum_diskusi', 'lampiran')) {
            Schema::table('forum_diskusi', function (Blueprint $table) {
                $table->string('lampiran', 255)->nullable()->change();
            });
        }

        if (Schema::hasColumn('forum_replies', 'attachment')) {
            Schema::table('forum_replies', function (Blueprint $table) {
                $table->string('attachment', 255)->nullable()->change();
            });
        }

        if (!Schema::hasColumn('forum_replies', 'attachment_type')) {
            Schema::table('forum_replies', function (Blueprint $table) {
                $table->string('attachment_type', 100)->nullable();
            });
        }
    }
};
