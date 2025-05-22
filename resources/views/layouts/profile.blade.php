<div class="relative">
    @auth {{-- Memastikan user sudah login --}}
        <span>{{ Auth::user()->name }}</span> {{-- Menampilkan nama user --}}
    @endauth
</div>


