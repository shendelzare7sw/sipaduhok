@extends('layouts.app')

@section('title', 'Daftar Kelas')
@section('page-title', 'Daftar Kelas')
@section('page-subtitle', 'Pilih kelas untuk mengelola pembelajaran')


@section('styles')
    @vite(['resources/css/guru/kelas/index.css'])
@endsection

@section('content')
    <div class="guru-kelas-page">
        <div class="container-fluid px-0">

            <div class="card shadow-sm mb-4 border-start border-primary border-4">
                <div class="card-body">
                    <h4 class="mb-0 fw-bold text-gray-800">
                        <i class="fas fa-chalkboard-teacher me-2 text-primary"></i>Kelas yang Anda Ajar
                    </h4>
                </div>
            </div>

            @if($dataKelas && count($dataKelas) > 0)
                <div class="row">
                    @foreach($dataKelas as $item)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm border-0 card-kelas">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="fw-bold text-primary mb-0 fs-4">
                                                {{ $item['kelas']->nama_kelas }}
                                            </h5>
                                            <span class="text-muted text-uppercase">Jenjang: {{ $item['kelas']->jenjang }}</span>
                                        </div>
                                        <div class="bg-light p-2 rounded">
                                            <i class="fas fa-school text-primary"></i>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <div class="row mb-3 g-0">
                                        <div class="col-6 border-end text-center">
                                            <div class="fw-bold text-gray-500 text-uppercase">Siswa</div>
                                            <div class="h5 fw-bold mb-0 text-gray-800">{{ $item['jumlah_siswa'] }}</div>
                                        </div>
                                        <div class="col-6 text-center">
                                            <div class="fw-bold text-gray-500 text-uppercase">Mapel</div>
                                            <div class="h5 fw-bold mb-0 text-gray-800">{{ $item['jumlah_mapel'] }}</div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <p class="fw-bold text-gray-600 mb-2">MATA PELAJARAN:</p>
                                        <div class="mapel-badge-list">
                                            @foreach($item['mapel'] as $mapel)
                                                <span class="badge bg-light border text-primary badge-mapel">
                                                    {{ $mapel->nama_mapel }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <a href="{{ route('guru.kelas.mapel', $item['kelas']->id) }}"
                                        class="btn btn-primary w-100 shadow-sm fw-bold py-2">
                                        Kelola Kelas <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card shadow mb-4 border-0">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-folder-open fa-5x text-gray-200"></i>
                        </div>
                        <h4 class="text-gray-800 fw-bold">Belum Ada Kelas</h4>
                        <p class="text-gray-600 mb-4">Anda belum memiliki tugas mengajar di tahun ajaran aktif.</p>
                        <div class="alert alert-info d-inline-block small">
                            <i class="fas fa-info-circle me-2"></i>Mencari konten kelas tahun ajaran lalu? Buka menu
                            <a href="{{ route('guru.lms.arsip.index') }}">Arsip LMS</a>. Jika Anda merasa ini kesalahan
                            untuk TA aktif, silakan hubungi bagian Akademik atau Admin.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
