@extends('layouts.sneat')

@section('title', 'Dashboard Admin')

@section('page-title', 'Overview')
@section('page-subtitle', 'Pantau aktivitas dan statistik sekolah')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/dashboard/admin.css'])
@endsection

@section('content')
    @include('dashboard.admin.partials.developer-alert')
    @include('dashboard.admin.partials.stats')

    <div class="row g-4 mb-4">
        @include('dashboard.admin.partials.charts')

        <div class="col-lg-4 d-flex flex-column gap-4">
            @include('dashboard.admin.partials.quick-links')
            @include('dashboard.admin.partials.recent-logins')
        </div>
    </div>

    @php
        $adminDashboardChartData = compact('chartPendaftaran', 'genderData', 'kelasLabels', 'kelasCounts');
    @endphp

    <script type="application/json" id="admin-dashboard-chart-data">
        {!! json_encode($adminDashboardChartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>
@endsection

@section('scripts')
    @vite(['resources/js/dashboard/admin.js'])
@endsection
