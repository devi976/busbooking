@extends('layout.app')

@section('content')
<div class="container">
    <div class="row">
        {{-- User Details Card --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> My Profile</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Account Type:</strong> <span class="badge bg-info text-capitalize">{{ $user->role }}</span></p>
                </div>
            </div>
        </div>

        {{-- Booking History --}}
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Booking History</h5>
                </div>
                <div class="card-body p-0">
                    @if($bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bus</th>
                                        <th>Date</th>
                                        <th>Seats</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->bus->name ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($booking->travel_date)->format('d M, Y') }}</td>
                                            <td>{{ $booking->total_seats }}</td>
                                            <td>₹{{ number_format($booking->total_amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('ticket.download', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <p class="text-muted">You haven't made any bookings yet.</p>
                            <a href="/search" class="btn btn-primary btn-sm">Search Buses</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
