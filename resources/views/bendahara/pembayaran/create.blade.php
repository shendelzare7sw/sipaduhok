@extends('layouts.sneat')

@section('title', 'Input Pembayaran Manual')
@section('page-title', 'Input Pembayaran Manual')
@section('page-subtitle', 'Input pembayaran tunai atau transfer manual')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Header Card --}}
    <div class="card shadow mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">Form Pembayaran untuk {{ $siswa->nama_lengkap }}</h5>
                <a href="{{ route('bendahara.tagihan.show', $siswa->id) }}" class="btn btn-secondary shadow-sm">
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
                    <li><strong>Pembayaran via Midtrans</strong> akan otomatis tercatat saat siswa bayar online (tidak perlu input manual)</li>
                    <li><strong>Pembayaran Tunai/Loket</strong> dan <strong>Transfer Manual</strong> perlu diinput di form ini</li>
                    <li>Semua pembayaran manual perlu divalidasi sebelum status tagihan berubah</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('bendahara.pembayaran.store', $siswa->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    {{-- Kolom Kiri: Pilih Tagihan --}}
                    <div class="col-lg-6 mb-4">
                        <h6 class="fw-bold text-gray-800 mb-3">Pilih Tagihan yang Dibayar</h6>

                        @if($tagihanBelumLunas->count() > 0)
                            <div class="border rounded shadow-sm" style="max-height: 400px; overflow-y: auto;">
                                @foreach($tagihanBelumLunas as $tagihan)
                                    <label class="d-flex align-items-center p-3 border-bottom tagihan-item" style="cursor: pointer; transition: background 0.2s;">
                                        <input type="checkbox" name="tagihan_ids[]" value="{{ $tagihan->id }}"
                                               data-jumlah="{{ $tagihan->jumlah }}"
                                               class="form-check-input me-3"
                                               style="width: 20px; height: 20px;"
                                               onchange="hitungTotal()">
                                        <div class="flex-fill">
                                            <div class="fw-bold">
                                                {{ $jenisTagihan[$tagihan->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $tagihan->jenis_tagihan)) }}
                                            </div>
                                            <small class="text-muted">
                                                Jatuh tempo: {{ $tagihan->tanggal_jatuh_tempo ? $tagihan->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}
                                            </small>
                                        </div>
                                        <div class="fw-bold text-danger">
                                            Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}
                                        </div>
                                    </label>
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
                        <h6 class="fw-bold text-gray-800 mb-3">Detail Pembayaran Manual</h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select name="metode_pembayaran" class="form-select shadow-sm" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="tunai" {{ old('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>💵 Tunai / Loket (Cash)</option>
                                <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>🏦 Transfer Bank Manual</option>
                            </select>
                            <small class="text-muted fst-italic">
                                <i class="fas fa-info-circle"></i> Pembayaran via Midtrans online otomatis tercatat dari sistem
                            </small>
                            @error('metode_pembayaran')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
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
                            <label class="form-label fw-bold">Upload Bukti Pembayaran</label>
                            <input type="file" name="bukti_pembayaran" class="form-control shadow-sm" accept="image/*,.pdf">
                            <small class="text-muted">Format: JPG, PNG, PDF. Maksimal: 2MB. Upload foto struk atau bukti transfer.</small>
                            @error('bukti_pembayaran')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan / Keterangan</label>
                            <textarea name="catatan" class="form-control shadow-sm" rows="3" placeholder="Contoh: Dibayar oleh ibu siswa, via BCA">{{ old('catatan') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="d-flex align-items-center p-3 bg-success bg-opacity-10 rounded border border-success border-2" style="cursor: pointer;">
                                <input type="checkbox" name="validasi_langsung" value="1" class="form-check-input me-2" style="width: 20px; height: 20px;">
                                <span class="text-success fw-bold">
                                    <i class="fas fa-check-circle"></i> Langsung validasi pembayaran ini (untuk pembayaran tunai di loket)
                                </span>
                            </label>
                            <small class="text-muted">Centang jika uang tunai sudah diterima langsung di loket. Pembayaran transfer tetap perlu validasi manual.</small>
                        </div>

                        {{-- Summary --}}
                        <div class="bg-primary bg-opacity-10 p-3 rounded border border-primary border-2 mb-3">
                            <h6 class="small fw-bold text-primary mb-3">📊 Ringkasan Pembayaran</h6>
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
                    <li>Pastikan jumlah pembayaran dan metode sudah benar sebelum menyimpan</li>
                    <li>Bukti pembayaran sangat disarankan untuk diupload sebagai dokumentasi</li>
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
function hitungTotal() {
    let total = 0;
    let count = 0;
    const checkboxes = document.querySelectorAll('input[name="tagihan_ids[]"]:checked');

    checkboxes.forEach(function(checkbox) {
        total += parseInt(checkbox.dataset.jumlah);
        count++;
    });

    document.getElementById('jumlah_bayar').value = total;
    document.getElementById('jumlah_bayar_display').value = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('count-selected').textContent = count + ' item';
    document.getElementById('total-display').textContent = 'Rp ' + total.toLocaleString('id-ID');
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
});
</script>
@endsection
