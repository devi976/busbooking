@extends('layout.app')

@section('content')
<div class="container mt-4">

    <h2>Admin Dashboard</h2>

    <div class="row g-4 mb-4 mt-2">
        <div class="col-md-3">
            <div class="card bg-primary text-white text-center p-3 shadow-sm border-0">
                <i class="bi bi-people fs-2 mb-2"></i>
                <h5>Total Users</h5>
                <h2 class="fw-bold">{{ $totalUsers }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white text-center p-3 shadow-sm border-0">
                <i class="bi bi-shield-lock fs-2 mb-2"></i>
                <h5>Operators</h5>
                <h2 class="fw-bold">{{ $totalOperators }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white text-center p-3 shadow-sm border-0">
                <i class="bi bi-bus-front fs-2 mb-2"></i>
                <h5>Total Buses</h5>
                <h2 class="fw-bold">{{ $totalBuses }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark text-center p-3 shadow-sm border-0">
                <i class="bi bi-megaphone fs-2 mb-2"></i>
                <h5>Announcements</h5>
                <h2 class="fw-bold">{{ $totalAnnouncements }}</h2>
            </div>
        </div>
    </div>

    <h4 class="mb-3">Quick Actions</h4>
    <div class="row g-3">
        <div class="col-md-4">
            <a href="/admin/buses" class="btn btn-outline-primary w-100 p-3 shadow-sm">
                <i class="bi bi-plus-circle me-2"></i> Manage All Buses
            </a>
        </div>
        <div class="col-md-4">
            <a href="/admin/operators" class="btn btn-outline-success w-100 p-3 shadow-sm">
                <i class="bi bi-people me-2"></i> Manage Operators
            </a>
        </div>
        <div class="col-md-4">
            <a href="/admin/announcement" class="btn btn-outline-warning w-100 p-3 shadow-sm">
                <i class="bi bi-megaphone me-2"></i> Announcement Settings
            </a>
        </div>
    </div>

</div>
@endsection