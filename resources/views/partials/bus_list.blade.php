@if($buses->count())
    @foreach($buses as $bus)
        @php
            // Local variables for arrival and booking status
            $to = request('to');
            $from = request('from');
            $requestDate = request('date');
            
            $arrival = $bus->stopTimings->first(function ($t) use ($to) {
                return strtolower($t->stop_name) === strtolower($to);
            });

            $bookingOpen = false;
            if ($arrival && $requestDate) {
                $busDateTime = \Carbon\Carbon::parse($requestDate . ' ' . $arrival->arrival_time);
                $cutoffTime  = $busDateTime->copy()->subMinutes(30);
                $bookingOpen = now()->lt($cutoffTime);
            }

            $fare = $bus->fares->first(function ($f) use ($from, $to) {
                return strtolower($f->from_stop) === strtolower($from)
                    && strtolower($f->to_stop) === strtolower($to);
            });
        @endphp

        <div class="card mb-4 shadow-sm border-0 bus-card overflow-hidden">
            <div class="row g-0">
                <div class="col-md-8 p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="text-primary mb-1">{{ $bus->bus_name }}</h4>
                            <p class="text-muted mb-0">
                                <i class="bi bi-geo-alt-fill me-1"></i>
                                {{ ucfirst($from) }} <i class="bi bi-arrow-right mx-2"></i> {{ ucfirst($to) }}
                            </p>
                        </div>
                        <div class="text-end">
                            <h3 class="text-success fw-bold mb-0">
                                @if($fare) ₹{{ $fare->fare }} @else N/A @endif
                            </h3>
                            <small class="text-muted">per seat</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded shadow-xs">
                                <small class="text-muted d-block">Type</small>
                                <strong>{{ ucfirst($bus->availability_type) }}</strong>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            @if($bus->last_location_update)
                                <span class="badge bg-success-soft text-success">
                                    <i class="bi bi-broadcast me-1"></i> Live Tracking Available
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($bus->stopTimings->count())
                        <div class="mt-3">
                            <button class="btn btn-sm btn-link p-0 text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#timings-{{ $bus->id }}">
                                <i class="bi bi-clock me-1"></i> View All Stop Timings
                            </button>
                            <div class="collapse mt-2" id="timings-{{ $bus->id }}">
                                <div class="list-group list-group-flush small border rounded shadow-xs">
                                    @foreach($bus->stopTimings->sortBy('arrival_time') as $t)
                                        <div class="list-group-item d-flex justify-content-between py-1">
                                            <span>{{ $t->stop_name }}</span>
                                            <span class="fw-bold">{{ \Carbon\Carbon::parse($t->arrival_time)->format('h:i A') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-4 bg-light p-4 d-flex flex-column justify-content-center border-start text-center">
                    @if($bookingOpen)
                        @auth
                            <button type="button" class="btn btn-primary btn-lg w-100 mb-2 shadow-sm book-btn-ajax" data-bus-id="{{ $bus->id }}" data-date="{{ $requestDate }}">
                                <i class="bi bi-ticket-perforated me-2"></i> Book Now
                            </button>
                        @else
                            <a href="/login?redirect=/select-seat/{{ $bus->id }}/{{ $requestDate }}" class="btn btn-outline-primary btn-lg w-100 mb-2">
                                Login to Book
                            </a>
                        @endauth
                    @else
                        <button class="btn btn-secondary btn-lg w-100 mb-2" disabled>
                            <i class="bi bi-lock-fill me-2"></i> Booking Closed
                        </button>
                    @endif

                    <a href="/bus/{{ $bus->id }}/live-tracking" class="btn btn-outline-info w-100 shadow-xs">
                        <i class="bi bi-geo-fill me-2"></i> Track Live Location
                    </a>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="text-center py-5 bg-light rounded border shadow-sm">
        <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
        <h5 class="text-muted">No buses found for this route and time.</h5>
        <p class="text-muted small">Try adjusting your search criteria or checking a different date.</p>
    </div>
@endif
