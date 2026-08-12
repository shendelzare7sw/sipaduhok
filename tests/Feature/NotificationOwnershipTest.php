<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NotificationOwnershipTest extends TestCase
{
    public function test_pengguna_tidak_dapat_membaca_mengubah_atau_menghapus_notifikasi_lain(): void
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
            $suffix = substr(md5(uniqid('', true)), 0, 8);
            $pemilik = $this->makeUser('Pemilik '.$suffix, 'pemilik.'.$suffix.'@test.local');
            $penyerang = $this->makeUser('Penyerang '.$suffix, 'penyerang.'.$suffix.'@test.local');

            $notification = Notification::create([
                'user_id' => $pemilik->id,
                'tipe' => Notification::TIPE_SISTEM,
                'judul' => 'Notifikasi privat '.$suffix,
                'pesan' => 'Hanya pemilik yang boleh mengakses.',
            ]);

            $this->actingAs($penyerang);

            $this->get(route('notifications.show', $notification->id))->assertNotFound();
            $this->postJson(route('notifications.mark-read', $notification->id))->assertNotFound();
            $this->deleteJson(route('notifications.destroy', $notification->id))->assertNotFound();

            $this->postJson(route('notifications.bulk-action'), [
                'ids' => [$notification->id],
                'action' => 'read',
            ])->assertOk();

            $notification->refresh();
            $this->assertNull($notification->read_at);
            $this->assertDatabaseHas('notifications', [
                'id' => $notification->id,
                'user_id' => $pemilik->id,
            ]);
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
            'role' => 'siswa',
            'is_active' => true,
        ]);
    }
}
