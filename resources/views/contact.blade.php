@extends('layout.app')
@section('body-bg')
    url("{{ asset('images/bg.jpg') }}")
@endsection
@section('content')

<div class="card shadow-sm p-4">
    <h2>Contact Us</h2>

    <p><i class="bi bi-envelope"></i> support@viorabusbookingsystem.com</p>
    <p><i class="bi bi-telephone"></i> +91 98765 43210</p>
    <p><i class="bi bi-geo-alt"></i> Chennai, India</p>
</div>

@endsection
