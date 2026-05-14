@extends('layouts.sneat')

@section('title', 'Monitoring Wali Kelas')
@section('page-title', 'Monitoring Data Wali Kelas')
@section('page-subtitle', 'Lihat progress penyelesaian rapor per wali kelas')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.monitoring.styles')
@endsection

@section('content')
    @include('shared.monitoring.wali-kelas', [
        'routeBase' => 'admin.monitoring',
        'scopeLabel' => request('cabang_id') ? 'Cabang terfilter' : 'Semua cabang',
    ])
@endsection
