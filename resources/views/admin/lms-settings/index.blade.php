@extends('layouts.sneat')

@section('title', 'Pengaturan LMS')

@section('page-title', 'Pengaturan LMS')
@section('page-subtitle', 'Kelola akses fitur Learning Management System')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <div class="row g-4">
        <!-- Settings Column -->
        <div class="col-12 col-md-8 col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-sliders-h me-2 text-primary"></i>
                        Konfigurasi Akses Jenjang
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.lms-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-primary d-flex align-items-center mb-4" role="alert">
                            <i class="fas fa-info-circle me-3 fs-4"></i>
                            <div>
                                Aktifkan toggle pada jenjang yang diizinkan untuk mengakses fitur LMS (Tugas, Materi, Ujian Online).
                            </div>
                        </div>

                        <div class="list-group list-group-flush">
                            @php
                                $branding = [
                                    'KB' => ['icon' => 'fa-shapes', 'color' => 'success', 'desc' => 'Kelompok Bermain'],
                                    'TKA' => ['icon' => 'fa-child', 'color' => 'info', 'desc' => 'Taman Kanak-Kanak A'],
                                    'TKB' => ['icon' => 'fa-child', 'color' => 'info', 'desc' => 'Taman Kanak-Kanak B'],
                                    'SD' => ['icon' => 'fa-school', 'color' => 'danger', 'desc' => 'Sekolah Dasar'],
                                    'SMP' => ['icon' => 'fa-book', 'color' => 'primary', 'desc' => 'Sekolah Menengah Pertama'],
                                    'SMA' => ['icon' => 'fa-university', 'color' => 'warning', 'desc' => 'Sekolah Menengah Atas'],
                                ];
                            @endphp

                            @foreach ($allJenjang as $jenjang)
                                @php
                                    $info = $branding[$jenjang] ?? ['icon' => 'fa-check', 'color' => 'secondary', 'desc' => ''];
                                    $isChecked = in_array($jenjang, $allowedJenjang);
                                @endphp
                                <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="rounded-circle bg-label-{{ $info['color'] }} d-flex align-items-center justify-content-center" style="width: 100%; height: 100%;">
                                                <i class="fas {{ $info['icon'] }}"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark fw-semibold">{{ $jenjang }}</h6>
                                            <small class="text-muted">{{ $info['desc'] }}</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch form-switch-md">
                                        <input class="form-check-input" type="checkbox" name="jenjang[]"
                                            value="{{ $jenjang }}" id="jenjang_{{ $jenjang }}"
                                            style="cursor: pointer; transform: scale(1.3);"
                                            {{ $isChecked ? 'checked' : '' }}>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Column -->
        <div class="col-12 col-md-4 col-lg-5">
            <div class="card bg-label-secondary border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                         <div class="avatar me-2">
                            <div class="rounded bg-label-primary d-flex align-items-center justify-content-center" style="width: 100%; height: 100%;">
                                <i class="fas fa-info-circle"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="card-title fw-bold text-dark mb-1">Apa itu HOK-LMS?</h5>
                            <p class="card-text text-muted mb-0">Learning Management System terintegrasi untuk mengelola pembelajaran digital.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                 <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-question-circle text-warning me-2"></i>Dampak Penonaktifan</h6>
                    <ul class="timeline ms-2">
                        <li class="timeline-item pb-4 border-start border-2 ps-3" style="border-color: #e5e7eb;">
                            <span class="timeline-indicator-advanced text-danger fw-bold">•</span>
                            <div class="ms-2">
                                <div class="fw-bold text-dark">Menu Hilang</div>
                                <p class="text-muted small mb-0">Siswa tidak akan melihat menu "Learning Management" di sidebar mereka.</p>
                            </div>
                        </li>
                        <li class="timeline-item pb-4 border-start border-2 ps-3" style="border-color: #e5e7eb;">
                            <span class="timeline-indicator-advanced text-danger fw-bold">•</span>
                            <div class="ms-2">
                                <div class="fw-bold text-dark">Akses Terbatas</div>
                                <p class="text-muted small mb-0">Siswa tidak bisa mengakses halaman Tugas, Materi, dan Ujian.</p>
                            </div>
                        </li>
                        <li class="timeline-item border-start border-2 ps-3" style="border-color: transparent;">
                             <span class="timeline-indicator-advanced text-success fw-bold">•</span>
                            <div class="ms-2">
                                <div class="fw-bold text-dark">Data Aman</div>
                                <p class="text-muted small mb-0">Data tugas atau nilai yang sudah ada tidak akan dihapus, hanya disembunyikan.</p>
                            </div>
                        </li>
                    </ul>
                 </div>
            </div>
        </div>
    </div>

    <style>
        .timeline-item:last-child {
            border-left-color: transparent !important;
        }
        .form-switch .form-check-input:checked {
            background-color: #696cff; /* Primary Sneat Color */
            border-color: #696cff;
        }
    </style>
@endsection
