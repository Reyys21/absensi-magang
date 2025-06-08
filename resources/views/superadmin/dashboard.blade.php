@extends('layouts.app')

@section('content')
<main id="main-content" class="flex-1 p-4 sm:p-6 md:p-10 bg-white shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl sm:text-2xl font-bold">Superadmin Dashboard</h1>
        @include('layouts.profile')
    </div>
    <div class="container mx-auto">
        <p>Selamat datang, Superadmin! Anda memiliki akses penuh.</p>
    </div>
</main>
@endsection