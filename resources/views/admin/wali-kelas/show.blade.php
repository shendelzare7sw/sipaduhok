@extends('layouts.sneat')

@section('title', 'Detail Wali Kelas - ' . $kelas->nama_kelas)

@section('page-title', 'Detail Wali Kelas')
@section('page-subtitle', 'Kelas ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/wali-kelas/show.css'])
@endsection

@section('content')
<div class="wali-kelas-show-page">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('admin.wali-kelas.index') }}">Data Wali Kelas</a>
        <span>/</span>
        <span class="current">{{ $kelas->nama_kelas }}</span>
    </div>

    <div class="header-card">
        <div class="header-content">
            <div class="header-top">
                <div class="header-info">
                    <div class="header-icon"><i class="fas fa-chalkboard"></i></div>
                    <div class="header-text">
                        <h1>Kelas {{ $kelas->nama_kelas }}</h1>
                        <div class="header-meta">
                            <span class="header-badge">
                                <i class="fas fa-tag"></i> {{ $kelas->kode_kelas }}
                            </span>
                            <span class="header-badge">
                                <i class="fas fa-layer-group"></i> {{ $kelas->jenjang }}
                            </span>
                            <span class="header-badge">
                                <i class="fas fa-building"></i> {{ $kelas->cabang->nama_cabang ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.kelas.show', $kelas) }}" class="btn btn-white">
                        <i class="fas fa-eye"></i> Detail Kelas
                    </a>
                    <a href="{{ route('admin.wali-kelas.index') }}" class="btn btn-white-outline">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="header-stats">
                <div class="header-stat">
                    <div class="header-stat-value">{{ $stats['totalSiswa'] }}</div>
                    <div class="header-stat-label">Total Siswa</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">{{ $stats['siswaLaki'] }}</div>
                    <div class="header-stat-label">Laki-laki</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">{{ $stats['siswaPerempuan'] }}</div>
                    <div class="header-stat-label">Perempuan</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-2">
        {{-- Wali Kelas Info --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-tie"></i> Wali Kelas</h5>
            </div>
            <div class="card-body">
                @if($kelas->waliKelas)
                    <div class="wali-kelas-card">
                        <div class="wali-avatar">{{ strtoupper(substr($kelas->waliKelas->nama_lengkap, 0, 1)) }}</div>
                        <div class="wali-info">
                            <h3>{{ $kelas->waliKelas->nama_lengkap }}</h3>
                            <p><i class="fas fa-id-badge"></i> <span>NIP: {{ $kelas->waliKelas->nip ?? '-' }}</span></p>
                            <p><i class="fas fa-phone"></i> <span>{{ $kelas->waliKelas->telepon ?? '-' }}</span></p>
                            <p><i class="fas fa-envelope"></i> <span>{{ $kelas->waliKelas->user->email ?? '-' }}</span></p>
                        </div>
                    </div>
                @else
                    <div class="empty-wali">
                        <i class="fas fa-user-slash"></i>
                        <h4>Belum Ada Wali Kelas</h4>
                        <p>Silakan tunjuk wali kelas untuk kelas ini</p>
                    </div>
                @endif

                <div class="assign-form">
                    <button type="button" id="toggleAssignBtn" class="btn btn-primary btn-full" data-toggle-assign-panel>
                        <i class="fas fa-user-edit"></i> {{ $kelas->waliKelas ? 'Ganti Wali Kelas' : 'Tunjuk Wali Kelas' }}
                    </button>

                    <div id="assignFormPanel" class="assign-panel">
                        <form action="{{ route('admin.wali-kelas.assign', $kelas) }}" method="POST" id="assignForm">
                            @csrf

                            <div class="assign-filter-row">
                                <div class="assign-search-wrap">
                                    <i class="fas fa-search assign-search-icon"></i>
                                    <input type="text" id="searchWali" class="assign-search-input" placeholder="Cari nama guru...">
                                </div>
                                <select id="filterCabang" class="assign-filter-select">
                                    <option value="">Semua Cabang</option>
                                    @foreach($cabangs as $cabang)
                                        <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="waliList" class="assign-wali-list">
                                @foreach($waliKelasOptions as $wk)
                                    @php $assignedKelas = $wk->waliKelasAssignments ?? collect(); @endphp
                                    <label class="wali-option"
                                           data-name="{{ strtolower($wk->nama_lengkap) }}"
                                           data-cabang="{{ $wk->user->cabang_id ?? '' }}"
                                           >
                                        <input type="radio" name="wali_kelas_id" value="{{ $wk->id }}" class="assign-radio"
                                            {{ $kelas->wali_kelas_id == $wk->id ? 'checked' : '' }}>
                                        <div class="assign-option-avatar">
                                            {{ strtoupper(substr($wk->nama_lengkap, 0, 2)) }}
                                        </div>
                                        <div class="assign-option-info">
                                            <div class="assign-option-name">{{ $wk->nama_lengkap }}</div>
                                            <div class="assign-option-branch">{{ $wk->user->cabang->nama_cabang ?? '-' }}</div>
                                            @if($assignedKelas->count() > 0)
                                                <div class="assign-option-classes">
                                                    <i class="fas fa-chalkboard-teacher"></i>
                                                    {{ $assignedKelas->map(fn($a) => $a->kelas->nama_kelas ?? '')->filter()->join(', ') }}
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="assign-actions">
                                <button type="button" class="btn assign-cancel" data-toggle-assign-panel>
                                    <i class="fas fa-times"></i> Batal
                                </button>
                                <button type="submit" class="btn btn-primary assign-submit">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kelas Info --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Informasi Kelas</h5>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Kode Kelas</span>
                        <span class="info-value code-value">{{ $kelas->kode_kelas }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nama Kelas</span>
                        <span class="info-value">{{ $kelas->nama_kelas }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Jenjang</span>
                        <span class="info-value">{{ $kelas->jenjang }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Cabang</span>
                        <span class="info-value">{{ $kelas->cabang->nama_cabang ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tahun Ajaran</span>
                        <span class="info-value">{{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Kuota Siswa</span>
                        <span class="info-value">{{ $kelas->kuota_siswa }} siswa</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Siswa --}}
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-user-graduate"></i> Daftar Siswa ({{ $stats['totalSiswa'] }})</h5>
        </div>
        <div class="card-body">
            @if($kelas->siswa->count() > 0)
                <div class="table-responsive">
                    <table class="table table-card-mobile">
                        <thead>
                            <tr>
                                <th class="mobile-card-hide">No</th>
                                <th data-label="NIS">NIS</th>
                                <th class="mobile-card-head">Nama Siswa</th>
                                <th data-label="Jenis Kelamin">Jenis Kelamin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelas->siswa->sortBy('nama_lengkap') as $index => $siswa)
                            <tr>
                                <td class="mobile-card-hide">{{ $index + 1 }}</td>
                                <td data-label="NIS"><code class="student-code">{{ $siswa->nis }}</code></td>
                                <td class="mobile-card-head">
                                    <div class="user-info">
                                        <div class="user-avatar">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</div>
                                        <span>{{ $siswa->nama_lengkap }}</span>
                                    </div>
                                </td>
                                <td data-label="Jenis Kelamin">
                                    <span class="badge {{ $siswa->jenis_kelamin == 'L' ? 'badge-info' : 'badge-purple' }}">
                                        {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>Belum ada siswa di kelas ini</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite(['resources/js/admin/wali-kelas/show.js'])
@endsection
