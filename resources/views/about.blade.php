@extends('layout.app')
@section('body-bg')
    url("{{ asset('images/bg.jpg') }}")
@endsection
@section('content')

<div class="card shadow-sm p-4">
    <h2 class="mb-3">About the System</h2>

    <p>
        The Bus Ticket Booking System is a Laravel-based web application designed
        to provide a simple and efficient online bus reservation experience.
    </p>

    <p>
        This system allows users to search buses, view routes, and understand
        the booking workflow in a user-friendly manner.
    </p>

    <hr>

    <h5>Project Objectives</h5>
    <ul>
        <li>Reduce manual ticket booking</li>
        <li>Provide quick and easy reservations</li>
        <li>Improve customer experience</li>
    </ul>
</div>

@endsection
