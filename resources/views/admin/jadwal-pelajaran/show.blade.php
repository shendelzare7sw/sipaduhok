@extends('layouts.sneat')

@section('title', 'Jadwal Pelajaran - ' . $kelas->nama_kelas)
@section('page-title', 'Jadwal Pelajaran')
@section('page-subtitle', $kelas->nama_kelas . ' - ' . $kelas->cabang->nama_cabang)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
/* Schedule Grid */
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
    position: relative;
    transition: all 0.2s;
}

.schedule-cell:hover {
    background: #f9fafb;
    cursor: pointer;
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

.status-chip {
    position: absolute;
    top: 8px;
    right: 8px;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
}

.status-kosong {
    background: #fee2e2;
    color: #991b1b;
}

/* Kelas Info Card */
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

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    .schedule-grid {
        page-break-inside: avoid;
    }

    .schedule-cell {
        min-height: 60px;
    }
}
</style>
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
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.jadwal-pelajaran.index', ['tahun_ajaran_id' => $currentTahunAjaran->id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.jadwal-pelajaran.create', ['tahun_ajaran_id' => $currentTahunAjaran->id, 'kelas_id' => $kelas->id]) }}"
                   class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah Jadwal
                </a>
                <a href="{{ route('admin.jadwal-pelajaran.preview-print', ['kelas' => $kelas->id, 'tahun_ajaran_id' => $currentTahunAjaran->id]) }}"
                   class="btn btn-info"
                   target="_blank">
                    <i class="fas fa-eye me-1"></i> Preview Cetak
                </a>
                <a href="{{ route('admin.jadwal-pelajaran.print', ['kelas' => $kelas->id, 'tahun_ajaran_id' => $currentTahunAjaran->id]) }}"
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
                             onclick="location.href='{{ route('admin.jadwal-pelajaran.edit', $jadwalForSlot) }}'">
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
                <div style="width: 20px; height: 20px; background: #dbeafe; border-left: 4px solid #3b82f6; border-radius: 4px;"></div>
                <span>Jadwal Aktif</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div style="width: 20px; height: 20px; background: white; border: 1px solid #e5e7eb; border-radius: 4px;"></div>
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
<script>
// Optional: Add keyboard navigation or other interactions
document.addEventListener('DOMContentLoaded', function() {
    console.log('Jadwal pelajaran loaded for {{ $kelas->nama_kelas }}');
});
</script>
@endsection
