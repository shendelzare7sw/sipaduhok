@extends('layouts.sneat')

@section('title', 'Kelola Flyer')
@section('page-title', 'Kelola Flyer / Iklan')
@section('page-subtitle', 'Pop-up informasi untuk siswa saat login')

@section('sidebar-menu')
    @include('sekretaris.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite('resources/css/sekretaris/flyer/index.css')
@endsection

@section('content')
@php
    $routePrefix = 'sekretaris';
    $basePath = '/sekretaris/flyer';
    $items = is_object($flyer) && method_exists($flyer, 'getCollection') ? $flyer->getCollection() : collect($flyer);
    $total = is_object($flyer) && method_exists($flyer, 'total') ? $flyer->total() : $items->count();
@endphp

<div class="ak-page">
    <div class="ak-toolbar">
        <div class="ak-toolbar-title">
            <span class="ak-toolbar-icon"><i class="fas fa-images"></i></span>
            <div>
                <h5>Kelola Flyer</h5>
                <p>Pop-up informasi dan media promosi untuk pengguna saat login.</p>
            </div>
        </div>
        <a href="{{ route($routePrefix . '.flyer.create') }}" class="ak-btn primary">
            <i class="fas fa-plus"></i>
            Tambah Flyer
        </a>
    </div>

    <div class="ak-stats three">
        <div class="ak-stat primary">
            <span>Total Flyer</span>
            <strong>{{ number_format($total) }}</strong>
            <small>Media promosi dan info</small>
            <i class="fas fa-images"></i>
        </div>
        <div class="ak-stat success">
            <span>Flyer Aktif</span>
            <strong>{{ number_format($items->where('status', 'aktif')->count()) }}</strong>
            <small>Siap tampil ke target</small>
            <i class="fas fa-toggle-on"></i>
        </div>
        <div class="ak-stat warning">
            <span>Nonaktif</span>
            <strong>{{ number_format($items->where('status', 'nonaktif')->count()) }}</strong>
            <small>Belum dipublikasi</small>
            <i class="fas fa-layer-group"></i>
        </div>
    </div>

    @if($items->count() > 0)
        <div class="ak-flyer-grid">
            @foreach($items as $item)
                @php
                    $statusClass = ($item->status ?? '') === 'aktif' ? 'success' : 'muted';
                @endphp
                <article class="ak-flyer-card">
                    <div class="ak-flyer-media">
                        <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}">
                        <div class="ak-flyer-status">
                            <span class="ak-badge {{ $statusClass }}">{{ $item->status_badge['label'] ?? ucfirst($item->status) }}</span>
                        </div>
                    </div>
                    <div class="ak-flyer-body">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="ak-title">{{ $item->judul }}</div>
                            <span class="ak-badge primary">#{{ $item->urutan_tampil }}</span>
                        </div>
                        <div class="ak-sub">{{ \Illuminate\Support\Str::limit($item->deskripsi, 110) }}</div>

                        <div class="ak-flyer-meta">
                            <div><i class="fas fa-users text-primary me-1"></i>Target: <strong>{{ $item->target_label }}</strong></div>
                            <div><i class="fas fa-calendar-alt text-info me-1"></i>{{ $item->tanggal_mulai?->format('d M') }} - {{ $item->tanggal_selesai?->format('d M Y') }}</div>
                        </div>

                        @if($item->link_url)
                            <a href="{{ $item->link_url }}" target="_blank" class="ak-btn secondary w-100 mb-2">
                                <i class="fas fa-external-link-alt"></i>
                                Kunjungi Tautan
                            </a>
                        @endif

                        <div class="ak-actions">
                            <a href="{{ route($routePrefix . '.flyer.edit', $item->id) }}" class="ak-btn warning flex-fill">
                                <i class="fas fa-edit"></i>Edit
                            </a>
                            <button type="button"
                                class="ak-btn danger flex-fill"
                                data-ak-delete
                                data-ak-delete-id="{{ $item->id }}"
                                data-ak-delete-title="{{ $item->judul }}">
                                <i class="fas fa-trash"></i>Hapus
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if(is_object($flyer) && method_exists($flyer, 'hasPages') && $flyer->hasPages())
            <div class="ak-panel mt-3">
                <div class="ak-pagination">{{ $flyer->links() }}</div>
            </div>
        @endif
    @else
        <div class="ak-panel">
            <div class="ak-empty">
                <i class="fas fa-images"></i>
                <h5>Belum ada flyer</h5>
                <p>Tambahkan flyer untuk pop-up informasi saat pengguna login.</p>
                <a href="{{ route($routePrefix . '.flyer.create') }}" class="ak-btn primary">
                    <i class="fas fa-plus"></i> Tambah Flyer
                </a>
            </div>
        </div>
    @endif
</div>

@php
    $modalTitle = 'Hapus Flyer';
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
@vite('resources/js/sekretaris/flyer/index.js')
@endsection
