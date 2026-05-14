@extends('layouts.sneat')

@section('title', 'Detail Catatan')
@section('page-title', 'Detail Catatan')
@section('page-subtitle', 'Informasi lengkap catatan yang dikirim')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.catatan.styles')
@endsection

@section('content')
    @include('shared.catatan.show', ['routePrefix' => 'waka'])
@endsection
