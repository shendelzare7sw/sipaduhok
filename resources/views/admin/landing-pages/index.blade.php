@extends('layouts.sneat')

@section('title', 'Manajemen Landing Page')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin /</span> Manajemen Landing Page
        </h4>

        <div class="card">
            <h5 class="card-header">Daftar Halaman</h5>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Judul Halaman</th>
                            <th>Slug</th>
                            <th>Terakhir Diupdate</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($pages as $page)
                            <tr>
                                <td><strong>{{ $page->title }}</strong></td>
                                <td><code>/{{ $page->slug === 'home' ? '' : $page->slug }}</code></td>
                                <td>{{ $page->updated_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('admin.landing-pages.edit', $page->slug) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="bx bx-edit-alt me-1"></i> Edit Konten
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data halaman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection