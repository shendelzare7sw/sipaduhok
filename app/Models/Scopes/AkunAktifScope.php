<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Sembunyikan siswa yang AKUN USER-nya dinonaktifkan (users.is_active = 0)
 * dari SELURUH query di aplikasi.
 *
 * Kenapa global scope, bukan menambah where() di tiap controller?
 * Ada ~164 titik query siswa di 32 file, dan sebagian (mis.
 * Admin\KelasController::show()) sama sekali TIDAK memfilter status. Menambal
 * satu-satu berisiko ada yang terlewat - dan satu yang terlewat = siswa nonaktif
 * masih nongol di suatu menu (persis bug yang mau dihilangkan). Global scope
 * menutup semuanya sekaligus, termasuk relasi seperti $kelas->siswa.
 *
 * Sengaja HANYA memeriksa users.is_active, BUKAN siswa.status. Alasannya:
 * status 'lulus' tetap harus terlihat di fitur "Alumni Menunggak" milik
 * Bendahara (lihat Bendahara\TagihanController::index()), jadi status tidak
 * boleh ikut disaring di level global.
 *
 * Untuk halaman yang memang perlu melihat siswa nonaktif (Kelola Siswa admin,
 * supaya bisa diaktifkan kembali / dihapus), pakai:
 *     Siswa::withoutGlobalScope(AkunAktifScope::class)
 * atau helper Siswa::termasukNonaktif().
 */
class AkunAktifScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $table = $model->getTable();

        $builder->whereExists(function ($query) use ($table) {
            $query->select(\Illuminate\Support\Facades\DB::raw(1))
                ->from('users')
                ->whereColumn('users.id', $table.'.user_id')
                ->where('users.is_active', 1);
        });
    }
}
