@extends('layouts.sneat')

@section('title', 'Kirim Catatan Baru')
@section('page-title', 'Kirim Catatan Baru')
@section('page-subtitle', 'Kirim catatan atau teguran kepada tenaga pendidik dan siswa')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.catatan.styles')
@endsection

@section('content')
    @include('shared.catatan.create', [
        'routePrefix' => 'waka',
        'recipientMode' => 'single',
    ])
@endsection

@section('scripts')
    @include('shared.catatan.create-scripts', ['recipientMode' => 'single'])
@endsection
