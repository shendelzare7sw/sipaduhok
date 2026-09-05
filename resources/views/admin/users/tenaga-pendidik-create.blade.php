@extends('layouts.app')

@section('title', 'Tambah Tenaga Pendidik')
@section('page-title', 'Tambah Tenaga Pendidik')
@section('page-subtitle', 'Buat akun dan biodata tenaga pendidik')

@section('content')
    @include('admin.users.partials.tenaga-pendidik-form')
@endsection
