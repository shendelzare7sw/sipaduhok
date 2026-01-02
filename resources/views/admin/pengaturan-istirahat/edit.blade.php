@extends('layouts.sneat')

@section('title', 'Edit Waktu Istirahat')
@section('page-title', 'Edit Waktu Istirahat')
@section('page-subtitle', 'Edit Pengaturan Waktu Istirahat')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="fas fa-edit text-primary me-2"></i>Form Edit Waktu Istirahat
                </h5>
                <a href="{{ route('admin.pengaturan-istirahat.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.pengaturan-istirahat.update', $pengaturan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Jenjang --}}
                    <div class="mb-3">
                        <label for="jenjang" class="form-label">
                            Jenjang Pendidikan <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('jenjang') is-invalid @enderror"
                                id="jenjang"
                                name="jenjang"
                                required>
                            <option value="">-- Pilih Jenjang --</option>
                            @foreach($jenjangList as $jenjang)
                                <option value="{{ $jenjang }}" {{ old('jenjang', $pengaturan->jenjang) == $jenjang ? 'selected' : '' }}>
                                    {{ $jenjang }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenjang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Pilih jenjang untuk waktu istirahat ini
                        </small>
                    </div>

                    {{-- Nama Istirahat --}}
                    <div class="mb-3">
                        <label for="nama_istirahat" class="form-label">
                            Nama Istirahat <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('nama_istirahat') is-invalid @enderror"
                               id="nama_istirahat"
                               name="nama_istirahat"
                               value="{{ old('nama_istirahat', $pengaturan->nama_istirahat) }}"
                               placeholder="Contoh: Istirahat 1, Istirahat 2, Istirahat Siang, dll"
                               required>
                        @error('nama_istirahat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Urutan --}}
                    <div class="mb-3">
                        <label for="urutan" class="form-label">
                            Urutan Istirahat <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('urutan') is-invalid @enderror"
                                id="urutan"
                                name="urutan"
                                required>
                            <option value="">-- Pilih Urutan --</option>
                            <option value="1" {{ old('urutan', $pengaturan->urutan) == '1' ? 'selected' : '' }}>1 - Istirahat Pertama</option>
                            <option value="2" {{ old('urutan', $pengaturan->urutan) == '2' ? 'selected' : '' }}>2 - Istirahat Kedua</option>
                        </select>
                        @error('urutan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Maksimal 2 waktu istirahat per jenjang
                        </small>
                    </div>

                    {{-- Jam Mulai & Jam Selesai --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jam_mulai" class="form-label">
                                    Jam Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="time"
                                       class="form-control @error('jam_mulai') is-invalid @enderror"
                                       id="jam_mulai"
                                       name="jam_mulai"
                                       value="{{ old('jam_mulai', substr($pengaturan->jam_mulai, 0, 5)) }}"
                                       required>
                                @error('jam_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jam_selesai" class="form-label">
                                    Jam Selesai <span class="text-danger">*</span>
                                </label>
                                <input type="time"
                                       class="form-control @error('jam_selesai') is-invalid @enderror"
                                       id="jam_selesai"
                                       name="jam_selesai"
                                       value="{{ old('jam_selesai', substr($pengaturan->jam_selesai, 0, 5)) }}"
                                       required>
                                @error('jam_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Hari Aktif --}}
                    <div class="mb-4">
                        <label class="form-label">
                            Hari Aktif <span class="text-danger">*</span>
                        </label>
                        <div class="@error('hari_aktif') is-invalid @enderror">
                            @foreach($hariList as $hari)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="hari_aktif[]"
                                       id="hari_{{ $hari }}"
                                       value="{{ $hari }}"
                                       {{ in_array($hari, old('hari_aktif', $pengaturan->hari_aktif ?? [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="hari_{{ $hari }}">
                                    {{ $hari }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('hari_aktif')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Pilih hari-hari dimana waktu istirahat ini berlaku
                        </small>
                    </div>

                    {{-- Status Aktif --}}
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   role="switch"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $pengaturan->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>Status Aktif</strong>
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            Jika tidak aktif, waktu istirahat ini tidak akan memblokir jadwal dan tidak muncul di cetak
                        </small>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.pengaturan-istirahat.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="card mt-4">
            <div class="card-body">
                <h6 class="mb-3"><i class="fas fa-info-circle text-info me-2"></i>Informasi</h6>
                <ul class="mb-0">
                    <li>Field yang bertanda <span class="text-danger">*</span> wajib diisi</li>
                    <li>Maksimal 2 waktu istirahat per jenjang</li>
                    <li>Jam selesai harus lebih besar dari jam mulai</li>
                    <li>Sistem akan otomatis mengecek bentrok waktu dengan istirahat lain di jenjang yang sama</li>
                    <li>Nonaktifkan status jika ingin menonaktifkan sementara tanpa menghapus</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
