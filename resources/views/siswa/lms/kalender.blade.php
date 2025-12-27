@extends('layouts.lms')

@section('title', 'Kalender Akademik')
@section('page-title', 'Kalender Akademik')
@section('page-subtitle', 'Lihat jadwal kegiatan dan agenda sekolah')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
<div class="card-custom">
    <div class="card-header-custom">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-calendar3 me-2"></i>Kalender Akademik Tahun Ini
        </h6>
    </div>

    @if($kalenderTahunan->isEmpty())
    <div style="text-align: center; padding: 80px 20px;">
        <i class="bi bi-calendar-x" style="font-size: 64px; color: #dee2e6; display: block; margin-bottom: 20px;"></i>
        <h3 style="color: #6c757d; margin-bottom: 8px; font-size: 20px;">Belum Ada Kegiatan</h3>
        <p style="color: #adb5bd; margin: 0;">Kalender akademik untuk tahun ini belum tersedia.</p>
    </div>
    @else
    <div style="padding: 24px;">
        @foreach($kalenderTahunan as $bulan => $kegiatanList)
            @php
                $bulanNama = \Carbon\Carbon::parse($bulan . '-01')->locale('id')->isoFormat('MMMM YYYY');
            @endphp
            
            <div style="margin-bottom: 40px;">
                <h5 style="color: var(--primary); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e0e0e0; font-weight: 700;">
                    <i class="bi bi-calendar-event me-2"></i>{{ $bulanNama }}
                </h5>
                
                <div style="display: grid; gap: 16px;">
                    @foreach($kegiatanList as $kegiatan)
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; border-left: 4px solid var(--primary); transition: all 0.2s;" 
                         onmouseover="this.style.background='#e7f3ff'; this.style.transform='translateX(4px)'"
                         onmouseout="this.style.background='#f8f9fa'; this.style.transform='translateX(0)'">
                        <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 12px;">
                            <div style="flex: 1; min-width: 250px;">
                                <h6 style="margin: 0 0 12px 0; color: #212529; font-weight: 600;">
                                    {{ $kegiatan->nama_kegiatan }}
                                </h6>
                                <div style="display: flex; gap: 20px; font-size: 13px; color: #6c757d; flex-wrap: wrap;">
                                    <span>
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ $kegiatan->tanggal_mulai->format('d M Y') }}
                                        @if($kegiatan->tanggal_selesai && $kegiatan->tanggal_selesai != $kegiatan->tanggal_mulai)
                                            - {{ $kegiatan->tanggal_selesai->format('d M Y') }}
                                        @endif
                                    </span>
                                    @if($kegiatan->waktu_mulai)
                                    <span>
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $kegiatan->waktu_mulai }}
                                        @if($kegiatan->waktu_selesai)
                                            - {{ $kegiatan->waktu_selesai }}
                                        @endif
                                    </span>
                                    @endif
                                </div>
                                @if($kegiatan->keterangan)
                                <p style="margin: 12px 0 0 0; font-size: 14px; color: #495057; line-height: 1.6;">
                                    {{ $kegiatan->keterangan }}
                                </p>
                                @endif
                            </div>
                            <span class="badge bg-info text-dark" style="font-size: 11px; padding: 6px 12px;">
                                {{ $kegiatan->getJenisLabelAttribute() }}
                            </span>
                        </div>
                        
                        @if($kegiatan->lampiran_surat)
                        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #dee2e6;">
                            <a href="{{ asset('storage/' . $kegiatan->lampiran_surat) }}" 
                               target="_blank" 
                               class="btn btn-sm btn-primary-custom">
                                <i class="bi bi-file-pdf me-1"></i>Lihat Surat
                            </a>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>
@endsection