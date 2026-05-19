@extends('layouts.sneat')

@section('title', 'Manajemen Landing Page')

@section('page-title', 'Manajemen Landing Page')
@section('page-subtitle', 'Kelola konten halaman landing website')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    <style>
        .landing-page-table td,
        .landing-page-table th {
            vertical-align: middle;
        }

        .landing-page-table code {
            white-space: normal;
            word-break: break-word;
        }

        .landing-page-action {
            white-space: nowrap;
        }

        @media (max-width: 767.98px) {
            .landing-page-card .card-header {
                padding: 1rem 1.25rem;
            }

            .landing-page-table-wrap {
                overflow: visible;
            }

            .landing-page-table {
                margin-bottom: 0;
            }

            .landing-page-table thead {
                display: none;
            }

            .landing-page-table tbody,
            .landing-page-table tr,
            .landing-page-table td {
                display: block;
                width: 100%;
            }

            .landing-page-table tbody {
                padding: 0.75rem;
            }

            .landing-page-table tr {
                border: 1px solid #e7e7e7;
                border-radius: 0.5rem;
                margin-bottom: 0.75rem;
                padding: 0.85rem 0.9rem;
            }

            .landing-page-table tr:last-child {
                margin-bottom: 0;
            }

            .landing-page-table td {
                border: 0;
                padding: 0.45rem 0;
                text-align: left;
                white-space: normal;
            }

            .landing-page-table td::before {
                content: attr(data-label);
                display: block;
                margin-bottom: 0.2rem;
                color: #a1acb8;
                font-size: 0.72rem;
                font-weight: 600;
                text-transform: uppercase;
            }

            .landing-page-table td:first-child {
                padding-top: 0;
            }

            .landing-page-table td:last-child {
                padding-bottom: 0;
            }

            .landing-page-table .btn {
                justify-content: center;
                width: 100%;
            }

            .landing-page-table .empty-state {
                border: 0;
                padding: 1rem;
            }

            .landing-page-table .empty-state td {
                text-align: center;
            }
        }
    </style>
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
