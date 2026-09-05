@extends('layouts.app')

@section('title', 'Tambah Kelas')
@section('page-title', 'Tambah Kelas Baru')
@section('page-subtitle', 'Buat ruang belajar untuk tahun ajaran yang dipilih')

@section('content')
    @include('admin.kelas.partials.form')
@endsection
