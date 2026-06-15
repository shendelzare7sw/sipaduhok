@extends('layouts.lms-guru')

@section('title', $tipeUjian === 'latihan' ? 'Edit Latihan' : 'Edit Ujian')
@section('page-title', $tipeUjian === 'latihan' ? 'Edit Latihan' : 'Edit Ujian')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@php
    $bisaDiulang = old('bisa_diulang', $ujian->bisa_diulang);
@endphp

@push('styles')
    @vite(['resources/css/guru/lms/ujian/edit.css'])
@endpush

@section('content')
<div class="guru-lms-ujian-edit-page">
    <div class="mb-3">
        <a href="{{ $tipeUjian === 'latihan' ? route('guru.lms.latihan.index', [$kelas->id, $mapel->id]) : route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-edit me-2"></i>Edit {{ $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian' }}
        </div>
        <div class="p-4">
            <form action="{{ $tipeUjian === 'latihan' ? route('guru.lms.latihan.update', [$kelas->id, $mapel->id, $ujian->id]) : route('guru.lms.ujian.update', [$kelas->id, $mapel->id, $ujian->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Judul {{ $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian' }} <span class="text-danger">*</span></label>
                    <input type="text" name="judul_ujian" class="form-control"
                        value="{{ old('judul_ujian', $ujian->judul_ujian) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control"
                        rows="3">{{ old('deskripsi', $ujian->deskripsi) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipe <span class="text-danger">*</span></label>
                        <select name="tipe_ujian" class="form-control" @if($tipeUjian === 'latihan') disabled @endif required>
                            @if($tipeUjian === 'latihan')
                                <option value="latihan" selected>Latihan</option>
                            @else
                                <optgroup label="Ulangan">
                                    <option value="ulangan_harian" {{ $ujian->tipe_ujian == 'ulangan_harian' ? 'selected' : '' }}>
                                        Ulangan Harian</option>
                                </optgroup>
                                <optgroup label="Semester Ganjil">
                                    <option value="pts_ganjil" {{ $ujian->tipe_ujian == 'pts_ganjil' ? 'selected' : '' }}>PTS
                                        Ganjil</option>
                                    <option value="pas_ganjil" {{ $ujian->tipe_ujian == 'pas_ganjil' ? 'selected' : '' }}>PAS
                                        Ganjil</option>
                                </optgroup>
                                <optgroup label="Semester Genap">
                                    <option value="pts_genap" {{ $ujian->tipe_ujian == 'pts_genap' ? 'selected' : '' }}>PTS Genap
                                    </option>
                                    <option value="pas_genap" {{ $ujian->tipe_ujian == 'pas_genap' ? 'selected' : '' }}>PAS Genap
                                    </option>
                                </optgroup>
                                @if($isTingkatAkhir)
                                <optgroup label="Ujian Kelulusan">
                                    <option value="to_1" {{ $ujian->tipe_ujian == 'to_1' ? 'selected' : '' }}>Try Out 1</option>
                                    <option value="to_2" {{ $ujian->tipe_ujian == 'to_2' ? 'selected' : '' }}>Try Out 2</option>
                                    <option value="to_3" {{ $ujian->tipe_ujian == 'to_3' ? 'selected' : '' }}>Try Out 3</option>
                                    <option value="upk" {{ $ujian->tipe_ujian == 'upk' ? 'selected' : '' }}>UPK</option>
                                    <option value="ujian_praktek" {{ $ujian->tipe_ujian == 'ujian_praktek' ? 'selected' : '' }}>Ujian Praktek</option>
                                </optgroup>
                                @endif
                            @endif
                        </select>
                        @if($tipeUjian === 'latihan')
                            <input type="hidden" name="tipe_ujian" value="latihan">
                        @endif
                        <small class="text-muted">PTS/PAS wajib memiliki 4 tipe soal</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_mulai" class="form-control"
                            value="{{ old('tanggal_mulai', $ujian->tanggal_mulai->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_selesai" class="form-control"
                            value="{{ old('tanggal_selesai', $ujian->tanggal_selesai->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Durasi (Menit)</label>
                    <input type="number" name="durasi_menit" class="form-control"
                        value="{{ old('durasi_menit', $ujian->durasi_menit) }}" min="0">
                    <small class="text-muted">Isi 0 untuk waktu tidak terbatas.</small>
                </div>

                <div class="mb-4">
                    <h5 class="form-label fw-bold border-bottom pb-2">Pengaturan Penilaian & Pengulangan</h5>
                    <div class="p-3 border rounded bg-light">
                        <!-- Tampilkan Nilai -->
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="tampilkanNilai" name="tampilkan_nilai" value="1" {{ old('tampilkan_nilai', $ujian->tampilkan_nilai) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="tampilkanNilai">Tampilkan Nilai ke Siswa</label>
                            <small class="text-muted d-block mt-1">Jika dinonaktifkan, siswa hanya akan melihat ucapan terima kasih setelah mengerjakan.</small>
                        </div>

                        <!-- Tampilkan Riwayat / Pembahasan -->
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="tampilkanRiwayat" name="tampilkan_riwayat" value="1" {{ old('tampilkan_riwayat', $ujian->tampilkan_riwayat) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-success" for="tampilkanRiwayat">Izinkan Siswa Melihat Riwayat & Jawaban Benar</label>
                            <small class="text-muted d-block mt-1">Siswa dapat melihat riwayat jawaban dan mencocokkannya dengan kunci jawaban setelah ujian selesai.</small>
                        </div>

                        <hr>

                        <!-- Pengulangan -->
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="bisaDiulang" name="bisa_diulang" value="1" data-repeat-toggle {{ $bisaDiulang ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-primary" for="bisaDiulang">Bisa Dikerjakan Ulang (Pengulangan)</label>
                        </div>
                        
                        <div id="batasPengulanganContainer" data-repeat-container @if(!$bisaDiulang) hidden @endif>
                            <div class="d-flex align-items-center mt-2 ms-4">
                                <label class="me-2 text-muted">Diulang</label>
                                <input type="number" class="form-control form-control-sm text-center repeat-limit-input" name="batas_pengulangan" value="{{ old('batas_pengulangan', $ujian->batas_pengulangan ?? 2) }}" min="0">
                                <label class="ms-2 text-muted">kali</label>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2 ms-4">
                            <i class="fas fa-info-circle me-1"></i> Jika diaktifkan, siswa dapat mengulang pengerjaan sesuai batas. Nilai yang diambil adalah nilai terbaik.
                        </small>
                    </div>
                </div>

                @include('guru.partials.multi-kelas-selector')

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update {{ $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian' }}
                    </button>
                    <a href="{{ $tipeUjian === 'latihan' ? route('guru.lms.latihan.index', [$kelas->id, $mapel->id]) : route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}"
                        class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/guru/lms/ujian/edit.js'])
@endpush
