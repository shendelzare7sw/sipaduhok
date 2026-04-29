@extends('layouts.lms-guru')

@section('title', $tipeUjian === 'latihan' ? 'Buat Latihan' : 'Buat Ujian')
@section('page-title', $tipeUjian === 'latihan' ? 'Buat Latihan Baru' : 'Buat Ujian Baru')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="mb-3">
        @if($tipeUjian === 'latihan')
            <a href="{{ route('guru.lms.latihan.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            @else
                <a href="{{ route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            @endif
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-plus-circle me-2"></i>Form Buat {{ $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian' }}
        </div>
        <div class="p-4">
            <form action="{{ $tipeUjian === 'latihan' ? route('guru.lms.latihan.store', [$kelas->id, $mapel->id]) : route('guru.lms.ujian.store', [$kelas->id, $mapel->id]) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Judul {{ $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian' }} <span class="text-danger">*</span></label>
                    <input type="text" name="judul_ujian" class="form-control @error('judul_ujian') is-invalid @enderror"
                        value="{{ old('judul_ujian') }}" required>
                    @error('judul_ujian')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipe <span class="text-danger">*</span></label>
                        <select name="tipe_ujian" class="form-control @error('tipe_ujian') is-invalid @enderror" required @if($tipeUjian === 'latihan') disabled @endif>
                            @if($tipeUjian === 'latihan')
                                <option value="latihan" selected>Latihan</option>
                            @else
                                <option value="">-- Pilih Tipe --</option>
                                <optgroup label="Ulangan">
                                    <option value="ulangan_harian" {{ old('tipe_ujian') == 'ulangan_harian' ? 'selected' : '' }}>
                                        Ulangan Harian</option>
                                </optgroup>
                                <optgroup label="Semester Ganjil">
                                    <option value="pts_ganjil" {{ old('tipe_ujian') == 'pts_ganjil' ? 'selected' : '' }}>PTS
                                        Ganjil</option>
                                    <option value="pas_ganjil" {{ old('tipe_ujian') == 'pas_ganjil' ? 'selected' : '' }}>PAS
                                        Ganjil</option>
                                </optgroup>
                                <optgroup label="Semester Genap">
                                    <option value="pts_genap" {{ old('tipe_ujian') == 'pts_genap' ? 'selected' : '' }}>PTS Genap
                                    </option>
                                    <option value="pas_genap" {{ old('tipe_ujian') == 'pas_genap' ? 'selected' : '' }}>PAS Genap
                                    </option>
                                </optgroup>
                                @if($isTingkatAkhir)
                                <optgroup label="Ujian Kelulusan">
                                    <option value="to_1" {{ old('tipe_ujian') == 'to_1' ? 'selected' : '' }}>Try Out 1</option>
                                    <option value="to_2" {{ old('tipe_ujian') == 'to_2' ? 'selected' : '' }}>Try Out 2</option>
                                    <option value="to_3" {{ old('tipe_ujian') == 'to_3' ? 'selected' : '' }}>Try Out 3</option>
                                    <option value="upk" {{ old('tipe_ujian') == 'upk' ? 'selected' : '' }}>UPK</option>
                                    <option value="ujian_praktek" {{ old('tipe_ujian') == 'ujian_praktek' ? 'selected' : '' }}>Ujian Praktek</option>
                                </optgroup>
                                @endif
                            @endif
                        </select>
                        @if($tipeUjian === 'latihan')
                            <input type="hidden" name="tipe_ujian" value="latihan">
                        @endif
                        <small class="text-muted">PTS/PAS wajib memiliki 4 tipe soal: Pilihan Ganda, Benar/Salah, Isian,
                            Uraian</small>
                        @error('tipe_ujian')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_mulai"
                            class="form-control @error('tanggal_mulai') is-invalid @enderror"
                            value="{{ old('tanggal_mulai') }}" required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_selesai"
                            class="form-control @error('tanggal_selesai') is-invalid @enderror"
                            value="{{ old('tanggal_selesai') }}" required>
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Durasi {{ $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian' }} (menit) <span class="text-danger">*</span></label>
                    <input type="number" name="durasi_menit"
                        class="form-control @error('durasi_menit') is-invalid @enderror"
                        value="{{ old('durasi_menit', 90) }}" min="0" required>
                    <small class="text-muted">Isi 0 untuk waktu tidak terbatas.</small>
                    @error('durasi_menit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <h5 class="form-label fw-bold border-bottom pb-2">Pengaturan Penilaian & Pengulangan</h5>
                    <div class="p-3 border rounded bg-light">
                        <!-- Tampilkan Nilai -->
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="tampilkanNilai" name="tampilkan_nilai" value="1" checked>
                            <label class="form-check-label fw-bold" for="tampilkanNilai">Tampilkan Nilai ke Siswa</label>
                            <small class="text-muted d-block mt-1">Jika dinonaktifkan, siswa hanya akan melihat ucapan terima kasih setelah mengerjakan.</small>
                        </div>

                        <!-- Tampilkan Riwayat / Pembahasan -->
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="tampilkanRiwayat" name="tampilkan_riwayat" value="1" checked>
                            <label class="form-check-label fw-bold text-success" for="tampilkanRiwayat">Izinkan Siswa Melihat Riwayat & Jawaban Benar</label>
                            <small class="text-muted d-block mt-1">Siswa dapat melihat riwayat jawaban dan mencocokkannya dengan kunci jawaban setelah ujian selesai.</small>
                        </div>

                        <hr>

                        <!-- Pengulangan -->
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="bisaDiulang" name="bisa_diulang" value="1" onchange="toggleBatasPengulangan()">
                            <label class="form-check-label fw-bold text-primary" for="bisaDiulang">Bisa Dikerjakan Ulang (Pengulangan)</label>
                        </div>
                        
                        <div id="batasPengulanganContainer" style="display:none;">
                            <div class="d-flex align-items-center mt-2 ms-4">
                                <label class="me-2 text-muted">Diulang</label>
                                <input type="number" class="form-control form-control-sm text-center" name="batas_pengulangan" style="width: 70px;" value="2" min="0">
                                <label class="ms-2 text-muted">kali</label>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2 ms-4">
                            <i class="fas fa-info-circle me-1"></i> Jika diaktifkan, siswa dapat mengulang pengerjaan sesuai batas. Nilai yang diambil adalah nilai terbaik.
                        </small>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Catatan:</strong> Setelah {{ $tipeUjian === 'latihan' ? 'latihan' : 'ujian' }} dibuat, Anda dapat menambahkan soal di halaman edit.
                </div>

                @include('guru.partials.multi-kelas-selector')

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Buat {{ $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian' }}
                    </button>
                    <a href="{{ $tipeUjian === 'latihan' ? route('guru.lms.latihan.index', [$kelas->id, $mapel->id]) : route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}"
                        class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleBatasPengulangan() {
            var checkbox = document.getElementById('bisaDiulang');
            var container = document.getElementById('batasPengulanganContainer');
            if (checkbox.checked) {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleBatasPengulangan();
        });
    </script>
@endpush
