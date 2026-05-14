@extends('layouts.sneat')

@section('title', 'Monitoring Data Pengguna')
@section('page-title', 'Monitoring Data Pengguna')
@section('page-subtitle', 'Lihat status akun tenaga pendidik dan siswa')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.monitoring.styles')
@endsection

@section('content')
    @include('shared.monitoring.pengguna', [
        'routeBase' => 'ketua.monitoring',
        'scopeLabel' => 'Semua cabang',
    ])
@endsection
