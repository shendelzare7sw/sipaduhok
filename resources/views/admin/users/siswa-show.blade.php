@extends('layouts.sneat')

@section('title')
Detail Siswa - {{ $siswa->nama_lengkap ?? 'N/A' }}
@endsection

@section('page-title', 'Detail Siswa')

@section('page-subtitle')
{{ $siswa->nama_lengkap ?? 'N/A' }}
@endsection

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/users/show.css'])
@endsection

@section('content')
<div class="user-show user-show--siswa">
    <div class="show-shell">
        <div class="show-header">
            <div class="show-avatar">
                @if($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto">
                @else
                    <i class="fas fa-user-graduate"></i>
                @endif
            </div>
            <h3 class="show-title">{{ $siswa->user->name ?? $siswa->nama_lengkap }}</h3>
            <div class="show-subtitle">{{ $siswa->nis }} / {{ $siswa->nisn }}</div>
            <div class="show-badges">
                <span class="show-pill show-pill--outline">
                    {{ $siswa->kelas->nama_kelas ?? 'Belum Ada Kelas' }}
                </span>
                <span class="show-pill show-pill--solid">
                    Status: {{ ucfirst($siswa->status) }}
                </span>
            </div>
        </div>

        <div class="show-body">
            <div class="detail-two-col">
                <div>
                    <h5 class="show-section-title">Data Akademik</h5>
                    <table class="show-table">
                        <tr>
                            <td class="show-label">NIS</td>
                            <td class="show-value">{{ $siswa->nis ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">NISN</td>
                            <td class="show-value">{{ $siswa->nisn }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">Tanggal Masuk</td>
                            <td class="show-value">{{ \Carbon\Carbon::parse($siswa->tanggal_masuk)->locale('id')->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">Cabang</td>
                            <td class="show-value">{{ $siswa->cabang->nama_cabang ?? '-' }}</td>
                        </tr>
                    </table>

                    <h5 class="show-section-title">Data Pribadi</h5>
                    <table class="show-table">
                        <tr>
                            <td class="show-label">Jenis Kelamin</td>
                            <td class="show-value">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">Tempat Lahir</td>
                            <td class="show-value">{{ $siswa->tempat_lahir }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">Tanggal Lahir</td>
                            <td class="show-value">{{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">Alamat</td>
                            <td class="show-value">{{ $siswa->alamat }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">Agama</td>
                            <td class="show-value">{{ $siswa->agama ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <div>
                    <h5 class="show-section-title">Data Wali Siswa (Biodata)</h5>
                    <table class="show-table">
                        <tr>
                            <td class="show-label">Nama Ayah</td>
                            <td class="show-value">{{ $siswa->nama_ayah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">Nama Ibu</td>
                            <td class="show-value">{{ $siswa->nama_ibu ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">No. Telepon Ortu</td>
                            <td class="show-value">{{ $siswa->telepon_orangtua ?? '-' }}</td>
                        </tr>
                    </table>

                    <h5 class="show-section-title">Informasi Akun Siswa</h5>
                    <table class="show-table">
                        <tr>
                            <td class="show-label">Username</td>
                            <td class="show-value">{{ $siswa->user->username }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">Email</td>
                            <td class="show-value">{{ $siswa->user->email }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">Email Pribadi</td>
                            <td class="show-value">{{ $siswa->user->personal_email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">No. Telepon (Akun)</td>
                            <td class="show-value">{{ $siswa->user->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">Status Akun</td>
                            <td>
                                <span class="show-status {{ $siswa->user->is_active ? 'show-status--active' : 'show-status--inactive' }}">
                                    <i class="fas fa-{{ $siswa->user->is_active ? 'check' : 'times' }}"></i>
                                    {{ $siswa->user->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="show-label">Tgl. Daftar (Akun)</td>
                            <td class="show-value">
                                {{ $siswa->user->created_at ? \Carbon\Carbon::parse($siswa->user->created_at)->locale('id')->translatedFormat('d F Y H:i') : '-' }}
                            </td>
                        </tr>
                    </table>

                    <h5 class="show-section-title show-section-title--warning">Akun Wali Siswa Terdaftar</h5>
                    @if($siswa->studentParents && $siswa->studentParents->count() > 0)
                        <div class="show-card-list">
                            @foreach($siswa->studentParents as $sp)
                                <div class="show-related-card show-related-card--warning">
                                    <div class="show-related-header">
                                        <div>
                                            <div class="show-related-title show-related-title--warning">
                                                <i class="fas fa-user"></i>
                                                {{ $sp->parent->name }}
                                            </div>
                                            <div class="show-related-meta show-related-meta--warning">
                                                <i class="fas fa-link"></i>
                                                {{ ucwords(str_replace('_', ' ', $sp->relationship)) }}
                                            </div>
                                        </div>
                                        @if($sp->is_primary)
                                            <span class="show-pill show-pill--active-solid">Kontak Utama</span>
                                        @endif
                                    </div>
                                    <div class="show-related-detail-list">
                                        <div>
                                            <i class="fas fa-at"></i>
                                            {{ $sp->parent->username }}
                                        </div>
                                        <div>
                                            <i class="fas fa-envelope"></i>
                                            {{ $sp->parent->email }}
                                        </div>
                                        <div>
                                            <i class="fas fa-circle status-dot {{ $sp->parent->is_active ? 'status-dot--active' : 'status-dot--inactive' }}"></i>
                                            {{ $sp->parent->is_active ? 'Akun Aktif' : 'Akun Non-Aktif' }}
                                        </div>
                                        @if($sp->can_access_academic)
                                            <div class="show-related-flag--info">
                                                <i class="fas fa-check-circle"></i>
                                                Dapat Akses Akademik
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="show-empty-state">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div class="show-empty-title">Belum ada akun wali siswa terdaftar</div>
                            <small class="show-empty-hint">Tambahkan melalui halaman edit siswa</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="show-footer">
            <a href="{{ route('admin.users.siswa') }}" class="show-btn show-btn--secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <a href="{{ route('admin.users.edit-siswa', $siswa->id) }}" class="show-btn show-btn--warning">
                <i class="fas fa-edit"></i>
                Edit Data
            </a>
        </div>
    </div>
</div>
@endsection
