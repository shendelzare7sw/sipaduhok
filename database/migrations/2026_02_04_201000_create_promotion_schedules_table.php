<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates table for scheduling automatic promotion execution.
     */
    public function up(): void
    {
        Schema::create('promotion_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            
            // Schedule Configuration
            $table->dateTime('scheduled_at'); // When to execute
            $table->enum('status', ['PENDING', 'RUNNING', 'COMPLETED', 'FAILED', 'CANCELLED'])->default('PENDING');
            
            // Who scheduled it
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Execution Details
            $table->dateTime('executed_at')->nullable();
            $table->integer('students_processed')->default(0);
            $table->integer('students_promoted')->default(0);
            $table->integer('students_graduated')->default(0);
            $table->integer('students_failed')->default(0);
            $table->text('execution_log')->nullable();
            
            // Notification Settings
            $table->boolean('notify_on_complete')->default(true);
            $table->string('notification_email')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_schedules');
    }
};
