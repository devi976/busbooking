<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /* =====================
       REGISTER
    ====================== */

    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // default role
        ]);

        return redirect('/login')->with('success', 'Registration successful. Please login.');
    }

    /* =====================
       LOGIN
    ====================== */

    public function showLogin(Request $request)
{
    // store intended URL if passed
    if ($request->has('redirect')) {
        session(['url.intended' => $request->redirect]);
    }

    return view('login');
}


    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = auth()->user();
        //admin redirect
        if ($user->role === 'admin') {
    return redirect('/admin/dashboard');
} 
       

if ($user->role === 'admin') {
    return redirect('/admin/dashboard');
}

if ($user->role === 'operator') {
    return redirect('/operator/buses');
}

        // 🔥 THIS IS THE MAGIC LINE
        return redirect()->intended('/search');
    }

    return back()->withErrors([
        'email' => 'Invalid login credentials',
    ]);
}

    public function changePasswordForm()
{
    return view('operator.change-password');
}

public function changePassword(Request $request)
{
    $request->validate([
        'password' => 'required|min:6|confirmed'
    ]);

    auth()->user()->update([
        'password' => Hash::make($request->password)
    ]);

    return back()->with('success','Password changed successfully');
}

    /* =====================
       LOGOUT
    ====================== */

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
