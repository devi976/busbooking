<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display the user's profile and booking history.
     */
    public function index()
    {
        $user = Auth::user();

        // Load bookings with bus details, ordered by travel date descending
        $bookings = $user->bookings()
            ->with('bus')
            ->orderBy('travel_date', 'desc')
            ->get();

        return view('profile', compact('user', 'bookings'));
    }
}
