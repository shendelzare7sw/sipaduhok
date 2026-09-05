@extends('layouts.app')

@section('title', 'Edit Siswa - ' . ($siswa->nama_lengkap ?? 'N/A'))
@section('page-title', 'Edit Data Siswa')
@section('page-subtitle', 'Perbarui data ' . ($siswa->nama_lengkap ?? 'siswa'))

@section('content')
    @include('admin.users.partials.siswa-form')
@endsection
