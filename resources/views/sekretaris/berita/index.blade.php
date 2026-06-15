@extends('layouts.sneat')

@section('title', 'Kelola Berita')
@section('page-title', 'Kelola Berita')
@section('page-subtitle', 'Kelola konten dan informasi publik')

@section('sidebar-menu')
    @include('sekretaris.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite('resources/css/sekretaris/berita/index.css')
@endsection

@section('content')
@php
    $routePrefix = 'sekretaris';
    $basePath = '/sekretaris/berita';
    $items = is_object($berita) && method_exists($berita, 'getCollection') ? $berita->getCollection() : collect($berita);
    $total = is_object($berita) && method_exists($berita, 'total') ? $berita->total() : $items->count();
@endphp

<div class="ak-page">
    <div class="ak-toolbar">
        <div class="ak-toolbar-title">
            <span class="ak-toolbar-icon"><i class="fas fa-newspaper"></i></span>
            <div>
                <h5>Kelola Berita</h5>
                <p>Kelola konten berita publik, kategori, status tayang, dan berita unggulan.</p>
            </div>
        </div>
        <a href="{{ route($routePrefix . '.berita.create') }}" class="ak-btn primary">
            <i class="fas fa-plus"></i>
            Tambah Berita
        </a>
    </div>

    <div class="ak-stats">
        <div class="ak-stat primary">
            <span>Total Berita</span>
            <strong>{{ number_format($total) }}</strong>
            <small>Semua berita terdaftar</small>
            <i class="fas fa-newspaper"></i>
        </div>
        <div class="ak-stat success">
            <span>Terbit</span>
            <strong>{{ number_format($items->where('status', 'aktif')->count()) }}</strong>
            <small>Tayang di website</small>
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="ak-stat warning">
            <span>Unggulan</span>
            <strong>{{ number_format($items->where('is_featured', true)->count()) }}</strong>
            <small>Berita unggulan</small>
            <i class="fas fa-star"></i>
        </div>
        <div class="ak-stat purple">
            <span>Kategori</span>
            <strong>{{ number_format(count($kategoriOptions ?? [])) }}</strong>
            <small>Topik tersedia</small>
            <i class="fas fa-tags"></i>
        </div>
    </div>

    <div class="ak-panel">
        <div class="ak-panel-header">
            <h5><i class="fas fa-list text-primary me-2"></i>Daftar Berita</h5>
        </div>
        <div class="ak-panel-body">
            <form method="GET" action="{{ route($routePrefix . '.berita.index') }}" class="ak-filter">
                <input type="text" name="search" value="{{ request('search') }}" class="ak-input" placeholder="Cari judul berita...">

                <select name="kategori" class="ak-select" data-ak-auto-submit>
                    <option value="all" {{ request('kategori') === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    @foreach(($kategoriOptions ?? []) as $value => $label)
                        <option value="{{ $value }}" {{ request('kategori') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="status" class="ak-select" data-ak-auto-submit>
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    @foreach(($statusOptions ?? []) as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <button type="submit" class="ak-btn secondary">
                    <i class="fas fa-search"></i> Filter
                </button>

                @if(request()->hasAny(['search', 'kategori', 'status']))
                    <a href="{{ route($routePrefix . '.berita.index') }}" class="ak-btn danger">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        @if($items->count() > 0)
            <div class="ak-table-wrap">
                <table class="ak-table">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Informasi Berita</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            @php
                                $status = strtolower($item->status ?? 'draft');
                                $statusClass = match($status) {
                                    'aktif' => 'success',
                                    'draft' => 'warning',
                                    'arsip' => 'danger',
                                    default => 'muted',
                                };
                            @endphp
                            <tr>
                                <td data-label="Gambar">
                                    <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" class="ak-thumb">
                                </td>
                                <td class="ak-main-cell" data-label="Berita">
                                    <div class="ak-title">
                                        {{ $item->judul }}
                                        @if($item->is_featured)
                                            <i class="fas fa-star text-warning ms-1" title="Unggulan"></i>
                                        @endif
                                    </div>
                                    <div class="ak-sub">{{ \Illuminate\Support\Str::limit($item->deskripsi_singkat, 110) }}</div>
                                    @if($item->url_berita)
                                        <a href="{{ $item->url_berita }}" target="_blank" class="ak-sub d-inline-flex gap-1 text-primary">
                                            <i class="fas fa-link"></i>{{ \Illuminate\Support\Str::limit($item->url_berita, 42) }}
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center" data-label="Kategori">
                                    <span class="ak-badge info">{{ $item->kategori_label }}</span>
                                </td>
                                <td class="text-center" data-label="Tanggal">
                                    {{ $item->tanggal_berita?->format('d M Y') ?? '-' }}
                                    <div class="ak-sub">Urutan {{ $item->urutan_tampil }}</div>
                                </td>
                                <td class="text-center" data-label="Status">
                                    <span class="ak-badge {{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                                </td>
                                <td data-label="Aksi">
                                    <div class="ak-actions">
                                        <button type="button"
                                            class="ak-btn {{ $item->is_featured ? 'warning' : 'secondary' }} ak-icon-btn"
                                            title="{{ $item->is_featured ? 'Hapus dari Unggulan' : 'Jadikan Unggulan' }}"
                                            data-ak-open-modal="featuredModal{{ $item->id }}">
                                            <i class="{{ $item->is_featured ? 'fas' : 'far' }} fa-star"></i>
                                        </button>
                                        <a href="{{ route($routePrefix . '.berita.edit', $item->id) }}" class="ak-btn secondary ak-icon-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                            class="ak-btn danger ak-icon-btn"
                                            title="Hapus"
                                            data-ak-delete
                                            data-ak-delete-id="{{ $item->id }}"
                                            data-ak-delete-title="{{ $item->judul }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(is_object($berita) && method_exists($berita, 'hasPages') && $berita->hasPages())
                <div class="ak-pagination">{{ $berita->links() }}</div>
            @endif
        @else
            <div class="ak-empty">
                <i class="fas fa-newspaper"></i>
                <h5>Belum ada berita</h5>
                <p>Tambahkan berita atau informasi publik terbaru.</p>
                <a href="{{ route($routePrefix . '.berita.create') }}" class="ak-btn primary">
                    <i class="fas fa-plus"></i> Tambah Berita
                </a>
            </div>
        @endif
    </div>
</div>

@php
    $modalTitle = 'Hapus Berita';
    $itemLabelId = 'deleteAkademikName';
@endphp

@php
    $deleteLabelId = $itemLabelId ?? 'deleteAkademikName';
@endphp

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ $modalTitle ?? 'Hapus Data' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                <h6 class="fw-bold mb-2">Apakah Anda yakin ingin menghapus data ini?</h6>
                <p class="text-muted mb-0" id="{{ $deleteLabelId }}"></p>
                <small class="text-danger d-block mt-2">Tindakan ini tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="ak-btn secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>Batal
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ak-btn danger">
                        <i class="fas fa-trash"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<template
    id="akademikIndexConfig"
    data-base-path="{{ $basePath ?? '' }}"
    data-delete-label-id="{{ $deleteLabelId }}"
></template>
@vite('resources/js/sekretaris/berita/index.js')

@foreach($items as $item)
    <div class="modal fade" id="featuredModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-warning">
                        <i class="fas fa-star me-2"></i>Konfirmasi Unggulan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-star fa-3x text-warning mb-3"></i>
                    <h6 class="fw-bold mb-2">
                        {{ $item->is_featured ? 'Hapus berita ini dari unggulan?' : 'Jadikan berita ini sebagai unggulan?' }}
                    </h6>
                    <p class="text-muted mb-0">{{ $item->judul }}</p>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="ak-btn secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>Batal
                    </button>
                    <button type="button" class="ak-btn warning" data-ak-featured-confirm data-berita-id="{{ $item->id }}">
                        <i class="fas fa-star"></i>{{ $item->is_featured ? 'Hapus dari Unggulan' : 'Jadikan Unggulan' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
