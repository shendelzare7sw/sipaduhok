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

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">

    <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        {{-- Header Profil --}}
        <div style="background: #f0fdf4; padding: 30px; border-bottom: 1px solid #dcfce7; text-align: center;">
            <div style="width: 100px; height: 100px; background: #dcfce7; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #166534; overflow: hidden;">
                @if($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <i class="fas fa-user-graduate"></i>
                @endif
            </div>
            <h3 style="margin: 0; color: #166534; font-size: 24px;">{{ $siswa->nama_lengkap }}</h3>
            <div style="margin-top: 5px; font-weight: 500; color: #15803d;">
                {{ $siswa->nis }} / {{ $siswa->nisn }}
            </div>
            <div style="margin-top: 10px;">
                <span style="background: white; border: 1px solid #166534; color: #166534; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600;">
                    {{ $siswa->kelas->nama_kelas ?? 'Belum Ada Kelas' }}
                </span>
                <span style="background: #166534; color: white; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; margin-left: 5px;">
                    Status: {{ ucfirst($siswa->status) }}
                </span>
            </div>
        </div>

        {{-- Detail Data --}}
        <div style="padding: 30px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                
                {{-- Kolom Kiri --}}
                <div>
                    <h5 style="margin-bottom: 20px; color: #3b82f6; border-bottom: 2px solid #3b82f6; display: inline-block; padding-bottom: 5px;">Data Akademik</h5>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; width: 40%;">NIS</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->nis ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b;">NISN</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->nisn }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b;">Tanggal Masuk</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ \Carbon\Carbon::parse($siswa->tanggal_masuk)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b;">Cabang</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->cabang->nama_cabang ?? '-' }}</td>
                        </tr>
                    </table>

                    <h5 style="margin-bottom: 20px; color: #3b82f6; border-bottom: 2px solid #3b82f6; display: inline-block; padding-bottom: 5px;">Data Pribadi</h5>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; width: 40%;">Jenis Kelamin</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b;">Tempat Lahir</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->tempat_lahir }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b;">Tanggal Lahir</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b;">Alamat</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->alamat }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Kolom Kanan --}}
                <div>
                    <h5 style="margin-bottom: 20px; color: #3b82f6; border-bottom: 2px solid #3b82f6; display: inline-block; padding-bottom: 5px;">Data Orang Tua</h5>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; width: 40%;">Nama Ayah</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->nama_ayah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b;">Nama Ibu</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->nama_ibu ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b;">No. Telepon Ortu</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->telepon_orangtua ?? '-' }}</td>
                        </tr>
                    </table>

                    <h5 style="margin-bottom: 20px; color: #3b82f6; border-bottom: 2px solid #3b82f6; display: inline-block; padding-bottom: 5px;">Informasi Akun</h5>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; width: 40%;">Username</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->user->username }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b;">Email</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->user->email }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div style="padding: 20px; background: #f0fdf4; border-top: 1px solid #dcfce7; display: flex; justify-content: space-between; align-items: center;">
            <a href="{{ route('admin.users.siswa') }}" style="background: #6b7280; color: white; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('admin.users.edit-siswa', $siswa->id) }}" style="background: #f59e0b; color: white; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-block;">
                <i class="fas fa-edit"></i> Edit Data
            </a>
        </div>
    </div>
</div>
@endsection