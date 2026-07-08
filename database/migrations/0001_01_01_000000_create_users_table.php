<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Catatan: kolom role_id & cabang_id dibuat di sini, tetapi foreign key-nya
     * ditambahkan pada migration terpisah (add_foreign_keys_to_users_table)
     * karena tabel roles & cabang dibuat setelah tabel users.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('personal_email')->nullable();
            $table->string('foto_profil')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->enum('role', [
                'admin', 'ketua_pkbm', 'wakil_kepala_sekolah', 'sekretaris',
                'bendahara', 'wali_kelas', 'guru_pengajar', 'siswa', 'orang_tua',
            ]);
            $table->foreignId('role_id')->nullable();
            $table->foreignId('cabang_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('security_question')->nullable();
            $table->string('security_answer')->nullable();
            $table->string('security_pin')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
