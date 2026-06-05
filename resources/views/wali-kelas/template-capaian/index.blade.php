@extends('layouts.sneat')

@section('title', 'Template Capaian Kompetensi')
@section('page-title', 'Template Capaian Kompetensi')
@section('page-subtitle', 'Kelola template deskripsi capaian untuk rapor')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Template Capaian Kompetensi</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus"></i> Tambah Template
        </button>
    </div>

    <!-- Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-5">
                        <select name="mata_pelajaran_id" class="form-control">
                            <option value="">Semua Mata Pelajaran</option>
                            @foreach($mataPelajaranList as $mapel)
                                <option value="{{ $mapel->id }}" {{ request('mata_pelajaran_id') == $mapel->id ? 'selected' : '' }}>
                                    {{ $mapel->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama template..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Mata Pelajaran</th>
                            <th>Nama Template</th>
                            <th>Template Text</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                        <tr>
                            <td>{{ $loop->iteration + ($templates->currentPage() - 1) * $templates->perPage() }}</td>
                            <td>{{ $template->mataPelajaran->nama_mapel ?? '-' }}</td>
                            <td>{{ $template->nama_template }}</td>
                            <td>{{ Str::limit($template->template_text, 100) }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info btn-sm" onclick="editTemplate({{ json_encode($template) }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#hapusTemplateModal"
                                    data-action="{{ route('wali.template-capaian.destroy', $template->id) }}"
                                    data-nama="{{ $template->nama_template }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Tidak ada template
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $templates->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('wali.template-capaian.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="mata_pelajaran_id" class="form-control" required>
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($mataPelajaranList as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Template <span class="text-danger">*</span></label>
                        <input type="text" name="nama_template" class="form-control" placeholder="Contoh: Sangat Baik, Baik, Cukup" required>
                        <small class="text-muted">Nama untuk mengidentifikasi template (max 100 karakter)</small>
                    </div>
                    <div class="form-group">
                        <label>Template Text <span class="text-danger">*</span></label>
                        <textarea name="template_text" class="form-control" rows="5" placeholder="Tulis deskripsi capaian kompetensi..." required></textarea>
                        <small class="text-muted">Deskripsi yang akan digunakan untuk rapor siswa</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="mata_pelajaran_id" id="edit_mata_pelajaran_id" class="form-control" required>
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($mataPelajaranList as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Template <span class="text-danger">*</span></label>
                        <input type="text" name="nama_template" id="edit_nama_template" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Template Text <span class="text-danger">*</span></label>
                        <textarea name="template_text" id="edit_template_text" class="form-control" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Template -->
<div class="modal fade" id="hapusTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-trash me-2"></i>Hapus Template
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h6 class="fw-bold mb-2">Hapus template ini?</h6>
                <p class="text-muted small mb-1">Template: <strong id="namaTemplateDihapus"></strong></p>
                <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form id="formHapusTemplate" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-bold">
                        <i class="fas fa-trash me-1"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editTemplate(template) {
    document.getElementById('editForm').action = '/wali/template-capaian/' + template.id;
    document.getElementById('edit_mata_pelajaran_id').value = template.mata_pelajaran_id;
    document.getElementById('edit_nama_template').value = template.nama_template;
    document.getElementById('edit_template_text').value = template.template_text;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
}

document.getElementById('hapusTemplateModal')?.addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('formHapusTemplate').action = btn.dataset.action;
    document.getElementById('namaTemplateDihapus').textContent = btn.dataset.nama;
});
</script>
@endpush
@endsection
