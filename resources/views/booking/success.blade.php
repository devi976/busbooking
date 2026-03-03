<h2 style="color:green;">✅ Payment Successful! Seat Booked.</h2>

<p><strong>Bus:</strong> {{ $booking->bus->bus_name }}</p>

<p><strong>Passenger:</strong> {{ $booking->name }}</p>

<p><strong>Seats:</strong>
@foreach($booking->seats as $bookingSeat)
    {{ $bookingSeat->seat->seat_number }}{{ !$loop->last ? ',' : '' }}
@endforeach
</p>

<p><strong>From:</strong> {{ $booking->entry_point }}</p>
<p><strong>To:</strong> {{ $booking->exit_point }}</p>

<p><strong>Total Paid:</strong> ₹{{ $booking->total_amount }}</p>

<p><strong>Payment Method:</strong> {{ $booking->payment_method }}</p>

<a href="{{ route('ticket.download', $booking->id) }}" 
   class="btn btn-primary">
   Download Ticket
</a>