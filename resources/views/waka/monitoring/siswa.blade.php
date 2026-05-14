@extends('layouts.sneat')

@section('title', 'Monitoring Siswa')
@section('page-title', 'Monitoring Data Siswa')
@section('page-subtitle', 'Fokus pada siswa di cabang yang Anda kelola')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.monitoring.styles')
@endsection

@section('content')
    @include('shared.monitoring.siswa', [
        'routeBase' => 'waka.monitoring',
        'scopeLabel' => auth()->user()->cabang->nama_cabang ?? 'Cabang saya',
    ])
@endsection
