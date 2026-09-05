@extends('layouts.app')

@section('title', 'Edit Tenaga Pendidik - ' . ($tenagaPendidik->nama_lengkap ?? 'N/A'))
@section('page-title', 'Edit Tenaga Pendidik')
@section('page-subtitle', 'Perbarui data ' . ($tenagaPendidik->nama_lengkap ?? 'N/A'))

@section('content')
    @include('admin.users.partials.tenaga-pendidik-form')
@endsection
