{{-- resources/views/ketua/catatan/create.blade.php --}}

@extends('layouts.sneat')

@section('title', 'Kirim Catatan Baru')

@section('page-title', 'Kirim Catatan Baru')
@section('page-subtitle', 'Kirim catatan atau pesan kepada pengguna')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.card-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1a1a1a;
}

.card-body {
    padding: 24px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-label .required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #165fac;
    box-shadow: 0 0 0 3px rgba(22, 95, 172, 0.1);
}

textarea.form-control {
    min-height: 150px;
    resize: vertical;
}

.form-text {
    font-size: 12px;
    color: #6b7280;
    margin-top: 6px;
}

.invalid-feedback {
    color: #ef4444;
    font-size: 12px;
    margin-top: 6px;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.btn-primary {
    background: #165fac;
    color: white;
}

.btn-primary:hover {
    background: #124a87;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(22, 95, 172, 0.3);
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.form-actions {
    display: flex;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.radio-group {
    display: flex;
    gap: 16px;
}

.radio-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.radio-option:hover {
    border-color: #165fac;
    background: #f9fafb;
}

.radio-option input[type="radio"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.radio-option input[type="radio"]:checked ~ label {
    color: #165fac;
    font-weight: 600;
}

.info-box {
    padding: 16px;
    background: #eff6ff;
    border-left: 4px solid #3b82f6;
    border-radius: 8px;
    margin-bottom: 24px;
}

.info-box strong {
    color: #1e40af;
}

.hidden {
    display: none;
}

/* Individu Filter Panel */
.individu-filter-panel {
    border: 1px solid #d1d5db;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
}
.filter-row {
    display: flex;
    gap: 10px;
    padding: 12px 14px;
    background: #f3f4f6;
    border-bottom: 1px solid #e5e7eb;
    flex-wrap: wrap;
}
.filter-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 140px;
}
.filter-item.filter-search { flex: 1; min-width: 180px; }
.filter-label {
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.filter-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}
.btn-filter-action {
    padding: 4px 10px;
    font-size: 12px;
    background: #e5e7eb;
    color: #374151;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s;
}
.btn-filter-action:hover { background: #d1d5db; }
.btn-filter-action.btn-clear { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
.btn-filter-action.btn-clear:hover { background: #fecaca; }
.selected-count-badge {
    margin-left: auto;
    font-size: 12px;
    font-weight: 600;
    color: #165fac;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 20px;
    padding: 3px 10px;
}
.user-checklist {
    max-height: 280px;
    overflow-y: auto;
    padding: 8px 0;
}
.user-check-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 14px;
    cursor: pointer;
    transition: background 0.15s;
}
.user-check-item:hover { background: #f0f9ff; }
.user-check-item.hidden { display: none; }
.user-check-item input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: #165fac;
    flex-shrink: 0;
}
.user-check-label {
    flex: 1;
    font-size: 13px;
    color: #1f2937;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
}
.user-role-badge {
    font-size: 10px;
    padding: 2px 7px;
    border-radius: 20px;
    font-weight: 600;
    background: #e0e7ff;
    color: #3730a3;
    white-space: nowrap;
}
.user-cabang-text {
    font-size: 11px;
    color: #9ca3af;
    margin-left: auto;
}
.no-results-msg {
    padding: 20px;
    text-align: center;
    color: #9ca3af;
    font-size: 13px;
    display: none;
}
</style>
@endsection

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 0 1rem;">
    <div class="info-box">
        <strong><i class="fas fa-info-circle"></i> Informasi:</strong>
        Catatan yang Anda kirim dapat dilihat oleh penerima sesuai dengan tipe penerima yang dipilih.
        Catatan juga akan dikirim sebagai notifikasi kepada penerima.
    </div>

    <form action="{{ route('admin.catatan.store') }}" method="POST">
        @csrf
        
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-edit"></i> Form Kirim Catatan</h5>
            </div>
            <div class="card-body">
                {{-- Judul --}}
                <div class="form-group">
                    <label class="form-label">
                        Judul Catatan <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="judul" 
                        class="form-control @error('judul') is-invalid @enderror" 
                        value="{{ old('judul') }}"
                        placeholder="Masukkan judul catatan..."
                        required
                    >
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text">Buat judul yang jelas dan deskriptif</small>
                </div>

                {{-- Isi Catatan --}}
                <div class="form-group">
                    <label class="form-label">
                        Isi Catatan <span class="required">*</span>
                    </label>
                    <textarea 
                        name="isi_catatan" 
                        class="form-control @error('isi_catatan') is-invalid @enderror" 
                        placeholder="Tulis catatan Anda di sini..."
                        required
                    >{{ old('isi_catatan') }}</textarea>
                    @error('isi_catatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tipe Penerima --}}
                <div class="form-group">
                    <label class="form-label">
                        Kirim Kepada <span class="required">*</span>
                    </label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input 
                                type="radio" 
                                name="tipe_penerima" 
                                value="semua" 
                                {{ old('tipe_penerima') === 'semua' ? 'checked' : '' }}
                                required
                                onchange="togglePenerimaFields()"
                            >
                            <span><i class="fas fa-bullhorn"></i> Semua Pengguna</span>
                        </label>
                        <label class="radio-option">
                            <input 
                                type="radio" 
                                name="tipe_penerima" 
                                value="role" 
                                {{ old('tipe_penerima') === 'role' ? 'checked' : '' }}
                                onchange="togglePenerimaFields()"
                            >
                            <span><i class="fas fa-users"></i> Per Role</span>
                        </label>
                        <label class="radio-option">
                            <input 
                                type="radio" 
                                name="tipe_penerima" 
                                value="individu" 
                                {{ old('tipe_penerima') === 'individu' ? 'checked' : '' }}
                                onchange="togglePenerimaFields()"
                            >
                            <span><i class="fas fa-user"></i> Individu</span>
                        </label>
                    </div>
                    @error('tipe_penerima')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Role Penerima (conditional) --}}
                <div class="form-group hidden" id="roleField">
                    <label class="form-label">
                        Pilih Role <span class="required">*</span>
                    </label>
                    <select 
                        name="role_penerima" 
                        class="form-control @error('role_penerima') is-invalid @enderror"
                    >
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ old('role_penerima') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_penerima')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Penerima Individu (conditional) --}}
                <div class="form-group hidden" id="individuField">
                    <label class="form-label">
                        Pilih Penerima <span class="required">*</span>
                    </label>

                    {{-- Filter Panel --}}
                    <div class="individu-filter-panel">
                        <div class="filter-row">
                            <div class="filter-item">
                                <label class="filter-label">Role</label>
                                <select id="filterRole" class="form-control form-control-sm">
                                    <option value="">Semua Role</option>
                                    @foreach($roles as $roleKey => $roleLabel)
                                        <option value="{{ $roleKey }}">{{ $roleLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-item">
                                <label class="filter-label">Cabang</label>
                                <select id="filterCabang" class="form-control form-control-sm">
                                    <option value="">Semua Cabang</option>
                                    @foreach($cabangList as $cabang)
                                        <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-item filter-search">
                                <label class="filter-label">Cari</label>
                                <input type="text" id="filterSearch" class="form-control form-control-sm" placeholder="Cari nama...">
                            </div>
                        </div>
                        <div class="filter-actions">
                            <button type="button" class="btn-filter-action" id="selectAllBtn">
                                <i class="fas fa-check-square"></i> Pilih Semua
                            </button>
                            <button type="button" class="btn-filter-action btn-clear" id="clearAllBtn">
                                <i class="fas fa-square"></i> Hapus Semua
                            </button>
                            <span class="selected-count-badge" id="selectedCount">0 dipilih</span>
                        </div>

                        {{-- Checkbox User List --}}
                        <div class="user-checklist" id="userChecklist">
                            {{-- Diisi oleh JavaScript --}}
                        </div>
                    </div>

                    @error('penerima_ids')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text">Pilih satu atau lebih pengguna sebagai penerima</small>
                </div>

                {{-- Prioritas --}}
                <div class="form-group">
                    <label class="form-label">
                        Prioritas <span class="required">*</span>
                    </label>
                    <select
                        name="prioritas"
                        class="form-control @error('prioritas') is-invalid @enderror"
                        required
                    >
                        <option value="biasa" {{ old('prioritas') === 'biasa' ? 'selected' : '' }}>Biasa</option>
                        <option value="penting" {{ old('prioritas') === 'penting' ? 'selected' : '' }}>Penting</option>
                        <option value="mendesak" {{ old('prioritas') === 'mendesak' ? 'selected' : '' }}>Mendesak</option>
                    </select>
                    @error('prioritas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Kirim Catatan
                    </button>
                    <a href="{{ route('admin.catatan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// All users data from controller
const allUsers = @json($usersForIndividu ?? []);

function renderUserList(users) {
    const list = document.getElementById('userChecklist');
    list.innerHTML = '';

    if (users.length === 0) {
        list.innerHTML = '<div class="no-results-msg" style="display:block">Tidak ada pengguna ditemukan</div>';
        updateSelectedCount();
        return;
    }

    users.forEach(user => {
        const checked = document.querySelector(`input[name="penerima_ids[]"][value="${user.id}"]`)?.checked ?? false;
        const item = document.createElement('div');
        item.className = 'user-check-item';
        item.dataset.userId = user.id;
        item.dataset.role = user.role;
        item.dataset.cabangId = user.cabang_id ?? '';

        item.innerHTML = `
            <input type="checkbox" name="penerima_ids[]" value="${user.id}" id="user_${user.id}" ${checked ? 'checked' : ''}>
            <label class="user-check-label" for="user_${user.id}">
                <span>${escapeHtml(user.name)}</span>
                <span class="user-role-badge">${escapeHtml(user.role_label)}</span>
                <span class="user-cabang-text">${escapeHtml(user.cabang_name)}</span>
            </label>`;

        item.querySelector('input').addEventListener('change', updateSelectedCount);
        list.appendChild(item);
    });

    updateSelectedCount();
}

function escapeHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str ?? ''));
    return d.innerHTML;
}

function applyFilters() {
    const role = document.getElementById('filterRole').value;
    const cabang = document.getElementById('filterCabang').value;
    const search = document.getElementById('filterSearch').value.toLowerCase().trim();

    const filtered = allUsers.filter(u => {
        const matchRole = !role || u.role === role;
        const matchCabang = !cabang || String(u.cabang_id) === cabang;
        const matchSearch = !search || u.name.toLowerCase().includes(search);
        return matchRole && matchCabang && matchSearch;
    });

    // Preserve checked state from current DOM
    const checked = new Set(
        [...document.querySelectorAll('input[name="penerima_ids[]"]:checked')]
            .map(el => el.value)
    );

    renderUserList(filtered);

    // Restore checked state
    checked.forEach(id => {
        const el = document.querySelector(`input[name="penerima_ids[]"][value="${id}"]`);
        if (el) el.checked = true;
    });

    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('input[name="penerima_ids[]"]:checked').length;
    document.getElementById('selectedCount').textContent = count + ' dipilih';
}

function togglePenerimaFields() {
    const tipe = document.querySelector('input[name="tipe_penerima"]:checked')?.value;
    const roleField = document.getElementById('roleField');
    const individuField = document.getElementById('individuField');

    roleField.classList.add('hidden');
    individuField.classList.add('hidden');

    if (tipe === 'role') {
        roleField.classList.remove('hidden');
    } else if (tipe === 'individu') {
        individuField.classList.remove('hidden');
        if (document.getElementById('userChecklist').children.length === 0) {
            renderUserList(allUsers);
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    togglePenerimaFields();

    document.getElementById('filterRole').addEventListener('change', applyFilters);
    document.getElementById('filterCabang').addEventListener('change', applyFilters);
    document.getElementById('filterSearch').addEventListener('input', applyFilters);

    document.getElementById('selectAllBtn').addEventListener('click', function () {
        document.querySelectorAll('#userChecklist input[type="checkbox"]').forEach(el => {
            el.checked = true;
        });
        updateSelectedCount();
    });

    document.getElementById('clearAllBtn').addEventListener('click', function () {
        document.querySelectorAll('input[name="penerima_ids[]"]').forEach(el => {
            el.checked = false;
        });
        updateSelectedCount();
    });
});
</script>
@endsection