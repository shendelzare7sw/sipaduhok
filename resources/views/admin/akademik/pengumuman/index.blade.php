@extends('layouts.sneat')

@section('title', 'Kelola Pengumuman')
@section('page-title', 'Kelola Pengumuman')
@section('page-subtitle', 'Pengumuman otomatis dari kalender atau manual')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.akademik.styles')
@endsection

@section('content')
    @include('shared.akademik.pengumuman-index', [
        'routePrefix' => 'admin.akademik',
        'basePath' => '/admin/akademik/pengumuman',
    ])
@endsection
