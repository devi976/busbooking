@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">My Buses</h3>

    <a href="/operator/bus/create" class="btn btn-primary mb-3">
        Add Bus
    </a>

    @if($buses->count())
        @foreach($buses as $bus)
            <div class="card mb-3 p-3">
                <strong>{{ $bus->bus_name }}</strong>

                <p class="mb-1">
                    Route: {{ $bus->from }} → {{ $bus->to }}
                </p>

                @if($bus->stops)
                    <p class="mb-1">
                      Stops: {{ $bus->stops }}
                    </p>
                @endif
                @if($bus->fares->count())
                    <h6 class="mt-2">Fare Details</h6>
                    <ul class="mb-1">
                         @foreach($bus->fares as $fare)
                            <li>
                                {{ $fare->from_stop }} → {{ $fare->to_stop }}
                                : ₹{{ $fare->fare }}
                            </li>
                        @endforeach
                    </ul>
                @endif


                <p class="mb-1">
                    Availability:
                    @if($bus->availability_type === 'daily')
                        Every Day
                    @elseif($bus->availability_type === 'weekly')
                        {{ $bus->available_days }}
                    @else
                        One Day Only
                    @endif
                </p>


                <p class="mb-1">
                  Available Seats: {{ $bus->available_seats }}
                </p>

                <p class="mb-1">
                  Fare: ₹{{ $bus->fare }}
                </p>

                <div class="d-flex gap-2 mt-3">
                    <a href="/operator/bus/{{ $bus->id }}/edit" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    
                    <button class="btn btn-sm btn-info text-white report-location-btn" data-bus-id="{{ $bus->id }}">
                        <i class="bi bi-geo-alt-fill"></i> Report Live Location
                    </button>

                    <a href="/operator/bus/{{ $bus->id }}/bookings" class="btn btn-sm btn-primary">
                        <i class="bi bi-journal-text"></i> View Bookings
                    </a>
                    
                    <span class="ms-auto small text-muted align-self-center last-update-text">
                        @if($bus->last_location_update)
                            Last: {{ $bus->last_location_update->diffForHumans() }}
                        @endif
                    </span>
                </div>

            </div>
        @endforeach

        <script>
            document.querySelectorAll('.report-location-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const busId = this.dataset.busId;
                    const btnElement = this;
                    const originalContent = btnElement.innerHTML;
                    
                    if (!navigator.geolocation) {
                        alert('Geolocation is not supported by your browser');
                        return;
                    }

                    btnElement.disabled = true;
                    btnElement.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Locating...';

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            fetch(`/bus/${busId}/update-location`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ lat, lng })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    btnElement.classList.replace('btn-info', 'btn-success');
                                    btnElement.innerHTML = '<i class="bi bi-check-circle"></i> Reported!';
                                    setTimeout(() => {
                                        btnElement.classList.replace('btn-success', 'btn-info');
                                        btnElement.innerHTML = originalContent;
                                        btnElement.disabled = false;
                                    }, 3000);
                                } else {
                                    throw new Error(data.error || 'Update failed');
                                }
                            })
                            .catch(error => {
                                alert('Error updating location: ' + error.message);
                                btnElement.innerHTML = originalContent;
                                btnElement.disabled = false;
                            });
                        },
                        (error) => {
                            alert('Error getting location: ' + error.message);
                            btnElement.innerHTML = originalContent;
                            btnElement.disabled = false;
                        },
                        { enableHighAccuracy: true }
                    );
                });
            });
        </script>

    @else
        <p>No buses added yet.</p>
    @endif
</div>
@endsection
