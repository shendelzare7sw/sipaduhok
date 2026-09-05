@extends('layouts.app')

@php
    $kontenLabel = match($kontenType) {
        'materi' => 'Materi',
        'tugas' => 'Tugas',
        'latihan' => 'Latihan',
        default => 'Ujian',
    };
@endphp

@section('title', 'Salin ' . $kontenLabel . ' ke Kelas Aktif')
@section('page-title', 'Salin Konten Arsip')
@section('page-subtitle', 'Pilih kelas + mata pelajaran tujuan di TA aktif')


@section('styles')
    @vite(['resources/css/guru/lms/arsip/form-salin.css'])
@endsection

@section('content')
<div class="guru-lms-arsip-salin-page">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('guru.lms.arsip.index') }}">Arsip LMS</a></li>
            <li class="breadcrumb-item active">Salin {{ $kontenLabel }}</li>
        </ol>
    </nav>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="salin-card">
        <h5 class="fw-bold mb-3"><i class="fas fa-copy me-2 text-primary"></i>Salin {{ $kontenLabel }} ke Kelas Aktif</h5>

        <div class="konten-info">
            <div class="label">Konten Sumber</div>
            <div class="value">{{ $previewTitle }}</div>
            <div class="meta">
                {{ $konten->kelas?->nama_kelas ?? '-' }}
                - {{ $konten->mataPelajaran?->nama_mapel ?? '-' }}
                - TA {{ $konten->kelas?->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
            </div>
        </div>

        @if($kelasMapelTujuan->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Anda belum ditugaskan ke kelas+mapel di TA aktif. Hubungi admin agar Anda di-assign sebagai guru pengajar terlebih dahulu.
            </div>
            <div class="text-end">
                <a href="{{ route('guru.lms.arsip.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        @else
            <form method="POST" action="{{ route('guru.lms.arsip.salin') }}">
                @csrf
                <input type="hidden" name="type" value="{{ $kontenType }}">
                <input type="hidden" name="sumber_id" value="{{ $kontenId }}">
                <input type="hidden" name="kelas_id" value="" data-kelas-input>
                <input type="hidden" name="mata_pelajaran_id" value="" data-mapel-input>

                <div class="form-section">
                    <label class="main-label">Pilih Kelas + Mata Pelajaran Tujuan</label>
                    <div class="target-list">
                        @foreach($kelasMapelTujuan as $idx => $tujuan)
                            <label class="target-item {{ $idx === 0 ? 'selected' : '' }}" data-target>
                                <input type="radio"
                                    name="target"
                                    value="{{ $tujuan['kelas_id'] }}|{{ $tujuan['mata_pelajaran_id'] }}"
                                    @checked($idx === 0)
                                    required>
                                <div class="info">
                                    <div class="kelas">
                                        <i class="fas fa-school me-1 text-primary"></i>{{ $tujuan['kelas']?->nama_kelas ?? '-' }}
                                        <small class="text-muted">({{ $tujuan['kelas']?->jenjang ?? '' }})</small>
                                    </div>
                                    <div class="mapel">
                                        <i class="fas fa-book me-1"></i>{{ $tujuan['mata_pelajaran']?->nama_mapel ?? '-' }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <div class="helper">Hanya kelas+mapel yang Anda ampu di TA aktif yang bisa dipilih.</div>
                </div>

                @if(in_array($kontenType, ['ujian', 'latihan']))
                    <div class="form-section">
                        <label class="main-label">Opsi Salin</label>
                        <label class="switch-row">
                            <input type="checkbox" name="sertakan_soal" value="1" checked>
                            <span><strong>Sertakan semua soal</strong> - duplikat seluruh soal beserta kunci jawaban (jawaban siswa lama TIDAK ikut tersalin).</span>
                        </label>
                    </div>
                @endif

                <div class="alert alert-info copy-note-alert">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Catatan:</strong> Setelah disalin, konten akan muncul di kelas tujuan dengan tanggal mulai = hari ini.
                    @if($kontenType === 'tugas')
                        Anda bisa edit deadline & detail lain setelah salin.
                    @elseif(in_array($kontenType, ['ujian', 'latihan']))
                        Status awal = nonaktif. Anda perlu aktifkan secara manual setelah cek/edit jadwal.
                    @endif
                    File materi/lampiran tidak diduplikasi di storage (gunakan path yang sama untuk hemat ruang).
                </div>

                <div class="action-bar">
                    <a href="{{ route('guru.lms.arsip.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-copy me-1"></i>Salin Sekarang
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/guru/lms/arsip/form-salin.js'])
@endpush
