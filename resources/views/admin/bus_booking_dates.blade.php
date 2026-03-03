@extends('layout.app')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/buses">All Buses</a></li>
            <li class="breadcrumb-item active" aria-current="page">Booking Dates</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Booking Dates: {{ $bus->bus_name }}</h3>
        <span class="badge bg-primary fs-6">{{ $bus->from }} → {{ $bus->to }}</span>
    </div>

    <div class="row g-3">
        @forelse($dates as $item)
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-event fs-1 text-primary mb-3"></i>
                        <h5 class="card-title">{{ \Carbon\Carbon::parse($item->date)->format('d M, Y') }}</h5>
                        <p class="text-muted">{{ \Carbon\Carbon::parse($item->date)->format('l') }}</p>
                        <a href="/admin/bus/{{ $bus->id }}/bookings/{{ $item->date }}" class="btn btn-outline-primary w-100">
                            View {{ \App\Models\Booking::where('bus_id', $bus->id)->whereDate('travel_date', $item->date)->where('status', 'paid')->count() }} Tickets
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-info-circle fs-1 d-block mb-3"></i>
                    <h4>No bookings found for this bus yet.</h4>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
