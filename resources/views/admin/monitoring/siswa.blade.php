@extends('layouts.sneat')

@section('title', 'Monitoring Siswa')
@section('page-title', 'Monitoring Data Siswa')
@section('page-subtitle', 'Lihat progress LMS dan status tagihan siswa')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.monitoring.styles')
@endsection

@section('content')
    @include('shared.monitoring.siswa', [
        'routeBase' => 'admin.monitoring',
        'scopeLabel' => request('cabang_id') ? 'Cabang terfilter' : 'Semua cabang',
    ])
@endsection
