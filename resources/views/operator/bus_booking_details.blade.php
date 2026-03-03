@extends('layout.app')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/operator/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/operator/buses">My Buses</a></li>
            <li class="breadcrumb-item"><a href="/operator/bus/{{ $bus->id }}/bookings">Booking Dates</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Tickets Sold: {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</h3>
            <p class="text-muted mb-0">{{ $bus->bus_name }} ({{ $bus->from }} → {{ $bus->to }})</p>
        </div>
        <div class="text-end">
            <h4 class="text-primary mb-0">Total: ₹{{ $bookings->sum('total_amount') }}</h4>
            <span class="badge bg-success">{{ $bookings->count() }} Bookings</span>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Passenger Details</th>
                            <th>Seats</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold">{{ $booking->name }}</div>
                                    <small class="text-muted"><i class="bi bi-telephone"></i> {{ $booking->phone }}</small>
                                </td>
                                <td class="py-3">
                                    @foreach($booking->seats as $bookingSeat)
                                        <span class="badge bg-info text-dark">
                                            {{ $bookingSeat->seat->seat_number ?? 'N/A' }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="py-3">₹{{ $booking->total_amount }}</td>
                                <td class="py-3">
                                    <span class="text-uppercase small fw-bold">{{ $booking->payment_method }}</span>
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <a href="{{ route('ticket.download', $booking->id) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
