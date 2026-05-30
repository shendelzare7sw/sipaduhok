@extends('layouts.sneat')

@section('title', 'Pratinjau Data dari Google Sheets')

@section('page-title', 'Pratinjau Data dari Google Sheets')
@section('page-subtitle', 'Verifikasi data sebelum melakukan impor')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .preview-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
    }

    .preview-header {
        background: white;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .preview-header h2 {
        margin: 0 0 8px 0;
        font-size: 24px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .preview-header p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
    }

    .preview-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 16px;
    }

    .info-item {
        padding: 12px;
        background: #f9fafb;
        border-radius: 8px;
    }

    .info-label {
        display: block;
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 4px;
        font-weight: 600;
    }

    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
    }

    .preview-table {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .table thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    .table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        white-space: nowrap;
    }

    .table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f3f4f6;
        word-break: break-word;
    }

    .table tbody tr:hover {
        background: #f9fafb;
    }

    .table tbody tr.selected {
        background: #eff6ff;
    }

    .table tbody tr.selected td {
        background: #eff6ff;
    }

    .checkbox-cell {
        width: 40px;
    }

    .checkbox-cell input {
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: #6b7280;
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }

    .empty-title {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
    }

    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-info {
        background: #eff6ff;
        border-left: 3px solid #3b82f6;
        color: #0c4a6e;
    }

    .alert-warning {
        background: #fef3c7;
        border-left: 3px solid #f59e0b;
        color: #92400e;
    }

    .alert-danger {
        background: #fef2f2;
        border-left: 3px solid #ef4444;
        color: #991b1b;
    }

    .actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding: 24px;
        background: white;
        border-top: 1px solid #e5e7eb;
        margin-top: 24px;
    }

    .btn {
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #d1d5db;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-success:hover {
        background: #059669;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .loading {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #e5e7eb;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 8px;
        vertical-align: middle;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .select-all-row {
        background: #f0fdf4;
        border-bottom: 2px solid #bbf7d0;
    }

    .cell-truncate {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .cell-truncate:hover {
        white-space: normal;
        word-break: break-word;
    }

    .stats {
        display: flex;
        gap: 20px;
        font-size: 13px;
        color: #6b7280;
        margin-top: 12px;
    }

    .stat {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .stat-label {
        color: #9ca3af;
    }

    .stat-value {
        font-weight: 600;
        color: #1a1a1a;
    }
</style>
@endsection

@section('content')
<div class="preview-container">
    <!-- Header -->
    <div class="preview-header">
        <h2>📊 Pratinjau Data dari Google Sheets</h2>
        <p>Tinjau data sebelum mengimpornya ke database.</p>
        <div class="preview-info">
            <div class="info-item">
                <span class="info-label">Modul</span>
                <span class="info-value">{{ $module }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Nama Sheet</span>
                <span class="info-value">{{ $sheet_name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Total Baris</span>
                <span class="info-value">{{ $total_rows }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Baris Pratinjau</span>
                <span class="info-value">{{ $preview_rows }}</span>
            </div>
        </div>
    </div>

    <!-- Warnings -->
    @if ($total_rows === 0)
        <div class="alert alert-warning">
            ⚠️ Spreadsheet kosong. Pastikan data berada di sheet yang benar.
        </div>
    @elseif ($total_rows > 1000)
        <div class="alert alert-info">
            ℹ️ Dataset besar terdeteksi ({{ $total_rows }} baris). Untuk menjaga performa, hanya {{ $preview_rows }} baris pertama yang ditampilkan.
        </div>
    @endif

    <!-- Data Table -->
    @if ($total_rows > 0)
        <div class="preview-table">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr class="select-all-row">
                            <th class="checkbox-cell">
                                <input type="checkbox" id="selectAll" 
                                       onchange="toggleSelectAll(this)" 
                                       title="Pilih semua baris">
                            </th>
                            @foreach ($headers as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $index => $row)
                            <tr class="data-row" data-row-index="{{ $index }}">
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="row-checkbox" 
                                           onchange="updateRowSelection()" 
                                           value="{{ $index }}">
                                </td>
                                @foreach ($row as $cell)
                                    <td>
                                        <span class="cell-truncate" title="{{ $cell ?? '' }}">
                                            {{ $cell ?? '(kosong)' }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 24px; background: #f9fafb; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280;">
                <div class="stats">
                    <div class="stat">
                        <span class="stat-label">Baris dipilih:</span>
                        <span class="stat-value" id="selectedCount">0</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">Total terlihat:</span>
                        <span class="stat-value">{{ count($rows) }}</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">Kolom:</span>
                        <span class="stat-value">{{ count($headers) }}</span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="preview-table">
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <div class="empty-title">Data Tidak Ditemukan</div>
                <p>Spreadsheet tampaknya kosong atau belum dikonfigurasi dengan benar.</p>
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="actions">
        <button type="button" class="btn btn-secondary" onclick="window.history.back();">
            Batal
        </button>

        @if ($total_rows > 0)
            <button type="button" class="btn btn-success" id="importBtn" onclick="confirmImport()">
                ✓ Impor Data Terpilih
            </button>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    const MODULE = '{{ $module }}';
    const TOTAL_ROWS = {{ $total_rows }};

    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checkbox.checked;
            updateRowHighlight(cb);
        });
        updateRowSelection();
    }

    function updateRowSelection() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        document.getElementById('selectedCount').textContent = checkboxes.length;

        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.row-checkbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        selectAllCheckbox.checked = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
    }

    function updateRowHighlight(checkbox) {
        const row = checkbox.closest('tr');
        if (checkbox.checked) {
            row.classList.add('selected');
        } else {
            row.classList.remove('selected');
        }
    }

    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', () => updateRowHighlight(cb));
    });

    function confirmImport() {
        const selected = document.querySelectorAll('.row-checkbox:checked').length;
        
        if (selected === 0) {
            alert('⚠️ Pilih minimal satu baris untuk diimpor');
            return;
        }

        const selectedRows = Array.from(document.querySelectorAll('.row-checkbox:checked'))
            .map(cb => parseInt(cb.value));

        if (confirm(`Impor ${selected} baris ke database?`)) {
            const importBtn = document.getElementById('importBtn');
            importBtn.disabled = true;
            importBtn.innerHTML = '<span class="loading"></span>Mengimpor...';

            fetch('{{ route("admin.google-sheets.pull", ":module") }}'.replace(':module', MODULE), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    selected_rows: selectedRows,
                    total_selected: selected
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert(`✓ Pekerjaan impor telah masuk antrean!\n\n${data.message}`);
                    window.location.href = '{{ route("admin.google-sheets.index") }}';
                } else {
                    alert(`✕ Kesalahan: ${data.message}`);
                    importBtn.disabled = false;
                    importBtn.innerHTML = '✓ Impor Data Terpilih';
                }
            })
            .catch(e => {
                alert(`✕ Kesalahan: ${e.message}`);
                importBtn.disabled = false;
                importBtn.innerHTML = '✓ Impor Data Terpilih';
            });
        }
    }

    // Initialize row selection count
    updateRowSelection();
</script>
@endsection
