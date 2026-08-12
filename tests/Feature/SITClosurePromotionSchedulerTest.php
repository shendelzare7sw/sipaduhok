<?php

namespace Tests\Feature;

use App\Models\PromotionSchedule;
use App\Models\TahunAjaran;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SITClosurePromotionSchedulerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.database' => 'db_sipaduhok',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
        DB::purge('mysql');
        DB::connection('mysql')->beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::connection('mysql')->rollBack();
        parent::tearDown();
    }

    public function test_due_schedule_diklaim_sekali_dan_scheduler_memakai_overlap_lock(): void
    {
        $suffix = substr(md5(uniqid('', true)), 0, 8);
        $tahun = TahunAjaran::create([
            'nama_tahun_ajaran' => 'SIT-'.$suffix,
            'tanggal_mulai' => now()->startOfYear()->toDateString(),
            'tanggal_selesai' => now()->endOfYear()->toDateString(),
            'is_active' => false,
        ]);
        $schedule = PromotionSchedule::create([
            'tahun_ajaran_id' => $tahun->id,
            'scheduled_at' => now()->subMinute(),
            'status' => PromotionSchedule::STATUS_PENDING,
            'notify_on_complete' => false,
        ]);

        $this->assertSame(0, Artisan::call('promotion:execute-scheduled'));
        $schedule->refresh();
        $this->assertSame(PromotionSchedule::STATUS_COMPLETED, $schedule->status);
        $this->assertNotNull($schedule->executed_at);
        $this->assertSame(0, $schedule->students_processed);

        $executedAt = $schedule->executed_at->toDateTimeString();
        $this->assertSame(0, Artisan::call('promotion:execute-scheduled'));
        $this->assertSame($executedAt, $schedule->fresh()->executed_at->toDateTimeString());
        $this->assertSame(0, PromotionSchedule::readyToExecute()->whereKey($schedule->id)->count());

        $event = collect(app(Schedule::class)->events())
            ->first(fn ($candidate) => str_contains($candidate->command ?? '', 'promotion:execute-scheduled'));
        $this->assertNotNull($event);
        $this->assertTrue($event->withoutOverlapping);
    }
}
