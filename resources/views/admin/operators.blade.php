@extends('layout.app')

@section('content')
<h2>Operators</h2>

<a href="/admin/operator/create" class="btn btn-success mb-3">
    Add Operator
</a>

@foreach($operators as $operator)
<div class="card p-3 mb-4">

    <h5>{{ $operator->name }}</h5>
    <p>{{ $operator->email }}</p>

    <form method="POST" action="/admin/operator/{{ $operator->id }}">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-sm">Delete Operator</button>
    </form>

    <hr>

    <h6>Buses Added:</h6>

    @forelse($operator->buses as $bus)
        <div class="border p-2 mb-2">
            <strong>{{ $bus->bus_name }}</strong>
            <p>
                Route: {{ $bus->from }} → {{ $bus->to }}
            </p>
            <p>
                Seats: {{ $bus->total_seats }}
            </p>
        </div>
    @empty
        <p>No buses added.</p>
    @endforelse

</div>
@endforeach

@endsection