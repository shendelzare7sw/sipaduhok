@extends('layouts.app')

@section('title', 'Info Pembayaran')
@section('page-title', 'Kelola Informasi Pembayaran')
@section('page-subtitle', 'Atur rekening, pembayaran tunai, dan kanal pembayaran digital')

@section('content')
    @include('keuangan.info-pembayaran.content', ['updateRoute' => route('admin.keuangan.info-pembayaran.update')])
@endsection
