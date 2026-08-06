<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\PengaturanBatasPembayaran;
use App\Models\PengajuanRaporKetua;
use App\Models\TahunAjaran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;

class ValidasiAksesService
{
    /**
     * Tentukan periode berdasarkan semester + jenis rapor.
     */
    public function getPeriode(string $semester, string $jenisRapor): string
    {
        $map = [
            'ganjil_tengah_semester' => 'pts_ganjil',
            'ganjil_akhir_semester' => 'pas_ganjil',
            'genap_tengah_semester' => 'pts_genap',
            'genap_akhir_semester' => 'pas_genap',
        ];

        return $map["{$semester}_{$jenisRapor}"] ?? 'pas_ganjil';
    }

    /**
     * Cek apakah siswa lunas untuk periode tertentu.
     * Return true = lunas, false = belum lunas.
     */
    public function cekLunas(Siswa $siswa, string $periode): bool
    {
        $activeYear = TahunAjaran::where('is_active', true)->first();
        if (!$activeYear) return true;

        $pengaturan = PengaturanBatasPembayaran::where('tahun_ajaran_id', $activeYear->id)
            ->where('periode', $periode)
            ->first();

        if ($pengaturan) {
            return $pengaturan->cekSiswaLunas($siswa);
        }

        // Belum ada aturan jenis tagihan wajib untuk periode ini.
        //
        // Dulu di sini langsung `return true` (semua siswa dianggap lunas).
        // Karena tabel pengaturan itu tidak pernah diisi, gerbang keuangan jadi
        // tidak berfungsi sama sekali: siswa dengan tunggakan pun dinyatakan
        // lunas. Sekarang jatuh ke arti "lunas" yang paling wajar dan sama
        // dengan yang dilihat Bendahara di layar: tidak ada tagihan tersisa
        // di tahun ajaran berjalan.
        return ! Tagihan::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $activeYear->id)
            ->where('status', '!=', 'sudah_bayar')
            ->exists();
    }

    /**
     * Cek akses rapor untuk siswa.
     * Siswa bisa akses rapor jika:
     * - status rapor = 'diterbitkan' DAN tanggal_rilis <= today
     * - DAN (siswa lunas ATAU validasi_rapor_bendahara = true)
     */
    public function cekAksesRapor(Siswa $siswa, string $periode = null): bool
    {
        // Validasi ketua harus sudah
        if (!$siswa->validasi_rapor_ketua) {
            return false;
        }

        // Cek lunas
        if ($periode && $this->cekLunas($siswa, $periode)) {
            return true;
        }

        // Cek validasi bendahara (manual/dispensasi)
        if ($siswa->validasi_rapor_bendahara) {
            return true;
        }

        // Cek dispensasi yang disetujui
        if ($this->cekDispensasiDisetujui($siswa, 'rapor', $periode)) {
            return true;
        }

        // Cek izin naik kelas khusus untuk PAS Genap
        if ($periode === 'pas_genap' && $this->cekIzinNaikKelasKhusus($siswa)) {
            return true;
        }

        return false;
    }

    /**
     * Cek akses ujian untuk siswa.
     * Siswa bisa akses ujian jika:
     * - Lunas → otomatis akses
     * - Belum lunas → butuh dispensasi bendahara→ketua
     */
    public function cekAksesUjian(Siswa $siswa): bool
    {
        // Cek lunas untuk ujian
        if ($this->cekLunas($siswa, 'ujian_akhir')) {
            return true;
        }

        // Cek validasi bendahara (manual)
        if ($siswa->validasi_ujian_bendahara) {
            return true;
        }

        // Cek dispensasi ujian yang disetujui
        if ($this->cekDispensasiDisetujui($siswa, 'ujian')) {
            return true;
        }

        return false;
    }

    /**
     * Cek apakah ada dispensasi yang disetujui untuk siswa.
     */
    public function cekDispensasiDisetujui(Siswa $siswa, string $tipe, string $periode = null): bool
    {
        $query = PengajuanRaporKetua::where('siswa_id', $siswa->id)
            ->where('tipe', $tipe)
            ->where('status', 'disetujui');

        if ($periode) {
            $query->where('periode', $periode);
        }

        return $query->exists();
    }

    /**
     * Cek izin naik kelas khusus (disetujui ketua) → bypass pembayaran PAS Genap.
     */
    public function cekIzinNaikKelasKhusus(Siswa $siswa): bool
    {
        $activeYear = TahunAjaran::where('is_active', true)->first();
        if (!$activeYear) return false;

        return DB::table('izin_naik_kelas_khusus')
            ->where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $activeYear->id)
            ->where('status', 'DISETUJUI')
            ->exists();
    }

    /**
     * Auto-set validasi saat pembayaran disetujui.
     * Dipanggil dari PembayaranController setelah validasi pembayaran.
     */
    public function autoValidasiSetelahBayar(Siswa $siswa): void
    {
        $activeYear = TahunAjaran::where('is_active', true)->first();
        if (!$activeYear) return;

        // Cek ujian: jika lunas untuk ujian_akhir, auto-set validasi ujian
        if (!$siswa->validasi_ujian_bendahara && $this->cekLunas($siswa, 'ujian_akhir')) {
            $siswa->update([
                'validasi_ujian_bendahara' => true,
                'tanggal_validasi_ujian_bendahara' => now(),
                'validasi_ujian_oleh' => auth()->id(),
            ]);
        }

        // Cek rapor per periode: jika lunas dan ketua sudah approve, auto-set validasi rapor
        $periodeList = ['pts_ganjil', 'pas_ganjil', 'pts_genap', 'pas_genap'];
        foreach ($periodeList as $periode) {
            if (!$siswa->validasi_rapor_bendahara
                && $siswa->validasi_rapor_ketua
                && $this->cekLunas($siswa, $periode)) {
                $siswa->update([
                    'validasi_rapor_bendahara' => true,
                    'tanggal_validasi_rapor_bendahara' => now(),
                    'validasi_rapor_oleh' => auth()->id(),
                ]);
                break;
            }
        }
    }
}
