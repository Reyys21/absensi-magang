@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-white flex items-center justify-center px-6 md:px-16 py-12">
        <div class="flex flex-col md:flex-row w-full max-w-6xl items-center gap-12">

            <div class="w-full md:w-1/2 space-y-6">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Selamat Datang di Absensi Check-Out</h2>
                <p class="text-base md:text-lg text-gray-700">
                    Sepertinya Anda akan check-out.<br>
                    Sekarang, mari isi informasi aktivitas Anda hari ini.
                </p>
                <img src="{{ asset('assets/images/undraw_playing-fetch_x508.svg') }}" alt="Ilustrasi"
                    class="w-[280px] md:w-[360px] mt-6">
            </div>

            <div class="w-full md:w-1/2">
                <form action="{{ route('checkout.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" id="current_time" name="current_time">

                    <div>
                        <label for="activity_title" class="block text-sm font-semibold text-gray-800 mb-1">Judul</label>
                        <input type="text" name="activity_title" id="activity_title" required
                            class="w-full px-4 py-2 rounded-md border border-gray-300 text-gray-900 focus:outline-none active:border-black focus:ring-2 focus:ring-black"
                            placeholder="Masukkan judul aktivitas Anda">
                    </div>

                    <div>
                        <label for="activity_description"
                            class="block text-sm font-semibold text-gray-800 mb-1">Deskripsi</label>
                        <textarea name="activity_description" id="activity_description" rows="5" required
                            class="w-full px-4 py-2 rounded-md border border-gray-300 text-gray-900 focus:outline-none focus:border-black focus:ring-2 focus:ring-black resize-none"
                            placeholder="Masukkan deskripsi singkat aktivitas Anda"></textarea>
                    </div>

                    <div class="flex justify-start">
                        <button type="submit"
                            class="bg-[#171D1D] text-white px-6 py-2 rounded-md font-semibold hover:bg-[#2c3534] transition">
                            Check-Out
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function pad(num) {
            return num < 10 ? '0' + num : num;
        }

        function updateTimeAndDate() {
            const now = new Date();
            const hours = pad(now.getHours());
            const minutes = pad(now.getMinutes());
            // Mengatur detik ke '00' untuk pembaruan setiap menit, kemudian menggunakan detik aktual saat submit.
            document.getElementById('current_time').value = `${hours}:${minutes}:00`;
        }
        updateTimeAndDate(); // Panggil saat halaman dimuat
        setInterval(updateTimeAndDate, 60000); // Perbarui setiap menit

        document.querySelector('form').addEventListener('submit', function() {
            const now = new Date();
            const hours = pad(now.getHours());
            const minutes = pad(now.getMinutes());
            const seconds = pad(now.getSeconds());
            // Perbarui dengan detik aktual saat formulir disubmit
            document.getElementById('current_time').value = `${hours}:${minutes}:${seconds}`;
        });
    </script>
@endsection
