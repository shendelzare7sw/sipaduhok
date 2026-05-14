@extends('layouts.sneat')

@section('title', 'Monitoring Wali Kelas')
@section('page-title', 'Monitoring Data Wali Kelas')
@section('page-subtitle', 'Fokus pada wali kelas di cabang yang Anda kelola')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.monitoring.styles')
@endsection

@section('content')
    @include('shared.monitoring.wali-kelas', [
        'routeBase' => 'waka.monitoring',
        'scopeLabel' => auth()->user()->cabang->nama_cabang ?? 'Cabang saya',
    ])
@endsection
