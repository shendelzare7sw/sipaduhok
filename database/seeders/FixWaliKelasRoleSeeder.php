<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Role;

class FixWaliKelasRoleSeeder extends Seeder
{
    /**
     * Fix wali kelas role based on kelas assignment
     */
    public function run(): void
    {
        $waliKelasRole = Role::where('name', 'wali_kelas')->first();

        if (!$waliKelasRole) {
            echo "⚠ Wali Kelas role not found!\n";
            return;
        }

        echo "=== Fixing Wali Kelas Roles ===\n\n";

        $kelasWithWali = Kelas::whereNotNull('wali_kelas_id')
            ->with('waliKelas.user')
            ->get();

        $updated = 0;
        foreach ($kelasWithWali as $kelas) {
            if ($kelas->waliKelas && $kelas->waliKelas->user) {
                $user = $kelas->waliKelas->user;

                if ($user->role_id != $waliKelasRole->id) {
                    $user->update(['role_id' => $waliKelasRole->id]);
                    echo "✓ {$user->name} → Wali Kelas {$kelas->nama_kelas} (role_id: {$waliKelasRole->id})\n";
                    $updated++;
                }
            }
        }

        echo "\n✓ Updated {$updated} users to Wali Kelas role\n";
        echo "=== Done! ===\n";
    }
}
