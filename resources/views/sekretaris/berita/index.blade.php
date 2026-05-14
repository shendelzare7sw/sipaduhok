@extends('layouts.sneat')

@section('title', 'Kelola Berita')
@section('page-title', 'Kelola Berita')
@section('page-subtitle', 'Kelola konten dan informasi publik')

@section('sidebar-menu')
    @include('sekretaris.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.akademik.styles')
@endsection

@section('content')
    @include('shared.akademik.berita-index', [
        'routePrefix' => 'sekretaris',
        'basePath' => '/sekretaris/berita',
    ])
@endsection
