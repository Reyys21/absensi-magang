@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white flex items-center justify-center px-6 md:px-16 py-12">
    <div class="flex flex-col md:flex-row w-full max-w-6xl items-center gap-12">

        <!-- LEFT: Text and Illustration -->
        <div class="w-full md:w-1/2 space-y-6">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Welcome to Attendance Check-Out</h2>
            <p class="text-base md:text-lg text-gray-700">
                Looks like you’re going to check out.<br>
                Now let’s fill in your activity information to day.
            </p>
            <img src="{{ asset('assets/images/undraw_playing-fetch_x508.svg') }}" alt="Illustration"
                class="w-[280px] md:w-[360px] mt-6">
        </div>

        <!-- RIGHT: Form -->
        <div class="w-full md:w-1/2">
            <form action="{{ route('checkout.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" id="current_time" name="current_time">

                <!-- Title -->
                <div>
                    <label for="activity_title" class="block text-sm font-semibold text-gray-800 mb-1">Title</label>
                    <input type="text" name="activity_title" id="activity_title" required
                        class="w-full px-4 py-2 rounded-md border border-gray-300 text-gray-900 focus:outline-none active:border-black focus:ring-2 focus:ring-black"
                        placeholder="Enter your activity title">
                </div>

                <!-- Description -->
                <div>
                    <label for="activity_description"
                        class="block text-sm font-semibold text-gray-800 mb-1">Description</label>
                    <textarea name="activity_description" id="activity_description" rows="5" required
                        class="w-full px-4 py-2 rounded-md border border-gray-300 text-gray-900 focus:outline-none focus:border-black focus:ring-2 focus:ring-black resize-none"
                        placeholder="Enter a short description of your activity"></textarea>
                </div>

                <!-- Submit Button -->
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

<!-- Local Time Script -->
<script>
function pad(num) {
    return num < 10 ? '0' + num : num;
}

function updateTimeAndDate() {
    const now = new Date();
    const hours = pad(now.getHours());
    const minutes = pad(now.getMinutes());
    document.getElementById('current_time').value = `${hours}:${minutes}:00`;
}
updateTimeAndDate();
setInterval(updateTimeAndDate, 60000);

document.querySelector('form').addEventListener('submit', function() {
    const now = new Date();
    const hours = pad(now.getHours());
    const minutes = pad(now.getMinutes());
    const seconds = pad(now.getSeconds());
    document.getElementById('current_time').value = `${hours}:${minutes}:${seconds}`;
});
</script>
@endsection