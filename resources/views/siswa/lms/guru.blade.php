@extends('layouts.lms')

@section('title', 'Daftar Guru')
@section('page-title', 'Daftar Guru Pengajar')
@section('page-subtitle', 'Informasi kontak guru mata pelajaran')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/siswa/lms/guru.css'])
@endpush

@section('content')
<div class="siswa-lms-guru-page">
<div class="card-custom">
    <div class="card-header-custom">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-person-video3 me-2"></i>Daftar Guru Pengajar
        </h6>
    </div>

    @if($guruPengajar->count() > 0)
        <div class="teacher-list-body">
            <div class="teacher-grid">
                @foreach($guruPengajar as $guru)
                <div class="card-custom teacher-card">
                    <div class="teacher-card-header">
                        @if($guru->foto)
                            <img src="{{ asset('storage/' . $guru->foto) }}" 
                                 alt="{{ $guru->nama_lengkap }}" 
                                 class="teacher-photo">
                        @else
                            <div class="teacher-avatar-fallback">
                                {{ strtoupper(substr($guru->nama_lengkap, 0, 1)) }}
                            </div>
                        @endif
                        
                        <h6 class="teacher-name">
                            {{ $guru->nama_lengkap }}
                        </h6>
                        
                        <div class="teacher-subject-list">
                            @foreach($guru->guruKelas as $penugasan)
                                <span class="badge bg-primary teacher-subject-badge">
                                    {{ $penugasan->mataPelajaran->nama_mapel }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="teacher-card-body">
                        @if($guru->telepon)
                        <div class="contact-item contact-item--spaced">
                            <div class="contact-icon contact-icon--whatsapp">
                                <i class="bi bi-whatsapp contact-icon-symbol contact-icon-symbol--whatsapp"></i>
                            </div>
                            <div class="contact-content">
                                <div class="contact-label">WhatsApp</div>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $guru->telepon) }}" 
                                   target="_blank"
                                   class="contact-link">
                                    {{ $guru->telepon }}
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($guru->email)
                        <div class="contact-item">
                            <div class="contact-icon contact-icon--email">
                                <i class="bi bi-envelope contact-icon-symbol contact-icon-symbol--email"></i>
                            </div>
                            <div class="contact-content">
                                <div class="contact-label">Email</div>
                                <a href="mailto:{{ $guru->email }}" 
                                   class="contact-link contact-link--email">
                                    {{ $guru->email }}
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if(!$guru->telepon && !$guru->email)
                            <div class="empty-contact">
                                <i class="bi bi-info-circle empty-contact-icon"></i>
                                <span class="empty-contact-text">Kontak tidak tersedia</span>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-people empty-state-icon"></i>
            <h3 class="empty-state-title">Belum Ada Guru Pengajar</h3>
            <p class="empty-state-description">Data guru pengajar belum tersedia untuk kelas Anda.</p>
        </div>
    @endif
</div>
</div>
@endsection
