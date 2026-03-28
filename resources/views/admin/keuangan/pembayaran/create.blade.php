@extends('layouts.sneat')

@section('title', 'Input Pembayaran Tunai')
@section('page-title', 'Input Pembayaran Tunai')
@section('page-subtitle', 'Input pembayaran tunai di loket sekolah')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    @media (max-width: 768px) {
        .tagihan-item .d-flex.align-items-center.justify-content-between.flex-wrap > div {
            width: 100%;
        }
        .tagihan-item .d-flex.align-items-center.justify-content-between.flex-wrap > div[style*="width: 160px"] {
            width: 100% !important;
            margin-top: 8px;
        }
        .card-body .d-flex.justify-content-between.align-items-center {
            flex-direction: column;
            align-items: stretch !important;
            gap: 15px;
        }
        .card-body .d-flex.justify-content-between.align-items-center > a {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Header Card --}}
    <div class="card shadow mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">Form Pembayaran untuk {{ $siswa->nama_lengkap }}</h5>
                <a href="{{ route('admin.keuangan.tagihan.show', $siswa->id) }}" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Info Siswa Card --}}
    <div class="card shadow mb-4 border-start border-primary border-4">
        <div class="card-body bg-light">
            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted fw-bold">NISN</small>
                    <p class="mb-0 fw-bold">{{ $siswa->nisn }}</p>
                </div>
                <div class="col-md-3">
                    <small class="text-muted fw-bold">Kelas</small>
                    <p class="mb-0 fw-bold">{{ $siswa->kelas->nama_kelas ?? '-' }}</p>
                </div>
                <div class="col-md-3">
                    <small class="text-muted fw-bold">Cabang</small>
                    <p class="mb-0 fw-bold">{{ $siswa->cabang->nama_cabang ?? '-' }}</p>
                </div>
                <div class="col-md-3">
                    <small class="text-muted fw-bold">Sisa Tagihan</small>
                    <p class="mb-0 fw-bold text-danger fs-5">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Alert --}}
    <div class="alert alert-info border-start border-info border-4 shadow-sm mb-4">
        <div class="d-flex">
            <i class="fas fa-info-circle fa-lg me-2 mt-1"></i>
            <div>
                <strong>Catatan Penting:</strong>
                <ul class="mb-0 mt-2">
                    <li>Form ini khusus untuk <strong>pembayaran tunai di loket sekolah</strong></li>
                    <li>Pembayaran via <strong>Midtrans</strong> dan <strong>Transfer Bank</strong> akan otomatis tercatat dari sistem orang tua</li>
                    <li>Centang "Langsung validasi" jika uang tunai sudah diterima</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.keuangan.pembayaran.store', $siswa->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    {{-- Kolom Kiri: Pilih Tagihan --}}
                    <div class="col-lg-6 mb-4">
                        <h6 class="fw-bold text-gray-800 mb-3">Pilih Tagihan yang Dibayar</h6>

                        @if($tagihanBelumLunas->count() > 0)
                            <div class="border rounded shadow-sm" style="max-height: 400px; overflow-y: auto;">
                                @foreach($tagihanBelumLunas as $tagihan)
                                    <div class="d-flex align-items-start p-3 border-bottom tagihan-item" style="transition: background 0.2s;">
                                        <div class="d-flex align-items-center pt-2">
                                            <input type="checkbox" name="tagihan_ids[]" value="{{ $tagihan->id }}"
                                                   data-id="{{ $tagihan->id }}"
                                                   class="form-check-input tagihan-checkbox"
                                                   style="width: 20px; height: 20px;"
                                                   onchange="hitungTotal()">
                                        </div>
                                        <div class="flex-fill ms-3">
                                            <div class="fw-bold">
                                                {{ $jenisTagihan[$tagihan->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $tagihan->jenis_tagihan)) }}
                                            </div>
                                            <small class="text-muted d-block mb-2">
                                                Jatuh tempo: {{ $tagihan->tanggal_jatuh_tempo ? $tagihan->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}
                                            </small>
                                            
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <div>
                                                    <small class="text-muted">Sisa Tagihan:</small>
                                                    <div class="fw-bold text-danger">
                                                        Rp {{ number_format($tagihan->sisa_per_item, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                                
                                                <div style="width: 160px;">
                                                    <small class="text-muted d-block mb-1">Bayar Sejumlah:</small>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text bg-white border-end-0">Rp</span>
                                                        <input type="text" 
                                                               name="nominal_bayar[{{ $tagihan->id }}]" 
                                                               id="nominal-{{ $tagihan->id }}"
                                                               class="form-control form-control-sm text-end currency-input nominal-input"
                                                               value="{{ number_format($tagihan->sisa_per_item, 0, ',', '.') }}"
                                                               data-id="{{ $tagihan->id }}"
                                                               data-max="{{ $tagihan->sisa_per_item }}"
                                                               {{ $tagihan->jenis_tagihan === 'spp' ? 'readonly style=background-color:#f3f4f6;' : '' }}
                                                               onkeyup="formatAndCalculate(this)">
                                                    </div>
                                                    @if($tagihan->jenis_tagihan === 'spp')
                                                        <small class="text-muted fst-italic" style="font-size: 10px;">SPP tidak dapat dicicil</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('tagihan_ids')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        @else
                            <div class="text-center py-5 bg-success bg-opacity-10 rounded">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="text-success fw-bold mb-0">Semua tagihan sudah lunas!</p>
                            </div>
                        @endif
                    </div>

                    {{-- Kolom Kanan: Detail Pembayaran --}}
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-gray-800 mb-3">Detail Pembayaran Tunai</h6>

                        {{-- Metode Pembayaran - Fixed Tunai --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Metode Pembayaran</label>
                            <div class="form-control shadow-sm bg-light" style="font-weight: 600;">
                                <i class="fas fa-money-bill-wave text-success me-2"></i> Tunai / Loket (Cash)
                            </div>
                            <input type="hidden" name="metode_pembayaran" value="tunai">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_bayar" class="form-control shadow-sm"
                                   value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                            @error('tanggal_bayar')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Jumlah Bayar <span class="text-danger">*</span></label>
                            <input type="text" name="jumlah_bayar_display" id="jumlah_bayar_display" class="form-control shadow-sm"
                                   placeholder="Rp 0" readonly style="background: #f9fafb; font-weight: 600; font-size: 18px; color: #059669;">
                            <input type="hidden" name="jumlah_bayar" id="jumlah_bayar" value="{{ old('jumlah_bayar', 0) }}">
                            <small class="text-muted">Jumlah otomatis dihitung dari tagihan yang dipilih</small>
                            @error('jumlah_bayar')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan / Keterangan</label>
                            <textarea name="catatan" class="form-control shadow-sm" rows="3" placeholder="Contoh: Dibayar oleh {{ $siswa->waliMurid->name ?? 'wali murid' }}">{{ old('catatan') }}</textarea>
                            <small class="text-muted">Opsional. Tambahkan catatan jika diperlukan.</small>
                        </div>

                        <div class="mb-3">
                            <label class="d-flex align-items-center p-3 bg-success bg-opacity-10 rounded border border-success border-2" style="cursor: pointer;">
                                <input type="checkbox" name="validasi_langsung" value="1" class="form-check-input me-2" style="width: 20px; height: 20px;" checked>
                                <span class="text-success fw-bold">
                                    <i class="fas fa-check-circle"></i> Langsung validasi pembayaran ini
                                </span>
                            </label>
                            <small class="text-muted">Centang jika uang tunai sudah diterima langsung di loket.</small>
                        </div>

                        {{-- Summary --}}
                        <div class="bg-primary bg-opacity-10 p-3 rounded border border-primary border-2 mb-3">
                            <h6 class="small fw-bold text-primary mb-3"><i class="fas fa-chart-bar"></i> Ringkasan Pembayaran</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tagihan dipilih:</span>
                                <span id="count-selected" class="fw-bold text-primary">0 item</span>
                            </div>
                            <div class="d-flex justify-content-between fs-5 fw-bold text-primary pt-2 border-top border-primary border-2" style="border-style: dashed !important;">
                                <span>Total Bayar:</span>
                                <span id="total-display">Rp 0</span>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary w-100 shadow-sm fw-bold" {{ $tagihanBelumLunas->count() == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-save me-1"></i> Simpan Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Warning Alert --}}
    <div class="alert alert-warning border-start border-warning border-4 shadow-sm">
        <div class="d-flex">
            <i class="fas fa-exclamation-triangle fa-lg me-2 mt-1"></i>
            <div>
                <strong>Perhatian:</strong>
                <ul class="mb-0 mt-2">
                    <li>Pastikan jumlah pembayaran sudah benar sebelum menyimpan</li>
                    <li>Pastikan uang tunai sudah diterima sebelum memvalidasi pembayaran</li>
                    <li>Pembayaran yang belum divalidasi tidak akan mengubah status tagihan siswa</li>
                </ul>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@section('scripts')
<script>
    function parseCurrency(str) {
        return parseInt(str.replace(/\D/g, '')) || 0;
    }

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function formatAndCalculate(input) {
        // Format input value
        let value = parseCurrency(input.value);
        input.value = formatNumber(value);
        
        // Validation: Check against Max Sisa Tagihan
        let max = parseInt(input.dataset.max);
        
        if (value > max) {
            input.classList.add('is-invalid', 'text-danger');
        } else {
            input.classList.remove('is-invalid', 'text-danger');
        }
        
        // Auto check checkbox if user types > 0
        const id = input.dataset.id;
        const checkbox = document.querySelector(`input[name="tagihan_ids[]"][value="${id}"]`);
        
        // Only auto-check if value is valid (>0) AND not over limit? 
        // Or auto-check regardless so validation error is visible on checked items?
        // Let's auto-check so user realizes they selected it with error.
        if (value > 0 && !checkbox.checked) {
            checkbox.checked = true;
        }

        hitungTotal();
    }

    function hitungTotal() {
        let total = 0;
        let count = 0;
        let hasError = false;
        
        const checkboxes = document.querySelectorAll('.tagihan-checkbox:checked');
        
        // Check for any validation errors in inputs
        const inputs = document.querySelectorAll('.nominal-input');
        inputs.forEach(inp => {
            // Check error class
            if (inp.classList.contains('is-invalid')) {
                // Only consider error if the corresponding checkbox is CHECKED?
                // Or globally? If user types error but unchecks, should we allow save others?
                // Logic: If unchecked, input is ignored by backend logic usually.
                // So check if corresponding ID is checked.
                const id = inp.dataset.id;
                const cb = document.querySelector(`input[name="tagihan_ids[]"][value="${id}"]`);
                if(cb && cb.checked) {
                    hasError = true;
                }
            }
        });

        checkboxes.forEach(function(checkbox) {
            const id = checkbox.dataset.id;
            const inputNominal = document.getElementById(`nominal-${id}`);
            
            if (inputNominal) {
                total += parseCurrency(inputNominal.value);
                count++;
            }
        });

        document.getElementById('jumlah_bayar').value = total;
        document.getElementById('jumlah_bayar_display').value = 'Rp ' + formatNumber(total);
        document.getElementById('count-selected').textContent = count + ' item';
        document.getElementById('total-display').textContent = 'Rp ' + formatNumber(total);
        
        // Update button state (Disable if error exists)
        const btn = document.querySelector('button[type="submit"]');
        
        // Also show error message near button if hasError?
        // For now just disable.
        
        if (count > 0 && total > 0 && !hasError) {
            btn.removeAttribute('disabled');
        } else {
            btn.setAttribute('disabled', 'disabled');
        }
    }

    // Hover effect for tagihan items
    document.addEventListener('DOMContentLoaded', function() {
        const items = document.querySelectorAll('.tagihan-item');
        items.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.background = '#f3f4f6';
            });
            item.addEventListener('mouseleave', function() {
                this.style.background = 'transparent';
            });
        });
        
        // Initial Calculation
        hitungTotal();
    });
</script>
@endsection
