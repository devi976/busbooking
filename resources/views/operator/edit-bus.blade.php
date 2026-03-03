@extends('layout.app')

@section('content')
<div class="container mt-4" style="max-width: 700px;">
    <h3 class="mb-3">Edit Bus</h3>

    <form method="POST" action="/operator/bus/{{ $bus->id }}/update">
        @csrf

        {{-- BASIC DETAILS --}}
        <label>Bus Name</label>
        <input class="form-control mb-2" name="bus_name"
               value="{{ $bus->bus_name }}" required>

        <label>Starting Point</label>
        <input class="form-control mb-2" name="from"
               value="{{ $bus->from }}" required>

        <label>Final Destination</label>
        <input class="form-control mb-2" name="to"
               value="{{ $bus->to }}" required>

        <label>Intermediate Stops (comma separated)</label>
        <textarea class="form-control mb-3" name="stops"
                  placeholder="Eg: HMT Jnc, Kakkanad Bus Station, Kakkanad Metro Station">{{ $bus->stops }}</textarea>

        <label>Bus Contact Number</label>
        <input class="form-control mb-3" name="contact_number"
               value="{{ $bus->contact_number }}" placeholder="Bus Contact Number">

        <hr>

        {{-- STOP TIMINGS --}}
        <h5>Stop Timings</h5>
        <p class="text-muted">Enter arrival time at each stop</p>

        <div id="timings-wrapper">
            @foreach($bus->stopTimings as $timing)
                <div class="row mb-2">
                    <div class="col">
                        <input class="form-control"
                               name="stop_name[]"
                               value="{{ $timing->stop_name }}"
                               placeholder="Stop Name">
                    </div>
                    <div class="col">
                        <input type="time"
                               class="form-control"
                               name="arrival_time[]"
                               value="{{ $timing->arrival_time }}">
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-sm btn-secondary mb-3"
                onclick="addTimingRow()">
            + Add Stop Timing
        </button>

        <hr>

        {{-- FARE PER STOP --}}
        <h5>Fare per Route</h5>

        <div id="fare-wrapper">
            @foreach($bus->fares as $fare)
                <div class="row mb-2">
                    <div class="col">
                        <input class="form-control"
                               name="from_stop[]"
                               value="{{ $fare->from_stop }}"
                               placeholder="From Stop">
                    </div>
                    <div class="col">
                        <input class="form-control"
                               name="to_stop[]"
                               value="{{ $fare->to_stop }}"
                               placeholder="To Stop">
                    </div>
                    <div class="col">
                        <input class="form-control"
                               name="fare[]"
                               value="{{ $fare->fare }}"
                               placeholder="Fare">
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-sm btn-secondary mb-3"
                onclick="addFareRow()">
            + Add Another Fare
        </button>

        <hr>

        {{-- AVAILABILITY --}}
        <label>Bus Availability</label>
        <select name="availability_type" class="form-control mb-2">
            <option value="single" {{ $bus->availability_type == 'single' ? 'selected' : '' }}>
                One Day Only
            </option>
            <option value="daily" {{ $bus->availability_type == 'daily' ? 'selected' : '' }}>
                Every Day
            </option>
            <option value="weekly" {{ $bus->availability_type == 'weekly' ? 'selected' : '' }}>
                Specific Days
            </option>
        </select>

        @php
            $days = $bus->available_days ? explode(',', $bus->available_days) : [];
        @endphp

        <label>Select Days (if Specific)</label>
        <select name="available_days[]" class="form-control mb-3" multiple>
            @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                <option value="{{ $day }}" {{ in_array($day, $days) ? 'selected' : '' }}>
                    {{ $day }}
                </option>
            @endforeach
        </select>

        <hr>

        {{-- SEATS --}}
        <label>Total Seats</label>
        <input type="number" class="form-control mb-2"
               name="total_seats"
               value="{{ $bus->total_seats }}" required>

        <p class="text-muted">
            Available seats will be recalculated automatically
        </p>

        <button class="btn btn-primary w-100">Update Bus</button>
    </form>
</div>

{{-- SCRIPTS --}}
<script>
function addFareRow() {
    document.getElementById('fare-wrapper').insertAdjacentHTML('beforeend', `
        <div class="row mb-2">
            <div class="col">
                <input class="form-control" name="from_stop[]" placeholder="From Stop">
            </div>
            <div class="col">
                <input class="form-control" name="to_stop[]" placeholder="To Stop">
            </div>
            <div class="col">
                <input class="form-control" name="fare[]" placeholder="Fare">
            </div>
        </div>
    `);
}

function addTimingRow() {
    document.getElementById('timings-wrapper').insertAdjacentHTML('beforeend', `
        <div class="row mb-2">
            <div class="col">
                <input class="form-control" name="stop_name[]" placeholder="Stop Name">
            </div>
            <div class="col">
                <input type="time" class="form-control" name="arrival_time[]">
            </div>
        </div>
    `);
}
</script>
@endsection
