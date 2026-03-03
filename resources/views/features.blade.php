@extends('layout.app')
@section('body-bg')
    url("{{ asset('images/bg.jpg') }}")
@endsection
@section('content')

<h2 class="text-center mb-4">System Features</h2>

<div class="row g-4">

    <div class="col-md-4">
        <div class="card feature-card shadow-sm h-100 p-3">
            <i class="bi bi-search fs-2 text-primary"></i>
            <h5 class="mt-2">Bus Search</h5>
            <p>Search buses by source, destination, and date.</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card feature-card shadow-sm h-100 p-3">
            <i class="bi bi-person-check fs-2 text-success"></i>
            <h5 class="mt-2">User Accounts</h5>
            <p>Login and manage booking history.</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card feature-card shadow-sm h-100 p-3">
            <i class="bi bi-shield-lock fs-2 text-danger"></i>
            <h5 class="mt-2">Secure System</h5>
            <p>Data protection with Laravel security features.</p>
        </div>
    </div>

</div>

@endsection
