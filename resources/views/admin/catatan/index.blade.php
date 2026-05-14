@extends('layouts.sneat')

@section('title', 'Daftar Catatan')
@section('page-title', 'Manajemen Catatan')
@section('page-subtitle', 'Kirim dan kelola catatan instruksi untuk pengguna')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.catatan.styles')
@endsection

@section('content')
    @include('shared.catatan.index', [
        'routePrefix' => 'admin',
        'basePath' => '/admin/catatan',
    ])
@endsection

@section('scripts')
    @include('shared.catatan.delete-script', ['basePath' => '/admin/catatan'])
@endsection
