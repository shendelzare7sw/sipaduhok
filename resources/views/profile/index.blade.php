{{-- resources/views/profile/index.blade.php --}}

@extends('layouts.sneat')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('sidebar-menu')
    @php
        // Mapping sidebar berdasarkan role name
        $sidebarMap = [
            'admin' => 'admin.partials.sneat-sidebar-menu',
            'ketua_pkbm' => 'ketua.partials.sneat-sidebar-menu',
            'wakil_kepala_sekolah' => 'waka.partials.sneat-sidebar-menu',
            'sekretaris' => 'sekretaris.partials.sneat-sidebar-menu',
            'bendahara' => 'bendahara.partials.sneat-sidebar-menu',
            'guru_pengajar' => 'guru.partials.sneat-sidebar-menu',
            'wali_kelas' => 'wali-kelas.partials.sneat-sidebar-menu',
            'siswa' => 'siswa.partials.sneat-sidebar-sia',
            'orang_tua' => 'orang-tua.partials.sneat-sidebar-menu',
        ];

        $sidebarView = $sidebarMap[$roleName] ?? 'admin.partials.sneat-sidebar-menu';
    @endphp
    @include($sidebarView)
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Profil Saya</li>
                </ol>
            </nav>
            <h4 class="fw-bold">Profil Saya</h4>
        </div>

        <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-5">
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <div class="mb-3 mt-2">
                                    @if($user->foto_profil)
                                        <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="user image"
                                            class="rounded-circle" height="120" width="120"
                                            style="object-fit: cover; border: 3px solid #696cff;">
                                    @else
                                        <div class="avatar avatar-xl">
                                            <span class="avatar-initial rounded-circle bg-label-primary"
                                                style="font-size: 2.5rem;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="user-info text-center">
                                    <h5 class="mb-2 fw-bold">{{ $user->name }}</h5>
                                    <span class="badge bg-label-primary mb-3">
                                        {{ $user->roleRelation ? $user->roleRelation->display_name : ucwords(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center flex-wrap gap-2 mt-2">
                            <form action="{{ route('profile.upload-foto') }}" method="POST" enctype="multipart/form-data"
                                id="uploadForm">
                                @csrf
                                <input type="file" name="foto_profil" id="foto_profil" class="d-none" accept="image/*"
                                    onchange="document.getElementById('uploadForm').submit()">
                                <label for="foto_profil" class="btn btn-primary btn-sm">
                                    <i class="bx bx-upload me-1"></i> Ganti Foto
                                </label>
                            </form>

                            @if($user->foto_profil)
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#deleteFotoModal">
                                    <i class="bx bx-trash me-1"></i> Hapus Foto
                                </button>
                            @endif
                        </div>

                        <hr class="my-4">

                        <div class="info-container">
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <span class="fw-bold me-2 text-heading">Email:</span>
                                    <span class="text-muted small">{{ $user->email }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold me-2 text-heading">Terdaftar:</span>
                                    <span class="text-muted small">{{ $user->created_at->format('d M Y') }}</span>
                                </li>
                                @if($profileData && isset($profileData->cabang))
                                    <li class="mb-3">
                                        <span class="fw-bold me-2 text-heading">Cabang:</span>
                                        <span class="text-muted small">{{ $profileData->cabang->nama_cabang }}</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                @if($roleName === 'siswa' && $profileData)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem;">Status Akademik</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tahun Ajaran</span>
                                <span class="fw-bold">{{ $profileData->kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Status</span>
                                <span
                                    class="badge {{ $profileData->status === 'aktif' ? 'bg-label-success' : 'bg-label-secondary' }}">
                                    {{ ucfirst($profileData->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-xl-8 col-lg-7 col-md-7">
                @if($profileData)
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0"><i class="bx bx-user me-2"></i>Informasi Detail</h5>
                            <a href="{{ route('account.settings') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-cog me-1"></i> Pengaturan Akun
                            </a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control bg-light"
                                            value="{{ $profileData->nama_lengkap ?? $user->name }}" readonly disabled>
                                    </div>

                                    @if(isset($profileData->nip) || isset($profileData->nis))
                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="form-label text-uppercase">{{ $roleName === 'siswa' ? 'NIS' : 'NIP' }}</label>
                                            <input type="text" class="form-control bg-light"
                                                value="{{ $profileData->nip ?? $profileData->nis ?? '-' }}" readonly disabled>
                                        </div>
                                    @endif

                                    <div class="col-md-6 mb-3">
                                        <label for="no_telepon" class="form-label">No. Telepon / WhatsApp</label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                            <input type="text" name="no_telepon" id="no_telepon"
                                                class="form-control @error('no_telepon') is-invalid @enderror"
                                                value="{{ old('no_telepon', $profileData->no_telepon ?? '') }}"
                                                placeholder="08xxxxxxxxxx">
                                        </div>
                                        @error('no_telepon') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jenis Kelamin</label>
                                        <input type="text" class="form-control bg-light"
                                            value="{{ ($profileData->jenis_kelamin ?? '') == 'L' ? 'Laki-laki' : 'Perempuan' }}"
                                            readonly disabled>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="alamat" class="form-label">Alamat Lengkap</label>
                                        <textarea name="alamat" id="alamat"
                                            class="form-control @error('alamat') is-invalid @enderror"
                                            rows="3">{{ old('alamat', $profileData->alamat ?? '') }}</textarea>
                                        @error('alamat') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0"><i class="bx bx-info-circle me-2 text-primary"></i>Detail Akun</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Nama Lengkap</small>
                                    <span class="fw-bold">{{ $user->name }}</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Email Utama</small>
                                    <span class="fw-bold">{{ $user->email }}</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Role Akses</small>
                                    <span
                                        class="badge bg-label-info">{{ $user->roleRelation ? $user->roleRelation->display_name : 'User' }}</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Status Akun</small>
                                    <span class="badge bg-label-success">Aktif</span>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-label-secondary rounded">
                                <h6><i class="bx bx-cog me-1"></i> Kelola Akun?</h6>
                                <p class="mb-2">Anda dapat mengubah email dan password melalui menu pengaturan akun.</p>
                                <a href="{{ route('account.settings') }}" class="btn btn-primary btn-sm">
                                    Buka Pengaturan Akun
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Foto -->
    @if($user->foto_profil)
        <div class="modal fade" id="deleteFotoModal" tabindex="-1" aria-labelledby="deleteFotoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="deleteFotoModalLabel">
                            <i class="bx bx-error-circle me-2"></i>Konfirmasi Hapus Foto Profil
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <!-- Preview foto yang akan dihapus -->
                            <div class="position-relative d-inline-block mb-3">
                                <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="Foto yang akan dihapus"
                                    class="rounded-circle"
                                    style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #dc3545;">
                                <div class="position-absolute top-0 start-0 w-100 h-100 rounded-circle d-flex align-items-center justify-content-center"
                                    style="background-color: rgba(220, 53, 69, 0.7);">
                                    <i class="bx bx-trash text-white" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                            <h6 class="fw-bold mb-2">Apakah Anda yakin ingin menghapus foto profil?</h6>
                            <p class="text-muted mb-0">Foto profil akan dihapus dan avatar default akan ditampilkan.</p>
                        </div>

                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="bx bx-error-circle me-2 fs-4"></i>
                            <div>
                                <strong>Perhatian!</strong> Tindakan ini tidak dapat dibatalkan.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>Batal
                        </button>
                        <form action="{{ route('profile.delete-foto') }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bx bx-trash me-1"></i>Ya, Hapus Foto
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection