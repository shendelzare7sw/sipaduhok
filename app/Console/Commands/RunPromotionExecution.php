<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PromotionService;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RunPromotionExecution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promotion:execute {--date= : Tanggal eksekusi (YYYY-MM-DD)} {--force : Paksa eksekusi tanpa cek jadwal}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Jalankan proses kenaikan kelas otomatis berdasarkan kriteria akademik dan keuangan';

    protected $promotionService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PromotionService $promotionService)
    {
        parent::__construct();
        $this->promotionService = $promotionService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->option('date') ?? date('Y-m-d');
        $force = $this->option('force');

        $activeTa = TahunAjaran::where('is_active', true)->first();
        if (!$activeTa) {
            $this->error('Tidak ada Tahun Ajaran aktif.');
            return 1;
        }

        // Check Schedule if not forced
        if (!$force) {
            $setting = DB::table('pengaturan_naik_kelas')->where('tahun_ajaran_id', $activeTa->id)->first();
            if (!$setting || !$setting->tanggal_eksekusi) {
                $this->warn('Tanggal eksekusi belum diatur pada Pengaturan Naik Kelas.');
                $this->warn('Hanya simulasi? Gunakan --force untuk memaksa eksekusi penyimpanan.');
                // By default we abort if no date scheduled to prevent Accidental Run
                if (!$this->confirm('Apakah Anda yakin ingin menjalankan proses INI SEKARANG? (Data siswa akan diperbarui)', false)) {
                     $this->info('Dibatalkan.');
                     return 0;
                }
            } elseif ($setting->tanggal_eksekusi != $date) {
                $this->warn("Jadwal eksekusi sistem adalah: {$setting->tanggal_eksekusi}.");
                $this->warn("Hari ini adalah: {$date}.");
                if (!$this->confirm('Tanggal tidak sesuai jadwal. Lanjutkan anyway?', false)) {
                    $this->info('Dibatalkan.');
                    return 0;
                }
            }
        }

        $this->info("Memulai Proses Kenaikan Kelas untuk Tahun Ajaran: {$activeTa->tahun_ajaran}");
        $this->info("Tanggal Eksekusi: {$date}");
        
        $students = Siswa::where('status', 'aktif')->get();
        if ($students->isEmpty()) {
            $this->warn('Tidak ada siswa aktif ditemukan.');
            return 0;
        }

        $bar = $this->output->createProgressBar($students->count());
        $bar->start();

        $stats = [
            'NAIK_KELAS' => 0,
            'LULUS' => 0,
            'NAIK_KELAS_TUNGGAKAN' => 0,
            'LULUS_TUNGGAKAN' => 0,
            'TIDAK_NAIK_KELAS' => 0
        ];

        foreach ($students as $siswa) {
            try {
                // Execute Safe Promotion
                $status = $this->promotionService->executeStudentPromotion($siswa, $activeTa->id, $date);
                
                if (isset($stats[$status])) {
                    $stats[$status]++;
                } else {
                    // Fallback for unexpected status
                    // count valid ones
                }
            } catch (\Exception $e) {
                // $this->error("Error Siswa ID {$siswa->id}: " . $e->getMessage());
                // Don't break progress bar
                // Log error?
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Proses Selesai.');
        $this->table(['Status', 'Jumlah'], [
            ['Naik Kelas Murni', $stats['NAIK_KELAS']],
            ['Lulus', $stats['LULUS']],
            ['Naik (Dispensasi)', $stats['NAIK_KELAS_TUNGGAKAN']],
            ['Tidak Naik', $stats['TIDAK_NAIK_KELAS']],
        ]);

        $this->info("Silakan cek menu Laporan Kenaikan Kelas untuk detail.");
        
        return 0;
    }
}
