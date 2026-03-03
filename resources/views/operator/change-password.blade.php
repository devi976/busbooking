@extends('layout.app')

@section('content')
<div class="container mt-4" style="max-width:400px;">
    <h3 class="mb-3">Change Password</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="/operator/change-password">
        @csrf

        <input type="password"
               name="password"
               class="form-control mb-2"
               placeholder="New Password"
               required>

        <input type="password"
               name="password_confirmation"
               class="form-control mb-3"
               placeholder="Confirm Password"
               required>

        <button class="btn btn-success w-100">
            Update Password
        </button>
    </form>
</div>
@endsection
