@extends('layouts.app')

@section('title', 'Edit Kelas')
@section('page-title', 'Edit Kelas')
@section('page-subtitle', 'Perbarui data kelas ' . $kelas->nama_kelas)

@section('content')
    @include('admin.kelas.partials.form')
@endsection
