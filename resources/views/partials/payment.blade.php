<div class="modal-header bg-success text-white border-0 py-3 px-4">
    <h5 class="modal-title"><i class="bi bi-credit-card me-2"></i>Payment Details</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-4">
    <div class="row g-4">
        <div class="col-md-6">
            <h6 class="text-muted small text-uppercase mb-3">Booking Summary</h6>
            <div class="p-3 bg-light rounded border">
                <p class="mb-1"><strong>Bus:</strong> {{ $bus->bus_name }}</p>
                <p class="mb-1"><strong>Route:</strong> {{ session('selected_from') }} <i class="bi bi-arrow-right"></i> {{ session('selected_to') }}</p>
                <p class="mb-1"><strong>Seats:</strong> 
                    @foreach($selectedSeats as $s) 
                        <span class="badge bg-primary">{{ $s }}</span> 
                    @endforeach
                </p>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Total Amount:</span>
                    <h5 class="text-success mb-0">₹{{ $totalAmount }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h6 class="text-muted small text-uppercase mb-3">Passenger Information</h6>
            <form id="payment-form" data-bus-id="{{ $bus->id }}" data-date="{{ $travelDate }}">
                @csrf
                <input type="hidden" name="total_amount" value="{{ $totalAmount }}">
                <input type="hidden" name="entry_point" value="{{ session('selected_from') }}">
                <input type="hidden" name="exit_point" value="{{ session('selected_to') }}">
                @foreach($selectedSeats as $s)
                    <input type="hidden" name="selected_seats[]" value="{{ $s }}">
                @endforeach

                <div class="mb-3">
                    <label class="form-label small">Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small">Phone Number</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small">Payment Method</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="upi">UPI / GPay / PhonePe</option>
                        <option value="card">Credit / Debit Card</option>
                    </div>
                </select>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-success btn-lg shadow-sm" id="pay-btn">
                        <span class="spinner-border spinner-border-sm d-none" id="pay-spinner"></span>
                        <span>Complete Booking</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
