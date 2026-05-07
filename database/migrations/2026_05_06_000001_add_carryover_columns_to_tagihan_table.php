<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->foreignId('tagihan_asal_id')->nullable()->after('tahun_ajaran_id')
                ->constrained('tagihan')->nullOnDelete()
                ->comment('Pointer ke tagihan TA lama jika ini adalah carryover');
            $table->foreignId('dialihkan_ke_id')->nullable()->after('tagihan_asal_id')
                ->constrained('tagihan')->nullOnDelete()
                ->comment('Pointer ke tagihan baru di TA aktif jika ini sudah dialihkan');
            $table->timestamp('dialihkan_pada')->nullable()->after('dialihkan_ke_id');

            $table->index('tagihan_asal_id', 'tagihan_asal_idx');
            $table->index('dialihkan_ke_id', 'tagihan_dialihkan_idx');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropForeign(['tagihan_asal_id']);
            $table->dropForeign(['dialihkan_ke_id']);
            $table->dropIndex('tagihan_asal_idx');
            $table->dropIndex('tagihan_dialihkan_idx');
            $table->dropColumn(['tagihan_asal_id', 'dialihkan_ke_id', 'dialihkan_pada']);
        });
    }
};
