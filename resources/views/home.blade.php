@extends('layout.app')
@section('body-bg')
    url("{{ asset('images/bg.jpg') }}")
@endsection
@section('content')
<h1 class="fw-bold mb-3">Book Bus Tickets Easily</h1>

<p class="lead">
Travel smarter with our secure and fast bus ticket booking platform.
</p>

<a href="/search" class="btn btn-primary btn-lg mb-4">
    Search Buses
</a>

<div class="row g-4 mt-3">
    <div class="col-md-4">
        <div class="border rounded p-3 text-center">
            <i class="bi bi-geo-alt fs-2 text-primary"></i>
            <h5 class="mt-2">Multiple Routes</h5>
            <p>Book buses across different cities.</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="border rounded p-3 text-center">
            <i class="bi bi-credit-card fs-2 text-success"></i>
            <h5 class="mt-2">Secure Payment</h5>
            <p>Safe and encrypted payment system.</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="border rounded p-3 text-center">
            <i class="bi bi-phone fs-2 text-warning"></i>
            <h5 class="mt-2">Easy Booking</h5>
            <p>Simple and quick reservation process.</p>
        </div>
    </div>
</div>
@endsection
