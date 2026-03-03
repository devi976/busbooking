@extends('layout.app')
@section('body-bg')
    url("{{ asset('images/bg.jpg') }}")
@endsection
@section('content')

<div class="card shadow-sm p-4 mx-auto" style="max-width: 400px;">
    <h3 class="text-center mb-3">User Registration</h3>

    <form method="POST" action="/register">
        @csrf

        <input 
            type="text" 
            name="name"
            class="form-control mb-3" 
            placeholder="Name"
            required
        >

        <input 
            type="email" 
            name="email"
            class="form-control mb-3" 
            placeholder="Email"
            required
        >

        <input 
            type="password" 
            name="password"
            class="form-control mb-3" 
            placeholder="Password"
            required
        >

        <button class="btn btn-primary w-100">
            Register
        </button>
    </form>

    <p class="text-center mt-3 mb-0">
        Already have an account?
        <a href="/login">Login</a>
    </p>
</div>

@endsection
