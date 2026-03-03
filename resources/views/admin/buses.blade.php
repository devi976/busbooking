@extends('layout.app')

@section('content')
<div class="container mt-4">

<h3>All Buses</h3>

@foreach($buses as $bus)
<div class="card p-3 mb-3">

    <strong>{{ $bus->bus_name }}</strong>
    <br>
    Route: {{ $bus->from }} → {{ $bus->to }}
    <br>
    Operator: {{ $bus->operator->name ?? 'N/A' }}
    <br>

    @php
        $bookedCount = $bus->bookings->where('status','paid')->sum('total_seats');
    @endphp

    <strong>Booked Seats:</strong> {{ $bookedCount }}
    <br>
    <strong>Available Seats:</strong> {{ $bus->total_seats - $bookedCount }}

    <div class="mt-2">

        {{-- Toggle Active --}}
        <form method="POST" action="/admin/bus/{{ $bus->id }}/toggle" style="display:inline;">
            @csrf
            <button class="btn btn-sm {{ $bus->is_active ? 'btn-warning' : 'btn-success' }}">
                {{ $bus->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </form>

        {{-- Delete --}}
        <form method="POST" action="/admin/bus/{{ $bus->id }}" style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger">
                Delete
            </button>
        </form>

        {{-- Edit --}}
        <a href="/admin/bus/{{ $bus->id }}/edit" class="btn btn-sm btn-primary">
            Edit
        </a>

    </div>

    <hr>

    <div class="d-flex justify-content-between align-items-center">
        <div>
            <strong>Booked Seats (All-time):</strong> {{ $bus->bookings->where('status','paid')->sum('total_seats') }}
        </div>
        <a href="/admin/bus/{{ $bus->id }}/bookings" class="btn btn-primary">
            <i class="bi bi-journal-text me-1"></i> View Booking Details
        </a>
    </div>

</div>
@endforeach

</div>
@endsection