@extends('layouts.app')

@section('title', 'Pilih Kelas')
@section('page-title', 'Pilih Kelas')
@section('page-subtitle', 'Pilih kelas yang ingin Anda kelola')


@section('styles')
    @vite(['resources/css/wali-kelas/pilih-kelas/index.css', 'resources/js/wali-kelas/pilih-kelas/index.js'])
@endsection

@section('content')
<div class="pilih-kelas-page">
    <div class="page-intro">
        <h2>Selamat Datang, {{ $waliKelas->nama_lengkap }}</h2>
        <p>Anda ditugaskan sebagai wali kelas untuk {{ $kelasList->count() }} kelas. Silakan pilih kelas yang ingin Anda kelola.</p>
    </div>

    <div class="kelas-grid">
        @foreach($kelasList as $kelas)
            <div class="kelas-card {{ $currentSelectedId == $kelas->id ? 'selected' : '' }}">
                <div class="kelas-card-header {{ strtolower($kelas->jenjang) }}">
                    @if($currentSelectedId == $kelas->id)
                        <div class="selected-badge">
                            <i class="fas fa-check-circle"></i> Aktif
                        </div>
                    @endif
                    <div class="kelas-nama">{{ $kelas->nama_kelas }}</div>
                    <div class="kelas-jenjang">{{ $kelas->jenjang }}</div>
                </div>
                <div class="kelas-card-body">
                    <div class="kelas-info-row">
                        <div class="kelas-info-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="kelas-info-content">
                            <div class="kelas-info-label">Cabang</div>
                            <div class="kelas-info-value">{{ $kelas->cabang->nama_cabang ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="kelas-info-row">
                        <div class="kelas-info-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="kelas-info-content">
                            <div class="kelas-info-label">Tahun Ajaran</div>
                            <div class="kelas-info-value">{{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="kelas-info-row">
                        <div class="kelas-info-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="kelas-info-content">
                            <div class="kelas-info-label">Jumlah Siswa</div>
                            <div class="kelas-info-value">{{ $kelas->siswa->count() }} Siswa</div>
                        </div>
                    </div>
                </div>
                <div class="kelas-card-footer">
                    <form action="{{ route('wali.pilih-kelas.select', $kelas) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-pilih">
                            @if($currentSelectedId == $kelas->id)
                                <i class="fas fa-check"></i> Sudah Dipilih
                            @else
                                <i class="fas fa-arrow-right"></i> Pilih Kelas Ini
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
