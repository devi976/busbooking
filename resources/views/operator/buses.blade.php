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

                    <a href="/operator/bus/{{ $bus->id }}/bookings" class="btn btn-sm btn-primary">
                        <i class="bi bi-journal-text"></i> View Bookings
                    </a>
                </div>

            </div>
        @endforeach


    @else
        <p>No buses added yet.</p>
    @endif
</div>
@endsection
