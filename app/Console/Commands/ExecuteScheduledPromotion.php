<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PromotionSchedule;
use App\Models\Siswa;
use App\Services\PromotionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExecuteScheduledPromotion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promotion:execute-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute all pending scheduled promotion jobs that are ready';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingSchedules = PromotionSchedule::readyToExecute()->get();
        
        if ($pendingSchedules->isEmpty()) {
            $this->info('No scheduled promotions ready to execute.');
            return 0;
        }
        
        $this->info("Found {$pendingSchedules->count()} scheduled promotion(s) to execute.");
        
        $promotionService = app(PromotionService::class);
        
        foreach ($pendingSchedules as $schedule) {
            $this->executeSchedule($schedule, $promotionService);
        }
        
        return 0;
    }

    private function executeSchedule(PromotionSchedule $schedule, PromotionService $promotionService)
    {
        $this->info("Executing schedule ID: {$schedule->id} for TA: {$schedule->tahunAjaran->nama_tahun_ajaran}");
        
        $schedule->markAsRunning();
        
        $stats = [
            'processed' => 0,
            'promoted' => 0,
            'graduated' => 0,
            'failed' => 0,
            'log' => '',
        ];
        
        DB::beginTransaction();
        try {
            // Get active students enrolled in classes of the scheduled academic year
            $students = Siswa::where('status', 'aktif')
                ->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $schedule->tahun_ajaran_id))
                ->get();
            $logMessages = [];
            
            foreach ($students as $siswa) {
                $result = $promotionService->executeStudentPromotion(
                    $siswa, 
                    $schedule->tahun_ajaran_id, 
                    now()
                );
                
                $stats['processed']++;
                
                switch ($result) {
                    case 'NAIK_KELAS':
                    case 'NAIK_KELAS_TUNGGAKAN':
                        $stats['promoted']++;
                        break;
                    case 'LULUS':
                        $stats['graduated']++;
                        break;
                    case 'TIDAK_NAIK_KELAS':
                        $stats['failed']++;
                        break;
                }
            }
            
            DB::commit();
            
            $stats['log'] = "Processed: {$stats['processed']}, Promoted: {$stats['promoted']}, Graduated: {$stats['graduated']}, Failed: {$stats['failed']}";
            $schedule->markAsCompleted($stats);
            
            $this->info("✓ Schedule {$schedule->id} completed successfully.");
            $this->info("  - {$stats['log']}");
            
            // TODO: Send notification email if configured
            if ($schedule->notify_on_complete && $schedule->notification_email) {
                $this->sendNotification($schedule, $stats);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            $errorMessage = "Error: " . $e->getMessage();
            $schedule->markAsFailed($errorMessage);
            
            Log::error("Scheduled promotion failed", [
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->error("✗ Schedule {$schedule->id} failed: {$e->getMessage()}");
        }
    }
    
    private function sendNotification(PromotionSchedule $schedule, array $stats)
    {
        // TODO: Implement email notification
        // For now, just log it
        Log::info("Promotion schedule completed notification should be sent to: {$schedule->notification_email}");
    }
}
