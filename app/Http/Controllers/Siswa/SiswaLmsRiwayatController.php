<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TugasSiswa;
use App\Models\Ujian;
use App\Models\UjianSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Riwayat LMS untuk siswa: lihat tugas/ujian/latihan yang sudah pernah dikerjakan
 * lintas tahun ajaran (tidak terbatas kelas saat ini).
 */
class SiswaLmsRiwayatController extends Controller
{
    /**
     * Resolve siswa dari user yang login.
     */
    protected function siswa(): Siswa
    {
        $siswa = Siswa::where('user_id', Auth::id())->first();
        abort_if(!$siswa, 403, 'Akun Anda tidak terhubung dengan data siswa.');
        return $siswa;
    }

    public function index(Request $request)
    {
        $siswa = $this->siswa();

        $taFilter = $request->integer('tahun_ajaran_id') ?: null;
        $type = $request->input('type'); // null = semua, 'tugas', 'latihan', 'ujian'

        // Tugas Siswa (lintas TA, by siswa_id saja)
        $tugasQ = TugasSiswa::where('siswa_id', $siswa->id)
            ->with(['tugas.mataPelajaran', 'tugas.kelas.tahunAjaran'])
            ->whereHas('tugas');

        if ($taFilter) {
            $tugasQ->whereHas('tugas.kelas', fn($k) => $k->where('tahun_ajaran_id', $taFilter));
        }

        $tugasSemua = ($type === null || $type === 'tugas' || $type === 'latihan')
            ? $tugasQ->orderByDesc('tanggal_submit')->orderByDesc('id')->get()
            : collect();

        // Filter by jenis_tugas (tugas vs latihan) jika type spesifik
        $tugas = collect();
        if ($type === null || $type === 'tugas') {
            $tugas = $tugasSemua->filter(fn($ts) => ($ts->tugas?->jenis_tugas ?? 'tugas') === 'tugas')->values();
        }
        // Tugas dengan jenis_tugas='latihan' (jika ada di DB) — biasanya kosong di sistem ini
        $tugasLatihan = collect();
        if ($type === null || $type === 'latihan') {
            $tugasLatihan = $tugasSemua->filter(fn($ts) => ($ts->tugas?->jenis_tugas ?? '') === 'latihan')->values();
        }

        // Ujian Siswa (lintas TA, by siswa_id saja)
        $ujianQ = UjianSiswa::where('siswa_id', $siswa->id)
            ->with(['ujian.mataPelajaran', 'ujian.kelas.tahunAjaran'])
            ->whereHas('ujian');

        if ($taFilter) {
            $ujianQ->whereHas('ujian.kelas', fn($k) => $k->where('tahun_ajaran_id', $taFilter));
        }

        $ujianSemua = ($type === null || $type === 'latihan' || $type === 'ujian')
            ? $ujianQ->orderByDesc('waktu_mulai')->orderByDesc('id')->get()
            : collect();

        $latihan = collect();
        $ujian = collect();
        if ($type === null || $type === 'latihan') {
            $latihan = $ujianSemua->filter(fn($us) => ($us->ujian?->tipe_ujian ?? null) === Ujian::TIPE_LATIHAN)->values();
        }
        if ($type === null || $type === 'ujian') {
            $ujian = $ujianSemua->filter(fn($us) => ($us->ujian?->tipe_ujian ?? null) !== Ujian::TIPE_LATIHAN)->values();
        }

        // List TA yang punya konten
        $tahunAjarans = $this->getTahunAjaranDenganRiwayat($siswa->id);

        return view('siswa.lms.riwayat.index', [
            'siswa' => $siswa,
            'tugas' => $tugas,
            'tugasLatihan' => $tugasLatihan,
            'latihan' => $latihan,
            'ujian' => $ujian,
            'tahunAjarans' => $tahunAjarans,
            'filters' => [
                'tahun_ajaran_id' => $taFilter,
                'type' => $type,
            ],
            'totalRiwayat' => $tugas->count() + $tugasLatihan->count() + $latihan->count() + $ujian->count(),
        ]);
    }

    public function showTugas(int $tugasSiswaId)
    {
        $siswa = $this->siswa();

        $tugasSiswa = TugasSiswa::where('id', $tugasSiswaId)
            ->where('siswa_id', $siswa->id)
            ->with(['tugas.mataPelajaran', 'tugas.kelas.tahunAjaran', 'tugas.guru'])
            ->firstOrFail();

        return view('siswa.lms.riwayat.tugas', [
            'siswa' => $siswa,
            'tugasSiswa' => $tugasSiswa,
            'tugas' => $tugasSiswa->tugas,
        ]);
    }

    public function showUjian(int $ujianSiswaId)
    {
        $siswa = $this->siswa();

        $ujianSiswa = UjianSiswa::where('id', $ujianSiswaId)
            ->where('siswa_id', $siswa->id)
            ->with([
                'ujian.mataPelajaran',
                'ujian.kelas.tahunAjaran',
                'ujian.guru',
                'ujian.soalUjian',
                'jawabanSiswa.soalUjian',
            ])
            ->firstOrFail();

        return view('siswa.lms.riwayat.ujian', [
            'siswa' => $siswa,
            'ujianSiswa' => $ujianSiswa,
            'ujian' => $ujianSiswa->ujian,
        ]);
    }

    protected function getTahunAjaranDenganRiwayat(int $siswaId)
    {
        $taIdsTugas = TugasSiswa::where('siswa_id', $siswaId)
            ->join('tugas', 'tugas_siswa.tugas_id', '=', 'tugas.id')
            ->join('kelas', 'tugas.kelas_id', '=', 'kelas.id')
            ->pluck('kelas.tahun_ajaran_id');

        $taIdsUjian = UjianSiswa::where('siswa_id', $siswaId)
            ->join('ujian', 'ujian_siswa.ujian_id', '=', 'ujian.id')
            ->join('kelas', 'ujian.kelas_id', '=', 'kelas.id')
            ->pluck('kelas.tahun_ajaran_id');

        $ids = $taIdsTugas->merge($taIdsUjian)->unique()->filter()->values();

        return TahunAjaran::whereIn('id', $ids)->orderByDesc('tanggal_mulai')->get();
    }
}
