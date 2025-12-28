<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\User;

class ShowRoleSummary extends Command
{
    protected $signature = 'role:summary';
    protected $description = 'Show summary of users per role with sample credentials';

    public function handle()
    {
        $this->info("=== ROLE SUMMARY & TEST CREDENTIALS ===\n");

        $roles = Role::orderBy('level')->get();

        foreach ($roles as $role) {
            $users = User::where('role_id', $role->id)->get();
            $count = $users->count();

            $this->line("📌 {$role->display_name} (Level {$role->level}): {$count} users");

            if ($count > 0) {
                $sampleUser = $users->first();
                $this->line("   ✓ Test Login: {$sampleUser->email} / password");
            }

            $this->newLine();
        }

        $noRole = User::whereNull('role_id')->count();
        if ($noRole > 0) {
            $this->warn("⚠ Users without role: {$noRole}");
        }

        $this->info("=== DONE ===");
    }
}
