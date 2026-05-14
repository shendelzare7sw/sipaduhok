@extends('layouts.sneat')

@section('title', 'Monitoring Guru Pengajar')
@section('page-title', 'Monitoring Data Guru Pengajar')
@section('page-subtitle', 'Fokus pada guru pengajar di cabang yang Anda kelola')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.monitoring.styles')
@endsection

@section('content')
    @include('shared.monitoring.guru-pengajar', [
        'routeBase' => 'waka.monitoring',
        'scopeLabel' => auth()->user()->cabang->nama_cabang ?? 'Cabang saya',
    ])
@endsection
