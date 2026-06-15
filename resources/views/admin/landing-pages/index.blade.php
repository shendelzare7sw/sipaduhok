@extends('layouts.sneat')

@section('title', 'Manajemen Landing Page')

@section('page-title', 'Manajemen Landing Page')
@section('page-subtitle', 'Kelola konten halaman landing website')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/landing-pages/index.css'])
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin /</span> Manajemen Landing Page
        </h4>

        <div class="card landing-page-card">
            <h5 class="card-header">Daftar Halaman</h5>
            <div class="table-responsive landing-page-table-wrap">
                <table class="table table-hover landing-page-table">
                    <thead>
                        <tr>
                            <th>Judul Halaman</th>
                            <th>Slug</th>
                            <th>Terakhir Diperbarui</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($pages as $page)
                            <tr>
                                <td data-label="Judul Halaman"><strong>{{ $page->title }}</strong></td>
                                <td data-label="Slug"><code>/{{ $page->slug === 'home' ? '' : $page->slug }}</code></td>
                                <td data-label="Terakhir Diperbarui" title="{{ $page->updated_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}">
                                    {{ $page->updated_at?->locale('id')->diffForHumans() ?? '-' }}
                                </td>
                                <td data-label="Aksi">
                                    <a href="{{ route('admin.landing-pages.edit', $page->slug) }}"
                                        class="btn btn-sm btn-primary landing-page-action">
                                        <i class="bx bx-edit-alt me-1"></i> Edit Konten
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-state">
                                <td colspan="4" class="text-center">Belum ada data halaman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
