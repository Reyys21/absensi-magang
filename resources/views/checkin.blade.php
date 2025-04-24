@extends('layouts.app')

@section('content')
<h4>Form Check-In</h4>
<form method="POST" action="{{ route('checkin.store') }}" onsubmit="return confirm('Apakah data sudah benar?')">
    @csrf
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
    </div>
    <div class="mb-3">
        <label>Tanggal</label>
        <input type="text" class="form-control" value="{{ now()->toDateString() }}" readonly>
    </div>
    <div class="mb-3">
        <label>Jam</label>
        <input type="text" class="form-control" value="{{ now()->format('H:i:s') }}" readonly>
    </div>
    <button type="submit" class="btn btn-success">Kirim</button>
</form>
@endsection