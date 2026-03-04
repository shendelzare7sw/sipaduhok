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
        Schema::create('recovery_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('tipe_recovery', ['lupa_username', 'lupa_password', 'lupa_keduanya']);
            $table->enum('status', ['processing', 'sent', 'pending_admin', 'resolved', 'expired', 'failed'])->default('processing');
            $table->string('token_reset')->nullable()->unique();
            $table->string('target_phone')->nullable();
            $table->string('requested_ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recovery_tickets');
    }
};
