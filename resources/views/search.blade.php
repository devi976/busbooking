@extends('layout.app')

@section('body-bg')
    url("{{ asset('images/roadmap.jpg') }}")
@endsection

@section('content')

<div class="search-bg">
    <div class="search-overlay">
        <div class="container py-5">
            <div class="card shadow-lg p-0 mx-auto search-card border-0 overflow-hidden" style="max-width: 900px;">
                <div class="bg-primary p-4 text-white text-center">
                    <h2 class="mb-1"><i class="bi bi-bus-front me-2"></i>Find Your Journey</h2>
                    <p class="mb-0 opacity-75">Search for available buses across your favorite routes</p>
                </div>

                <div class="p-4">
                    {{-- SEARCH FORM --}}
                    <form id="search-form" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="small text-muted mb-1">From</label>
                            <div class="input-group shadow-xs">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-geo-alt text-primary"></i></span>
                                <input type="text" name="from" class="form-control border-start-0" placeholder="Source" value="{{ old('from', $from ?? '') }}" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="small text-muted mb-1">To</label>
                            <div class="input-group shadow-xs">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-geo text-primary"></i></span>
                                <input type="text" name="to" class="form-control border-start-0" placeholder="Destination" value="{{ old('to', $to ?? '') }}" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="small text-muted mb-1">Date</label>
                            <div class="input-group shadow-xs">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-check text-primary"></i></span>
                                <input type="date" name="date" class="form-control border-start-0" value="{{ old('date', request('date', date('Y-m-d'))) }}" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="small text-muted mb-1">Time (Optional)</label>
                            <div class="input-group shadow-xs">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-clock text-primary"></i></span>
                                <input type="time" name="time" class="form-control border-start-0" value="{{ old('time') }}">
                            </div>
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5 mt-2 shadow-sm" id="search-btn">
                                <span class="spinner-border spinner-border-sm d-none" id="search-spinner" role="status"></span>
                                <span id="search-text">Search Buses</span>
                            </button>
                        </div>
                    </form>

                    <hr class="my-4 opacity-10">

                    {{-- SEARCH RESULTS --}}
                    <div id="search-results">
                        @if(isset($buses))
                            @include('partials.bus_list', ['buses' => $buses])
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-compass fs-1 text-muted mb-3 d-block opacity-25"></i>
                                <h5 class="text-muted">Enter your route details to see available buses.</h5>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- BOOKING MODAL --}}
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem;">
            <div id="booking-modal-body" class="p-0">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Loading seat layout...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .search-card { border-radius: 1.5rem; }
    .shadow-xs { box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
    .bus-card { 
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 1rem !important;
    }
    .bus-card:hover { 
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    /* Seat Layout Styles */
    .bus-layout { max-width: 320px; border: 4px solid #dee2e6; padding: 20px; border-radius: 20px; background: #f8f9fa; }
    .driver-wheel { width: 40px; height: 40px; border-radius: 50%; border: 3px solid #6c757d; display: flex; align-items: center; justify-content: center; }
    .seat-item input { display: none; }
    .seat-box { width: 45px; height: 45px; border-radius: 8px; border: 2px solid #ced4da; display: flex; align-items: center; justify-content: center; background: #fff; cursor: pointer; transition: all 0.2s; position: relative; }
    
    .seat-box.available { border-color: #198754; color: #198754; }
    .seat-box.available:hover { background: #e8f5e9; }
    
    /* Requested Locked color: Dark Yellow */
    .seat-box.locked { background: #d4a017; border-color: #b8860b; color: #fff; cursor: not-allowed; opacity: 0.9; }
    .seat-box.locked::after { content: 'L'; position: absolute; font-size: 0.6rem; bottom: 2px; right: 2px; opacity: 0.5; }

    /* Requested Booked color: Red */
    .seat-box.booked { background: #dc3545; border-color: #b02a37; color: #fff; cursor: not-allowed; }
    .seat-box.booked::after { content: 'B'; position: absolute; font-size: 0.6rem; bottom: 2px; right: 2px; opacity: 0.5; }

    .seat-item input:checked + .seat-box { background: #198754; color: #fff; border-color: #198754; transform: scale(0.95); }
    .seat-num { font-weight: bold; font-size: 0.9rem; }
</style>

<script>
// Search AJAX Logic
document.getElementById('search-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const results = document.getElementById('search-results');
    const formData = new FormData(this);
    const params = new URLSearchParams(formData).toString();

    results.style.opacity = '0.5';
    fetch(`/search-ajax?${params}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            results.innerHTML = html;
            results.style.opacity = '1';
            window.history.pushState({}, '', `${window.location.pathname}?${params}`);
        });
});

// Event Delegation for Modal Booking flow
document.addEventListener('click', function(e) {
    // 1. Open Seat Selection Modal
    if (e.target.closest('.book-btn-ajax')) {
        e.preventDefault();
        const btn = e.target.closest('.book-btn-ajax');
        const busId = btn.dataset.busId;
        const travelDate = btn.dataset.date;
        const url = `/select-seat/${busId}/${travelDate}`;
        const modalBody = document.getElementById('booking-modal-body');
        const modalElement = document.getElementById('bookingModal');
        const modal = new bootstrap.Modal(modalElement);
        
        modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2 text-muted">Loading seat layout...</p></div>';
        modal.show();

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => modalBody.innerHTML = html);
    }
});

// 2. Handle Confirm Seats (Next Step)
document.addEventListener('submit', function(e) {
    if (e.target.id === 'confirm-seats-form') {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const bus_id = form.dataset.busId;
        const travelDate = form.dataset.date; // We'll add this to the partial
        
        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';

        fetch(`/confirm-seats/${bus_id}/${travelDate}`, {
            method: 'POST',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new FormData(form)
        })
        .then(async res => {
            if (!res.ok) {
                const data = await res.json();
                throw new Error(data.error || data.message || 'Please select at least one seat.');
            }
            return res.text();
        })
        .then(html => {
            document.getElementById('booking-modal-body').innerHTML = html;
        })
        .catch(err => {
            alert(err.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    // 3. Handle Payment Submission (Final Step)
    if (e.target.id === 'payment-form') {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const bus_id = form.dataset.busId;
        const travelDate = form.dataset.date; // We'll add this to the payment partial

        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Completing Booking...';

        fetch(`/payment-success/${bus_id}/${travelDate}`, {
            method: 'POST',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new FormData(form)
        })
        .then(async res => {
            if (!res.ok) {
                const data = await res.json();
                throw new Error(data.error || data.message || 'Payment failed');
            }
            return res.text();
        })
        .then(html => {
            document.getElementById('booking-modal-body').innerHTML = html;
        })
        .catch(err => {
            alert(err.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
});
</script>

@endsection
@if(isset($announcement))
    <div class="alert alert-danger text-center">
        {{ $announcement->message }}
    </div>
@endif
