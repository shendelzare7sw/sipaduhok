@extends('layouts.sneat')

@section('title', 'Info Pembayaran')
@section('page-title', 'Kelola Informasi Pembayaran')
@section('page-subtitle', 'Atur rekening, pembayaran tunai, dan kanal pembayaran digital')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/bendahara/info-pembayaran/index.css'])
@endsection

@section('content')
    @include('keuangan.info-pembayaran.content', ['updateRoute' => route('bendahara.info-pembayaran.update')])
@endsection

@section('scripts')
    @vite(['resources/js/bendahara/info-pembayaran/index.js'])
@endsection
