@extends('layouts.sneat')

@section('title', 'Kelola Flyer')
@section('page-title', 'Kelola Flyer / Iklan')
@section('page-subtitle', 'Pop-up informasi untuk siswa saat login')

@section('sidebar-menu')
    @include('sekretaris.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.akademik.styles')
@endsection

@section('content')
    @include('shared.akademik.flyer-index', [
        'routePrefix' => 'sekretaris',
        'basePath' => '/sekretaris/flyer',
    ])
@endsection
