<h3>Payment</h3>
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<form method="POST" action="/payment-success/{{ $bus->id }}">
@csrf

<input type="text" name="name" placeholder="Enter Name" required>
<input type="text" name="phone" placeholder="Enter Phone" required>

<p>Seats: {{ implode(',', $selectedSeats) }}</p>

<p>Fare Per Seat: ₹{{ $farePerSeat }}</p>

<p><strong>Total Amount: ₹{{ $totalAmount }}</strong></p>

{{-- ✅ SEND SEATS TO CONTROLLER --}}
@foreach($selectedSeats as $seat)
    <input type="hidden" name="selected_seats[]" value="{{ $seat }}">
@endforeach

{{-- ✅ SEND TOTAL AMOUNT --}}
<input type="hidden" name="total_amount" value="{{ $totalAmount }}">

{{-- ✅ SEND ROUTE --}}
<input type="hidden" name="entry_point" value="{{ session('selected_from') }}">
<input type="hidden" name="exit_point" value="{{ session('selected_to') }}">

<label>Select Payment Method:</label>
<select name="payment_method" required>
    <option value="">Choose Option</option>
    <option value="UPI">UPI</option>
    <option value="Bank Transfer">Bank Transfer</option>
    <option value="Card">Card</option>
</select>

<br><br>
<button class="btn btn-success">Pay Now</button>

</form>