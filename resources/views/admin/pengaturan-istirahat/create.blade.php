@extends('layouts.app')

@section('title', 'Tambah Waktu Istirahat')
@section('page-title', 'Tambah Waktu Istirahat')
@section('page-subtitle', 'Tambahkan jeda yang akan memblokir slot jadwal')

@section('content')
    @include('admin.pengaturan-istirahat.partials.form')
@endsection
