@extends('layouts.sneat')

@section('title', 'Jadwal Pelajaran')
@section('page-title', 'Jadwal Pelajaran Kelas')
@section('page-subtitle', isset($kelas) ? $kelas->nama_kelas : '')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
/* Sama dengan admin show.blade.php styles */
.schedule-grid {
    display: grid;
    grid-template-columns: 100px repeat(6, 1fr);
    gap: 2px;
    background: #e5e7eb;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}

.schedule-header {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    padding: 16px 12px;
    font-weight: 600;
    text-align: center;
    font-size: 14px;
}

.schedule-time {
    background: #f9fafb;
    padding: 12px;
    font-weight: 600;
    font-size: 13px;
    color: #6b7280;
    text-align: center;
    font-family: 'Monaco', 'Consolas', monospace;
}

.schedule-cell {
    background: white;
    padding: 12px;
    min-height: 80px;
}

.schedule-cell.has-schedule {
    background: #dbeafe;
    border-left: 4px solid #3b82f6;
}

.schedule-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.schedule-mapel {
    font-weight: 700;
    color: #111827;
    font-size: 14px;
}

.schedule-guru {
    font-size: 12px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 6px;
}

.schedule-guru i {
    color: #3b82f6;
}

.schedule-time-badge {
    font-size: 11px;
    color: #9ca3af;
    font-family: 'Monaco', 'Consolas', monospace;
}

.schedule-empty {
    color: #9ca3af;
    font-style: italic;
    font-size: 12px;
    text-align: center;
}

.kelas-info-card {
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    color: white;
    padding: 24px;
    border-radius: 12px;
    margin-bottom: 24px;
}

.kelas-info-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 8px;
}

.kelas-info-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    font-size: 14px;
    opacity: 0.9;
}

.kelas-info-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

@media print {
    .no-print {
        display: none !important;
    }
    .schedule-grid {
        page-break-inside: avoid;
    }
}
</style>
@endsection

@section('content')
@if(isset($message))
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>{{ $message }}
    </div>
@endif

@if(isset($kelas) && $kelas)
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
            <span>{{ $kelas->tahunAjaran->nama_tahun_ajaran }}</span>
        </div>
        <div class="kelas-info-item">
            <i class="fas fa-user-tie"></i>
            <span>Wali Kelas: {{ $waliKelas->nama_lengkap }}</span>
        </div>
    </div>
</div>

{{-- Action Buttons --}}
<div class="card mb-4 no-print">
    <div class="card-body">
        <div class="d-flex justify-content-end">
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print me-1"></i> Cetak Jadwal
            </button>
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
                        <div class="schedule-cell has-schedule">
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

        @if($jadwalByHari->flatten()->isEmpty())
            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle me-2"></i>
                Belum ada jadwal pelajaran untuk kelas ini.
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
                <div style="width: 20px; height: 20px; background: #dbeafe; border-left: 4px solid #3b82f6; border-radius: 4px;"></div>
                <span>Jadwal Pelajaran</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div style="width: 20px; height: 20px; background: white; border: 1px solid #e5e7eb; border-radius: 4px;"></div>
                <span>Kosong</span>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
