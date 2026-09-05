@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')
@section('page-title', 'Edit Mata Pelajaran')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('content')
    @include('admin.mata-pelajaran.partials.form')
@endsection
