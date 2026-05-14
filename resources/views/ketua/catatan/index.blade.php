@extends('layouts.sneat')

@section('title', 'Daftar Catatan')
@section('page-title', 'Manajemen Catatan')
@section('page-subtitle', 'Kirim dan kelola catatan instruksi untuk pengguna')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.catatan.styles')
@endsection

@section('content')
    @include('shared.catatan.index', [
        'routePrefix' => 'ketua',
        'basePath' => '/ketua/catatan',
    ])
@endsection

@section('scripts')
    @include('shared.catatan.delete-script', ['basePath' => '/ketua/catatan'])
@endsection
