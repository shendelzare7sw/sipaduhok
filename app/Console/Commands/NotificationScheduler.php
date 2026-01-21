<?php

namespace App\Console\Commands;

use App\Models\Tugas;
use App\Models\KalenderAkademik;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotificationScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled notifications for deadlines and announcements';

    /**
     * The notification service instance.
     */
    protected NotificationService $notificationService;

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;

        $this->info('Starting scheduled notifications...');

        // 1. Deadline reminders (H-1)
        $this->sendDeadlineReminders();

        // 2. Academic calendar announcements (H-3)
        $this->sendKalenderAnnouncements();

        $this->info('Scheduled notifications completed!');
    }

    /**
     * Send deadline reminders for tugas expiring tomorrow
     */
    protected function sendDeadlineReminders(): void
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $tugasExpiring = Tugas::where('tanggal_deadline', $tomorrow)
            ->where('status', 'aktif')
            ->with(['mataPelajaran', 'kelas'])
            ->get();

        $count = 0;
        foreach ($tugasExpiring as $tugas) {
            $this->notificationService->notifyDeadlineReminder($tugas);
            $count++;
        }

        $this->info("  - Sent {$count} deadline reminders for tugas expiring tomorrow");
    }

    /**
     * Send announcements for events happening in 3 days
     */
    protected function sendKalenderAnnouncements(): void
    {
        $threesDaysAhead = Carbon::now()->addDays(3)->toDateString();

        $events = KalenderAkademik::whereDate('tanggal_mulai', $threesDaysAhead)
            ->where('is_published', true)
            ->whereDoesntHave('notifications') // Avoid duplicate notifications
            ->get();

        $count = 0;
        foreach ($events as $event) {
            $this->notificationService->notifyPengumuman($event);
            $count++;
        }

        $this->info("  - Sent {$count} calendar announcements for events in 3 days");
    }
}
