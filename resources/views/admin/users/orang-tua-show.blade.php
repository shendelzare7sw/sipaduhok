@extends('layouts.sneat')

@section('title')
Detail Orang Tua - {{ $orangTua->name ?? 'N/A' }}
@endsection

@section('page-title', 'Detail Orang Tua')

@section('page-subtitle')
{{ $orangTua->name ?? 'N/A' }}
@endsection

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/users/show.css'])
@endsection

@section('content')
<div class="user-show user-show--orang-tua">
    <div class="show-shell">
        <div class="show-header">
            <div class="show-avatar">
                <i class="fas fa-user-friends"></i>
            </div>
            <h3 class="show-title">{{ $orangTua->name }}</h3>
            <div class="show-subtitle">{{ $orangTua->username }}</div>
            <div class="show-badges">
                <span class="show-pill show-pill--outline">
                    <i class="fas fa-user-friends"></i>
                    Orang Tua
                </span>
                <span class="show-pill {{ $orangTua->is_active ? 'show-pill--active-solid' : 'show-pill--inactive-solid' }}">
                    Status: {{ $orangTua->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        <div class="show-body">
            <div class="detail-two-col">
                <div>
                    <h5 class="show-section-title">Informasi Akun</h5>
                    <table class="show-table">
                        <tr>
                            <td class="show-label">Nama Lengkap</td>
                            <td class="show-value">{{ $orangTua->name }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">Username</td>
                            <td class="show-value show-value--mono">{{ $orangTua->username }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">Email</td>
                            <td class="show-value">{{ $orangTua->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">Email Pribadi</td>
                            <td class="show-value">{{ $orangTua->personal_email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="show-label">No. Telepon/WA</td>
                            <td class="show-value">
                                @if($orangTua->phone)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $orangTua->phone) }}" target="_blank" class="show-link--whatsapp">
                                        <i class="fab fa-whatsapp"></i> {{ $orangTua->phone }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="show-label">Status Akun</td>
                            <td>
                                <span class="show-status {{ $orangTua->is_active ? 'show-status--active' : 'show-status--inactive' }}">
                                    <i class="fas fa-{{ $orangTua->is_active ? 'check' : 'times' }}"></i>
                                    {{ $orangTua->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="show-label">Terdaftar Sejak</td>
                            <td class="show-value">{{ \Carbon\Carbon::parse($orangTua->created_at)->locale('id')->translatedFormat('d F Y') }}</td>
                        </tr>
                    </table>

                    @if($orangTua->studentParents && $orangTua->studentParents->count() > 0)
                        <h5 class="show-section-title">Informasi Tambahan</h5>
                        <table class="show-table">
                            <tr>
                                <td class="show-label">Total Anak</td>
                                <td class="show-value">{{ $orangTua->studentParents->count() }} Siswa</td>
                            </tr>
                            @php
                                $jenjangList = $orangTua->studentParents->pluck('siswa.kelas.jenjang')->filter()->unique();
                            @endphp
                            @if($jenjangList->count() > 0)
                                <tr>
                                    <td class="show-label">Jenjang Anak</td>
                                    <td>
                                        <div class="show-tag-list">
                                            @foreach($jenjangList as $jenjang)
                                                <span class="show-tag show-tag--info">{{ $jenjang }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @php
                                $cabangList = $orangTua->studentParents->pluck('siswa.cabang.nama_cabang')->filter()->unique();
                            @endphp
                            @if($cabangList->count() > 0)
                                <tr>
                                    <td class="show-label">Cabang</td>
                                    <td class="show-value">{{ $cabangList->join(', ') }}</td>
                                </tr>
                            @endif
                        </table>
                    @endif
                </div>

                <div>
                    <h5 class="show-section-title show-section-title--warning">Data Anak (Siswa)</h5>

                    @if($orangTua->studentParents && $orangTua->studentParents->count() > 0)
                        <div class="show-card-list">
                            @foreach($orangTua->studentParents as $sp)
                                <div class="show-related-card">
                                    <div class="show-related-header">
                                        <div>
                                            <div class="show-related-title">
                                                <i class="fas fa-user-graduate"></i>
                                                {{ $sp->siswa->nama_lengkap }}
                                            </div>
                                            <div class="show-related-meta">
                                                <i class="fas fa-id-card"></i>
                                                NIS: {{ $sp->siswa->nis }} / NISN: {{ $sp->siswa->nisn }}
                                            </div>
                                        </div>
                                        <span class="show-tag show-tag--info">
                                            {{ ucwords(str_replace('_', ' ', $sp->relationship)) }}
                                        </span>
                                    </div>

                                    <div class="show-tag-list">
                                        @if($sp->siswa->kelas)
                                            <span class="show-tag show-tag--success">
                                                <i class="fas fa-school"></i>
                                                {{ $sp->siswa->kelas->nama_kelas }}
                                            </span>
                                            <span class="show-tag show-tag--info">{{ $sp->siswa->kelas->jenjang }}</span>
                                        @endif
                                        @if($sp->siswa->cabang)
                                            <span class="show-tag show-tag--purple">
                                                <i class="fas fa-building"></i>
                                                {{ $sp->siswa->cabang->nama_cabang }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="show-related-flags">
                                        @if($sp->is_primary)
                                            <span class="show-related-flag--success">
                                                <i class="fas fa-star"></i>
                                                Kontak Utama
                                            </span>
                                        @endif
                                        @if($sp->can_access_academic)
                                            <span class="show-related-flag--info">
                                                <i class="fas fa-check-circle"></i>
                                                Akses Akademik
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="show-empty-state">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div class="show-empty-title">Belum ada anak terdaftar</div>
                            <small class="show-empty-hint">Hubungkan akun orang tua ini dengan siswa melalui halaman edit siswa</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="show-footer">
            <a href="{{ route('admin.users.orang-tua') }}" class="show-btn show-btn--secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <div class="show-footer-actions">
                <a href="{{ route('admin.users.edit-orang-tua', $orangTua->id) }}" class="show-btn show-btn--warning">
                    <i class="fas fa-edit"></i>
                    Edit Data
                </a>
                <form action="{{ route('admin.users.toggle-orang-tua-status', $orangTua->id) }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="show-btn {{ $orangTua->is_active ? 'show-btn--danger' : 'show-btn--success' }}">
                        <i class="fas fa-{{ $orangTua->is_active ? 'ban' : 'check' }}"></i>
                        {{ $orangTua->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
