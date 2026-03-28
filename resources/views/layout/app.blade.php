<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Viora – Bus Booking System</title>
<link rel="icon" type="image/png" href="{{ asset('images/favicon.ico') }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: @yield('body-bg', "url('".asset('images/bg.jpg')."')") no-repeat center center fixed;
            background-size: cover;
        }

        .sidebar {
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
            padding-top: 20px;
            overflow-y: auto; /* 🔥 Enable scroll if too many links */
            transition: all 0.3s;
            border-right: 1px solid #333;
        }

        .sidebar a, .sidebar .logout-link {
            color: #ddd;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            transition: 0.2s;
        }

        .sidebar a:hover, .sidebar .logout-link:hover {
            background-color: #0d6efd;
            color: white;
            padding-left: 25px;
        }

        .logout-link {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    color: white;
    font-size: 22px;
    font-weight: 500;
    letter-spacing: 3px;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #444;
}

        .content {
            margin-left: 260px; /* Adjust for wider sidebar */
            padding: 30px;
        }

        .content-card {
            background: rgba(255,255,255,0.95);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        footer {
            text-align: center;
            margin-top: 30px;
            color: #555;
        }
    </style>
</head>
<body>

<!-- LEFT SIDEBAR -->
<div class="sidebar">
    <div class="brand">
    <img src="{{ asset('images/viora-logo.png') }}" 
         alt="Viora Logo" 
         style="height:42px;">
    <span>VIORA</span>
</div>

    <a href="/"><i class="bi bi-house"></i> Home</a>
    <a href="/about"><i class="bi bi-info-circle"></i> About</a>
    <a href="/features"><i class="bi bi-stars"></i> Features</a>
    <a href="/search"><i class="bi bi-search"></i> Search</a>

    @guest
        <a href="/login"><i class="bi bi-box-arrow-in-right"></i> Login</a>
    @endguest

    <a href="/contact"><i class="bi bi-telephone"></i> Contact</a>

    @auth
        @if(auth()->user()->isAdmin())
            <div class="px-3 py-2 text-white fw-bold small">ADMIN PANEL</div>
            <a href="/admin/dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="/admin/operators"><i class="bi bi-people"></i> Manage Operators</a>
            <a href="/admin/buses"><i class="bi bi-bus-front"></i> All Buses</a>
            <a href="/admin/announcement"><i class="bi bi-megaphone"></i> Announcements</a>
            <hr class="text-secondary mx-3 my-2">
        @endif

        @if(auth()->user()->isOperator() && !auth()->user()->isAdmin())
            <div class="px-3 py-2 text-white fw-bold small">OPERATOR PANEL</div>
            <a href="/operator/buses"><i class="bi bi-bus-front"></i> My Buses</a>
            <a href="/operator/bus/create"><i class="bi bi-plus-circle"></i> Add Bus</a>
            <hr class="text-secondary mx-3 my-2">
        @endif

        <a href="{{ route('profile') }}"><i class="bi bi-person-circle"></i> My Profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-link text-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    @endauth
</div>

<!-- MAIN CONTENT -->
<div class="content">

    {{-- Notification Bar Section --}}
    @php
        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');
        $user = auth()->user();
        $dismissed = session()->get('dismissed_announcements', []);

        $announcements = \App\Models\Announcement::whereDate('show_date', $today)
            ->whereTime('start_time', '<=', $currentTime)
            ->whereTime('end_time', '>=', $currentTime)
            ->get()
            ->filter(function($item) use ($user, $dismissed) {
                // Skip if dismissed by current user in this session
                if (in_array($item->id, $dismissed)) return false;

                if ($item->target_group === 'all') return true;
                if (!$user) return false;
                if ($user->isAdmin()) return true;
                if ($item->target_group === 'operators' && $user->isOperator()) return true;
                if ($item->target_group === 'users' && $user->isUser()) return true;
                if ($item->target_group === 'booked_users' && $user->isUser()) {
                    return \App\Models\Booking::where('user_id', $user->id)
                        ->whereDate('travel_date', $item->target_date)
                        ->exists();
                }
                return false;
            });
    @endphp

    @foreach($announcements as $item)
        <div class="alert alert-warning border shadow-sm mb-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="bi bi-megaphone-fill fs-4 me-3 text-danger"></i>
                <div>
                    <strong>IMPORTANT:</strong> {{ $item->message }}
                    @if($item->close_bookings)
                        <span class="badge bg-danger ms-2">Booking Closed for {{ \Carbon\Carbon::parse($item->target_date)->format('d M') }}</span>
                    @endif
                </div>
            </div>
            <form action="/announcement/{{ $item->id }}/dismiss" method="POST" class="ms-3">
                @csrf
                <button type="submit" class="btn-close" aria-label="Close"></button>
            </form>
        </div>
    @endforeach

    {{-- Back Button Logic --}}
    @php
        $showBack = false;
        $backRoutes = [
            'operator/bus/create',
            'operator/bus/*/edit',
            'operator/change-password',
            'operator/bus/*/bookings',
            'operator/bus/*/bookings/*',
            'admin/operator/create',
            'admin/bus/*/bookings',
            'admin/bus/*/bookings/*',
            'payment/*',
            'bus/*/live-tracking',
            'select-seat/*/*',
            'profile'
        ];
        foreach($backRoutes as $route) {
            if(request()->is($route)) {
                $showBack = true;
                break;
            }
        }
    @endphp

    @if($showBack)
        <button onclick="history.back()" class="btn btn-secondary btn-sm mb-3">
            ← Back
        </button>
    @endif

    {{-- Session Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-octagon-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="content-card">
        @yield('content')
    </div>

    <footer>
       © 2026 Viora – Bus Booking System
    </footer>

</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>