<div class="modal-body p-5 text-center">
    <div class="mb-4">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
    </div>
    <h2 class="fw-bold mb-3">Booking Completed!</h2>
    <p class="text-muted mb-4">Your seat has been successfully reserved. You can download your ticket below.</p>

    <div class="card bg-light border-0 rounded-4 p-4 mb-4 text-start">
        <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-bus-front me-2"></i>{{ $booking->bus->bus_name }}</h5>
        <div class="row g-3">
            <div class="col-6">
                <small class="text-muted d-block">Passenger</small>
                <span class="fw-semibold">{{ $booking->name }}</span>
            </div>
            <div class="col-6 text-end">
                <small class="text-muted d-block">Travel Date</small>
                <span class="fw-semibold">{{ \Carbon\Carbon::parse($booking->travel_date)->format('d M, Y') }}</span>
            </div>
            <div class="col-6">
                <small class="text-muted d-block">From</small>
                <span class="fw-semibold">{{ $booking->entry_point }}</span>
            </div>
            <div class="col-6 text-end">
                <small class="text-muted d-block">To</small>
                <span class="fw-semibold">{{ $booking->exit_point }}</span>
            </div>
            <div class="col-12">
                <small class="text-muted d-block">Seats</small>
                <span class="fw-semibold text-primary">
                    @foreach($booking->seats as $s)
                        {{ $s->seat->seat_number }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </span>
            </div>
        </div>
    </div>

    <div class="d-grid gap-2">
        <a href="{{ route('ticket.download', $booking->id) }}" class="btn btn-success btn-lg rounded-pill shadow-sm">
            <i class="bi bi-download me-2"></i>Download Ticket PDF
        </a>
        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
    </div>
</div>
