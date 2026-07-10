<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Foreign key untuk users dipasang di sini karena tabel roles & cabang
     * baru tersedia setelah tabel users dibuat.
     *
     * Idempotent: lewati FK yang sudah ada. Ini menangani DB yang skema-nya sudah
     * memiliki FK (mis. sisa konsolidasi migrasi) tetapi migrasi ini masih "pending",
     * sekaligus tetap benar untuk instalasi baru (migrate:fresh).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!$this->foreignKeyExists('users', 'users_role_id_foreign')) {
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
            }
            if (!$this->foreignKeyExists('users', 'users_cabang_id_foreign')) {
                $table->foreign('cabang_id')->references('id')->on('cabang')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if ($this->foreignKeyExists('users', 'users_role_id_foreign')) {
                $table->dropForeign(['role_id']);
            }
            if ($this->foreignKeyExists('users', 'users_cabang_id_foreign')) {
                $table->dropForeign(['cabang_id']);
            }
        });
    }

    /**
     * Cek keberadaan foreign key constraint pada tabel (via information_schema).
     */
    private function foreignKeyExists(string $table, string $constraint): bool
    {
        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }
};
