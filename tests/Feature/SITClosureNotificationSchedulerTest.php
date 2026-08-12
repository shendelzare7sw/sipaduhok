<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\KalenderAkademik;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Notification;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SITClosureNotificationSchedulerTest extends TestCase
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

    public function test_scheduler_memakai_schema_aktual_dan_tidak_mengirim_duplikat(): void
    {
        $suffix = substr(md5(uniqid('', true)), 0, 8);
        $tahun = TahunAjaran::where('is_active', true)->firstOrFail();
        $kelas = Kelas::create([
            'cabang_id' => Cabang::firstOrFail()->id,
            'tahun_ajaran_id' => $tahun->id,
            'nama_kelas' => 'SIT Scheduler '.$suffix,
            'jenjang' => 'SMP',
            'kode_kelas' => 'SN'.$suffix,
            'kuota_siswa' => 30,
        ]);
        $mapel = MataPelajaran::create([
            'kode_mapel' => 'NS'.$suffix,
            'nama_mapel' => 'Scheduler '.$suffix,
            'jenjang' => 'SMP',
        ]);
        $user = User::create([
            'name' => 'Siswa Scheduler '.$suffix,
            'email' => 'scheduler.'.$suffix.'@test.local',
            'username' => 'scheduler'.$suffix,
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);
        Siswa::create([
            'user_id' => $user->id,
            'cabang_id' => $kelas->cabang_id,
            'kelas_id' => $kelas->id,
            'nisn' => 'NSN'.$suffix,
            'nis' => 'NIS'.$suffix,
            'nama_lengkap' => $user->name,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2010-01-01',
            'alamat' => 'Alamat SIT scheduler',
            'tanggal_masuk' => now()->toDateString(),
            'status' => 'aktif',
            'agama' => 'Islam',
        ]);
        $tugas = Tugas::create([
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => TenagaPendidik::firstOrFail()->id,
            'jenis_tugas' => 'tugas',
            'judul_tugas' => 'Deadline '.$suffix,
            'deskripsi' => 'Bukti scheduler SIT closure.',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_deadline' => now()->addDay()->toDateString(),
        ]);
        $event = KalenderAkademik::create([
            'tahun_ajaran_id' => $tahun->id,
            'nama_kegiatan' => 'Kegiatan '.$suffix,
            'tanggal_mulai' => now()->addDays(3)->toDateString(),
            'jenis_kegiatan' => 'lainnya',
            'status' => 'aktif',
            'is_hidden_siswa' => false,
        ]);

        $this->assertSame(0, Artisan::call('notifications:schedule'));
        $this->assertSame(1, Notification::where('user_id', $user->id)
            ->where('tipe', Notification::TIPE_DEADLINE)
            ->where('data->tugas_id', $tugas->id)->count());
        $this->assertSame(1, Notification::where('user_id', $user->id)
            ->where('tipe', Notification::TIPE_PENGUMUMAN)
            ->where('data->kalender_id', $event->id)->count());
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'judul' => 'Pengumuman: '.$event->nama_kegiatan,
        ]);

        $this->assertSame(0, Artisan::call('notifications:schedule'));
        $this->assertSame(1, Notification::where('user_id', $user->id)
            ->where('tipe', Notification::TIPE_DEADLINE)
            ->where('data->tugas_id', $tugas->id)->count());
        $this->assertSame(1, Notification::where('user_id', $user->id)
            ->where('tipe', Notification::TIPE_PENGUMUMAN)
            ->where('data->kalender_id', $event->id)->count());
    }
}
