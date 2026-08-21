@extends('layouts.sneat')

@section('title', 'Info Pembayaran')
@section('page-title', 'Kelola Informasi Pembayaran')
@section('page-subtitle', 'Atur rekening, pembayaran tunai, dan kanal pembayaran digital')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/keuangan/info-pembayaran/index.css'])
@endsection

@section('content')
    @include('keuangan.info-pembayaran.content', ['updateRoute' => route('admin.keuangan.info-pembayaran.update')])
@endsection

@section('scripts')
    @vite(['resources/js/admin/keuangan/info-pembayaran/index.js'])
@endsection
