@extends('layouts.sneat')

@section('title', 'Kelola Rapor')
@section('page-title', 'Kelola Rapor')
@section('page-subtitle', isset($kelas) && $kelas ? 'Kelola dan terbitkan rapor siswa kelas ' . $kelas->nama_kelas : 'Kelola rapor siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .table-rapor thead th {
        background-color: #f8f9fc;
        text-align: center;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #4e73df;
        border-bottom: 2px solid #e3e6f0;
    }
    .rata-rata-val {
        font-size: 1rem;
        font-weight: 800;
        color: #165fac;
    }
    .badge-status {
        padding: 0.5rem 0.75rem;
        border-radius: 5px;
        font-weight: 700;
    }
    .btn-action-group .btn {
        margin: 2px;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">
    @if($error ?? false)
        <div class="alert alert-danger shadow-sm border-start border-danger border-4">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}
        </div>
    @else
        {{-- HEADER ACTIONS --}}
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h4 class="m-0 fw-bold text-primary">Kelola Rapor - {{ $kelas->nama_kelas }}</h4>
                        <p class="text-muted small mb-0">Tahun Ajaran: {{ $kelas->tahunAjaran->nama_tahun_ajaran }} | Cabang: {{ $kelas->cabang->nama_cabang }}</p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <button type="button" class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#generateAllModal">
                            <i class="fas fa-file-invoice me-1"></i> Generate Semua Rapor
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER SEMESTER & JENIS RAPOR --}}
        <div class="card shadow mb-4">
            <div class="card-body py-3">
                <form action="{{ route('wali.rapor.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="small fw-bold text-uppercase">Pilih Semester</label>
                            <select name="semester" class="form-select" onchange="this.form.submit()">
                                <option value="ganjil" {{ $semester == 'ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                                <option value="genap" {{ $semester == 'genap' ? 'selected' : '' }}>Semester Genap</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="small fw-bold text-uppercase">Jenis Rapor</label>
                            <select name="jenis_rapor" class="form-select" onchange="this.form.submit()">
                                <option value="akhir_semester" {{ $jenisRapor == 'akhir_semester' ? 'selected' : '' }}>Akhir Semester (PAS)</option>
                                <option value="tengah_semester" {{ $jenisRapor == 'tengah_semester' ? 'selected' : '' }}>Tengah Semester (PTS)</option>
                            </select>
                        </div>
                        <div class="col-md-2 mt-2">
                            <a href="{{ route('wali.rapor.index') }}" class="btn btn-secondary w-100 shadow-sm">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABEL DATA SISWA & RAPOR --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-graduation-cap me-2"></i>Daftar Rapor - {{ ucwords(str_replace('_', ' ', $jenisRapor)) }} {{ ucfirst($semester) }}
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="50">NO</th>
                                <th width="120">NIS</th>
                                <th class="text-start">NAMA LENGKAP SISWA</th>
                                <th width="150">VALIDASI AKSES</th>
                                <th>STATUS RAPOR</th>
                                <th>RATA-RATA</th>
                                <th width="350">AKSI KELOLA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($raporList ?? [] as $index => $item)
                                @php
                                    $siswa = $item['siswa'];
                                    $rapor = $item['rapor'];
                                    $status = $rapor ? $rapor->status : 'draft';

                                    // Hitung rata-rata preview (kasar)
                                    // Idealnya dari RaporNilai jika sudah ada, atau hitung on-the-fly
                                    $nilaiSiswa = \App\Models\Nilai::where('siswa_id', $siswa->id)
                                        ->where('kelas_id', $kelas->id)
                                        ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
                                        ->get();
                                    
                                    // Kalkulasi sederhana untuk preview di tabel
                                    // Akuratnya ada di dalam Rapor (rapor_nilai)
                                    if ($rapor && $rapor->raporNilai->count() > 0) {
                                        $rataRata = $rapor->raporNilai->avg('nilai_angka');
                                    } else {
                                        $rataRata = 0; 
                                        // Kalau belum generate, kita tampilkan 0 atau hitung manual (optional, expensive)
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center align-middle fw-bold text-gray-600">{{ $index + 1 }}</td>
                                    <td class="align-middle text-center fw-bold text-gray-800">{{ $siswa->nis }}</td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-gray-900">{{ $siswa->nama_lengkap }}</div>
                                        <small class="text-muted text-uppercase">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($siswa->hasFullRaporAccess())
                                            <span class="badge bg-success" title="Divalidasi oleh Bendahara, Wali, & Ketua">
                                                <i class="fas fa-check-double me-1"></i> Valid (3)
                                            </span>
                                        @else
                                            <span class="badge bg-danger" title="Belum lengkap">
                                                <i class="fas fa-times-circle me-1"></i> Belum Valid
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($status == 'diterbitkan')
                                            <span class="badge bg-success badge-status"><i class="fas fa-check-circle me-1"></i> Diterbitkan</span>
                                        @elseif($rapor)
                                            <span class="badge bg-warning badge-status text-white"><i class="fas fa-edit me-1"></i> Draft</span>
                                        @else
                                            <span class="text-xs text-muted fst-italic">Belum Generate</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="rata-rata-val">{{ number_format($rataRata, 2) }}</span>
                                    </td>
                                    <td class="text-center align-middle btn-action-group">
                                        @if($rapor)
                                            <a href="{{ route('wali.rapor.edit', $rapor->id) }}" class="btn btn-warning btn-sm shadow-sm" title="Edit Catatan & Kehadiran">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="{{ route('wali.rapor.preview', $rapor->id) }}" class="btn btn-info btn-sm shadow-sm" target="_blank" title="Lihat PDF">
                                                <i class="fas fa-eye"></i> Preview
                                            </a>
                                            @if($status == 'draft')
                                                <button type="button" class="btn btn-success btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#terbitkanModal{{ $rapor->id }}" title="Terbitkan rapor">
                                                    <i class="fas fa-paper-plane"></i> Terbitkan
                                                </button>

                                                {{-- Modal Terbitkan --}}
                                                <div class="modal fade" id="terbitkanModal{{ $rapor->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow-lg">
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title fw-bold text-white">
                                                                    <i class="fas fa-paper-plane me-2"></i>Konfirmasi Terbitkan Rapor
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body py-4">
                                                                <div class="text-center mb-3">
                                                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                                                    <h6 class="fw-bold mb-2">Terbitkan rapor {{ $siswa->nama_lengkap }}?</h6>
                                                                </div>
                                                                <div class="alert alert-info bg-light border-info mb-0">
                                                                    <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-1"></i> Yang akan terjadi:</h6>
                                                                    <ul class="mb-0 small">
                                                                        <li>Status rapor berubah dari <strong>Draft</strong> ke <strong>Diterbitkan</strong></li>
                                                                        <li>Rapor <strong>akan terlihat</strong> oleh orang tua dan siswa</li>
                                                                        <li>Orang tua dapat melihat dan mengunduh rapor</li>
                                                                        <li>Pastikan semua data sudah benar sebelum terbitkan</li>
                                                                        <li class="text-muted fst-italic mt-2">💡 Jika ada kesalahan, Anda bisa tarik kembali rapor setelah diterbitkan</li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <i class="fas fa-times me-1"></i> Batal
                                                                </button>
                                                                <form action="{{ route('wali.rapor.terbitkan', $rapor->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success">
                                                                        <i class="fas fa-paper-plane me-1"></i> Ya, Terbitkan Sekarang
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <form action="{{ route('wali.rapor.destroy', $rapor->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm shadow-sm" onclick="return confirm('Hapus rapor draft ini? Anda bisa generate ulang setelah perbaikan nilai.')">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            @elseif($status == 'diterbitkan')
                                                <button type="button" class="btn btn-warning btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#tarikKembaliModal{{ $rapor->id }}" title="Tarik kembali rapor">
                                                    <i class="fas fa-undo"></i> Tarik Kembali
                                                </button>

                                                {{-- Modal Tarik Kembali --}}
                                                <div class="modal fade" id="tarikKembaliModal{{ $rapor->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow-lg">
                                                            <div class="modal-header bg-warning text-white">
                                                                <h5 class="modal-title fw-bold text-white">
                                                                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Tarik Kembali Rapor
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body py-4">
                                                                <div class="text-center mb-3">
                                                                    <i class="fas fa-undo fa-3x text-warning mb-3"></i>
                                                                    <h6 class="fw-bold mb-2">Tarik kembali rapor {{ $siswa->nama_lengkap }}?</h6>
                                                                </div>
                                                                <div class="alert alert-warning bg-light border-warning mb-0">
                                                                    <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-1"></i> Yang akan terjadi:</h6>
                                                                    <ul class="mb-0 small">
                                                                        <li>Status rapor berubah dari <strong>Diterbitkan</strong> ke <strong>Draft</strong></li>
                                                                        <li>Rapor <strong>tidak akan terlihat</strong> oleh orang tua/siswa</li>
                                                                        <li>Data rapor <strong>tetap tersimpan</strong>, Anda bisa edit dan terbitkan ulang</li>
                                                                        <li>Gunakan fitur ini jika ada kesalahan input yang perlu diperbaiki</li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <i class="fas fa-times me-1"></i> Batal
                                                                </button>
                                                                <form action="{{ route('wali.rapor.tarik-kembali', $rapor->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-warning">
                                                                        <i class="fas fa-undo me-1"></i> Ya, Tarik Kembali
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            @if($siswa->hasFullRaporAccess())
                                                <form action="{{ route('wali.rapor.generate-single', $siswa->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="semester" value="{{ $semester }}">
                                                    <input type="hidden" name="jenis_rapor" value="{{ $jenisRapor }}">
                                                    <button type="submit" class="btn btn-primary btn-sm shadow-sm" title="Generate rapor siswa ini">
                                                        <i class="fas fa-file-invoice"></i> Generate
                                                    </button>
                                                </form>
                                            @else
                                                <small class="text-danger"><i class="fas fa-lock me-1"></i>Validasi belum lengkap</small>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-users-slash fa-3x text-gray-200 mb-2"></i>
                                        <p class="text-gray-500 mb-0">Tidak ada data siswa ditemukan di kelas ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- INFO PETUNJUK --}}
        <div class="card shadow-sm border-start border-info border-4 mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-info"><i class="fas fa-info-circle me-2"></i>Petunjuk Pengelolaan Rapor</h6>
                <ul class="small text-gray-700 mb-0 mt-2">
                    <li><strong>Generate:</strong> Digunakan untuk membuat draf rapor secara massal berdasarkan nilai yang ada.</li>
                    <li><strong>Edit:</strong> Digunakan untuk mengisi <strong>Catatan Wali Kelas</strong>, data kehadiran, dan saran-saran.</li>
                    <li><strong>Preview:</strong> Melihat tampilan hasil akhir rapor dalam format PDF sebelum diberikan ke siswa.</li>
                    <li><strong>Terbitkan:</strong> Finalisasi rapor agar dapat diakses oleh siswa/orang tua melalui akun mereka.</li>
                </ul>
            </div>
        </div>
    @endif
</div>
</div>

{{-- MODAL KONFIRMASI GENERATE ALL --}}
<div class="modal fade" id="generateAllModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-file-invoice me-2"></i>Konfirmasi Generate Rapor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-file-invoice fa-3x text-primary mb-3"></i>
                <h6 class="fw-bold mb-2">Generate rapor untuk semua siswa?</h6>
                <p class="text-muted small mb-0">
                    Sistem akan membuat draf rapor <strong>{{ ucwords(str_replace('_', ' ', $jenisRapor)) }}</strong> secara otomatis untuk <strong>semua siswa</strong> di kelas ini
                    berdasarkan nilai semester <strong>{{ ucfirst($semester) }}</strong> yang tersedia.
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form action="{{ route('wali.rapor.generate-all') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="semester" value="{{ $semester }}">
                    <input type="hidden" name="jenis_rapor" value="{{ $jenisRapor }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i> Ya, Generate Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
