@extends('layouts.lms')

@section('title', 'Detail Pengumuman')
@section('page-title', 'Detail Pengumuman')
@section('page-subtitle', 'Informasi dan pengumuman sekolah')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <a href="{{ route('siswa.lms.dashboard') }}" class="btn btn-link text-decoration-none mb-3 ps-0">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
            </a>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        @if($pengumuman->prioritas == 'tinggi')
                            <span class="badge bg-danger me-2">PENTING</span>
                        @else
                            <span class="badge bg-info me-2 text-dark">INFORMASI</span>
                        @endif
                        <span class="text-muted small">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ \Carbon\Carbon::parse($pengumuman->tanggal_pengumuman)->translatedFormat('d F Y') }}
                        </span>
                    </div>

                    <h2 class="fw-bold text-dark mb-4">{{ $pengumuman->judul }}</h2>

                    <div class="content-body text-secondary mb-5" style="line-height: 1.8; font-size: 1.05rem;">
                        {!! nl2br(e($pengumuman->isi_pengumuman)) !!}
                    </div>

                    @if($pengumuman->file_lampiran)
                        <div class="attachment-box bg-light p-3 rounded-3 border d-flex align-items-center">
                            <div class="icon-box bg-white p-3 rounded-3 shadow-sm me-3 text-danger">
                                <i class="fas fa-file-pdf fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">Lampiran Dokumen</h6>
                                <p class="mb-0 text-muted small">Klik tombol untuk mengunduh</p>
                            </div>
                            <a href="{{ asset('storage/' . $pengumuman->file_lampiran) }}" target="_blank" class="btn btn-primary px-4 rounded-pill">
                                <i class="fas fa-download me-2"></i>Unduh
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
