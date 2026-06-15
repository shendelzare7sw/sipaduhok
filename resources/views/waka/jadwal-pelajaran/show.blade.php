@extends('layouts.sneat')

@section('title', 'Jadwal Pelajaran - ' . $kelas->nama_kelas)
@section('page-title', 'Jadwal Pelajaran')
@section('page-subtitle', $kelas->nama_kelas . ' - ' . $kelas->cabang->nama_cabang)

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
@vite(['resources/css/waka/jadwal-pelajaran/show.css'])
@endsection

@section('content')
{{-- Kelas Info Card --}}
<div class="kelas-info-card">
    <div class="kelas-info-title">{{ $kelas->nama_kelas }}</div>
    <div class="kelas-info-meta">
        <div class="kelas-info-item">
            <i class="fas fa-building"></i>
            <span>{{ $kelas->cabang->nama_cabang }}</span>
        </div>
        <div class="kelas-info-item">
            <i class="fas fa-graduation-cap"></i>
            <span>{{ $kelas->jenjang }}</span>
        </div>
        <div class="kelas-info-item">
            <i class="fas fa-calendar"></i>
            <span>{{ $currentTahunAjaran->nama_tahun_ajaran }}</span>
        </div>
        @if($kelas->waliKelas)
        <div class="kelas-info-item">
            <i class="fas fa-user-tie"></i>
            <span>Wali Kelas: {{ $kelas->waliKelas->nama_lengkap }}</span>
        </div>
        @endif
    </div>
</div>

{{-- Action Buttons --}}
<div class="card mb-4 no-print">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ route('waka.jadwal-pelajaran.index', ['tahun_ajaran_id' => $currentTahunAjaran->id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('waka.jadwal-pelajaran.create', ['tahun_ajaran_id' => $currentTahunAjaran->id, 'kelas_id' => $kelas->id]) }}"
                   class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah Jadwal
                </a>
                <a href="{{ route('waka.jadwal-pelajaran.preview-print', ['kelas' => $kelas->id, 'tahun_ajaran_id' => $currentTahunAjaran->id]) }}"
                   class="btn btn-info"
                   target="_blank">
                    <i class="fas fa-eye me-1"></i> Preview Cetak
                </a>
                <a href="{{ route('waka.jadwal-pelajaran.print', ['kelas' => $kelas->id, 'tahun_ajaran_id' => $currentTahunAjaran->id]) }}"
                   class="btn btn-success"
                   target="_blank">
                    <i class="fas fa-print me-1"></i> Cetak PDF
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Weekly Schedule Grid --}}
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-calendar-week text-primary me-2"></i>Jadwal Pelajaran Mingguan
        </h5>
    </div>
    <div class="card-body">
        <div class="schedule-scroll">
        <div class="schedule-grid">
            {{-- Header Row --}}
            <div class="schedule-header">Waktu</div>
            @foreach($hariList as $hari)
                <div class="schedule-header">{{ $hari }}</div>
            @endforeach

            {{-- Time Slots - Generate from 07:00 to 16:00 --}}
            @php
                $timeSlots = [];
                for ($hour = 7; $hour <= 15; $hour++) {
                    $timeSlots[] = sprintf('%02d:00', $hour);
                }
            @endphp

            @foreach($timeSlots as $time)
                {{-- Time Column --}}
                <div class="schedule-time">{{ $time }}</div>

                {{-- Schedule for each day --}}
                @foreach($hariList as $hari)
                    @php
                        // Find jadwal for this time slot
                        $jadwalForSlot = $jadwalByHari[$hari]->filter(function($j) use ($time) {
                            $jamMulai = \Carbon\Carbon::parse($j->jam_mulai)->format('H:00');
                            return $jamMulai == $time;
                        })->first();
                    @endphp

                    @if($jadwalForSlot)
                        <div class="schedule-cell has-schedule"
                             data-schedule-href="{{ route('waka.jadwal-pelajaran.edit', $jadwalForSlot) }}">
                            @if($jadwalForSlot->status == 'kosong')
                                <span class="status-chip status-kosong">KOSONG</span>
                            @endif

                            <div class="schedule-item">
                                <div class="schedule-mapel">{{ $jadwalForSlot->mataPelajaran->nama_mapel }}</div>
                                <div class="schedule-guru">
                                    <i class="fas fa-user"></i>
                                    <span>{{ $jadwalForSlot->guru ? $jadwalForSlot->guru->nama_lengkap : 'Belum ditentukan' }}</span>
                                </div>
                                <div class="schedule-time-badge">
                                    {{ \Carbon\Carbon::parse($jadwalForSlot->jam_mulai)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($jadwalForSlot->jam_selesai)->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="schedule-cell">
                            <div class="schedule-empty">-</div>
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>
        </div>

        @if($jadwalByHari->flatten()->isEmpty())
            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle me-2"></i>
                Belum ada jadwal pelajaran untuk kelas ini. Silakan tambahkan jadwal baru.
            </div>
        @endif
    </div>
</div>

{{-- Legend --}}
<div class="card mt-4 no-print">
    <div class="card-body">
        <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Keterangan</h6>
        <div class="d-flex gap-4 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <div class="legend-box legend-box-active"></div>
                <span>Jadwal Aktif</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="legend-box legend-box-empty"></div>
                <span>Kosong (Belum ada jadwal)</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="status-chip status-kosong">KOSONG</span>
                <span>Menunggu guru pengganti</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/waka/jadwal-pelajaran/show.js'])
@endsection
