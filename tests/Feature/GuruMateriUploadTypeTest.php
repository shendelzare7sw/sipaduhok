<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Upload materi: format file WAJIB cocok dengan tipe yang dipilih.
 * Dulu memilih "pdf" tetap menerima file lain (mis. xlsx). Sekarang ditolak
 * ("format tidak didukung"), sekaligus menutup upload tipe berbahaya.
 */
class GuruMateriUploadTypeTest extends TestCase
{
    public function test_format_harus_cocok_dengan_tipe_yang_dipilih(): void
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
        Storage::fake('public');

        DB::connection('mysql')->beginTransaction();
        try {
            $suffix = substr(md5(uniqid('', true)), 0, 8);

            $cabang = Cabang::first();
            $ta = TahunAjaran::where('is_active', true)->first();
            $this->assertNotNull($cabang);
            $this->assertNotNull($ta);

            $guruUser = $this->makeUser('guru_pengajar', "guru.$suffix@test.local");
            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $guruUser->id;
            $tenaga->nama_lengkap = 'Guru ' . $suffix;
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            $mapel = $this->makeMapel($suffix);
            $kelas = $this->makeKelas($cabang->id, $ta->id, $suffix);
            GuruPengajarKelas::create([
                'tenaga_pendidik_id' => $tenaga->id,
                'kelas_id' => $kelas->id,
                'mata_pelajaran_id' => $mapel->id,
            ]);

            $this->actingAs($guruUser)->withoutMiddleware();

            // Tipe PDF tapi upload file non-PDF (xlsx) -> DITOLAK dengan error format.
            // Regression guard: bila aturan mimes dihapus, file ini akan lolos (tak ada
            // error) sehingga test gagal.
            // Catatan: uji "format benar diterima" tidak diotomasi karena UploadedFile::fake
            // tidak menghasilkan MIME asli (mimes menolak semua fake) — diverifikasi manual.
            $resWrong = $this->post(route('guru.lms.materi.store', [$kelas->id, $mapel->id]), [
                'judul_materi' => 'Materi ' . $suffix,
                'kategori' => 'materi',
                'deskripsi' => 'desc',
                'tipe_file' => 'pdf',
                'file_materi' => UploadedFile::fake()->create('salah.xlsx', 40, 'application/vnd.ms-excel'),
            ]);
            $resWrong->assertSessionHasErrors('file_materi');
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function makeUser(string $role, string $email): User
    {
        $u = new User();
        $u->name = 'User ' . $email;
        $u->email = $email;
        $u->role = $role;
        $u->password = bcrypt('password');
        $u->save();
        return $u;
    }

    private function makeMapel(string $suffix): MataPelajaran
    {
        $m = new MataPelajaran();
        $m->kode_mapel = 'K' . $suffix;
        $m->nama_mapel = 'Mapel ' . $suffix;
        $m->jenjang = 'SMA';
        $m->save();
        return $m;
    }

    private function makeKelas(int $cabangId, int $taId, string $suffix): Kelas
    {
        $k = new Kelas();
        $k->cabang_id = $cabangId;
        $k->tahun_ajaran_id = $taId;
        $k->nama_kelas = 'Kelas ' . $suffix;
        $k->jenjang = 'SMA';
        $k->kode_kelas = 'K' . $suffix;
        $k->kuota_siswa = 30;
        $k->save();
        return $k;
    }
}
