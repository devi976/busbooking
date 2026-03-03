@extends('layout.app')

@section('content')
<h2>Announcement Settings</h2>

<form method="POST" action="/admin/announcement">
    @csrf

    <div class="mb-3">
        <label>Message</label>
        <textarea name="message" class="form-control" required></textarea>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Show Date</label>
            <input type="date" name="show_date" class="form-control" required>
        </div>
        <div class="col-md-3 mb-3">
            <label>Start Time</label>
            <input type="time" name="start_time" class="form-control" required>
        </div>
        <div class="col-md-3 mb-3">
            <label>End Time</label>
            <input type="time" name="end_time" class="form-control" required>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Target Group</label>
            <select name="target_group" id="target_group" class="form-control" required onchange="updateFormVisibility()">
                <option value="all">Everyone (Guest + Users + Operators)</option>
                <option value="operators">Only Operators</option>
                <option value="users">Only Registered Users</option>
                <option value="booked_users">Users with Booking on Specific Date</option>
            </select>
        </div>
        <div class="col-md-6 mb-3" id="target_date_wrapper" style="display: none;">
            <label id="target_date_label">Impact / Booking Date</label>
            <input type="date" name="target_date" class="form-control" id="target_date_input">
        </div>
    </div>

    <div class="mb-3 form-check ms-1">
        <input type="checkbox" name="close_bookings" class="form-check-input" id="close_bookings" onchange="updateFormVisibility()">
        <label class="form-check-label text-danger fw-bold" for="close_bookings">
            <i class="bi bi-slash-circle"></i> Close bookings for this date? (Strike/Maintenance)
        </label>
    </div>

    <script>
        function updateFormVisibility() {
            var group = document.getElementById('target_group').value;
            var closeChecked = document.getElementById('close_bookings').checked;
            var wrapper = document.getElementById('target_date_wrapper');
            var input = document.getElementById('target_date_input');
            var label = document.getElementById('target_date_label');

            if (closeChecked) {
                wrapper.style.display = 'block';
                input.setAttribute('required', 'required');
                label.innerText = "Strike / Closure Date";
            } else if (group === 'booked_users') {
                wrapper.style.display = 'block';
                input.setAttribute('required', 'required');
                label.innerText = "Target Booking Date";
            } else {
                wrapper.style.display = 'none';
                input.removeAttribute('required');
            }
        }
        
        // Initial run to set correct state
        updateFormVisibility();
    </script>

    <button class="btn btn-danger w-100 py-2 mt-2">Save Announcement</button>
</form>
@endsection