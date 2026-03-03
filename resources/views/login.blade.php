@extends('layout.app')
@section('body-bg')
    url("{{ asset('images/bg.jpg') }}")
@endsection
@section('content')

<div class="card shadow-sm p-4 mx-auto" style="max-width: 400px;">
    <h3 class="text-center mb-3">User Login</h3>
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="/login">
        @csrf

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

        <button class="btn btn-primary w-100">Login</button>
    </form>

    {{-- REGISTER OPTION --}}
    <p class="text-center mt-3 mb-0">
        Don’t have an account?
        <a href="/register">Register</a>
    </p>
</div>

@endsection
