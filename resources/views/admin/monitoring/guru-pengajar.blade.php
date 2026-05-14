@extends('layouts.sneat')

@section('title', 'Monitoring Guru Pengajar')
@section('page-title', 'Monitoring Data Guru Pengajar')
@section('page-subtitle', 'Lihat aktivitas LMS, koreksi, dan progress nilai guru')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.monitoring.styles')
@endsection

@section('content')
    @include('shared.monitoring.guru-pengajar', [
        'routeBase' => 'admin.monitoring',
        'scopeLabel' => request('cabang_id') ? 'Cabang terfilter' : 'Semua cabang',
    ])
@endsection
