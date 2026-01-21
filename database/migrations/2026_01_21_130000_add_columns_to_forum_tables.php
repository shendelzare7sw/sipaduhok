<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add missing columns to forum_replies
        Schema::table('forum_replies', function (Blueprint $table) {
            $table->boolean('is_teacher_reply')->default(false)->after('is_answer');
            $table->string('attachment')->nullable()->after('is_teacher_reply');
            $table->string('attachment_type')->nullable()->after('attachment');
        });

        // Add pertemuan_id to forum_diskusi if not exists
        if (!Schema::hasColumn('forum_diskusi', 'pertemuan_id')) {
            Schema::table('forum_diskusi', function (Blueprint $table) {
                $table->foreignId('pertemuan_id')->nullable()->after('kelas_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('forum_replies', function (Blueprint $table) {
            $table->dropColumn(['is_teacher_reply', 'attachment', 'attachment_type']);
        });

        if (Schema::hasColumn('forum_diskusi', 'pertemuan_id')) {
            Schema::table('forum_diskusi', function (Blueprint $table) {
                $table->dropColumn('pertemuan_id');
            });
        }
    }
};
