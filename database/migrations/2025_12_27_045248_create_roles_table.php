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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // super_admin, admin, bendahara, etc
            $table->string('display_name'); // Super Admin, Admin, Bendahara
            $table->integer('level'); // 1=highest (super admin), 6=lowest (siswa)
            $table->text('description')->nullable();
            $table->json('permissions')->nullable(); // future-proof for granular permissions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
