@extends('layouts.sneat')

@section('title', 'Daftar Catatan')
@section('page-title', 'Manajemen Catatan')
@section('page-subtitle', 'Kelola catatan yang dikirim dan diterima')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.catatan.styles')
@endsection

@section('content')
    @include('shared.catatan.index', [
        'routePrefix' => 'waka',
        'basePath' => '/waka/catatan',
        'showDirection' => true,
        'toolbarDescription' => 'Kelola catatan, instruksi, dan teguran yang Anda kirim atau terima.',
        'listTitle' => 'Daftar Catatan',
        'emptyTitle' => 'Belum ada catatan',
        'emptyDescription' => 'Catatan yang Anda kirim atau terima akan tampil di halaman ini.',
    ])
@endsection

@section('scripts')
    @include('shared.catatan.delete-script', ['basePath' => '/waka/catatan'])
@endsection
