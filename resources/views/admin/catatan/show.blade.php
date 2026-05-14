@extends('layouts.sneat')

@section('title', 'Detail Catatan')
@section('page-title', 'Detail Catatan')
@section('page-subtitle', 'Lihat detail catatan yang telah dikirim')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.catatan.styles')
@endsection

@section('content')
    @include('shared.catatan.show', ['routePrefix' => 'admin'])
@endsection
