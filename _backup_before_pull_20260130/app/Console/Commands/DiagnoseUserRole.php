<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class DiagnoseUserRole extends Command
{
    protected $signature = 'diagnose:user {email}';
    protected $description = 'Diagnose user role and relationships';

    public function handle()
    {
        $email = $this->argument('email');
        $this->info("Checking user: $email");

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found: $email");
            
            $firstSiswa = User::where('role', 'siswa')->first();
            if ($firstSiswa) {
                 $this->info("Found a student: {$firstSiswa->email}");
                 $user = $firstSiswa; // Switch focus to this user
            } else {
                 $this->error("No students found in DB at all!");
                 return;
            }
        }

        $this->info("User ID: " . $user->id);
        $this->info("Role (Attribute): " . $user->role);
        $this->info("Role ID: " . $user->role_id);
        
        if ($user->role_id) {
            $role = Role::find($user->role_id);
            if ($role) {
                $this->info("Role Table Name: " . $role->name);
                $this->info("Role Table ID: " . $role->id);
            } else {
                $this->error("Role ID set but Role not found in DB!");
            }
        }

        $this->info("--- Method Checks ---");
        $this->info("isSiswa(): " . ($user->isSiswa() ? 'YES' : 'NO'));
        $this->info("isOrangTua(): " . ($user->isOrangTua() ? 'YES' : 'NO'));
        $this->info("isAdmin(): " . ($user->isAdmin() ? 'YES' : 'NO'));
        
        $this->info("--- Relationship Check ---");
        try {
            if ($user->roleRelation) {
                $this->info("Relation Loaded. Name: " . $user->roleRelation->name);
            } else {
                $this->warn("Relation returned null.");
            }
        } catch (\Exception $e) {
            $this->error("Error accessing relation: " . $e->getMessage());
        }
    }
}
