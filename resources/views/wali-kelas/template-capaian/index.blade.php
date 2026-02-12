@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Template Capaian Kompetensi</h1>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
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
                        <button type="submit" class="btn btn-primary btn-block">
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
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
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
                                <form action="{{ route('wali.template-capaian.destroy', $template->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus template ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
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
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
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
    $('#editModal').modal('show');
}
</script>
@endpush
@endsection
