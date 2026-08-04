@extends('layouts.sneat')

@section('title', 'Edit Cabang')

@section('page-title', 'Edit Cabang')
@section('page-subtitle')
Perbarui data cabang {{ $cabang->nama_cabang }}
@endsection

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite('resources/css/admin/cabang/edit.css')
@endsection

@section('content')

<div class="form-shell">
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('admin.cabang.index') }}">Manajemen Cabang</a>
        <span>/</span>
        <span class="current">Edit: {{ $cabang->nama_cabang }}</span>
    </div>

    {{-- Current Data Badge --}}
    <div class="current-data-badge">
        <i class="fas fa-edit"></i>
        Mengedit data cabang dengan kode: <strong>{{ $cabang->kode_cabang }}</strong>
    </div>

    {{-- Warning Box --}}
    <div class="warning-box">
        <h6><i class="fas fa-exclamation-triangle"></i> Perhatian</h6>
        <p>
            Perubahan pada data cabang akan mempengaruhi seluruh data terkait seperti siswa, kelas, dan tenaga pendidik yang terdaftar di cabang ini.
            Pastikan data yang dimasukkan sudah benar.
        </p>
    </div>

    {{-- Stats Summary --}}
    @php
        $siswaCount = \App\Models\Siswa::where('cabang_id', $cabang->id)->count();
        $kelasCount = \App\Models\Kelas::where('cabang_id', $cabang->id)->count();
        $userCount = \App\Models\User::where('cabang_id', $cabang->id)->where('role', '!=', 'siswa')->count();
    @endphp
    <div class="stats-summary">
        <div class="stats-item {{ $siswaCount > 0 ? 'has-data' : '' }}">
            <div class="stats-number">{{ $siswaCount }}</div>
            <div class="stats-label">Siswa Terdaftar</div>
        </div>
        <div class="stats-item {{ $kelasCount > 0 ? 'has-data' : '' }}">
            <div class="stats-number">{{ $kelasCount }}</div>
            <div class="stats-label">Kelas Aktif</div>
        </div>
        <div class="stats-item {{ $userCount > 0 ? 'has-data' : '' }}">
            <div class="stats-number">{{ $userCount }}</div>
            <div class="stats-label">Tenaga Pendidik</div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-edit"></i> Form Edit Cabang</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.cabang.update', $cabang) }}" method="POST" id="cabangForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="_return_url" value="{{ url()->previous(route('admin.cabang.index')) }}">

                {{-- Basic Information --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i> Informasi Dasar
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="kode_cabang">Kode Cabang <span class="required">*</span></label>
                            <input type="text"
                                   class="form-control text-uppercase-input @error('kode_cabang') is-invalid @enderror"
                                   id="kode_cabang"
                                   name="kode_cabang"
                                   value="{{ old('kode_cabang', $cabang->kode_cabang) }}"
                                   placeholder="Contoh: RUKO, PAUD, CMNGS"
                                   maxlength="10"
                                   required>
                            <div class="form-hint">Maksimal 10 karakter, hanya huruf dan angka (akan otomatis uppercase)</div>
                            @error('kode_cabang')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nama_cabang">Nama Cabang <span class="required">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nama_cabang') is-invalid @enderror" 
                                   id="nama_cabang" 
                                   name="nama_cabang" 
                                   value="{{ old('nama_cabang', $cabang->nama_cabang) }}"
                                   placeholder="Contoh: PKBM House of Knowledge - Ruko"
                                   required>
                            @error('nama_cabang')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Contact Information --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-map-marker-alt"></i> Lokasi & Kontak
                    </div>

                    <div class="form-row single">
                        <div class="form-group">
                            <label for="alamat">Alamat Lengkap <span class="required">*</span></label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                      id="alamat" 
                                      name="alamat" 
                                      rows="3"
                                      placeholder="Masukkan alamat lengkap cabang..."
                                      required>{{ old('alamat', $cabang->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telepon">Nomor Telepon</label>
                            <input type="text" 
                                   class="form-control @error('telepon') is-invalid @enderror" 
                                   id="telepon" 
                                   name="telepon" 
                                   value="{{ old('telepon', $cabang->telepon) }}"
                                   placeholder="Contoh: (021) 12345678">
                            <div class="form-hint">Opsional - nomor telepon kantor cabang</div>
                            @error('telepon')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Status Cabang</label>
                            <label class="form-check">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $cabang->is_active) ? 'checked' : '' }}>
                                <span class="form-check-label">
                                    <span class="label-title">Aktif</span>
                                    <span class="label-desc">Cabang beroperasi dan dapat menerima siswa baru</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <div class="form-actions-left">
                        <a href="{{ url()->previous(route('admin.cabang.index')) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('admin.cabang.show', $cabang) }}" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                    <div class="form-actions-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite('resources/js/admin/cabang/edit.js')
@endsection
