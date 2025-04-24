@extends('layouts.app')

@section('content')
<h4>Form Check-Out</h4>
<form method="POST" action="{{ route('checkout.store') }}" onsubmit="return confirm('Apakah data sudah benar?')">
    @csrf
    <div class="mb-3">
        <label>Tanggal</label>
        <input type="text" class="form-control" value="{{ now()->toDateString() }}" readonly>
    </div>
    <div class="mb-3">
        <label>Jam</label>
        <input type="text" class="form-control" value="{{ now()->format('H:i:s') }}" readonly>
    </div>
    <div class="mb-3">
        <label>Judul Aktivitas</label>
        <input type="text" name="activity_title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Deskripsi Aktivitas</label>
        <textarea name="activity_description" class="form-control" rows="3" required></textarea>
    </div>
    <button type="submit" class="btn btn-warning">Kirim</button>
</form>
@endsection