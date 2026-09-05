@extends('layouts.app')

@section('title', 'Tambah Siswa')
@section('page-title', 'Tambah Siswa Baru')
@section('page-subtitle', 'Buat akun dan lengkapi data siswa')

@section('content')
    @include('admin.users.partials.siswa-form')
@endsection
