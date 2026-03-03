@extends('layout.app')

@section('content')
<div class="container mt-4" style="max-width: 600px;">
    <h3 class="mb-3">Add Bus</h3>

    <form method="POST" action="/operator/bus/store">
        @csrf

        <input class="form-control mb-2" name="bus_name" placeholder="Bus Name" required>

        <input class="form-control mb-2" name="from" placeholder="From" required>

        <input class="form-control mb-2" name="to" placeholder="To" required>

        <textarea class="form-control mb-2" name="stops"
                  placeholder="Intermediate Stops (comma separated)"></textarea>

        <input type="number" class="form-control mb-2"
               name="total_seats" placeholder="Total Seats" required>
        <input type="text" class="form-control mb-2" name="contact_number" placeholder="Bus Contact Number">
        {{-- Availability --}}
        <label class="mt-2">Bus Availability</label>
        <select name="availability_type" class="form-control mb-2">
            <option value="single">One Day Only</option>
            <option value="daily">Every Day</option>
            <option value="weekly">Specific Days</option>
        </select>

        <label>Select Days (if Specific)</label>
        <select name="available_days[]" class="form-control mb-3" multiple>
            @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                <option value="{{ $day }}">{{ $day }}</option>
            @endforeach
        </select>
        <h5 class="mt-4">Stop Timings</h5>

<div id="timing-wrapper">
    <div class="row mb-2">
        <div class="col">
            <input class="form-control" name="stop_name[]" placeholder="Stop Name">
        </div>
        <div class="col">
            <input type="time" class="form-control" name="arrival_time[]">
        </div>
    </div>
</div>

<button type="button"
        class="btn btn-sm btn-secondary"
        onclick="addTimingRow()">
    + Add Stop Time
</button>

<script>
function addTimingRow() {
    document.getElementById('timing-wrapper').insertAdjacentHTML('beforeend', `
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

        {{-- Fare per Stop --}}
        <h5>Fare per Stop</h5>

        <div id="fare-wrapper">
            <div class="row mb-2">
                <div class="col">
                    <input class="form-control" name="from_stop[]" placeholder="From">
                </div>
                <div class="col">
                    <input class="form-control" name="to_stop[]" placeholder="To">
                </div>
                <div class="col">
                    <input class="form-control" name="fare[]" placeholder="Fare">
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-sm btn-secondary mb-3"
                onclick="addFareRow()">+ Add Another Fare</button>

        <button class="btn btn-primary w-100">Save Bus</button>
    </form>
</div>

<script>
function addFareRow() {
    document.getElementById('fare-wrapper').insertAdjacentHTML('beforeend', `
        <div class="row mb-2">
            <div class="col">
                <input class="form-control" name="from_stop[]" placeholder="From">
            </div>
            <div class="col">
                <input class="form-control" name="to_stop[]" placeholder="To">
            </div>
            <div class="col">
                <input class="form-control" name="fare[]" placeholder="Fare">
            </div>
        </div>
    `);
}
</script>
@endsection
