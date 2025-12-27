@extends('layouts.lms')

@section('title', 'Daftar Guru')
@section('page-title', 'Daftar Guru Pengajar')
@section('page-subtitle', 'Informasi kontak guru mata pelajaran')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
<div class="card-custom">
    <div class="card-header-custom">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-person-video3 me-2"></i>Daftar Guru Pengajar
        </h6>
    </div>

    @if($guruPengajar->count() > 0)
        <div style="padding: 24px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
                @foreach($guruPengajar as $guru)
                <div class="card-custom" style="border: 1px solid #e5e7eb; transition: all 0.3s;"
                     onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.12)'"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">
                    <div style="text-align: center; padding: 24px; border-bottom: 1px solid #e5e7eb; background: linear-gradient(135deg, #f8f9fa, #ffffff);">
                        @if($guru->foto)
                            <img src="{{ asset('storage/' . $guru->foto) }}" 
                                 alt="{{ $guru->nama_lengkap }}" 
                                 style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 16px; border: 4px solid white; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        @else
                            <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 36px; font-weight: 700; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                {{ strtoupper(substr($guru->nama_lengkap, 0, 1)) }}
                            </div>
                        @endif
                        
                        <h6 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 700; color: #1a1a1a;">
                            {{ $guru->nama_lengkap }}
                        </h6>
                        
                        <div style="display: flex; flex-wrap: wrap; gap: 6px; justify-content: center; margin-top: 12px;">
                            @foreach($guru->guruKelas as $penugasan)
                                <span class="badge bg-primary" style="font-size: 11px; padding: 4px 10px;">
                                    {{ $penugasan->mataPelajaran->nama_mapel }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div style="padding: 20px;">
                        @if($guru->telepon)
                        <div style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: #e8f5e9; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-whatsapp" style="color: #25D366; font-size: 18px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 11px; color: #6c757d; margin-bottom: 2px;">WhatsApp</div>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $guru->telepon) }}" 
                                   target="_blank"
                                   style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 14px;">
                                    {{ $guru->telepon }}
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($guru->email)
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: #ffebee; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-envelope" style="color: #dc3545; font-size: 18px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 11px; color: #6c757d; margin-bottom: 2px;">Email</div>
                                <a href="mailto:{{ $guru->email }}" 
                                   style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 14px; word-break: break-all;">
                                    {{ $guru->email }}
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if(!$guru->telepon && !$guru->email)
                            <div style="text-align: center; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                                <i class="bi bi-info-circle" style="color: #6c757d; margin-right: 6px;"></i>
                                <span style="color: #6c757d; font-size: 13px;">Kontak tidak tersedia</span>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 80px 20px;">
            <i class="bi bi-people" style="font-size: 64px; color: #dee2e6; margin-bottom: 20px; display: block;"></i>
            <h3 style="color: #6c757d; margin-bottom: 10px; font-size: 20px;">Belum Ada Guru Pengajar</h3>
            <p style="color: #adb5bd; margin: 0;">Data guru pengajar belum tersedia untuk kelas Anda.</p>
        </div>
    @endif
</div>
@endsection