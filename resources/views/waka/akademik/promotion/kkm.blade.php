@extends('layouts.sneat')

@section('title', 'Pengaturan KKM')
@section('page-title', 'Pengaturan KKM')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0">Daftar Mata Pelajaran & KKM</h5>
                @if($tahun)
                    <small class="text-muted">Tahun Ajaran: {{ $tahun->nama ?? $tahun->tahun_ajaran }}</small>
                @endif
            </div>
            <form action="{{ route('waka.promotion.kkm.index') }}" method="GET">
                <select name="jenjang" class="form-select form-select-sm" style="min-width: 130px;" onchange="this.form.submit()">
                    <option value="PAUD" {{ $jenjang == 'PAUD' ? 'selected' : '' }}>PAUD</option>
                    <option value="SD"   {{ $jenjang == 'SD'   ? 'selected' : '' }}>SD</option>
                    <option value="SMP"  {{ $jenjang == 'SMP'  ? 'selected' : '' }}>SMP</option>
                    <option value="SMA"  {{ $jenjang == 'SMA'  ? 'selected' : '' }}>SMA</option>
                </select>
            </form>
        </div>

        <div class="card-body px-3 px-md-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('waka.promotion.kkm.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                <input type="hidden" name="jenjang" value="{{ $jenjang }}">

                @if($mapelList->isEmpty())
                    <div class="text-center py-5">
                        <i class="bx bx-book-open fs-1 text-muted"></i>
                        <p class="mt-2 text-muted">Belum ada mata pelajaran untuk jenjang <strong>{{ $jenjang }}</strong>.</p>
                    </div>
                @else
                    {{-- Desktop Table --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Jenjang</th>
                                    <th class="text-center">KKM Saat Ini</th>
                                    <th>Set KKM Baru</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mapelList as $i => $mapel)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td class="fw-medium">{{ $mapel->nama_mapel }}</td>
                                    <td><span class="badge bg-label-secondary">{{ $mapel->jenjang }}</span></td>
                                    <td class="text-center">
                                        <span class="badge bg-label-primary fs-6 px-3">
                                            {{ $existingKKM[$mapel->id] ?? 70 }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number"
                                               name="kkm[{{ $mapel->id }}]"
                                               class="form-control form-control-sm"
                                               style="width: 90px;"
                                               value="{{ $existingKKM[$mapel->id] ?? 70 }}"
                                               min="0" max="100" required>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="d-md-none">
                        @foreach($mapelList as $i => $mapel)
                        <div class="d-flex align-items-center justify-content-between border-bottom py-3">
                            <div class="flex-grow-1 me-3">
                                <div class="fw-medium">{{ $mapel->nama_mapel }}</div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge bg-label-secondary small">{{ $mapel->jenjang }}</span>
                                    <span class="text-muted small">KKM saat ini:
                                        <strong>{{ $existingKKM[$mapel->id] ?? 70 }}</strong>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <label class="form-label small text-muted mb-1">KKM</label>
                                <input type="number"
                                       name="kkm[{{ $mapel->id }}]"
                                       class="form-control form-control-sm text-center"
                                       style="width: 72px;"
                                       value="{{ $existingKKM[$mapel->id] ?? 70 }}"
                                       min="0" max="100" required>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100 w-md-auto">
                            <i class="bx bx-save me-1"></i> Simpan Pengaturan KKM
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<style>
@media (min-width: 768px) {
    .w-md-auto { width: auto !important; }
}
</style>
@endsection
