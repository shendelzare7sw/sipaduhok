<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\TenagaPendidik;

class SyncTenagaPendidikData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:sync-tenaga-pendidik';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create missing TenagaPendidik records for existing Users with relevant roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roles = [
            'guru_pengajar',
            'wali_kelas',
            'wakil_kepala_sekolah',
            'ketua_pkbm',
            'bendahara',
            'sekretaris'
        ];

        $users = User::whereIn('role', $roles)->get();
        $count = 0;

        foreach ($users as $user) {
            $exists = TenagaPendidik::where('user_id', $user->id)->exists();

            if (!$exists) {
                $this->info("Creating TenagaPendidik for: {$user->name} ({$user->role})");
                
                TenagaPendidik::create([
                    'user_id' => $user->id,
                    'nip' => 'DISYNC-' . rand(100000, 999999), // Placeholder
                    'nama_lengkap' => $user->name,
                    'jenis_kelamin' => 'L', // Default
                    'tempat_lahir' => 'Jakarta', // Default
                    'tanggal_lahir' => '1990-01-01', // Default
                    'alamat' => 'Alamat belum diisi',
                    'telepon' => $user->phone ?? '08123456789',
                    'email' => $user->email,
                    'pendidikan_terakhir' => 'S1', // Default
                ]);
                $count++;
            }
        }

        $this->info("Sync completed. Created {$count} records.");
    }
}
