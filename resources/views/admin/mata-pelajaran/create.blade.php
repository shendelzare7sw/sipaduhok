@extends('layouts.app')

@section('title', 'Tambah Mata Pelajaran')
@section('page-title', 'Tambah Mata Pelajaran')
@section('page-subtitle', 'Siapkan pelajaran sebelum menyusun guru dan jadwal')

@section('content')
    @include('admin.mata-pelajaran.partials.form')
@endsection
