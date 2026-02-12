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
<div style="max-width: 1000px; margin: 0 auto;">

    <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        {{-- Header Profil --}}
        <div style="background: #f8fafc; padding: 30px; border-bottom: 1px solid #e2e8f0; text-align: center;">
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
        <div style="padding: 30px;">
            <h5 style="margin-bottom: 20px; color: #3b82f6; border-bottom: 2px solid #3b82f6; display: inline-block; padding-bottom: 5px;">Informasi Pribadi</h5>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <tr>
                    <td style="padding: 12px 0; color: #64748b; width: 30%; border-bottom: 1px solid #f1f5f9;">NIP</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9;">{{ $tenagaPendidik->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">Jenis Kelamin</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9;">{{ $tenagaPendidik->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">Tempat, Tanggal Lahir</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9;">{{ $tenagaPendidik->tempat_lahir }}, {{ \Carbon\Carbon::parse($tenagaPendidik->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">Pendidikan Terakhir</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9;">{{ $tenagaPendidik->pendidikan_terakhir ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">Alamat</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9;">{{ $tenagaPendidik->alamat ?? '-' }}</td>
                </tr>
            </table>

            <h5 style="margin-bottom: 20px; color: #3b82f6; border-bottom: 2px solid #3b82f6; display: inline-block; padding-bottom: 5px;">Kontak & Akun</h5>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 12px 0; color: #64748b; width: 30%; border-bottom: 1px solid #f1f5f9;">Email</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9;">{{ $tenagaPendidik->user->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">No. Telepon</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9;">{{ $tenagaPendidik->telepon ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">Username</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9;">{{ $tenagaPendidik->user->username }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">Cabang Penempatan</td>
                    <td style="padding: 12px 0; font-weight: 500; border-bottom: 1px solid #f1f5f9;">{{ $tenagaPendidik->user->cabang->nama_cabang ?? 'Pusat' }}</td>
                </tr>
            </table>
        </div>
        
        <div style="padding: 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <a href="{{ route('admin.users.tenaga-pendidik') }}" style="background: #6b7280; color: white; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('admin.users.edit-tenaga-pendidik', $tenagaPendidik->user_id) }}" style="background: #f59e0b; color: white; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-block;">
                <i class="fas fa-edit"></i> Edit Data
            </a>
        </div>
    </div>
</div>
@endsection