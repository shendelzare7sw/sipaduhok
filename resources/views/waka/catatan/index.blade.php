@extends('layouts.sneat')

@section('title', 'Daftar Catatan')
@section('page-title', 'Manajemen Catatan')
@section('page-subtitle', 'Kirim catatan atau teguran kepada tenaga pendidik dan siswa')

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
    ])
@endsection

@section('scripts')
    @include('shared.catatan.delete-script', ['basePath' => '/waka/catatan'])
@endsection
