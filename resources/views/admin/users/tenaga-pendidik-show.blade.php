@extends('layouts.sneat')

@section('title')
Detail Tenaga Pendidik - {{ $tenagaPendidik->nama_lengkap ?? 'N/A' }}
@endsection

@section('page-title', 'Detail Tenaga Pendidik')

@section('page-subtitle')
{{ $tenagaPendidik->nama_lengkap ?? 'N/A' }}
@endsection

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/users/show.css'])
@endsection

@section('content')
<div class="user-show user-show--tenaga-pendidik">
    <div class="show-shell">
        <div class="show-header">
            <div class="show-avatar">
                @if($tenagaPendidik->foto)
                    <img src="{{ asset('storage/' . $tenagaPendidik->foto) }}" alt="Foto">
                @else
                    <i class="fas fa-user"></i>
                @endif
            </div>
            <h3 class="show-title">{{ $tenagaPendidik->user->name ?? $tenagaPendidik->nama_lengkap }}</h3>
            <div class="show-badges">
                <span class="show-pill show-pill--role">
                    {{ ucwords(str_replace('_', ' ', $tenagaPendidik->user->role)) }}
                </span>
                <span class="show-pill {{ $tenagaPendidik->user->is_active ? 'show-pill--active' : 'show-pill--inactive' }}">
                    {{ $tenagaPendidik->user->is_active ? 'Aktif' : 'Non-Aktif' }}
                </span>
            </div>
        </div>

        <div class="show-body">
            <h5 class="show-section-title">Informasi Pribadi</h5>
            <table class="show-table show-table--lined">
                <tr>
                    <td class="show-label">NIP</td>
                    <td class="show-value">{{ $tenagaPendidik->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="show-label">Jenis Kelamin</td>
                    <td class="show-value">{{ $tenagaPendidik->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td class="show-label">Tgl. Lahir</td>
                    <td class="show-value">{{ $tenagaPendidik->tempat_lahir }}, {{ \Carbon\Carbon::parse($tenagaPendidik->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="show-label">Pendidikan</td>
                    <td class="show-value">{{ $tenagaPendidik->pendidikan_terakhir ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="show-label">Alamat</td>
                    <td class="show-value">{{ $tenagaPendidik->alamat ?? '-' }}</td>
                </tr>
            </table>

            <h5 class="show-section-title">Kontak & Akun</h5>
            <table class="show-table show-table--lined">
                <tr>
                    <td class="show-label">Email</td>
                    <td class="show-value">{{ $tenagaPendidik->user->email }}</td>
                </tr>
                <tr>
                    <td class="show-label">Email Pemulihan</td>
                    <td class="show-value">{{ $tenagaPendidik->user->personal_email ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="show-label">No. Telepon</td>
                    <td class="show-value">{{ $tenagaPendidik->telepon ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="show-label">Username</td>
                    <td class="show-value">{{ $tenagaPendidik->user->username }}</td>
                </tr>
                <tr>
                    <td class="show-label">Cabang</td>
                    <td class="show-value">{{ $tenagaPendidik->user->cabang->nama_cabang ?? 'Pusat' }}</td>
                </tr>
            </table>
        </div>

        <div class="show-footer">
            <a href="{{ route('admin.users.tenaga-pendidik') }}" class="show-btn show-btn--secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <a href="{{ route('admin.users.edit-tenaga-pendidik', $tenagaPendidik->user_id) }}" class="show-btn show-btn--warning">
                <i class="fas fa-edit"></i>
                Edit Data
            </a>
        </div>
    </div>
</div>
@endsection
