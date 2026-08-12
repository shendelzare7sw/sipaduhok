<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('mata_pelajaran', 'filter_agama')) {
            Schema::table('mata_pelajaran', function (Blueprint $table) {
                $table->string('filter_agama', 20)
                    ->nullable()
                    ->after('kelompok')
                    ->index()
                    ->comment('Isi hanya untuk mapel agama: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu');
            });
        }

        $keywords = [
            'Islam' => ['islam'],
            'Kristen' => ['kristen', 'protestan'],
            'Katolik' => ['katolik', 'katholik'],
            'Hindu' => ['hindu'],
            'Buddha' => ['buddha', 'budha'],
            'Konghucu' => ['konghucu', 'khonghucu'],
        ];

        DB::table('mata_pelajaran')
            ->select('id', 'nama_mapel')
            ->whereNull('filter_agama')
            ->orderBy('id')
            ->get()
            ->each(function ($mapel) use ($keywords) {
                $namaMapel = strtolower((string) $mapel->nama_mapel);

                if (! str_contains($namaMapel, 'agama') && ! str_contains($namaMapel, 'religi')) {
                    return;
                }

                foreach ($keywords as $agama => $agamaKeywords) {
                    foreach ($agamaKeywords as $keyword) {
                        if (str_contains($namaMapel, $keyword)) {
                            DB::table('mata_pelajaran')
                                ->where('id', $mapel->id)
                                ->update(['filter_agama' => $agama]);

                            return;
                        }
                    }
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('mata_pelajaran', 'filter_agama')) {
            Schema::table('mata_pelajaran', function (Blueprint $table) {
                $table->dropColumn('filter_agama');
            });
        }
    }
};
