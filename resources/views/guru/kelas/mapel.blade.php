@extends('layouts.app')

@section('title', 'Pilih Mata Pelajaran')
@section('page-title', 'Kelas ' . $kelas->nama_kelas)
@section('page-subtitle', 'Pilih mata pelajaran untuk masuk LMS')


@section('styles')
    @vite(['resources/css/guru/kelas/mapel.css'])
@endsection

@section('content')
    <div class="guru-mapel-page">
        <div class="container-fluid px-0">

            <div class="mb-4">
                <a href="{{ route('guru.kelas.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar Kelas
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold text-primary">
                                <i class="fas fa-chalkboard me-2"></i>Kelas {{ $kelas->nama_kelas }}
                            </h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-users me-1"></i>{{ $jumlahSiswa }} Siswa &bull;
                                <i class="fas fa-book me-1"></i>{{ $mapelYangDiajar->count() }} Mata Pelajaran
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="mb-3 fw-bold text-gray-700">
                        <i class="fas fa-list me-2"></i>Pilih Mata Pelajaran:
                    </h5>

                    <div class="row g-3">
                        @foreach($mapelYangDiajar as $pengajaran)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm border-start border-warning border-4 mapel-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <div
                                                class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3 icon-circle">
                                                <i class="fas fa-book-open"></i>
                                            </div>
                                            <div>
                                                <h5 class="card-title fw-bold mb-1 text-gray-800 fs-5">
                                                    {{ $pengajaran->mataPelajaran->nama_mapel }}
                                                </h5>
                                                <div class="text-muted">
                                                    {{ $pengajaran->mataPelajaran->kode_mapel }}
                                                </div>
                                            </div>
                                        </div>

                                        <a href="{{ route('guru.lms.dashboard', [$kelas->id, $pengajaran->mata_pelajaran_id]) }}"
                                            class="btn btn-primary w-100 shadow-sm fw-bold">
                                            <i class="fas fa-door-open me-1"></i>Masuk LMS
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Information Card --}}
            <div class="card shadow-sm border-start border-info border-4">
                <div class="card-body">
                    <h6 class="fw-bold text-info mb-2">
                        <i class="fas fa-info-circle me-2"></i>Informasi
                    </h6>
                    <p class="text-muted mb-0">
                        Klik tombol <strong>"Masuk LMS"</strong> untuk mengakses sistem pembelajaran untuk mata pelajaran
                        yang Anda pilih.
                        Di dalam LMS, Anda dapat mengelola materi, tugas, ujian, dan forum diskusi.
                    </p>
                </div>
            </div>

        </div>
    </div>
@endsection
