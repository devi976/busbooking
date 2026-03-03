@extends('layout.app')

@section('content')
<div class="container mt-4" style="max-width:500px;">
    <h3>Create Operator</h3>

    <form method="POST" action="/admin/operator/store">
        @csrf

        <input class="form-control mb-2" name="name" placeholder="Operator Name" required>
        <input class="form-control mb-2" name="email" placeholder="Email" required>
        <input type="password" class="form-control mb-3" name="password" placeholder="Temporary Password" required>

        <button class="btn btn-primary w-100">Create Operator</button>
    </form>
</div>
@endsection