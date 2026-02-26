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

@section('content')
<style>
.tp-show-footer {
    padding: 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
@media (max-width: 768px) {
    .tp-show-header-block {
        padding: 20px !important;
    }
    .tp-show-detail-body {
        padding: 16px !important;
    }
    .tp-show-footer {
        flex-direction: column;
        gap: 10px;
    }
    .tp-show-footer a {
        width: 100%;
        text-align: center;
        display: flex !important;
        justify-content: center;
        align-items: center;
        box-sizing: border-box;
    }
    .tp-show-table td {
        word-break: break-word;
    }
    .tp-show-table td:first-child {
        width: 40%;
        padding-right: 8px;
    }
}
</style>
<div style="max-width: 1000px; margin: 0 auto;">

    <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        {{-- Header Profil --}}
        <div class="tp-show-header-block" style="background: #f8fafc; padding: 30px; border-bottom: 1px solid #e2e8f0; text-align: center;">
            <div style="width: 100px; height: 100px; background: #e2e8f0; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #94a3b8; overflow: hidden;">
                @if($tenagaPendidik->foto)
                    <img src="{{ asset('storage/' . $tenagaPendidik->foto) }}" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <i class="fas fa-user"></i>
                @endif
            </div>
            <h3 style="margin: 0; color: #1e293b; font-size: 24px;">{{ $tenagaPendidik->user->name ?? $tenagaPendidik->nama_lengkap }}</h3>
            <div style="margin-top: 8px;">
                <span style="background: #e0f2fe; color: #0284c7; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600;">
                    {{ ucwords(str_replace('_', ' ', $tenagaPendidik->user->role)) }}
                </span>
                @if($tenagaPendidik->user->is_active)
                    <span style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; margin-left: 5px;">Aktif</span>
                @else
                    <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; margin-left: 5px;">Non-Aktif</span>
                @endif
            </div>
        </div>

        {{-- Detail Data --}}
        <div class="tp-show-detail-body" style="padding: 30px;">
            <h5 style="margin-bottom: 20px; color: #3b82f6; border-bottom: 2px solid #3b82f6; display: inline-block; padding-bottom: 5px;">Informasi Pribadi</h5>
            <table class="tp-show-table" style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <tr>
                    <td style="padding: 12px 0; color: #64748b; width: 40%; padding-right: 8px; border-bottom: 1px solid #f1f5f9;">NIP</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9; word-break: break-word;">{{ $tenagaPendidik->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; padding-right: 8px; border-bottom: 1px solid #f1f5f9;">Jenis Kelamin</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9; word-break: break-word;">{{ $tenagaPendidik->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; padding-right: 8px; border-bottom: 1px solid #f1f5f9;">Tgl. Lahir</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9; word-break: break-word;">{{ $tenagaPendidik->tempat_lahir }}, {{ \Carbon\Carbon::parse($tenagaPendidik->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; padding-right: 8px; border-bottom: 1px solid #f1f5f9;">Pendidikan</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9; word-break: break-word;">{{ $tenagaPendidik->pendidikan_terakhir ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; padding-right: 8px; border-bottom: 1px solid #f1f5f9;">Alamat</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9; word-break: break-word;">{{ $tenagaPendidik->alamat ?? '-' }}</td>
                </tr>
            </table>

            <h5 style="margin-bottom: 20px; color: #3b82f6; border-bottom: 2px solid #3b82f6; display: inline-block; padding-bottom: 5px;">Kontak & Akun</h5>
            <table class="tp-show-table" style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 12px 0; color: #64748b; width: 40%; padding-right: 8px; border-bottom: 1px solid #f1f5f9;">Email</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9; word-break: break-word;">{{ $tenagaPendidik->user->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; padding-right: 8px; border-bottom: 1px solid #f1f5f9;">No. Telepon</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9; word-break: break-word;">{{ $tenagaPendidik->telepon ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; padding-right: 8px; border-bottom: 1px solid #f1f5f9;">Username</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9; word-break: break-word;">{{ $tenagaPendidik->user->username }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; padding-right: 8px; border-bottom: 1px solid #f1f5f9;">Cabang</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9; word-break: break-word;">{{ $tenagaPendidik->user->cabang->nama_cabang ?? 'Pusat' }}</td>
                </tr>
            </table>
        </div>
        
        <div class="tp-show-footer">
            <a href="{{ route('admin.users.tenaga-pendidik') }}" style="background: #6b7280; color: white; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; justify-content: center;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('admin.users.edit-tenaga-pendidik', $tenagaPendidik->user_id) }}" style="background: #f59e0b; color: white; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; justify-content: center;">
                <i class="fas fa-edit"></i> Edit Data
            </a>
        </div>
    </div>
</div>
@endsection