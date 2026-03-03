@extends('layout.app')

@section('content')
<div class="container mt-4">

    <h3 class="text-center mb-2">
        Select Seats – {{ $bus->bus_name }}
    </h3>

    <p class="text-center">
        Route: {{ $bus->from }} → {{ $bus->to }}
    </p>

    <form method="POST" action="/confirm-seats/{{$bus->id}}">
        @csrf

        <div class="bus-layout">

            @foreach(array_chunk($seats, 4) as $row)
                <div class="bus-row">

                    {{-- LEFT SIDE --}}
                    <div class="seat-group">
                        @foreach(array_slice($row, 0, 2) as $seat)
                            <label class="seat">
                               <input type="checkbox"
       name="seats[]"
       value="{{ $seat['number'] }}"
       {{ $seat['status'] !== 'available' ? 'disabled' : '' }}>
                                <div class="seat-box {{ $seat['status'] }}">
                                    <span>{{ $seat['number'] }}</span>
                                    <small>{{ $seat['type'] }}</small>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- AISLE --}}
                    <div class="aisle"></div>

                    {{-- RIGHT SIDE --}}
                    <div class="seat-group">
                        @foreach(array_slice($row, 2, 2) as $seat)
                            <label class="seat">
                                <input type="checkbox" name="seats[]" value="{{ $seat['number'] }}">
                                <div class="seat-box {{ $seat['status'] }}">
                                    <span>{{ $seat['number'] }}</span>
                                    <small>{{ $seat['type'] }}</small>
                                </div>
                            </label>
                        @endforeach
                    </div>

                </div>
            @endforeach

        </div>

        <div class="text-center mt-4">
            <button class="btn btn-success px-5">
                Continue to Payment
            </button>
        </div>

    </form>
</div>

<style>
.bus-layout {
    margin-top: 30px;
}

.bus-row {
    display: flex;
    justify-content: center;
    margin-bottom: 18px;
}

.seat-group {
    display: flex;
    gap: 14px;
}

.aisle {
    width: 70px;
}

.seat input {
    display: none;
}

.seat-box {
    width: 60px;
    height: 80px;
    border-radius: 12px;
    border: 2px solid #6c757d;
    text-align: center;
    padding-top: 8px;
    cursor: pointer;
    background: #fff;
}
/* Available seat */
.seat-box.available {
    background: #28a745;
    color: white;
}

/* Locked seat */
.seat-box.locked {
    background: #ffc107;
    color: black;
}

/* Booked seat */
.seat-box.booked {
    background: #dc3545;
    color: white;
}
.seat-box span {
    font-weight: bold;
    display: block;
}

.seat-box small {
    font-size: 12px;
}

/* seat types */
.seat-box.w { background: #e3f2fd; } /* Window */
.seat-box.m { background: #fff3cd; } /* Middle */
.seat-box.s { background: #e2e3e5; } /* Side */

/* selected */
.seat input:checked + .seat-box {
    background: #198754;
    color: #fff;
    border-color: #198754;
}
.seat input:disabled + .seat-icon {
    background: #adb5bd;
    cursor: not-allowed;
    opacity: 0.6;
}

</style>
@endsection
