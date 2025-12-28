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
        Schema::create('financial_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // who did it
            $table->string('action'); // create, update, delete, verify, etc
            $table->string('model_type'); // Pembayaran, Tagihan, etc
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected record
            $table->json('old_values')->nullable(); // before state
            $table->json('new_values')->nullable(); // after state
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('description')->nullable(); // human-readable description
            $table->timestamps();

            // Index for faster queries
            $table->index(['model_type', 'model_id']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_audit_logs');
    }
};
