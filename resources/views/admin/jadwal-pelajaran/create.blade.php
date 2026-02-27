@extends('layouts.sneat')

@section('title', 'Tambah Jadwal Pelajaran')
@section('page-title', 'Tambah Jadwal Pelajaran')
@section('page-subtitle', 'Buat jadwal pelajaran baru')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
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

        .time-input-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .conflict-warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            border-radius: 6px;
            margin-top: 12px;
            display: none;
        }

        .conflict-warning i {
            color: #f59e0b;
        }
    </style>
@endsection

@section('content')
{{-- Error Messages --}}
@section('content')
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

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-calendar-plus text-primary me-2"></i>Form Tambah Jadwal Pelajaran
            </h5>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.jadwal-pelajaran.store') }}" method="POST" id="jadwalForm">
                @csrf
                <input type="hidden" name="is_multi_jenjang" id="isMultiJenjang" value="0">

                {{-- Tahun Ajaran & Kelas Section --}}
                <div class="form-section">
                        <div class="form-section-title">Informasi Dasar</div>
    
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                <select name="tahun_ajaran_id" id="tahunAjaranSelect" class="form-select" required>
                                    @foreach($tahunAjarans as $ta)
                                        <option value="{{ $ta->id }}" {{ old('tahun_ajaran_id', $currentTahunAjaran?->id) == $ta->id ? 'selected' : '' }}>
                                            {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
    
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kelas (Bisa Pilih Lebih dari Satu) <span class="text-danger">*</span></label>
                                
                                {{-- Hidden Select for Form Submission & Logic Compatibility --}}
                                <select name="kelas_ids[]" id="kelasSelect" class="d-none" multiple required>
                                    @foreach($kelasList as $kls)
                                        <option value="{{ $kls->id }}" 
                                            data-jenjang="{{ $kls->jenjang }}" 
                                            data-cabang-id="{{ $kls->cabang_id }}"
                                            {{ in_array($kls->id, old('kelas_ids', [])) ? 'selected' : '' }}>
                                            {{ $kls->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>

                                {{-- Trigger Box --}}
                                <div class="kelas-display" onclick="openKelasModal()"
                                     style="cursor: pointer; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: white; min-height: 50px;">
                                    <div id="selectedKelasText" class="text-muted" style="font-style: italic;">
                                        <i class="fas fa-school me-2"></i> Klik untuk memilih kelas...
                                    </div>
                                    <div id="selectedKelasChips" class="d-flex flex-wrap gap-2 mt-1" style="display: none !important;">
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
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach($mataPelajaranList as $mapel)
                                    <option value="{{ $mapel->id }}" data-jenjang="{{ $mapel->jenjang }}" {{ old('mata_pelajaran_id') == $mapel->id ? 'selected' : '' }}>
                                        {{ $mapel->nama_mapel }} ({{ $mapel->jenjang }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Mata pelajaran akan difilter otomatis sesuai jenjang kelas</small>
                        </div>

                        {{-- Multi Mapel Section (shown when classes span multiple jenjang) --}}
                        <div class="col-md-6 mb-3" id="multiMapelContainer" style="display: none;">
                            <label class="form-label">Mata Pelajaran Per Jenjang <span class="text-danger">*</span></label>
                            <div id="multiMapelSections"></div>
                            <small class="text-muted">Kelas dari jenjang berbeda terdeteksi. Pilih mapel untuk setiap jenjang.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Guru Pengajar <small class="text-muted">(Opsional)</small></label>
                            <input type="hidden" id="guru_id" name="guru_id" value="{{ old('guru_id') }}">
                            <div class="guru-display" onclick="openGuruModal()"
                                style="cursor: pointer; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 10px; background: white; transition: all 0.2s;">
                                <div style="color: #f59e0b; font-style: italic;">
                                    <i class="fas fa-chalkboard-teacher"></i> Klik untuk memilih guru pengajar
                                </div>
                            </div>
                            <small class="text-muted">Guru bisa mengajar di banyak kelas. Kosongkan jika belum
                                ditentukan.</small>
                        </div>
                    </div>
                </div>


                <div class="form-section">
                    <div class="form-section-title">Jadwal Waktu</div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Hari <span class="text-danger">*</span></label>
                            <select name="hari" id="hariSelect" class="form-select" required>
                                <option value="">-- Pilih Hari --</option>
                                @foreach($hariList as $hari)
                                    <option value="{{ $hari }}" {{ old('hari') == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', '07:00') }}"
                                required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_selesai" class="form-control"
                                value="{{ old('jam_selesai', '08:30') }}" required>
                        </div>
                    </div>

                    {{-- Info Waktu Istirahat (Expandable) --}}
                    @if($pengaturanIstirahat->count() > 0)
                        <div class="mb-3">
                            <button type="button"
                                class="btn btn-outline-info w-100 d-flex align-items-center justify-content-between"
                                data-bs-toggle="collapse" data-bs-target="#istirahatCollapse" aria-expanded="false">
                                <span>
                                    <i class="fas fa-coffee me-2"></i>
                                    <strong>Lihat Waktu Istirahat</strong>
                                    <small class="text-muted ms-2"
                                        id="istirahatSummary">({{ $pengaturanIstirahat->sum(fn($items) => $items->count()) }}
                                        jadwal istirahat)</small>
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
                                                    Berikut adalah waktu istirahat yang telah dikonfigurasi. Pilih kelas dan
                                                    hari untuk melihat istirahat yang relevan.
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
                                                                        <span
                                                                            class="badge {{ $item->urutan == 1 ? 'bg-warning' : 'bg-info' }} text-dark">
                                                                            Istirahat {{ $item->urutan }}
                                                                        </span>
                                                                        <strong class="text-dark">{{ substr($item->jam_mulai, 0, 5) }} -
                                                                            {{ substr($item->jam_selesai, 0, 5) }}</strong>
                                                                    </div>
                                                                    <small
                                                                        class="text-muted d-block">{{ $item->nama_istirahat }}</small>
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
                                    <p class="mb-0 small">Silakan konfigurasi waktu istirahat terlebih dahulu di menu <a
                                            href="{{ route('admin.pengaturan-istirahat.index') }}" class="alert-link">Pengaturan
                                            Istirahat</a></p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Keterangan <small class="text-muted">(Opsional)</small></label>
                        <textarea name="keterangan" class="form-control" rows="2"
                            placeholder="Catatan tambahan untuk jadwal ini">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <a href="{{ route('admin.jadwal-pelajaran.index', request()->query()) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const kelasSelect = document.getElementById('kelasSelect');
            const mapelSelect = document.getElementById('mapelSelect');
            const hariSelect = document.getElementById('hariSelect');

            const istirahatDesc = document.getElementById('istirahatDesc');
            const istirahatCollapse = document.getElementById('istirahatCollapse');

            // Animate chevron when collapse is toggled
            if (istirahatCollapse) {
                const collapseBtn = document.querySelector('[data-bs-target="#istirahatCollapse"]');
                const chevronIcon = collapseBtn ? collapseBtn.querySelector('.fa-chevron-down') : null;

                istirahatCollapse.addEventListener('show.bs.collapse', function () {
                    if (chevronIcon) chevronIcon.style.transform = 'rotate(180deg)';
                });

                istirahatCollapse.addEventListener('hide.bs.collapse', function () {
                    if (chevronIcon) chevronIcon.style.transform = 'rotate(0deg)';
                });
            }

            // Filter mata pelajaran berdasarkan jenjang kelas
            kelasSelect.addEventListener('change', function () {
                renderMapelSections();

                // Update istirahat info
                filterIstirahatDisplay();

                // Filter Guru by Branch
                filterGuruByCabang();
            });
            



            // Filter Guru Logic
            function filterGuruByCabang() {
                const kelasSelect = document.getElementById('kelasSelect');
                const selectedOptions = Array.from(kelasSelect.selectedOptions);
                
                // Collect all branches from selected classes
                const branchIds = selectedOptions.map(opt => opt.getAttribute('data-cabang-id'));
                const uniqueBranches = [...new Set(branchIds)];

                const guruOptions = document.querySelectorAll('.guru-option-item');
                
                if (uniqueBranches.length === 0) {
                    guruOptions.forEach(el => el.setAttribute('data-visible-branch', 'true'));
                    return;
                }

                // Show guru if matches ANY of the branches (or ALL? "Combined Class" usually same branch).
                // Let's assume strict: Guru must match the branch.
                // If classes from different branches are selected (e.g. Branch A and Branch B),
                // Should we show gurus from A or B?
                // Probably allow both.
                
                guruOptions.forEach(el => {
                    const guruCabang = el.getAttribute('data-cabang-id');
                    if (!guruCabang || uniqueBranches.includes(guruCabang)) {
                         el.setAttribute('data-visible-branch', 'true');
                         el.style.display = 'flex';
                    } else {
                         el.setAttribute('data-visible-branch', 'false');
                         el.style.display = 'none';
                    }
                });
            }

            // Update istirahat info when hari changes
            hariSelect.addEventListener('change', function () {
                filterIstirahatDisplay();
            });

            // Trigger filters on page load
            if (kelasSelect.selectedOptions.length > 0) {
                 // Initialize UI Chips on Load
                 const selectedData = Array.from(kelasSelect.selectedOptions).map(opt => ({
                     id: opt.value,
                     name: opt.text.trim(),
                     jenjang: opt.getAttribute('data-jenjang')
                 }));
                 updateSelectedKelasUI(selectedData);
                 
                 // Trigger change details logic
                 kelasSelect.dispatchEvent(new Event('change'));
            }
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

        function filterGuruList() {
            const searchInput = document.getElementById('searchGuru');
            const searchTerm = searchInput.value.toLowerCase().trim();
            const guruOptions = document.querySelectorAll('.guru-option-item');

            guruOptions.forEach(option => {
                const name = option.getAttribute('data-name') || '';
                const isVisibleByBranch = option.getAttribute('data-visible-branch') !== 'false';
                
                if (isVisibleByBranch && (searchTerm === '' || name.includes(searchTerm))) {
                    option.style.display = 'flex';
                } else {
                    option.style.display = 'none';
                }
            });
        }

        function clearGuruSelection() {
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
        }

        /* --- KELAS MODAL LOGIC --- */
        function openKelasModal() {
            // Sync checkboxes with current select values
            const select = document.getElementById('kelasSelect');
            const selectedValues = Array.from(select.selectedOptions).map(opt => opt.value);
            
            document.querySelectorAll('.kelas-checkbox').forEach(cb => {
                cb.checked = selectedValues.includes(cb.value);
            });
            
            updateTempSelection(); // Update counter
            
            const modal = new bootstrap.Modal(document.getElementById('kelasModal'));
            modal.show();
        }

        function filterKelasList() {
            const cabangFilter = document.getElementById('filterCabang').value;
            const jenjangFilter = document.getElementById('filterJenjang').value;
            const searchText = document.getElementById('searchKelas').value.toLowerCase();
            
            document.querySelectorAll('.kelas-item').forEach(item => {
                const itemCabang = item.getAttribute('data-cabang-id');
                const itemJenjang = item.getAttribute('data-jenjang');
                const itemName = item.getAttribute('data-name');
                
                let visible = true;
                
                if (cabangFilter && itemCabang !== cabangFilter) visible = false;
                if (jenjangFilter && itemJenjang !== jenjangFilter) visible = false;
                if (searchText && !itemName.includes(searchText)) visible = false;
                
                item.style.display = visible ? 'block' : 'none';
            });
        }

        function updateTempSelection() {
            const count = document.querySelectorAll('.kelas-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
        }

        function confirmKelasSelection() {
            const select = document.getElementById('kelasSelect');
            const checkboxes = document.querySelectorAll('.kelas-checkbox:checked');
            
            // Update Select Options
            Array.from(select.options).forEach(opt => opt.selected = false);
            
            const selectedData = [];
            checkboxes.forEach(cb => {
                const opt = select.querySelector(`option[value="${cb.value}"]`);
                if (opt) opt.selected = true;
                
                selectedData.push({
                    id: cb.value,
                    name: cb.getAttribute('data-name'),
                    jenjang: cb.getAttribute('data-jenjang')
                });
            });
            
            // Update UI Display
            updateSelectedKelasUI(selectedData);
            
            // Trigger Change Event for Listeners (Student loading, etc)
            select.dispatchEvent(new Event('change'));
            
            // Close Modal
            bootstrap.Modal.getInstance(document.getElementById('kelasModal')).hide();
        }

        function updateSelectedKelasUI(data) {
            const container = document.querySelector('.kelas-display');
            const textPlaceholder = document.getElementById('selectedKelasText');
            const chipsContainer = document.getElementById('selectedKelasChips');

            if (data.length === 0) {
                textPlaceholder.style.display = 'block';
                chipsContainer.style.display = 'none';
                chipsContainer.innerHTML = '';
            } else {
                textPlaceholder.style.display = 'none';
                chipsContainer.style.display = 'flex';
                chipsContainer.innerHTML = '';

                data.forEach(item => {
                    const chip = document.createElement('div');
                    chip.className = 'badge bg-primary d-flex align-items-center p-2';
                    chip.style.fontSize = '12px';
                    chip.innerHTML = `
                        <i class="fas fa-school me-2"></i>
                        ${item.name}
                        <span class="ms-2 badge bg-white text-primary" style="font-size: 10px;">${item.jenjang}</span>
                    `;
                    chipsContainer.appendChild(chip);
                });
            }
        }

        // All mapel data for multi-jenjang rendering
        const _allMapelData = @json($mataPelajaranList->map(fn($m) => ['id' => $m->id, 'nama' => $m->nama_mapel, 'jenjang' => $m->jenjang]));

        function renderMapelSections() {
            const kelasSelect = document.getElementById('kelasSelect');
            const mapelSelect = document.getElementById('mapelSelect');
            const singleSection = document.getElementById('singleMapelSection');
            const multiContainer = document.getElementById('multiMapelContainer');
            const multiSections = document.getElementById('multiMapelSections');
            const isMultiJenjang = document.getElementById('isMultiJenjang');

            const selectedOptions = Array.from(kelasSelect.selectedOptions);

            if (selectedOptions.length === 0) {
                // No classes selected - show single mode, reset
                singleSection.style.display = '';
                multiContainer.style.display = 'none';
                isMultiJenjang.value = '0';
                mapelSelect.setAttribute('required', 'required');
                mapelSelect.setAttribute('name', 'mata_pelajaran_id');
                mapelSelect.value = '';
                Array.from(mapelSelect.options).forEach(opt => { opt.style.display = ''; });
                multiSections.innerHTML = '';
                return;
            }

            // Group selected classes by jenjang
            const jenjangMap = {};
            selectedOptions.forEach(opt => {
                const j = opt.getAttribute('data-jenjang');
                if (!jenjangMap[j]) jenjangMap[j] = [];
                jenjangMap[j].push(opt.text.trim());
            });

            const jenjangKeys = Object.keys(jenjangMap);

            if (jenjangKeys.length <= 1) {
                // Single jenjang - use normal single mapel dropdown
                singleSection.style.display = '';
                multiContainer.style.display = 'none';
                isMultiJenjang.value = '0';
                mapelSelect.setAttribute('required', 'required');
                mapelSelect.setAttribute('name', 'mata_pelajaran_id');
                multiSections.innerHTML = '';

                // Filter mapel options by this jenjang
                const jenjang = jenjangKeys[0] || null;
                mapelSelect.value = '';
                Array.from(mapelSelect.options).forEach(option => {
                    if (option.value === '') { option.style.display = 'block'; return; }
                    const mapelJenjang = option.getAttribute('data-jenjang');
                    option.style.display = (jenjang && mapelJenjang !== jenjang) ? 'none' : 'block';
                });
            } else {
                // Multi jenjang - show per-jenjang dropdowns
                singleSection.style.display = 'none';
                multiContainer.style.display = '';
                isMultiJenjang.value = '1';
                mapelSelect.removeAttribute('required');
                mapelSelect.removeAttribute('name');
                multiSections.innerHTML = '';

                jenjangKeys.forEach(jenjang => {
                    const kelasNames = jenjangMap[jenjang].join(', ');
                    const filteredMapel = _allMapelData.filter(m => m.jenjang === jenjang);

                    const section = document.createElement('div');
                    section.className = 'mb-3 p-3 border rounded bg-white';
                    section.innerHTML = `
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-info">${jenjang}</span>
                            <small class="text-muted">Kelas: ${kelasNames}</small>
                        </div>
                        <input type="hidden" name="mapel_per_jenjang[${jenjang}]" value="">
                        <select class="form-select" required onchange="this.previousElementSibling.value=this.value">
                            <option value="">-- Pilih Mapel ${jenjang} --</option>
                            ${filteredMapel.map(m => `<option value="${m.id}">${m.nama} (${m.jenjang})</option>`).join('')}
                        </select>
                    `;
                    multiSections.appendChild(section);
                });
            }
        }
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
                        <input type="text" id="searchGuru" class="form-control" placeholder="Ketik nama guru..."
                            oninput="filterGuruList()"
                            style="border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; margin-bottom: 12px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #374151;">Pilih Guru</label>
                        <div id="guruList"
                            style="max-height: 350px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px;">
                            @foreach($guruList as $g)
                                <div class="guru-option-item" data-id="{{ $g->id }}"
                                    data-name="{{ strtolower($g->nama_lengkap) }}"
                                    data-cabang-id="{{ $g->user->cabang_id ?? '' }}"
                                    data-visible-branch="true"
                                    onclick="selectGuru({{ $g->id }}, '{{ $g->nama_lengkap }}', '{{ $g->user->cabang->nama_cabang ?? '-' }}')"
                                    style="padding: 12px; border-radius: 8px; margin-bottom: 4px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 12px; border: 1px solid transparent;"
                                    onmouseenter="this.style.background='#f9fafb'; this.style.borderColor='#d1d5db';"
                                    onmouseleave="this.style.background='transparent'; this.style.borderColor='transparent';">
                                    <div class="guru-avatar"
                                        style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0;">
                                        {{ substr($g->nama_lengkap, 0, 2) }}
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #111827;">{{ $g->nama_lengkap }}</div>
                                        <div style="font-size: 12px; color: #6b7280;">{{ $g->user->cabang->nama_cabang ?? '-' }}
                                        </div>
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
                    <button type="button" id="btnRemoveGuru" class="btn" onclick="clearGuruSelection()"
                        style="background: #fef2f2; color: #dc2626; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-times"></i> Hapus Guru
                    </button>
                    <button type="button" class="btn" data-bs-dismiss="modal"
                        style="background: #f3f4f6; color: #374151; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Pilih Kelas --}}
    <div class="modal fade" id="kelasModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-header" style="border-bottom: 1px solid #e5e7eb; padding: 20px 24px;">
                    <h5 class="modal-title" style="font-weight: 600; color: #111827;">
                        <i class="fas fa-school" style="color: #3b82f6; margin-right: 10px;"></i>
                        Pilih Kelas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 24px;">
                    {{-- Filters --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Filter Cabang</label>
                            <select id="filterCabang" class="form-select form-select-sm" onchange="filterKelasList()">
                                <option value="">Semua Cabang</option>
                                @foreach($kelasList->pluck('cabang.nama_cabang', 'cabang_id')->unique() as $id => $nama)
                                    <option value="{{ $id }}">{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Filter Jenjang</label>
                            <select id="filterJenjang" class="form-select form-select-sm" onchange="filterKelasList()">
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
                                <input type="text" id="searchKelas" class="form-control border-start-0" placeholder="Nama kelas..." oninput="filterKelasList()">
                            </div>
                        </div>
                    </div>

                    {{-- Kelas List Grid --}}
                    <div class="row g-2" id="kelasListGrid" style="max-height: 400px; overflow-y: auto;">
                        @foreach($kelasList as $kls)
                            <div class="col-md-6 kelas-item" 
                                 data-cabang-id="{{ $kls->cabang_id }}" 
                                 data-jenjang="{{ $kls->jenjang }}" 
                                 data-name="{{ strtolower($kls->nama_kelas) }}">
                                <label class="d-flex align-items-center p-3 border rounded cursor-pointer h-100 hover-bg-light" style="cursor: pointer; transition: all 0.2s;">
                                    <input type="checkbox" class="form-check-input me-3 kelas-checkbox" 
                                           value="{{ $kls->id }}" 
                                           data-name="{{ $kls->nama_kelas }}"
                                           data-jenjang="{{ $kls->jenjang }}"
                                           style="width: 1.2em; height: 1.2em;"
                                           onclick="updateTempSelection()">
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
                <div class="modal-footer bg-light" style="border-top: 1px solid #e5e7eb; padding: 16px 24px;">
                    <div class="me-auto text-muted small">
                        <span id="selectedCount">0</span> kelas dipilih
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="confirmKelasSelection()">
                        <i class="fas fa-check me-1"></i> Terapkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize modal events after DOM is ready
        document.addEventListener('DOMContentLoaded', function () {
            const guruModal = document.getElementById('guruModal');
            if (guruModal) {
                guruModal.addEventListener('shown.bs.modal', function () {
                    const searchInput = document.getElementById('searchGuru');
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.focus();
                    }
                    // Reset all guru options to visible
                    document.querySelectorAll('.guru-option-item').forEach(option => {
                        option.style.display = 'flex';
                    });
                });
            }
        });
    </script>

    <style>
        /* Chevron animation */
        .fa-chevron-down {
            transition: transform 0.3s ease;
        }

        /* Istirahat collapse button hover effect */
        [data-bs-toggle="collapse"]:hover {
            background-color: rgba(59, 130, 246, 0.1) !important;
            border-color: #3b82f6 !important;
        }

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