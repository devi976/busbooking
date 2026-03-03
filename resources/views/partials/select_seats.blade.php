<div class="modal-header bg-primary text-white border-0 py-3 px-4">
    <h5 class="modal-title"><i class="bi bi-ui-checks-grid me-2"></i>Select Seats – {{ $bus->bus_name }}</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-4">
    <div class="alert alert-info py-2 d-flex align-items-center mb-4">
        <i class="bi bi-info-circle-fill me-2"></i>
        <small>Click on available seats to select them. <span style="color: #d4a017; font-weight: bold;">Dark Yellow</span> = Locked, <span style="color: #dc3545; font-weight: bold;">Red</span> = Booked.</small>
    </div>

    <form id="confirm-seats-form" data-bus-id="{{ $bus->id }}" data-date="{{ $travelDate }}">
        @csrf
        <div class="bus-layout-wrapper py-3">
            <div class="bus-layout mx-auto">
                <div class="driver-section mb-4 d-flex justify-content-end pe-4">
                    <div class="driver-wheel shadow-sm">
                        <i class="bi bi-record-circle"></i>
                    </div>
                </div>

                @foreach(array_chunk($seats, 4) as $row)
                    <div class="bus-row mb-3">
                        <div class="seat-group d-flex gap-2">
                            @foreach(array_slice($row, 0, 2) as $seat)
                                <label class="seat-item">
                                    <input type="checkbox" name="seats[]" value="{{ $seat['number'] }}" {{ $seat['status'] !== 'available' ? 'disabled' : '' }}>
                                    <div class="seat-box {{ $seat['status'] }} {{ $seat['type'] }} shadow-xs">
                                        <span class="seat-num">{{ $seat['number'] }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div class="aisle-space mx-3"></div>
                        <div class="seat-group d-flex gap-2">
                            @foreach(array_slice($row, 2, 2) as $seat)
                                <label class="seat-item">
                                    <input type="checkbox" name="seats[]" value="{{ $seat['number'] }}" {{ $seat['status'] !== 'available' ? 'disabled' : '' }}>
                                    <div class="seat-box {{ $seat['status'] }} {{ $seat['type'] }} shadow-xs">
                                        <span class="seat-num">{{ $seat['number'] }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="confirm-seats-btn">
                <span class="spinner-border spinner-border-sm d-none" id="confirm-spinner"></span>
                <span>Continue to Payment</span>
            </button>
        </div>
    </form>
</div>
