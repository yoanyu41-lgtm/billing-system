@extends('layouts.app')

@section('content')
<h2>Add Customer</h2>

<form action="/customer/store" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control">
    </div>

    <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control">
    </div>

    <div class="mb-3">
        <label>Telegram ID</label>
        <input type="text" name="telegram_id" class="form-control">
    </div>

    <div class="mb-3">
        <label>QR Image</label>
        <input type="file" name="qr_image" class="form-control">
    </div>

    <button class="btn btn-primary">Save</button>
</form>
@endsection