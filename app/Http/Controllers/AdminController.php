<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bus;
use App\Models\Booking;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    /* ===========================
       DASHBOARD
    ============================ */

public function allBuses()
{
     $buses = \App\Models\Bus::with(['operator','bookings.user'])
        ->get();

    return view('admin.buses', compact('buses'));
}

public function deleteBus(Bus $bus)
{
    $bus->delete();
    return back()->with('success','Bus deleted');
}
    public function dashboard()
    {
        $totalUsers = \App\Models\User::where('role','user')->count();
        $totalOperators = \App\Models\User::where('role','operator')->count();
        $totalBuses = \App\Models\Bus::count();
        $totalAnnouncements = \App\Models\Announcement::count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalOperators',
            'totalBuses',
            'totalAnnouncements'
        ));
    }


    /* ===========================
       OPERATOR MANAGEMENT
    ============================ */

    // Show operator list
   public function operators()
{
    $operators = User::where('role', 'operator')
        ->with('buses')
        ->get();

    return view('admin.operators', compact('operators'));
}

    // Show create operator page
    public function createOperator()
    {
        return view('admin.create-operator');
    }

    // Store operator
    public function storeOperator(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'operator'
        ]);

        return redirect('/admin/operators')
                ->with('success','Operator created successfully');
    }

    // Delete operator
    public function deleteOperator(User $user)
    {
        if ($user->role !== 'operator') {
            abort(403);
        }

        $user->delete();

        return back()->with('success', 'Operator deleted successfully');
    }


    /* ===========================
       BUS ACTIVATION CONTROL
    ============================ */

    public function toggleBus(Bus $bus)
    {
        $bus->update([
            'is_active' => !$bus->is_active
        ]);

        return back()->with('success','Bus status updated');
    }


    /* ===========================
       ANNOUNCEMENT SYSTEM
    ============================ */

    public function announcementForm()
    {
        return view('admin.announcement');
    }

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'show_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'target_group' => 'required|in:all,operators,users,booked_users',
            'target_date' => 'required_if:target_group,booked_users|nullable|date'
        ]);

        Announcement::create([
            'message' => $request->message,
            'show_date' => $request->show_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'target_group' => $request->target_group,
            'target_date' => $request->target_date,
            'close_bookings' => $request->has('close_bookings')
        ]);

        return back()->with('success','Announcement added successfully');
    }

    /* ===========================
       ENHANCED BOOKING DETAILS
    ============================ */

    public function busBookings(\App\Models\Bus $bus)
    {
        // Get unique dates with paid bookings for this bus
        $dates = \App\Models\Booking::where('bus_id', $bus->id)
            ->where('status', 'paid')
            ->selectRaw('DATE(travel_date) as date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.bus_booking_dates', compact('bus', 'dates'));
    }

    public function busBookingsByDate(\App\Models\Bus $bus, $date)
    {
        // Get all paid bookings for this bus on this specific date
        $bookings = \App\Models\Booking::where('bus_id', $bus->id)
            ->whereDate('travel_date', $date)
            ->where('status', 'paid')
            ->with(['user'])
            ->get();

        return view('admin.bus_booking_details', compact('bus', 'date', 'bookings'));
    }

}