@extends('layouts.sneat')

@section('title', 'Edit Jadwal Pelajaran')
@section('page-title', 'Edit Jadwal Pelajaran')
@section('page-subtitle', 'Perbarui jadwal pelajaran')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
.form-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.form-section-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
    color: #111827;
    border-left: 4px solid #3b82f6;
    padding-left: 12px;
}

.info-badge {
    background: #e0f2fe;
    border-left: 4px solid #0ea5e9;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 20px;
}

/* Chevron animation */
.fa-chevron-down {
    transition: transform 0.3s ease;
}

/* Istirahat collapse button hover effect */
[data-bs-toggle="collapse"]:hover {
    background-color: rgba(59, 130, 246, 0.1) !important;
    border-color: #3b82f6 !important;
}
</style>
@endsection

@section('content')
{{-- Error Messages --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

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
                        <label class="form-label">Kelas <span class="text-danger">*</span></label>
                        <select name="kelas_id" id="kelasSelect" class="form-select" required>
                            @foreach($kelasList as $kls)
                                <option value="{{ $kls->id }}" data-jenjang="{{ $kls->jenjang }}"
                                        {{ old('kelas_id', $jadwalPelajaran->kelas_id) == $kls->id ? 'selected' : '' }}>
                                    {{ $kls->nama_kelas }} - {{ $kls->cabang->nama_cabang }} ({{ $kls->jenjang }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Mata Pelajaran & Guru Section --}}
            <div class="form-section">
                <div class="form-section-title">Penugasan Mengajar</div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="mata_pelajaran_id" id="mapelSelect" class="form-select" required>
                            @foreach($mataPelajaranList as $mapel)
                                <option value="{{ $mapel->id }}" data-jenjang="{{ $mapel->jenjang }}"
                                        {{ old('mata_pelajaran_id', $jadwalPelajaran->mata_pelajaran_id) == $mapel->id ? 'selected' : '' }}>
                                    {{ $mapel->nama_mapel }} ({{ $mapel->jenjang }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guru Pengajar <small class="text-muted">(Opsional)</small></label>
                        <input type="hidden" id="guru_id" name="guru_id" value="{{ old('guru_id', $jadwalPelajaran->guru_id) }}">
                        <div class="guru-display" onclick="openGuruModal()" style="cursor: pointer; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 10px; background: white; transition: all 0.2s;">
                            @if($jadwalPelajaran->guru)
                                <div class="guru-info" style="display: flex; align-items: center; gap: 12px;">
                                    <div class="guru-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px;">
                                        {{ substr($jadwalPelajaran->guru->nama_lengkap, 0, 2) }}
                                    </div>
                                    <div class="guru-details">
                                        <div class="guru-name" style="font-weight: 600; color: #111827;">{{ $jadwalPelajaran->guru->nama_lengkap }}</div>
                                        <div class="guru-role" style="font-size: 12px; color: #6b7280;">{{ $jadwalPelajaran->guru->user->cabang->nama_cabang ?? '-' }}</div>
                                    </div>
                                </div>
                            @else
                                <div style="color: #f59e0b; font-style: italic;">
                                    <i class="fas fa-chalkboard-teacher"></i> Klik untuk memilih guru pengajar
                                </div>
                            @endif
                        </div>
                        <small class="text-muted">Guru bisa mengajar di banyak kelas. Kosongkan jika belum ditentukan.</small>
                    </div>
                </div>
            </div>

            {{-- Waktu Section --}}
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
                            <p class="mb-0 small">Silakan konfigurasi waktu istirahat terlebih dahulu di menu <a href="{{ route('admin.pengaturan-istirahat.index') }}" class="alert-link">Pengaturan Istirahat</a></p>
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
            <div class="d-flex justify-content-between align-items-center">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kelasSelect = document.getElementById('kelasSelect');
    const mapelSelect = document.getElementById('mapelSelect');
    const hariSelect = document.getElementById('hariSelect');
    const istirahatDesc = document.getElementById('istirahatDesc');
    const istirahatCollapse = document.getElementById('istirahatCollapse');

    // Animate chevron when collapse is toggled
    if (istirahatCollapse) {
        const collapseBtn = document.querySelector('[data-bs-target="#istirahatCollapse"]');
        const chevronIcon = collapseBtn ? collapseBtn.querySelector('.fa-chevron-down') : null;

        istirahatCollapse.addEventListener('show.bs.collapse', function() {
            if (chevronIcon) chevronIcon.style.transform = 'rotate(180deg)';
        });

        istirahatCollapse.addEventListener('hide.bs.collapse', function() {
            if (chevronIcon) chevronIcon.style.transform = 'rotate(0deg)';
        });
    }

    // Filter mata pelajaran berdasarkan jenjang kelas
    kelasSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const jenjang = selectedOption.getAttribute('data-jenjang');

        // Filter options
        Array.from(mapelSelect.options).forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }

            const mapelJenjang = option.getAttribute('data-jenjang');
            if (jenjang && mapelJenjang !== jenjang) {
                option.style.display = 'none';
            } else {
                option.style.display = 'block';
            }
        });

        // Update istirahat info
        filterIstirahatDisplay();
    });

    // Update istirahat info when hari changes
    hariSelect.addEventListener('change', function() {
        filterIstirahatDisplay();
    });

    // Trigger filter on page load
    kelasSelect.dispatchEvent(new Event('change'));
});

// Function to filter istirahat display based on jenjang and hari
function filterIstirahatDisplay() {
    const kelasSelect = document.getElementById('kelasSelect');
    const hariSelect = document.getElementById('hariSelect');
    const istirahatDesc = document.getElementById('istirahatDesc');
    const istirahatSummary = document.getElementById('istirahatSummary');
    const jenjangGroups = document.querySelectorAll('.jenjang-group');

    const selectedKelasOption = kelasSelect.options[kelasSelect.selectedIndex];
    const selectedJenjang = selectedKelasOption ? selectedKelasOption.getAttribute('data-jenjang') : null;
    const selectedHari = hariSelect.value;

    let visibleTotalCount = 0;

    // If both selected, show only relevant istirahat
    if (selectedJenjang && selectedHari) {
        istirahatDesc.textContent = `Waktu istirahat untuk ${selectedJenjang} pada hari ${selectedHari}:`;

        jenjangGroups.forEach(group => {
            const groupJenjang = group.getAttribute('data-jenjang');

            if (groupJenjang === selectedJenjang) {
                // Show this jenjang group, but filter by hari
                const items = group.querySelectorAll('.col-md-6');
                let visibleCount = 0;

                items.forEach(item => {
                    const hariText = item.querySelector('small.text-muted:last-child').textContent;
                    if (hariText.includes(selectedHari)) {
                        item.style.display = 'block';
                        visibleCount++;
                        visibleTotalCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Show group only if has visible items
                group.style.display = visibleCount > 0 ? 'block' : 'none';
            } else {
                group.style.display = 'none';
            }
        });

        // Update summary
        if (istirahatSummary) {
            if (visibleTotalCount > 0) {
                istirahatSummary.textContent = `(${visibleTotalCount} istirahat pada ${selectedHari})`;
            } else {
                istirahatSummary.textContent = `(Tidak ada istirahat pada ${selectedHari})`;
            }
        }
    }
    // If only jenjang selected
    else if (selectedJenjang) {
        istirahatDesc.textContent = `Waktu istirahat untuk ${selectedJenjang}. Pilih hari untuk melihat istirahat yang lebih spesifik.`;

        jenjangGroups.forEach(group => {
            const groupJenjang = group.getAttribute('data-jenjang');
            if (groupJenjang === selectedJenjang) {
                group.style.display = 'block';
                // Show all items in this jenjang
                const items = group.querySelectorAll('.col-md-6');
                items.forEach(item => {
                    item.style.display = 'block';
                    visibleTotalCount++;
                });
            } else {
                group.style.display = 'none';
            }
        });

        // Update summary
        if (istirahatSummary) {
            istirahatSummary.textContent = `(${visibleTotalCount} istirahat untuk ${selectedJenjang})`;
        }
    }
    // Show all if nothing selected
    else {
        istirahatDesc.textContent = 'Berikut adalah waktu istirahat yang telah dikonfigurasi. Pilih kelas dan hari untuk melihat istirahat yang relevan.';

        jenjangGroups.forEach(group => {
            group.style.display = 'block';
            const items = group.querySelectorAll('.col-md-6');
            items.forEach(item => {
                item.style.display = 'block';
                visibleTotalCount++;
            });
        });

        // Update summary
        if (istirahatSummary) {
            istirahatSummary.textContent = `(${visibleTotalCount} jadwal istirahat)`;
        }
    }
}

// Guru Modal Functions
let selectedGuruId = null;
let selectedGuruName = '';
let selectedGuruCabang = '';

function openGuruModal() {
    const modal = new bootstrap.Modal(document.getElementById('guruModal'));
    modal.show();
}

// Search functionality
document.getElementById('searchGuru').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const guruOptions = document.querySelectorAll('.guru-option-item');

    guruOptions.forEach(option => {
        const name = option.getAttribute('data-name');
        if (name.includes(searchTerm)) {
            option.style.display = 'flex';
        } else {
            option.style.display = 'none';
        }
    });
});

// Hover effect
document.querySelectorAll('.guru-option-item').forEach(option => {
    option.addEventListener('mouseenter', function() {
        this.style.background = '#f9fafb';
        this.style.borderColor = '#d1d5db';
    });
    option.addEventListener('mouseleave', function() {
        this.style.background = 'transparent';
        this.style.borderColor = 'transparent';
    });
});

function selectGuru(id, name, cabang) {
    setGuru(id, name, cabang);

    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('guruModal'));
    if (modal) modal.hide();
}

function setGuru(id, name, cabang) {
    document.getElementById('guru_id').value = id;

    // Update display
    const display = document.querySelector('.guru-display');
    display.innerHTML = `
        <div class="guru-info" style="display: flex; align-items: center; gap: 12px;">
            <div class="guru-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px;">
                ${name.substring(0, 2)}
            </div>
            <div class="guru-details">
                <div class="guru-name" style="font-weight: 600; color: #111827;">${name}</div>
                <div class="guru-role" style="font-size: 12px; color: #6b7280;">${cabang}</div>
            </div>
        </div>
    `;
}

// Remove guru button
document.getElementById('btnRemoveGuru').addEventListener('click', function() {
    document.getElementById('guru_id').value = '';

    // Update display
    const display = document.querySelector('.guru-display');
    display.innerHTML = `
        <div style="color: #f59e0b; font-style: italic;">
            <i class="fas fa-chalkboard-teacher"></i> Klik untuk memilih guru pengajar
        </div>
    `;

    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('guruModal'));
    if (modal) modal.hide();
});
</script>

{{-- Modal Pilih Guru --}}
<div class="modal fade" id="guruModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="border-bottom: 1px solid #e5e7eb; padding: 24px;">
                <h5 class="modal-title" style="font-weight: 600; color: #111827;">
                    <i class="fas fa-chalkboard-teacher" style="color: #10b981; margin-right: 10px;"></i>
                    Pilih Guru Pengajar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; color: #374151;">
                        <i class="fas fa-search" style="color: #9ca3af; margin-right: 6px;"></i>
                        Cari Guru
                    </label>
                    <input type="text" id="searchGuru" class="form-control" placeholder="Ketik nama guru..." style="border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; margin-bottom: 12px;">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; color: #374151;">Pilih Guru</label>
                    <div id="guruList" style="max-height: 350px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px;">
                        @foreach($guruList as $g)
                            <div class="guru-option-item"
                                 data-id="{{ $g->id }}"
                                 data-name="{{ strtolower($g->nama_lengkap) }}"
                                 onclick="selectGuru({{ $g->id }}, '{{ $g->nama_lengkap }}', '{{ $g->user->cabang->nama_cabang ?? '-' }}')"
                                 style="padding: 12px; border-radius: 8px; margin-bottom: 4px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 12px; border: 1px solid transparent;">
                                <div class="guru-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0;">
                                    {{ substr($g->nama_lengkap, 0, 2) }}
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #111827;">{{ $g->nama_lengkap }}</div>
                                    <div style="font-size: 12px; color: #6b7280;">{{ $g->user->cabang->nama_cabang ?? '-' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div style="padding: 12px; background: #dbeafe; border-radius: 8px; border-left: 4px solid #3b82f6;">
                    <div style="display: flex; gap: 8px; align-items: start;">
                        <i class="fas fa-info-circle" style="color: #3b82f6; margin-top: 2px;"></i>
                        <div style="font-size: 13px; color: #1e40af;">
                            Guru bisa mengajar di banyak kelas. Untuk menghapus guru, klik tombol "Hapus Guru" di bawah.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 20px 24px; gap: 12px;">
                <button type="button" id="btnRemoveGuru" class="btn" style="background: #fef2f2; color: #dc2626; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                    <i class="fas fa-times"></i> Hapus Guru
                </button>
                <button type="button" class="btn" data-bs-dismiss="modal" style="background: #f3f4f6; color: #374151; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#guruList::-webkit-scrollbar {
    width: 8px;
}

#guruList::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#guruList::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}

#guruList::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

.guru-display:hover {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}
</style>
@endsection
