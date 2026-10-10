<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menyiapkan kredensial Midtrans per environment.
 *
 * Kolom Paywuz sengaja tidak dihapus agar branch Paywuz dan Midtrans dapat
 * dipertukarkan pada database yang sama tanpa kehilangan konfigurasi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('info_pembayaran', function (Blueprint $table) {
            if (! Schema::hasColumn('info_pembayaran', 'midtrans_sandbox_server_key')) {
                $table->text('midtrans_sandbox_server_key')->nullable();
            }

            if (! Schema::hasColumn('info_pembayaran', 'midtrans_production_server_key')) {
                $table->text('midtrans_production_server_key')->nullable();
            }
        });

        // Server key lama (satu kolom untuk environment aktif) sudah terenkripsi
        // dengan Crypt::encryptString. Hanya nilai yang dapat didekripsi dengan
        // APP_KEY saat ini yang dipindahkan; sisanya wajib diisi ulang admin.
        if (Schema::hasColumn('info_pembayaran', 'midtrans_server_key')) {
            DB::table('info_pembayaran')
                ->whereNotNull('midtrans_server_key')
                ->where('midtrans_server_key', '!=', '')
                ->orderBy('id')
                ->get(['id', 'midtrans_server_key', 'midtrans_is_production'])
                ->filter(function (object $row): bool {
                    try {
                        return filled(Crypt::decryptString($row->midtrans_server_key));
                    } catch (Throwable) {
                        return false;
                    }
                })
                ->each(function (object $row): void {
                    $column = $row->midtrans_is_production
                        ? 'midtrans_production_server_key'
                        : 'midtrans_sandbox_server_key';

                    DB::table('info_pembayaran')
                        ->where('id', $row->id)
                        ->whereNull($column)
                        ->update([$column => $row->midtrans_server_key]);
                });
        }

        // Status aktif hanya boleh menyala setelah administrator menyimpan
        // kredensial Midtrans yang valid melalui halaman Info Pembayaran.
        DB::table('info_pembayaran')
            ->whereNull('midtrans_sandbox_server_key')
            ->whereNull('midtrans_production_server_key')
            ->update(['midtrans_enabled' => false]);
    }

    public function down(): void
    {
        Schema::table('info_pembayaran', function (Blueprint $table) {
            $table->dropColumn(['midtrans_sandbox_server_key', 'midtrans_production_server_key']);
        });
    }
};
