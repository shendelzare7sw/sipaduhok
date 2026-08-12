<?php

namespace Tests\Feature;

use App\Models\Rapor;
use App\Models\RequestDownloadRapor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RaporDownloadTokenSecurityTest extends TestCase
{
    public function test_token_salah_kedaluwarsa_dan_milik_user_lain_ditolak(): void
    {
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

        try {
            $rapor = Rapor::first();
            $this->assertNotNull($rapor, 'Butuh satu rapor existing untuk verifikasi token read-only.');

            $suffix = substr(md5(uniqid('', true)), 0, 8);
            $pemilik = $this->makeUser('Pemilik Rapor '.$suffix, 'rapor.owner.'.$suffix.'@test.local');
            $userLain = $this->makeUser('User Lain '.$suffix, 'rapor.other.'.$suffix.'@test.local');
            $token = 'expired-'.$suffix;

            $downloadRequest = RequestDownloadRapor::create([
                'rapor_id' => $rapor->id,
                'user_id' => $pemilik->id,
                'siswa_id' => $rapor->siswa_id,
                'status' => 'disetujui',
                'tanggal_request' => now()->subDay(),
                'download_token' => $token,
                'download_expired_at' => now()->subMinute(),
            ]);

            $this->withoutMiddleware()->actingAs($pemilik)
                ->get(route('wali-siswa.rapor.download', $token))
                ->assertRedirect(route('wali-siswa.dashboard'))
                ->assertSessionHas('error', 'Link download sudah kadaluarsa.');

            $downloadRequest->update(['download_expired_at' => now()->addHour()]);

            $this->withoutMiddleware()->actingAs($userLain)
                ->get(route('wali-siswa.rapor.download', $token))
                ->assertRedirect(route('wali-siswa.dashboard'))
                ->assertSessionHas('error', 'Link download tidak valid.');

            $this->withoutMiddleware()->actingAs($pemilik)
                ->get(route('wali-siswa.rapor.download', 'token-yang-salah-'.$suffix))
                ->assertRedirect(route('wali-siswa.dashboard'))
                ->assertSessionHas('error', 'Link download tidak valid.');
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function makeUser(string $name, string $email): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'username' => strstr($email, '@', true),
            'password' => bcrypt('password'),
            'role' => 'orang_tua',
            'is_active' => true,
        ]);
    }
}
