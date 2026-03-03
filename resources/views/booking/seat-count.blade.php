<form method="POST" action="/book/{{ $bus->id }}/seats">
@csrf
<label>Number of Seats</label>
<input type="number" name="seat_count" min="1" max="6" required>
<button class="btn btn-primary">Continue</button>
</form>
