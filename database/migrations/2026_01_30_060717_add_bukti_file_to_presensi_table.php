<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->string('bukti_file')->nullable()->after('keterangan');
        });

        // Migrate existing data
        $presensis = DB::table('presensi')->where('keterangan', 'like', '%Bukti:%')->get();
        foreach ($presensis as $p) {
            if (preg_match('/\(Bukti: (.*?)\)/', $p->keterangan, $matches)) {
                $bukti = $matches[1];
                // Clean keterangan: Remove "(Bukti: path)"
                $cleanKeterangan = preg_replace('/ \(Bukti: .*?\)/', '', $p->keterangan);
                
                DB::table('presensi')->where('id', $p->id)->update([
                    'bukti_file' => $bukti,
                    'keterangan' => $cleanKeterangan
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropColumn('bukti_file');
        });
    }
};
