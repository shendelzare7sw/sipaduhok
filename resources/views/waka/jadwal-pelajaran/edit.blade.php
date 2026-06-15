@extends('layouts.sneat')

@section('title', 'Edit Jadwal Pelajaran')
@section('page-title', 'Edit Jadwal Pelajaran')
@section('page-subtitle', 'Perbarui jadwal pelajaran')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/waka/jadwal-pelajaran/form.css'])
@endsection

@section('content')
{{-- Error Messages --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="info-badge">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Info:</strong> Perubahan pada jadwal akan dicatat dalam history untuk audit trail.
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-edit text-primary me-2"></i>Form Edit Jadwal Pelajaran
        </h5>
    </div>

    <div class="card-body">
        <form action="{{ route('waka.jadwal-pelajaran.update', $jadwalPelajaran) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_multi_jenjang" id="isMultiJenjang" value="0">

            {{-- Tahun Ajaran & Kelas Section --}}
            <div class="form-section">
                <div class="form-section-title">Informasi Dasar</div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <select name="tahun_ajaran_id" class="form-select" required disabled>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $jadwalPelajaran->tahun_ajaran_id == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="tahun_ajaran_id" value="{{ $jadwalPelajaran->tahun_ajaran_id }}">
                        <small class="text-muted">Tahun ajaran tidak dapat diubah</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kelas (Bisa Pilih Lebih dari Satu) <span class="text-danger">*</span></label>
                        
                        {{-- Hidden Select for Form Submission & Logic Compatibility --}}
                        <select name="kelas_ids[]" id="kelasSelect" class="d-none" multiple required>
                            @php
                                $selectedKelasIds = old('kelas_ids', $jadwalPelajaran->kelas->pluck('id')->toArray());
                            @endphp
                            @foreach($kelasList as $kls)
                                <option value="{{ $kls->id }}" 
                                        data-jenjang="{{ $kls->jenjang }}" 
                                        data-cabang-id="{{ $kls->cabang_id }}"
                                        {{ in_array($kls->id, $selectedKelasIds) ? 'selected' : '' }}>
                                    {{ $kls->nama_kelas }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Trigger Box --}}
                        <div class="kelas-display" data-open-kelas-modal>
                            <div id="selectedKelasText" class="text-muted kelas-placeholder">
                                <i class="fas fa-school me-2"></i> Klik untuk memilih kelas...
                            </div>
                            <div id="selectedKelasChips" class="d-flex flex-wrap gap-2 mt-1 d-none">
                                {{-- Chips will appear here --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mata Pelajaran & Guru Section --}}
            <div class="form-section">
                <div class="form-section-title">Penugasan Mengajar</div>

                <div class="row">
                    {{-- Single Mapel Section (shown when all classes are same jenjang) --}}
                    <div class="col-md-6 mb-3" id="singleMapelSection">
                        <label class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="mata_pelajaran_id" id="mapelSelect" class="form-select">
                            @foreach($mataPelajaranList as $mapel)
                                <option value="{{ $mapel->id }}" data-jenjang="{{ $mapel->jenjang }}"
                                        {{ old('mata_pelajaran_id', $jadwalPelajaran->mata_pelajaran_id) == $mapel->id ? 'selected' : '' }}>
                                    {{ $mapel->nama_mapel }} ({{ $mapel->jenjang }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Multi Mapel Section (shown when classes span multiple jenjang) --}}
                    <div class="col-md-6 mb-3 multi-mapel-container" id="multiMapelContainer">
                        <label class="form-label">Mata Pelajaran Per Jenjang <span class="text-danger">*</span></label>
                        <div id="multiMapelSections"></div>
                        <small class="text-muted">Kelas dari jenjang berbeda terdeteksi. Pilih mapel untuk setiap jenjang.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guru Pengajar <small class="text-muted">(Opsional)</small></label>
                        <input type="hidden" id="guru_id" name="guru_id" value="{{ old('guru_id', $jadwalPelajaran->guru_id) }}">
                        <div class="guru-display" data-open-guru-modal>
                            @if($jadwalPelajaran->guru)
                                <div class="guru-info">
                                    <div class="guru-avatar">
                                        {{ substr($jadwalPelajaran->guru->nama_lengkap, 0, 2) }}
                                    </div>
                                    <div class="guru-details">
                                        <div class="guru-name">{{ $jadwalPelajaran->guru->nama_lengkap }}</div>
                                        <div class="guru-role">{{ $jadwalPelajaran->guru->user->cabang->nama_cabang ?? '-' }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="form-placeholder">
                                    <i class="fas fa-chalkboard-teacher"></i> Klik untuk memilih guru pengajar
                                </div>
                            @endif
                        </div>
                        <small class="text-muted">Guru bisa mengajar di banyak kelas. Kosongkan jika belum ditentukan.</small>
                    </div>
                </div>
            </div>
            

            <div class="form-section">
                <div class="form-section-title">Jadwal Waktu</div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hari <span class="text-danger">*</span></label>
                        <select name="hari" id="hariSelect" class="form-select" required>
                            @foreach($hariList as $hari)
                                <option value="{{ $hari }}"
                                        {{ old('hari', $jadwalPelajaran->hari) == $hari ? 'selected' : '' }}>
                                    {{ $hari }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai" class="form-control"
                               value="{{ old('jam_mulai', \Carbon\Carbon::parse($jadwalPelajaran->jam_mulai)->format('H:i')) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_selesai" class="form-control"
                               value="{{ old('jam_selesai', \Carbon\Carbon::parse($jadwalPelajaran->jam_selesai)->format('H:i')) }}" required>
                    </div>
                </div>

                {{-- Info Waktu Istirahat (Expandable) --}}
                @if($pengaturanIstirahat->count() > 0)
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-info w-100 d-flex align-items-center justify-content-between"
                            data-bs-toggle="collapse" data-bs-target="#istirahatCollapse" aria-expanded="false">
                        <span>
                            <i class="fas fa-coffee me-2"></i>
                            <strong>Lihat Waktu Istirahat</strong>
                            <small class="text-muted ms-2" id="istirahatSummary">({{ $pengaturanIstirahat->sum(fn($items) => $items->count()) }} jadwal istirahat)</small>
                        </span>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>

                    <div class="collapse mt-2" id="istirahatCollapse">
                        <div class="card border-info">
                            <div class="card-body bg-light">
                                <div class="d-flex align-items-start gap-2 mb-3">
                                    <i class="fas fa-info-circle mt-1 text-info"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-bold text-info">
                                            <i class="fas fa-coffee me-1"></i>Informasi Waktu Istirahat
                                        </h6>
                                        <p class="mb-2 small text-muted" id="istirahatDesc">
                                            Berikut adalah waktu istirahat yang telah dikonfigurasi. Pilih kelas dan hari untuk melihat istirahat yang relevan.
                                        </p>
                                    </div>
                                </div>

                                <div id="istirahatList">
                                    {{-- Show all istirahat by default --}}
                                    @foreach($pengaturanIstirahat as $jenjang => $istirahatItems)
                                        <div class="mb-3 jenjang-group" data-jenjang="{{ $jenjang }}">
                                            <div class="badge bg-primary mb-2">{{ $jenjang }}</div>
                                            <div class="row g-2">
                                                @foreach($istirahatItems as $item)
                                                    <div class="col-md-6">
                                                        <div class="p-2 bg-white rounded border border-info">
                                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                                <span class="badge {{ $item->urutan == 1 ? 'bg-warning' : 'bg-info' }} text-dark">
                                                                    Istirahat {{ $item->urutan }}
                                                                </span>
                                                                <strong class="text-dark">{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</strong>
                                                            </div>
                                                            <small class="text-muted d-block">{{ $item->nama_istirahat }}</small>
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar-day me-1"></i>
                                                                {{ is_array($item->hari_aktif) ? implode(', ', $item->hari_aktif) : $item->hari_aktif }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="alert alert-warning border-0 shadow-sm">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Belum ada pengaturan waktu istirahat</strong>
                            <p class="mb-0 small">Silakan konfigurasi waktu istirahat terlebih dahulu di menu <a href="{{ route('waka.pengaturan-istirahat.index') }}" class="alert-link">Pengaturan Istirahat</a></p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Keterangan <small class="text-muted">(Opsional)</small></label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan untuk jadwal ini">{{ old('keterangan', $jadwalPelajaran->keterangan) }}</textarea>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <a href="{{ route('waka.jadwal-pelajaran.index', ['tahun_ajaran_id' => $jadwalPelajaran->tahun_ajaran_id]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- History Section --}}
@if($jadwalPelajaran->histories->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-history text-info me-2"></i>Riwayat Perubahan
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Field</th>
                        <th>Nilai Lama</th>
                        <th>Nilai Baru</th>
                        <th>Keterangan</th>
                        <th>Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalPelajaran->histories->sortByDesc('changed_at') as $history)
                    <tr>
                        <td>{{ $history->changed_at->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ ucfirst(str_replace('_', ' ', $history->field_changed)) }}</strong></td>
                        <td>{{ $history->old_value }}</td>
                        <td>{{ $history->new_value }}</td>
                        <td>{{ $history->keterangan }}</td>
                        <td>{{ $history->changedBy->name ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
    @php
        $jadwalFormMapelData = $mataPelajaranList->map(fn($m) => [
            'id' => $m->id,
            'nama' => $m->nama_mapel,
            'jenjang' => $m->jenjang,
        ])->values();
    @endphp
    <div id="jadwalFormConfig"
        data-mapel-list="{{ $jadwalFormMapelData->toJson(JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_QUOT) }}"
        data-current-mapel-id="{{ $jadwalPelajaran->mata_pelajaran_id }}"
        data-current-mapel-jenjang="{{ $jadwalPelajaran->mataPelajaran->jenjang ?? '' }}"></div>
{{-- Modal Pilih Guru --}}
<div class="modal fade" id="guruModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content jp-modal-content">
            <div class="modal-header jp-modal-header">
                <h5 class="modal-title jp-modal-title">
                    <i class="fas fa-chalkboard-teacher jp-modal-icon-guru"></i>
                    Pilih Guru Pengajar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body jp-modal-body">
                <div class="mb-3">
                    <label class="form-label jp-modal-label">
                        <i class="fas fa-search jp-search-icon"></i>
                        Cari Guru
                    </label>
                    <input type="text" id="searchGuru" class="form-control jp-search-input" placeholder="Ketik nama guru...">
                </div>

                <div class="mb-3">
                    <label class="form-label jp-modal-label">Pilih Guru</label>
                    <div id="guruList" class="jp-scroll-list guru-list">
                        @foreach($guruList as $g)
                            <div class="guru-option-item"
                                 data-id="{{ $g->id }}"
                                 data-name="{{ strtolower($g->nama_lengkap) }}"
                                 data-display-name="{{ $g->nama_lengkap }}"
                                 data-cabang-id="{{ $g->user->cabang_id ?? '' }}"
                                 data-cabang-name="{{ $g->user->cabang->nama_cabang ?? '-' }}"
                                 data-visible-branch="true">
                                <div class="guru-avatar">
                                    {{ substr($g->nama_lengkap, 0, 2) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="guru-name">{{ $g->nama_lengkap }}</div>
                                    <div class="guru-role">{{ $g->user->cabang->nama_cabang ?? '-' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="guru-info-note">
                    <div class="guru-info-note-inner">
                        <i class="fas fa-info-circle"></i>
                        <div class="guru-info-note-text">
                            Guru bisa mengajar di banyak kelas. Untuk menghapus guru, klik tombol "Hapus Guru" di bawah.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer jp-modal-footer">
                <button type="button" id="btnRemoveGuru" class="btn btn-remove-guru" data-clear-guru>
                    <i class="fas fa-times"></i> Hapus Guru
                </button>
                <button type="button" class="btn btn-close-soft" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Pilih Kelas --}}
<div class="modal fade" id="kelasModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content jp-modal-content">
            <div class="modal-header jp-modal-header">
                <h5 class="modal-title jp-modal-title">
                    <i class="fas fa-school jp-modal-icon-kelas"></i>
                    Pilih Kelas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body jp-modal-body">
                {{-- Filters --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Filter Cabang</label>
                        <select id="filterCabang" class="form-select form-select-sm">
                            <option value="">Semua Cabang</option>
                            @foreach($kelasList->pluck('cabang.nama_cabang', 'cabang_id')->unique() as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Filter Jenjang</label>
                        <select id="filterJenjang" class="form-select form-select-sm">
                            <option value="">Semua Jenjang</option>
                            @php $jenjangs = ['KB','TKA','TKB','SD','SMP','SMA']; @endphp
                            @foreach($jenjangs as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Cari Kelas</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="searchKelas" class="form-control border-start-0" placeholder="Nama kelas...">
                        </div>
                    </div>
                </div>

                {{-- Kelas List Grid --}}
                <div class="row g-2 kelas-list-grid" id="kelasListGrid">
                    @foreach($kelasList as $kls)
                        <div class="col-md-6 kelas-item" 
                             data-cabang-id="{{ $kls->cabang_id }}" 
                             data-jenjang="{{ $kls->jenjang }}" 
                             data-name="{{ strtolower($kls->nama_kelas) }}">
                            <label class="d-flex align-items-center p-3 border rounded h-100 hover-bg-light kelas-option-label">
                                <input type="checkbox" class="form-check-input me-3 kelas-checkbox" 
                                       value="{{ $kls->id }}" 
                                       data-name="{{ $kls->nama_kelas }}"
                                       data-jenjang="{{ $kls->jenjang }}">
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark">{{ $kls->nama_kelas }}</div>
                                    <div class="small text-muted">
                                        <span class="badge bg-label-primary me-1">{{ $kls->jenjang }}</span>
                                        {{ $kls->cabang->nama_cabang }}
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-light jp-modal-footer-sm">
                <div class="me-auto text-muted small">
                    <span id="selectedCount">0</span> kelas dipilih
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" data-confirm-kelas>
                    <i class="fas fa-check me-1"></i> Terapkan
                </button>
            </div>
        </div>
    </div>
</div>
    @vite(['resources/js/waka/jadwal-pelajaran/form.js'])
@endsection

