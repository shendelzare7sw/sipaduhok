@extends('layouts.app')

@section('title', 'Arsip Kelas: ' . $kelas->nama_kelas)
@section('page-title', 'Arsip Kelas: ' . $kelas->nama_kelas)
@section('page-subtitle', 'TA ' . ($kelas->tahunAjaran->nama_tahun_ajaran ?? '-') . ' · ' . ($kelas->cabang->nama_cabang ?? '-'))


@section('styles')
    @vite(['resources/css/wali-kelas/arsip/show.css', 'resources/js/wali-kelas/arsip/show.js'])
@endsection

@section('content')
<div class="container-xxl">
    {{-- Banner read-only --}}
    <div class="arsip-banner">
        <i class="fas fa-archive"></i>
        <strong>Mode Arsip — Read-Only.</strong>
        Anda melihat data historis kelas <strong>{{ $kelas->nama_kelas }}</strong>
        di <strong>TA {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</strong>.
        Tidak ada operasi edit/hapus yang tersedia di mode ini.
        <a href="{{ route('wali.arsip.index') }}" class="ms-2 fw-bold arsip-back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke daftar
        </a>
    </div>

    {{-- Summary --}}
    <div class="summary-row">
        <div class="summary-card">
            <div class="label">Siswa Tercatat</div>
            <div class="value">{{ $totalSiswa ?? 0 }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Rapor</div>
            <div class="value">{{ $totalRapor ?? 0 }}</div>
            <div class="sub">{{ $totalRaporTerbit ?? 0 }} diterbitkan</div>
        </div>
        <div class="summary-card">
            <div class="label">Record Presensi</div>
            <div class="value">{{ $totalPresensi ?? 0 }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Record Nilai</div>
            <div class="value">{{ $totalNilai ?? 0 }}</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="arsip-tabs">
        <a href="{{ route('wali.arsip.show', $kelas->id) }}" class="{{ $activeTab === 'siswa' ? 'active' : '' }}">
            <i class="fas fa-users"></i> Siswa
        </a>
        <a href="{{ route('wali.arsip.rapor', $kelas->id) }}" class="{{ $activeTab === 'rapor' ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i> Rapor
        </a>
        <a href="{{ route('wali.arsip.presensi', $kelas->id) }}" class="{{ $activeTab === 'presensi' ? 'active' : '' }}">
            <i class="fas fa-clipboard-check"></i> Presensi
        </a>
        <a href="{{ route('wali.arsip.nilai', $kelas->id) }}" class="{{ $activeTab === 'nilai' ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Nilai
        </a>
    </div>

    {{-- TAB CONTENT --}}
    @if($activeTab === 'siswa')
        @if(!isset($siswaList) || $siswaList->isEmpty())
            <div class="empty-tab">
                <i class="fas fa-user-slash"></i>
                <div>Belum ada siswa tercatat di kelas ini.</div>
            </div>
        @else
            <table class="data-table wk-card-table">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama</th>
                        <th>NIS / NISN</th>
                        <th>JK</th>
                        <th>Status Saat Ini</th>
                        <th>Hasil di TA Ini</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswaList as $i => $siswa)
                        @php $snap = $siswa->statusNaikKelas->first(); @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $siswa->nama_lengkap }}</strong></td>
                            <td>
                                {{ $siswa->nis ?: '-' }}<br>
                                <small class="text-muted">{{ $siswa->nisn ?: '-' }}</small>
                            </td>
                            <td>
                                <span class="badge badge-mini {{ $siswa->jenis_kelamin === 'L' ? 'bg-info' : 'bg-pink' }}">
                                    {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-mini bg-{{ $siswa->status === 'aktif' ? 'success' : ($siswa->status === 'lulus' ? 'info' : 'secondary') }}">
                                    {{ ucfirst($siswa->status) }}
                                </span>
                            </td>
                            <td>
                                @if($snap)
                                    @php
                                        $clsMap = [
                                            'NAIK_KELAS' => 'badge bg-success',
                                            'NAIK_KELAS_TUNGGAKAN' => 'badge bg-warning',
                                            'TIDAK_NAIK_KELAS' => 'badge bg-danger',
                                            'LULUS' => 'badge bg-info',
                                            'LULUS_TUNGGAKAN' => 'badge bg-warning',
                                        ];
                                        $cls = $clsMap[$snap->status_kelulusan] ?? 'badge bg-secondary';
                                    @endphp
                                    <span class="{{ $cls }} badge-mini">
                                        {{ str_replace('_', ' ', $snap->status_kelulusan) }}
                                    </span>
                                    @if($snap->kelas_tujuan)
                                        <small class="text-muted d-block">→ {{ $snap->kelas_tujuan }}</small>
                                    @endif
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    @elseif($activeTab === 'rapor')
        @if(!isset($raporList) || $raporList->isEmpty())
            <div class="empty-tab">
                <i class="fas fa-file"></i>
                <div>Belum ada rapor di kelas ini.</div>
            </div>
        @else
            <table class="data-table wk-card-table">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Siswa</th>
                        <th width="100">Semester</th>
                        <th width="120">Status</th>
                        <th width="160">Review Ketua</th>
                        <th width="120">Diperbarui</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($raporList as $i => $rapor)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $rapor->siswa->nama_lengkap ?? '-' }}</strong></td>
                            <td>{{ $rapor->semester ?? '-' }}</td>
                            <td><span class="badge-rapor {{ $rapor->status }}">{{ ucfirst($rapor->status ?? '-') }}</span></td>
                            <td>
                                @if($rapor->status_review_ketua)
                                    <span class="badge-rapor {{ $rapor->status_review_ketua === 'revisi' ? 'revisi' : 'draft' }}">
                                        {{ ucfirst($rapor->status_review_ketua) }}
                                    </span>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td><small>{{ $rapor->updated_at?->format('d/m/Y H:i') ?? '-' }}</small></td>
                            <td>
                                @if($rapor->status === 'draft' || $rapor->status_review_ketua === 'revisi')
                                    <a href="{{ route('wali.rapor.edit', $rapor->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @else
                                    <a href="{{ route('wali.rapor.preview', $rapor->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    @elseif($activeTab === 'presensi')
        <div class="filter-bar">
            <form method="GET" class="d-flex gap-2 align-items-center">
                <label class="small fw-bold mb-0">Filter Bulan:</label>
                <select name="bulan" data-auto-submit class="form-select form-select-sm filter-select-presensi">
                    <option value="">Semua bulan</option>
                    @foreach(($bulanTersedia ?? collect()) as $bln)
                        <option value="{{ $bln }}" @selected(($bulanFilter ?? '') === $bln)>
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $bln)->locale('id')->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
                @if(!empty($bulanFilter))
                    <a href="{{ route('wali.arsip.presensi', $kelas->id) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>

        @if(!isset($rekapPresensi) || $rekapPresensi->isEmpty())
            <div class="empty-tab">
                <i class="fas fa-calendar-times"></i>
                <div>Belum ada record presensi untuk kelas/periode ini.</div>
            </div>
        @else
            <table class="data-table wk-card-table">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Siswa</th>
                        <th width="80" class="text-center">Hadir</th>
                        <th width="80" class="text-center">Sakit</th>
                        <th width="80" class="text-center">Izin</th>
                        <th width="80" class="text-center">Alpha</th>
                        <th width="80" class="text-center">Total</th>
                        <th width="100" class="text-center">% Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapPresensi as $i => $r)
                        @php $persen = $r->total > 0 ? round(($r->hadir / $r->total) * 100, 1) : 0; @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $r->siswa->nama_lengkap ?? 'Siswa #'.$r->siswa_id }}</strong></td>
                            <td class="text-center">{{ $r->hadir }}</td>
                            <td class="text-center">{{ $r->sakit }}</td>
                            <td class="text-center">{{ $r->izin }}</td>
                            <td class="text-center {{ $r->alpha > 0 ? 'text-alpha-danger' : 'text-alpha-muted' }}">{{ $r->alpha }}</td>
                            <td class="text-center fw-bold">{{ $r->total }}</td>
                            <td class="text-center fw-bold {{ $persen >= 80 ? 'text-attendance-good' : ($persen >= 60 ? 'text-attendance-warning' : 'text-attendance-danger') }}">
                                {{ $persen }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    @elseif($activeTab === 'nilai')
        <div class="filter-bar">
            <form method="GET" class="d-flex gap-2 align-items-center">
                <label class="small fw-bold mb-0">Filter Semester:</label>
                <select name="semester" data-auto-submit class="form-select form-select-sm filter-select-semester">
                    <option value="">Semua semester</option>
                    @foreach(($semesterTersedia ?? collect()) as $sm)
                        <option value="{{ $sm }}" @selected(($semesterFilter ?? '') == $sm)>Semester {{ $sm }}</option>
                    @endforeach
                </select>
                @if(!empty($semesterFilter))
                    <a href="{{ route('wali.arsip.nilai', $kelas->id) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>

        @if(!isset($nilaiBySiswa) || $nilaiBySiswa->isEmpty())
            <div class="empty-tab">
                <i class="fas fa-chart-bar"></i>
                <div>Belum ada record nilai untuk kelas/semester ini.</div>
            </div>
        @else
            @foreach($nilaiBySiswa as $siswaId => $nilaiItems)
                @php
                    $siswa = $nilaiItems->first()->siswa;
                    $nilaiByMapel = $nilaiItems->groupBy('mata_pelajaran_id');
                @endphp
                <div class="card mb-3">
                    <div class="card-header arsip-card-header-soft">
                        <strong>{{ $siswa->nama_lengkap ?? 'Siswa #'.$siswaId }}</strong>
                        <small class="text-muted ms-2">NIS: {{ $siswa->nis ?? '-' }}</small>
                    </div>
                    <div class="card-body p-0">
                        <table class="nilai-pivot wk-card-table">
                            <thead>
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th width="80">Semester</th>
                                    <th width="80">Tugas</th>
                                    <th width="80">UTS</th>
                                    <th width="80">UAS</th>
                                    <th width="100">Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nilaiByMapel as $mapelId => $records)
                                    @foreach($records as $n)
                                        @php $nilaiAkhir = (float) ($n->nilai_akhir ?? 0); @endphp
                                        <tr>
                                            <td>{{ $n->mataPelajaran->nama_mapel ?? 'Mapel #'.$mapelId }}</td>
                                            <td class="score">{{ $n->semester ?? '-' }}</td>
                                            <td class="score">{{ $n->nilai_tugas ?? '-' }}</td>
                                            <td class="score">{{ $n->nilai_uts ?? '-' }}</td>
                                            <td class="score">{{ $n->nilai_uas ?? '-' }}</td>
                                            <td class="score {{ $nilaiAkhir >= 70 ? 'pass' : ($nilaiAkhir > 0 ? 'below' : '') }}">
                                                {{ $n->nilai_akhir ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
</div>
@endsection
