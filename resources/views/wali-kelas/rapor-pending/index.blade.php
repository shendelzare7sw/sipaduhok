@extends('layouts.sneat')

@section('title', 'Rapor Pending Saya')
@section('page-title', 'Rapor Pending Saya')
@section('page-subtitle', 'Rapor draft & revisi dari semua kelas yang pernah Anda walikan')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/wali-kelas/rapor-pending/index.css', 'resources/js/wali-kelas/rapor-pending/index.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-none">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-history me-2 text-primary"></i>Rapor Pending Saya</h4>
            <p class="text-muted mb-0">
                Rapor yang masih draft, perlu dikirim, atau diminta revisi — dari semua kelas yang pernah Anda walikan
                (termasuk TA yang sudah lewat). Akses ini ada agar rapor TA lalu tidak terjebak setelah promosi.
            </p>
        </div>
    </div>

    <div class="d-none">
        <i class="fas fa-info-circle me-2 mt-1"></i>
        <div>
            <strong>Cara kerja:</strong> Sistem menarik kelas yang pernah Anda walikan dari <code>wali_kelas_assignments</code>
            tanpa filter tahun ajaran. Klik "Buka Rapor" untuk masuk ke halaman edit rapor.
            @if($kelasIds->isEmpty())
                <br><strong class="text-warning">Anda belum pernah ditugaskan sebagai wali kelas — daftar di bawah akan kosong.</strong>
            @endif
        </div>
    </div>

    @if($kelasIds->isEmpty())
        <div class="alert alert-warning d-flex align-items-start">
            <i class="fas fa-exclamation-circle me-2 mt-1"></i>
            <div>Anda belum pernah ditugaskan sebagai wali kelas. Daftar rapor pending akan kosong.</div>
        </div>
    @endif

    <div class="pending-summary">
        <div class="pending-stat">
            <div class="icon-circle icon-circle-draft"><i class="fas fa-file-alt"></i></div>
            <div><div class="label">Draft Belum Dikirim</div><div class="value">{{ $totalDraft }}</div></div>
        </div>
        <div class="pending-stat">
            <div class="icon-circle icon-circle-pending"><i class="fas fa-paper-plane"></i></div>
            <div><div class="label">Menunggu Ketua</div><div class="value">{{ $totalKirim }}</div></div>
        </div>
        <div class="pending-stat">
            <div class="icon-circle icon-circle-revision"><i class="fas fa-exclamation-triangle"></i></div>
            <div><div class="label">Diminta Revisi</div><div class="value">{{ $totalRevisi }}</div></div>
        </div>
    </div>

    @if($raporList->isEmpty())
        <div class="empty-state">
            <i class="fas fa-check-circle text-success"></i>
            <h5 class="fw-bold mb-1">Tidak Ada Rapor Pending</h5>
            <p class="mb-0">Semua rapor Anda sudah selesai/diterbitkan, atau belum ada rapor draft yang dibuat.</p>
        </div>
    @else
        @php $byKelas = $raporList->groupBy('kelas_id'); @endphp
        @foreach($byKelas as $kelasId => $rapors)
            @php $kelas = $rapors->first()->kelas; @endphp
            <div class="grouped-section">
                <div class="group-header">
                    <i class="fas fa-school text-primary"></i>
                    {{ $kelas?->nama_kelas ?? 'Kelas -' }}
                    <span class="badge-ta">TA {{ $kelas?->tahunAjaran?->nama_tahun_ajaran ?? '-' }}{{ $kelas?->tahunAjaran?->is_active ? ' · Aktif' : '' }}</span>
                    <span class="ms-auto small text-muted">{{ $rapors->count() }} rapor</span>
                </div>
                @foreach($rapors as $rapor)
                    <div class="rapor-item">
                        <div class="info">
                            <div class="judul">
                                {{ $rapor->siswa?->nama_lengkap ?? 'Siswa #' . $rapor->siswa_id }}
                            </div>
                            <div class="meta">
                                <i class="fas fa-calendar-alt"></i>Semester {{ $rapor->semester ?? '-' }}
                                · {{ $rapor->jenis_rapor === 'tengah_semester' ? 'PTS' : 'PAS' }}
                                @if($rapor->updated_at)
                                    · Terakhir diubah {{ $rapor->updated_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                @endif
                            </div>
                            @if(!empty($rapor->catatan_revisi_ketua))
                                <div class="revision-note mt-2 p-2">
                                    <strong class="text-danger">Catatan Ketua:</strong>
                                    {{ \Illuminate\Support\Str::limit($rapor->catatan_revisi_ketua, 200) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            @if($rapor->status_review_ketua === 'revisi')
                                <span class="badge-status revisi">Revisi</span>
                            @elseif($rapor->status_review_ketua === 'pending')
                                <span class="badge-status pending">Menunggu Ketua</span>
                            @else
                                <span class="badge-status draft">Draft</span>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('wali.rapor.edit', $rapor->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-pen me-1"></i>Buka Rapor
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
</div>
@endsection
