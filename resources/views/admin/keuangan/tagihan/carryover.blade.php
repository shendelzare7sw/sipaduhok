@extends('layouts.sneat')

@section('title', 'Tarik Tunggakan ke TA Aktif')
@section('page-title', 'Tarik Tunggakan')
@section('page-subtitle', 'Alihkan tunggakan TA lama menjadi tagihan baru di TA aktif')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@php $baseRouteName = 'admin.keuangan.tagihan'; @endphp

@section('content')
    @include('keuangan-shared.carryover._content', ['baseRouteName' => $baseRouteName])
@endsection
