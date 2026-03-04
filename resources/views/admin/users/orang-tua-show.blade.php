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

@section('content')
<style>
.ot-detail-two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}
.ot-show-footer {
    padding: 20px;
    background: #fffbeb;
    border-top: 1px solid #fef3c7;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.ot-show-footer-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
@media (max-width: 768px) {
    .ot-detail-two-col {
        grid-template-columns: 1fr;
        gap: 24px;
    }
    .ot-show-detail-body {
        padding: 16px !important;
    }
    .ot-show-header-block {
        padding: 20px !important;
    }
    .ot-show-footer {
        flex-direction: column;
        gap: 10px;
    }
    .ot-show-footer > a,
    .ot-show-footer-actions {
        width: 100%;
    }
    .ot-show-footer-actions {
        flex-direction: column;
        gap: 8px;
    }
    .ot-show-footer-actions a,
    .ot-show-footer-actions form,
    .ot-show-footer-actions button {
        width: 100%;
        box-sizing: border-box;
        text-align: center;
        display: flex !important;
        justify-content: center;
    }
    .ot-show-footer > a {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .show-ot-table td {
        word-break: break-word;
    }
}
</style>
<div style="max-width: 1000px; margin: 0 auto;">

    <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        {{-- Header Profil --}}
        <div class="ot-show-header-block" style="background: #fffbeb; padding: 30px; border-bottom: 1px solid #fef3c7; text-align: center;">
            <div style="width: 100px; height: 100px; background: #fef3c7; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #92400e;">
                <i class="fas fa-user-friends"></i>
            </div>
            <h3 style="margin: 0; color: #92400e; font-size: 24px;">{{ $orangTua->name }}</h3>
            <div style="margin-top: 5px; font-weight: 500; color: #b45309;">
                {{ $orangTua->username }}
            </div>
            <div style="margin-top: 10px;">
                <span style="background: white; border: 1px solid #f59e0b; color: #f59e0b; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600;">
                    <i class="fas fa-user-friends"></i> Orang Tua
                </span>
                <span style="background: {{ $orangTua->is_active ? '#10b981' : '#ef4444' }}; color: white; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; margin-left: 5px;">
                    Status: {{ $orangTua->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        {{-- Detail Data --}}
        <div class="ot-show-detail-body" style="padding: 30px;">
            <div class="ot-detail-two-col">

                {{-- Kolom Kiri --}}
                <div>
                    <h5 style="margin-bottom: 20px; color: #3b82f6; border-bottom: 2px solid #3b82f6; display: inline-block; padding-bottom: 5px;">Informasi Akun</h5>
                    <table class="show-ot-table" style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; width: 40%; padding-right: 8px;">Nama Lengkap</td>
                            <td style="padding: 10px 0; font-weight: 500; word-break: break-word;">{{ $orangTua->name }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; padding-right: 8px;">Username</td>
                            <td style="padding: 10px 0; font-weight: 500; word-break: break-word; font-family: 'Courier New', monospace;">{{ $orangTua->username }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; padding-right: 8px;">Email</td>
                            <td style="padding: 10px 0; font-weight: 500; word-break: break-word;">{{ $orangTua->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; padding-right: 8px;">Email Pribadi</td>
                            <td style="padding: 10px 0; font-weight: 500; word-break: break-word;">{{ $orangTua->personal_email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; padding-right: 8px;">No. Telepon/WA</td>
                            <td style="padding: 10px 0; font-weight: 500; word-break: break-word;">
                                @if($orangTua->phone)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $orangTua->phone) }}" target="_blank" style="color: #10b981; text-decoration: none;">
                                        <i class="fab fa-whatsapp"></i> {{ $orangTua->phone }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; padding-right: 8px;">Status Akun</td>
                            <td style="padding: 10px 0;">
                                <span style="background: {{ $orangTua->is_active ? '#dcfce7' : '#fee2e2' }}; color: {{ $orangTua->is_active ? '#166534' : '#991b1b' }}; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;">
                                    {!! $orangTua->is_active ? '<i class="fas fa-check"></i> Aktif' : '<i class="fas fa-times"></i> Non-Aktif' !!}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; padding-right: 8px;">Terdaftar Sejak</td>
                            <td style="padding: 10px 0; font-weight: 500; word-break: break-word;">{{ \Carbon\Carbon::parse($orangTua->created_at)->locale('id')->translatedFormat('d F Y') }}</td>
                        </tr>
                    </table>

                    @if($orangTua->studentParents && $orangTua->studentParents->count() > 0)
                    <h5 style="margin-bottom: 20px; color: #3b82f6; border-bottom: 2px solid #3b82f6; display: inline-block; padding-bottom: 5px;">Informasi Tambahan</h5>
                    <table class="show-ot-table" style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; width: 40%; padding-right: 8px;">Total Anak</td>
                            <td style="padding: 10px 0; font-weight: 500; word-break: break-word;">{{ $orangTua->studentParents->count() }} Siswa</td>
                        </tr>
                        @php
                            $jenjangList = $orangTua->studentParents->pluck('siswa.kelas.jenjang')->filter()->unique();
                        @endphp
                        @if($jenjangList->count() > 0)
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; padding-right: 8px;">Jenjang Anak</td>
                            <td style="padding: 10px 0; font-weight: 500; word-break: break-word;">
                                @foreach($jenjangList as $jenjang)
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; margin-right: 4px;">
                                        {{ $jenjang }}
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                        @endif
                        @php
                            $cabangList = $orangTua->studentParents->pluck('siswa.cabang.nama_cabang')->filter()->unique();
                        @endphp
                        @if($cabangList->count() > 0)
                        <tr>
                            <td style="padding: 10px 0; color: #64748b; padding-right: 8px;">Cabang</td>
                            <td style="padding: 10px 0; font-weight: 500; word-break: break-word;">{{ $cabangList->join(', ') }}</td>
                        </tr>
                        @endif
                    </table>
                    @endif
                </div>

                {{-- Kolom Kanan --}}
                <div>
                    <h5 style="margin-bottom: 20px; color: #f59e0b; border-bottom: 2px solid #f59e0b; display: inline-block; padding-bottom: 5px;">Data Anak (Siswa)</h5>

                    @if($orangTua->studentParents && $orangTua->studentParents->count() > 0)
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            @foreach($orangTua->studentParents as $sp)
                                <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; position: relative;">
                                    {{-- Relationship Badge --}}
                                    <div style="position: absolute; top: 10px; right: 10px;">
                                        <span style="background: #eff6ff; color: #1e40af; padding: 3px 8px; border-radius: 6px; font-size: 10px; font-weight: 600;">
                                            {{ ucwords(str_replace('_', ' ', $sp->relationship)) }}
                                        </span>
                                    </div>

                                    {{-- Student Info --}}
                                    <div style="margin-bottom: 8px;">
                                        <div style="font-weight: 600; color: #111827; font-size: 15px; margin-bottom: 4px;">
                                            <i class="fas fa-user-graduate" style="color: #3b82f6; margin-right: 6px;"></i>
                                            {{ $sp->siswa->nama_lengkap }}
                                        </div>
                                        <div style="font-size: 12px; color: #64748b; margin-bottom: 6px;">
                                            <i class="fas fa-id-card" style="font-size: 10px; margin-right: 4px;"></i>
                                            NIS: {{ $sp->siswa->nis }} / NISN: {{ $sp->siswa->nisn }}
                                        </div>
                                    </div>

                                    {{-- Class & Branch Info --}}
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 8px;">
                                        @if($sp->siswa->kelas)
                                            <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                <i class="fas fa-school" style="font-size: 9px;"></i>
                                                {{ $sp->siswa->kelas->nama_kelas }}
                                            </span>
                                            <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                {{ $sp->siswa->kelas->jenjang }}
                                            </span>
                                        @endif
                                        @if($sp->siswa->cabang)
                                            <span style="background: #f3e8ff; color: #7c3aed; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                <i class="fas fa-building" style="font-size: 9px;"></i>
                                                {{ $sp->siswa->cabang->nama_cabang }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Permissions --}}
                                    <div style="font-size: 11px; color: #64748b; display: flex; gap: 12px; padding-top: 8px; border-top: 1px solid #e5e7eb;">
                                        @if($sp->is_primary)
                                            <span style="color: #10b981;">
                                                <i class="fas fa-star" style="font-size: 9px;"></i>
                                                Kontak Utama
                                            </span>
                                        @endif
                                        @if($sp->can_access_academic)
                                            <span style="color: #3b82f6;">
                                                <i class="fas fa-check-circle" style="font-size: 9px;"></i>
                                                Akses Akademik
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="background: #fef3c7; border: 1px dashed #f59e0b; border-radius: 8px; padding: 24px; text-align: center;">
                            <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 28px; margin-bottom: 12px;"></i>
                            <div style="color: #92400e; font-size: 14px; font-weight: 500; margin-bottom: 4px;">
                                Belum ada anak terdaftar
                            </div>
                            <small style="color: #92400e; font-size: 12px;">
                                Hubungkan akun orang tua ini dengan siswa melalui halaman edit siswa
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="ot-show-footer">
            <a href="{{ route('admin.users.orang-tua') }}" style="background: #6b7280; color: white; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; justify-content: center;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div class="ot-show-footer-actions">
                <a href="{{ route('admin.users.edit-orang-tua', $orangTua->id) }}" style="background: #f59e0b; color: white; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; justify-content: center;">
                    <i class="fas fa-edit"></i>
                    Edit Data
                </a>
                <form action="{{ route('admin.users.toggle-orang-tua-status', $orangTua->id) }}" method="POST" style="display: inline; margin: 0;">
                    @csrf
                    <button type="submit" style="background: {{ $orangTua->is_active ? '#dc2626' : '#10b981' }}; color: white; padding: 10px 24px; border-radius: 6px; border: none; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; justify-content: center; width: 100%;">
                        <i class="fas fa-{{ $orangTua->is_active ? 'ban' : 'check' }}"></i>
                        {{ $orangTua->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
