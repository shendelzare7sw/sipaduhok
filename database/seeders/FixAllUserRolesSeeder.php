<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class FixAllUserRolesSeeder extends Seeder
{
    /**
     * Fix ALL users to have proper role_id based on email patterns
     */
    public function run(): void
    {
        // Get all roles
        $roles = Role::all()->keyBy('name');

        echo "=== Fixing ALL User Roles ===\n\n";

        // Fix berdasarkan email pattern
        $emailPatterns = [
            'admin' => ['admin@', 'superadmin@', 'administrator@'],
            'ketua_pkbm' => ['ketua@'],
            'sekretaris' => ['sekretaris@'],
            'bendahara' => ['bendahara@'],
            'wali_kelas' => ['wali@', 'walikelas@'],
            'guru_pengajar' => ['guru@'],
            'siswa' => ['siswa@'],
            'orang_tua' => ['orangtua@', 'parent@'],
        ];

        foreach ($emailPatterns as $roleName => $patterns) {
            $role = $roles->get($roleName);
            if (!$role) {
                echo "⚠ Role {$roleName} not found!\n";
                continue;
            }

            foreach ($patterns as $pattern) {
                $users = User::where('email', 'LIKE', $pattern . '%')->get();
                foreach ($users as $user) {
                    if ($user->role_id != $role->id) {
                        $user->update(['role_id' => $role->id]);
                        echo "✓ Fixed {$user->name} ({$user->email}) → {$roleName} (role_id: {$role->id})\n";
                    }
                }
            }
        }

        // Fix siswa yang punya data di tabel siswa
        $siswaRole = $roles->get('siswa');
        if ($siswaRole) {
            $siswaWithUser = \App\Models\Siswa::with('user')->get();
            foreach ($siswaWithUser as $siswa) {
                if ($siswa->user && $siswa->user->role_id != $siswaRole->id) {
                    $siswa->user->update(['role_id' => $siswaRole->id]);
                    echo "✓ Fixed siswa data: {$siswa->user->name} → siswa (role_id: {$siswaRole->id})\n";
                }
            }
        }

        // Fix tenaga pendidik yang belum punya role
        $guruRole = $roles->get('guru_pengajar');
        if ($guruRole) {
            $tenagaPendidikWithUser = \App\Models\TenagaPendidik::with('user')->get();
            foreach ($tenagaPendidikWithUser as $tp) {
                if ($tp->user && !$tp->user->role_id) {
                    // Default ke guru_pengajar jika belum ada role
                    $tp->user->update(['role_id' => $guruRole->id]);
                    echo "✓ Fixed tenaga pendidik (default): {$tp->user->name} → guru_pengajar (role_id: {$guruRole->id})\n";
                }
            }
        }

        // Summary
        echo "\n=== Summary ===\n";
        foreach ($roles as $role) {
            $count = User::where('role_id', $role->id)->count();
            echo "{$role->display_name}: {$count} users\n";
        }

        $noRole = User::whereNull('role_id')->count();
        echo "No Role: {$noRole} users\n";

        echo "\n=== Done! ===\n";
    }
}
