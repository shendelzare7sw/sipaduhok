@extends('layouts.sneat')

@section('title', 'Kelola Berita')
@section('page-title', 'Kelola Berita')
@section('page-subtitle', 'Kelola konten dan informasi publik')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.akademik.styles')
@endsection

@section('content')
    @include('shared.akademik.berita-index', [
        'routePrefix' => 'admin.akademik',
        'basePath' => '/admin/akademik/berita',
    ])
@endsection
