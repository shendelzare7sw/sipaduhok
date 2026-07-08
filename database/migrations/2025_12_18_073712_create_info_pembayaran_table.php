<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('info_pembayaran', function (Blueprint $table) {
            $table->id();

            // Info Rekening Bank
            $table->string('nama_bank')->nullable();
            $table->string('rekening_bank')->nullable();
            $table->string('atas_nama')->nullable();

            // Konfigurasi Midtrans
            $table->string('midtrans_merchant_id')->nullable();
            $table->text('midtrans_server_key')->nullable(); // encrypted
            $table->text('midtrans_client_key')->nullable();
            $table->boolean('midtrans_is_production')->default(false);
            $table->boolean('midtrans_enabled')->default(true);

            // Info pembayaran tunai
            $table->string('tunai_lokasi')->nullable();
            $table->string('tunai_jam_operasional')->nullable();
            $table->text('tunai_deskripsi')->nullable();

            // Metadata
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_pembayaran');
    }
};
