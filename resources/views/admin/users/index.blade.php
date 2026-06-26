@extends('layouts.sneat')

@section('title', 'Manajemen User')

@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Overview data Tenaga Pendidik, Siswa, dan Wali Siswa')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/users/index.css'])
@endsection

@section('content')
    <div data-admin-users-index
        data-delete-tenaga-pendidik-url-template="{{ route('admin.users.delete-tenaga-pendidik', ['id' => '__ID__']) }}"
        data-delete-siswa-url-template="{{ route('admin.users.delete-siswa', ['id' => '__ID__']) }}">
        @include('admin.users.partials.index-stats')

        @include('admin.users.partials.recent-tenaga-pendidik')
        @include('admin.users.partials.recent-siswa')
        @include('admin.users.partials.recent-wali-siswa')
    </div>

    @include('admin.users.partials.delete-modals')
@endsection

@section('scripts')
    @vite(['resources/js/admin/users/index.js'])
@endsection
