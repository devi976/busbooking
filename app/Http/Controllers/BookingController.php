<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\SeatLock;
use App\Models\BookingSeat;
use App\Models\Booking;
use App\Models\Seat;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{

    /**
     * Show seat selection page
     */
    public function selectSeats(Bus $bus, $date)
{
    // Remove expired locks
    SeatLock::where('expires_at','<',now())->delete();

    $travelDate = $date;

    // 🔥 Check for Strike / Closed Bookings
    $isClosed = \App\Models\Announcement::whereDate('target_date', $travelDate)
        ->where('close_bookings', true)
        ->exists();

    if ($isClosed) {
        return redirect('/search')->with('error', "Bookings are currently closed for " . \Carbon\Carbon::parse($travelDate)->format('d M, Y') . ".");
    }

    // Locked seats (ONLY for same date)
    $lockedSeats = SeatLock::where('bus_id',$bus->id)
        ->where('travel_date', $travelDate)
        ->pluck('seat_number')
        ->toArray();

    // Booked seats (ONLY paid + same date)
    $bookedSeatIds = BookingSeat::whereHas('booking', function($q) use ($bus, $travelDate) {
        $q->where('bus_id', $bus->id)
          ->where('travel_date', $travelDate)
          ->where('status', 'paid');
    })->pluck('seat_id')->toArray();

    $seats = [];

    for($i = 1; $i <= $bus->total_seats; $i++) {

        $seatNo = 'A'.$i;

        // Seat type logic
        $position = $i % 4;

        if ($position == 1) $type = 'w';
        elseif ($position == 2) $type = 's';
        elseif ($position == 3) $type = 's';
        else $type = 'w';

        $status = 'available';

        // 🔥 Find seat model
        $seatModel = Seat::where('bus_id', $bus->id)
            ->where('seat_number', $seatNo)
            ->first();

        if ($seatModel && in_array($seatModel->id, $bookedSeatIds)) {
            $status = 'booked';
        }
        elseif (in_array($seatNo, $lockedSeats)) {
            $status = 'locked';
        }

        $seats[] = [
            'number'=>$seatNo,
            'type'=>$type,
            'status'=>$status
        ];
    }

    if (request()->ajax()) {
        return view('partials.select_seats', compact('bus', 'seats', 'travelDate'))->render();
    }

    return view('booking.select-seats', compact('bus', 'seats', 'travelDate'));
}
    /**
     * Lock selected seats
     */
    
    public function confirmSeats(Request $request, Bus $bus, $date)
{
    $request->validate([
        'seats' => 'required|array|min:1',
        'seats.*' => 'string',
    ]);

    $travelDate = $date;

    // Remove expired locks
    SeatLock::where('expires_at','<',now())->delete();

    // Remove previous locks of THIS USER for THIS BUS and DATE
    SeatLock::where('user_id', auth()->id())
        ->where('bus_id', $bus->id)
        ->where('travel_date', $travelDate)
        ->delete();

    // 1. Check if seats are already booked (paid) for this date
    $alreadyBooked = \App\Models\BookingSeat::whereHas('booking', function($q) use ($bus, $travelDate) {
        $q->where('bus_id', $bus->id)
          ->where('travel_date', $travelDate)
          ->where('status', 'paid');
    })->whereHas('seat', function($q) use ($request) {
        $q->whereIn('seat_number', $request->seats);
    })->exists();

    if ($alreadyBooked) {
        if (request()->ajax()) {
            return response()->json(['success' => false, 'error' => 'One or more seats are already booked for this date.'], 422);
        }
        return back()->with('error', 'One or more seats are already booked.');
    }

    // 2. Check if seats are locked by OTHER users for SAME DATE
    $alreadyLocked = SeatLock::where('bus_id', $bus->id)
        ->where('travel_date', $travelDate)
        ->where('user_id', '!=', auth()->id()) // Don't block the user's own locks
        ->whereIn('seat_number', $request->seats)
        ->exists();

    if ($alreadyLocked) {
        if (request()->ajax()) {
            return response()->json(['success' => false, 'error' => 'One or more seats are currently being booked by someone else.'], 422);
        }
        return back()->withErrors([
            'seats' => 'One or more seats are currently locked. Please select different seats.'
        ]);
    }

    // Lock seats properly
    foreach($request->seats as $seat){
        SeatLock::create([
            'bus_id'=>$bus->id,
            'travel_date'=>$travelDate,
            'seat_number'=>$seat,
            'user_id'=>auth()->id(),
            'expires_at'=>now()->addMinutes(5)
        ]);
    }

    session([
        'selected_seats'=>$request->seats
    ]);

    if (request()->ajax()) {
        $selectedSeats = $request->seats;
        $from = session('selected_from');
        $to   = session('selected_to');
        $fareRow = \App\Models\BusFare::where('bus_id', $bus->id)
            ->where('from_stop', $from)
            ->where('to_stop', $to)
            ->first();
        $farePerSeat = $fareRow ? $fareRow->fare : $bus->fare;
        $totalAmount = count($selectedSeats) * $farePerSeat;

        return view('partials.payment', compact('bus', 'farePerSeat', 'totalAmount', 'selectedSeats', 'travelDate'))->render();
    }

    return redirect('/payment/'.$bus->id);
}
       

    /**
     * Show payment page
     */
    public function payment(Bus $bus)
    {
        $selectedSeats = session('selected_seats');

        if(empty($selectedSeats)){
            return redirect('/select-seat/'.$bus->id)
                ->with('error','Session expired. Please select seats again.');
        }

        $from = session('selected_from');
        $to   = session('selected_to');

        $fareRow = \App\Models\BusFare::where('bus_id', $bus->id)
            ->where('from_stop', $from)
            ->where('to_stop', $to)
            ->first();

        $farePerSeat = $fareRow ? $fareRow->fare : $bus->fare;

        $totalAmount = count($selectedSeats) * $farePerSeat;

        return view('booking.payment', compact(
            'bus',
            'farePerSeat',
            'totalAmount',
            'selectedSeats'
        ));
    }

    /**
     * Handle payment success
     */
    public function paymentSuccess(Request $request, Bus $bus, $date)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'payment_method' => 'required',
            'selected_seats' => 'required|array'
        ]);

        $selectedSeats = $request->selected_seats;
        $travelDate = $date;

        // 🔥 Defensive Check: Ensure seats weren't booked by someone else while user was paying
        $alreadyBooked = BookingSeat::whereHas('booking', function($q) use ($bus, $travelDate) {
            $q->where('bus_id', $bus->id)
              ->where('travel_date', $travelDate)
              ->where('status', 'paid');
        })->whereHas('seat', function($q) use ($bus, $selectedSeats) {
            $q->where('bus_id', $bus->id)
              ->whereIn('seat_number', $selectedSeats);
        })->exists();

        if ($alreadyBooked) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'error' => 'Sorry, one or more seats were booked by someone else while you were processing payment.'], 422);
            }
            return redirect('/search')->with('error', 'Seat already booked.');
        }

        $totalAmount = $request->total_amount;

        return \Illuminate\Support\Facades\DB::transaction(function() use ($bus, $request, $selectedSeats, $totalAmount, $travelDate) {
            $booking = Booking::create([
                'bus_id' => $bus->id,
                'user_id' => auth()->id(),
                'name' => $request->name,
                'phone' => $request->phone,
                'travel_date' => session('selected_travel_date'),
                'total_seats' => count($selectedSeats),
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'entry_point' => $request->entry_point,
                'exit_point' => $request->exit_point,
                'status' => 'paid'
            ]);
            
            foreach ($selectedSeats as $seat) {
                $seatModel = \App\Models\Seat::where('seat_number', $seat)
                                ->where('bus_id', $bus->id)
                                ->first();

                if (!$seatModel) {
                    throw new \Exception("Seat {$seat} not found for this bus");
                }

                BookingSeat::create([
                    'booking_id' => $booking->id,
                    'seat_id' => $seatModel->id
                ]);
            }

            // Remove temporary locks for these seats
            SeatLock::where('bus_id', $bus->id)
                ->where('travel_date', $travelDate)
                ->whereIn('seat_number', $selectedSeats)
                ->delete();

            if (request()->ajax()) {
                return view('partials.booking_success', compact('booking'))->render();
            }

            return redirect('/booking-success/'.$booking->id);
        });
    }

    /**
     * Booking success page
     */
    public function bookingSuccess($id)
    {
        $booking = Booking::with('bus','seats')
            ->findOrFail($id);

        return view('booking.success', compact('booking'));
    }

    /**
     * Download ticket PDF
     */
    public function downloadTicket($bookingId)
    {
        $booking = Booking::with('bus')->findOrFail($bookingId);

        $pdf = Pdf::loadView('booking.ticket-pdf', compact('booking'));

        return $pdf->download('Bus_Ticket.pdf');
    }
}