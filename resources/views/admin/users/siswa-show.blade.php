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
                        <tr>
                            <td style="padding: 10px 0; color: #64748b;">Agama</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->agama ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Kolom Kanan --}}
                <div>
                    <h5 style="margin-bottom: 20px; color: #3b82f6; border-bottom: 2px solid #3b82f6; display: inline-block; padding-bottom: 5px;">Data Orang Tua (Biodata)</h5>
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

                    <h5 style="margin-bottom: 20px; color: #3b82f6; border-bottom: 2px solid #3b82f6; display: inline-block; padding-bottom: 5px;">Informasi Akun Siswa</h5>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; width: 40%;">Username</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->user->username }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b;">Email</td>
                            <td style="padding: 10px 0; font-weight: 500;">{{ $siswa->user->email }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b;">Status Akun</td>
                            <td style="padding: 10px 0;">
                                <span style="background: {{ $siswa->user->is_active ? '#dcfce7' : '#fee2e2' }}; color: {{ $siswa->user->is_active ? '#166534' : '#991b1b' }}; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;">
                                    {{ $siswa->user->is_active ? '✓ Aktif' : '✗ Non-Aktif' }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    <h5 style="margin-bottom: 20px; color: #f59e0b; border-bottom: 2px solid #f59e0b; display: inline-block; padding-bottom: 5px;">Akun Orang Tua Terdaftar</h5>
                    @if($siswa->studentParents && $siswa->studentParents->count() > 0)
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            @foreach($siswa->studentParents as $sp)
                                <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 12px;">
                                    <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 8px;">
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; color: #92400e; margin-bottom: 4px;">
                                                <i class="fas fa-user" style="color: #f59e0b; margin-right: 6px;"></i>
                                                {{ $sp->parent->name }}
                                            </div>
                                            <div style="font-size: 12px; color: #78350f; margin-bottom: 2px;">
                                                <i class="fas fa-link" style="font-size: 10px; margin-right: 4px;"></i>
                                                {{ ucwords(str_replace('_', ' ', $sp->relationship)) }}
                                            </div>
                                        </div>
                                        @if($sp->is_primary)
                                            <span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600;">
                                                Kontak Utama
                                            </span>
                                        @endif
                                    </div>
                                    <div style="font-size: 12px; color: #78350f; display: flex; flex-direction: column; gap: 3px; padding-left: 22px;">
                                        <div>
                                            <i class="fas fa-at" style="width: 14px; font-size: 10px;"></i>
                                            {{ $sp->parent->username }}
                                        </div>
                                        <div>
                                            <i class="fas fa-envelope" style="width: 14px; font-size: 10px;"></i>
                                            {{ $sp->parent->email }}
                                        </div>
                                        <div>
                                            <i class="fas fa-circle" style="width: 14px; font-size: 6px; color: {{ $sp->parent->is_active ? '#10b981' : '#ef4444' }};"></i>
                                            {{ $sp->parent->is_active ? 'Akun Aktif' : 'Akun Non-Aktif' }}
                                        </div>
                                        @if($sp->can_access_academic)
                                            <div style="color: #3b82f6;">
                                                <i class="fas fa-check-circle" style="width: 14px; font-size: 10px;"></i>
                                                Dapat Akses Akademik
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="background: #fef3c7; border: 1px dashed #f59e0b; border-radius: 8px; padding: 16px; text-align: center;">
                            <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 20px; margin-bottom: 8px;"></i>
                            <div style="color: #92400e; font-size: 13px; font-weight: 500;">
                                Belum ada akun orang tua terdaftar
                            </div>
                            <small style="color: #92400e; font-size: 11px;">
                                Tambahkan melalui halaman edit siswa
                            </small>
                        </div>
                    @endif
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