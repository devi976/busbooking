<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\BusFare;
use App\Models\BusStopTiming;
use App\Models\Seat;
use Illuminate\Http\Request;

class BusController extends Controller
{

    /* ===================================
       SEARCH FORM
    =================================== */

    public function searchForm()
    {
        return view('search');
    }


    /* ===================================
       SEARCH FUNCTION (USER)
    =================================== */

    public function search(Request $request)
    {
        $request->validate([
            'from' => 'required',
            'to'   => 'required',
            'date' => 'required|date',
        ]);

        // 🔥 Check for Strike / Closed Bookings
        $isClosed = \App\Models\Announcement::whereDate('target_date', $request->date)
            ->where('close_bookings', true)
            ->exists();

        if ($isClosed) {
            return back()->withInput()->with('error', "Sorry, bookings are closed for " . \Carbon\Carbon::parse($request->date)->format('d M, Y') . " due to a scheduled strike or maintenance.");
        }

        $from = strtolower(trim($request->from));
        $to   = strtolower(trim($request->to));
        $day  = date('l', strtotime($request->date));
        $time = $request->time ?? null;

        // 🔥 Get buses (ONLY ACTIVE BUSES)
        $buses = Bus::with(['fares', 'stopTimings'])
            ->where('is_active', true)
            ->where(function ($query) use ($from, $to) {
                $query->whereRaw('LOWER(`from`) = ?', [$from])
                      ->orWhereRaw('LOWER(`to`) = ?', [$to])
                      ->orWhereRaw('LOWER(stops) LIKE ?', ["%$to%"]);
            })
            ->where(function ($query) use ($day) {
                $query->where('availability_type', 'daily')
                      ->orWhere(function ($q) use ($day) {
                          $q->where('availability_type', 'weekly')
                            ->where('available_days', 'LIKE', "%$day%");
                      });
            })
            ->get();

        // ⏱ Filter by time if provided
        if ($time) {
            $buses = $buses->filter(function ($bus) use ($to, $time) {
                foreach ($bus->stopTimings as $timing) {
                    if (
                        strtolower($timing->stop_name) === $to &&
                        $timing->arrival_time >= $time
                    ) {
                        return true;
                    }
                }
                return false;
            });
        }
        session([
    'selected_travel_date'=> $request->date,
    'selected_from' => $request->from,
    'selected_to'   => $request->to
    
]);
        return view('search', compact('buses', 'from', 'to', 'time'))
                ->with('requestDate', $request->date);
    }


    /* ===================================
       AJAX SEARCH
    =================================== */

    public function searchAjax(Request $request)
    {
        $from = strtolower(trim($request->from));
        $to   = strtolower(trim($request->to));
        $day  = date('l', strtotime($request->date));
        
        $buses = Bus::with(['fares', 'stopTimings'])
            ->where('is_active', true)
            ->where(function ($query) use ($from, $to) {
                $query->whereRaw('LOWER(`from`) = ?', [$from])
                      ->orWhereRaw('LOWER(`to`) = ?', [$to])
                      ->orWhereRaw('LOWER(stops) LIKE ?', ["%$to%"]);
            })
            ->where(function ($query) use ($day) {
                $query->where('availability_type', 'daily')
                      ->orWhere(function ($q) use ($day) {
                          $q->where('availability_type', 'weekly')
                            ->where('available_days', 'LIKE', "%$day%");
                      });
            })
            ->get();
        session([
            'selected_travel_date' => $request->date,
            'selected_from' => $request->from,
            'selected_to' => $request->to
        ]);

        return view('partials.bus_list', compact('buses'))->render();
    }

    /* ===================================
       LIVE TRACKING
    =================================== */

    public function updateLocation(Request $request, Bus $bus)
    {
        // Only operator or admin can update
        if (auth()->user()->role !== 'admin' && $bus->operator_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $bus->update([
            'current_lat' => $request->lat,
            'current_lng' => $request->lng,
            'last_location_update' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function getLocation(Bus $bus)
    {
        return response()->json([
            'lat' => $bus->current_lat,
            'lng' => $bus->current_lng,
            'last_update' => $bus->last_location_update ? $bus->last_location_update->diffForHumans() : 'Never',
            'bus_name' => $bus->bus_name
        ]);
    }

    public function liveTracking(Bus $bus)
    {
        // Load stop timings so the view can access the schedule
        $bus->load('stopTimings');
        return view('bus.tracking', compact('bus'));
    }


    /* ===================================
       OPERATOR FUNCTIONS
    =================================== */

    // Show operator buses
    public function operatorBuses()
    {
        $buses = Bus::where('operator_id', auth()->id())->get();
        return view('operator.buses', compact('buses'));
    }

    public function operatorBusBookings(\App\Models\Bus $bus)
    {
        if ($bus->operator_id !== auth()->id()) abort(403);

        $dates = \App\Models\Booking::where('bus_id', $bus->id)
            ->where('status', 'paid')
            ->selectRaw('DATE(travel_date) as date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('operator.bus_booking_dates', compact('bus', 'dates'));
    }

    public function operatorBusBookingsByDate(\App\Models\Bus $bus, $date)
    {
        if ($bus->operator_id !== auth()->id()) abort(403);

        $bookings = \App\Models\Booking::where('bus_id', $bus->id)
            ->whereDate('travel_date', $date)
            ->where('status', 'paid')
            ->with(['user', 'seats.seat'])
            ->get();

        return view('operator.bus_booking_details', compact('bus', 'date', 'bookings'));
    }


    // Show add bus form
    public function create()
    {
        return view('operator.add-bus');
    }


    // Store new bus
    public function store(Request $request)
    {
        $request->validate([
            'bus_name'     => 'required',
            'from'         => 'required',
            'to'           => 'required',
            'total_seats'  => 'required|integer|min:1',
            'fare'         => 'required|array',
            'fare.*'       => 'required|numeric|min:0',
            'contact_number'=>'nullable|string',
        ]);

        $maxFare = 0;
        if (!empty($request->fare) && is_array($request->fare)) {
            $validFares = array_filter($request->fare, 'is_numeric');
            if (count($validFares) > 0) {
                $maxFare = max($validFares);
            }
        }

        // ✅ Create Bus
        $bus = Bus::create([
            'bus_name'       => $request->bus_name,
            'from'           => $request->from,
            'to'             => $request->to,
            'travel_date'    => now()->toDateString(),
            'stops'          => $request->stops,
            'total_seats'    => $request->total_seats,
            'available_seats'=> $request->total_seats,
            'fare'           => $maxFare,
            'availability_type' => $request->availability_type,
            'available_days' => is_array($request->available_days)
                ? implode(',', $request->available_days)
                : null,
            'operator_id'    => auth()->id(),
            'contact_number' => $request->contact_number,
        ]);
        for ($i = 1; $i <= $request->total_seats; $i++) {
    Seat::create([
        'bus_id' => $bus->id,
        'seat_number' => 'A' . $i,
        'seat_type' => 'middle'
    ]);
}
        // ✅ Save Fares
        if ($request->fare) {
            foreach ($request->fare as $i => $fare) {
                if ($fare) {
                    BusFare::create([
                        'bus_id'   => $bus->id,
                        'from_stop'=> $request->from_stop[$i],
                        'to_stop'  => $request->to_stop[$i],
                        'fare'     => $fare,
                    ]);
                }
            }
        }

        // ✅ Save Stop Timings
        if ($request->stop_name && $request->arrival_time) {
            foreach ($request->stop_name as $i => $stop) {
                if ($stop && $request->arrival_time[$i]) {
                    BusStopTiming::create([
                        'bus_id'       => $bus->id,
                        'stop_name'    => $stop,
                        'arrival_time' => $request->arrival_time[$i],
                    ]);
                }
            }
        }

        return redirect('/operator/buses')
                ->with('success', 'Bus added successfully');
    }



    // Show edit form
    public function edit(Bus $bus)
    {
        if (
            auth()->user()->role !== 'admin' &&
            $bus->operator_id !== auth()->id()
        ) {
            abort(403);
        }

        return view('operator.edit-bus', compact('bus'));
    }



    // Update bus
    public function update(Request $request, Bus $bus)
    {
        if (
            auth()->user()->role !== 'admin' &&
            $bus->operator_id !== auth()->id()
        ) {
            abort(403);
        }

        $maxFare = $bus->fare;
        if (!empty($request->fare) && is_array($request->fare)) {
            $validFares = array_filter($request->fare, 'is_numeric');
            if (count($validFares) > 0) {
                $maxFare = max($validFares);
            }
        }

        // Update main details
        $bus->update([
            'bus_name'       => $request->bus_name,
            'from'           => $request->from,
            'to'             => $request->to,
            'stops'          => $request->stops,
            'fare'           => $maxFare,
            'total_seats'    => $request->total_seats,
            'available_seats'=> $request->total_seats,
            'availability_type' => $request->availability_type,
            'available_days' => is_array($request->available_days)
                ? implode(',', $request->available_days)
                : null,
            'contact_number' => $request->contact_number,
        ]);

        // 🔄 Reset fares
        $bus->fares()->delete();

        if ($request->fare) {
            foreach ($request->fare as $i => $fare) {
                if ($fare) {
                    BusFare::create([
                        'bus_id'   => $bus->id,
                        'from_stop'=> $request->from_stop[$i],
                        'to_stop'  => $request->to_stop[$i],
                        'fare'     => $fare,
                    ]);
                }
            }
        }

        // 🔄 Reset stop timings
        $bus->stopTimings()->delete();

        if ($request->stop_name && $request->arrival_time) {
            foreach ($request->stop_name as $i => $stop) {
                if ($stop && $request->arrival_time[$i]) {
                    BusStopTiming::create([
                        'bus_id'       => $bus->id,
                        'stop_name'    => $stop,
                        'arrival_time' => $request->arrival_time[$i],
                    ]);
                }
            }
        }

        return redirect('/operator/buses')
                ->with('success', 'Bus updated successfully');
    }

    public function dismissAnnouncement($id)
    {
        $dismissed = session()->get('dismissed_announcements', []);
        if (!in_array($id, $dismissed)) {
            $dismissed[] = $id;
        }
        session()->put('dismissed_announcements', $dismissed);
        return back();
    }

}