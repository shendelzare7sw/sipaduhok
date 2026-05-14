@extends('layouts.sneat')

@section('title', 'Kirim Catatan Baru')
@section('page-title', 'Kirim Catatan Baru')
@section('page-subtitle', 'Kirim catatan atau pesan kepada pengguna')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @include('shared.catatan.styles')
@endsection

@section('content')
    @include('shared.catatan.create', [
        'routePrefix' => 'admin',
        'recipientMode' => 'multi',
    ])
@endsection

@section('scripts')
    @include('shared.catatan.create-scripts', [
        'recipientMode' => 'multi',
        'usersForIndividu' => $usersForIndividu ?? [],
    ])
@endsection
