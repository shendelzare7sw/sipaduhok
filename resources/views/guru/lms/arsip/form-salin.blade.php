@extends('layouts.sneat')

@php
    $kontenLabel = match($kontenType) {
        'materi' => 'Materi',
        'tugas' => 'Tugas',
        'latihan' => 'Latihan',
        default => 'Ujian',
    };
@endphp

@section('title', 'Salin ' . $kontenLabel . ' ke Kelas Aktif')
@section('page-title', 'Salin Konten Arsip')
@section('page-subtitle', 'Pilih kelas + mata pelajaran tujuan di TA aktif')

@section('sidebar-menu')
    @include('guru.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .salin-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 26px;
        max-width: 720px;
        margin: 0 auto;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .konten-info {
        background: #f8fafc;
        border-left: 4px solid #4361ee;
        padding: 14px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .konten-info .label { font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: .5px; }
    .konten-info .value { font-size: 16px; font-weight: 700; color: #1e293b; line-height: 1.4; }
    .konten-info .meta { font-size: 12px; color: #64748b; margin-top: 4px; }

    .form-section { margin-bottom: 18px; }
    .form-section label.main-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
    }
    .form-section .helper { font-size: 11px; color: #64748b; margin-top: 4px; }

    .target-list { display: grid; gap: 8px; }
    .target-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all .15s ease;
    }
    .target-item:hover { border-color: #4361ee; background: rgba(67,97,238,0.03); }
    .target-item input[type=radio] { margin: 0; }
    .target-item.selected { border-color: #4361ee; background: rgba(67,97,238,0.06); }
    .target-item .info { flex: 1; }
    .target-item .info .kelas { font-weight: 700; color: #1e293b; }
    .target-item .info .mapel { font-size: 12px; color: #64748b; }

    .switch-row {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        padding: 10px 14px;
        border-radius: 8px;
    }

    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-top: 22px;
        padding-top: 18px;
        border-top: 1px solid #e5e7eb;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('guru.lms.arsip.index') }}">Arsip LMS</a></li>
            <li class="breadcrumb-item active">Salin {{ $kontenLabel }}</li>
        </ol>
    </nav>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="salin-card">
        <h5 class="fw-bold mb-3"><i class="fas fa-copy me-2 text-primary"></i>Salin {{ $kontenLabel }} ke Kelas Aktif</h5>

        <div class="konten-info">
            <div class="label">Konten Sumber</div>
            <div class="value">{{ $previewTitle }}</div>
            <div class="meta">
                {{ $konten->kelas?->nama_kelas ?? '-' }}
                · {{ $konten->mataPelajaran?->nama_mapel ?? '-' }}
                · TA {{ $konten->kelas?->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
            </div>
        </div>

        @if($kelasMapelTujuan->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Anda belum ditugaskan ke kelas+mapel di TA aktif. Hubungi admin agar Anda di-assign sebagai guru pengajar terlebih dahulu.
            </div>
            <div class="text-end">
                <a href="{{ route('guru.lms.arsip.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        @else
            <form method="POST" action="{{ route('guru.lms.arsip.salin') }}">
                @csrf
                <input type="hidden" name="type" value="{{ $kontenType }}">
                <input type="hidden" name="sumber_id" value="{{ $kontenId }}">

                <div class="form-section">
                    <label class="main-label">Pilih Kelas + Mata Pelajaran Tujuan</label>
                    <div class="target-list">
                        @foreach($kelasMapelTujuan as $idx => $tujuan)
                            <label class="target-item {{ $idx === 0 ? 'selected' : '' }}" data-target>
                                <input type="radio"
                                    name="target"
                                    value="{{ $tujuan['kelas_id'] }}|{{ $tujuan['mata_pelajaran_id'] }}"
                                    @checked($idx === 0)
                                    required>
                                <input type="hidden" name="kelas_id" value="">
                                <input type="hidden" name="mata_pelajaran_id" value="">
                                <div class="info">
                                    <div class="kelas">
                                        <i class="fas fa-school me-1 text-primary"></i>{{ $tujuan['kelas']?->nama_kelas ?? '-' }}
                                        <small class="text-muted">({{ $tujuan['kelas']?->jenjang ?? '' }})</small>
                                    </div>
                                    <div class="mapel">
                                        <i class="fas fa-book me-1"></i>{{ $tujuan['mata_pelajaran']?->nama_mapel ?? '-' }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <div class="helper">Hanya kelas+mapel yang Anda ampu di TA aktif yang bisa dipilih.</div>
                </div>

                @if(in_array($kontenType, ['ujian', 'latihan']))
                    <div class="form-section">
                        <label class="main-label">Opsi Salin</label>
                        <label class="switch-row">
                            <input type="checkbox" name="sertakan_soal" value="1" checked>
                            <span><strong>Sertakan semua soal</strong> — duplikat seluruh soal beserta kunci jawaban (jawaban siswa lama TIDAK ikut tersalin).</span>
                        </label>
                    </div>
                @endif

                <div class="alert alert-info" style="font-size: 13px;">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Catatan:</strong> Setelah disalin, konten akan muncul di kelas tujuan dengan tanggal mulai = hari ini.
                    @if($kontenType === 'tugas')
                        Anda bisa edit deadline & detail lain setelah salin.
                    @elseif(in_array($kontenType, ['ujian', 'latihan']))
                        Status awal = nonaktif. Anda perlu aktifkan secara manual setelah cek/edit jadwal.
                    @endif
                    File materi/lampiran tidak diduplikasi di storage (gunakan path yang sama untuk hemat ruang).
                </div>

                <div class="action-bar">
                    <a href="{{ route('guru.lms.arsip.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-copy me-1"></i>Salin Sekarang
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const targets = document.querySelectorAll('[data-target]');
    targets.forEach(label => {
        label.addEventListener('click', () => {
            targets.forEach(l => l.classList.remove('selected'));
            label.classList.add('selected');
            const radio = label.querySelector('input[type=radio]');
            if (radio) {
                radio.checked = true;
                const [kelasId, mapelId] = (radio.value || '|').split('|');
                document.querySelector('input[name=kelas_id]').value = kelasId;
                document.querySelector('input[name=mata_pelajaran_id]').value = mapelId;
            }
        });
    });
    // Set initial values
    const initial = document.querySelector('[data-target] input[type=radio]:checked');
    if (initial) {
        const [kelasId, mapelId] = (initial.value || '|').split('|');
        document.querySelector('input[name=kelas_id]').value = kelasId;
        document.querySelector('input[name=mata_pelajaran_id]').value = mapelId;
    }
})();
</script>
@endpush
