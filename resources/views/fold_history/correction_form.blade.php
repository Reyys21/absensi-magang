<form action="{{ route('correction.store') }}" method="POST"
    class="space-y-6 p-6 md:p-8 bg-white rounded-2xl shadow-lg mx-auto w-full max-w-2xl">
    @csrf

    {{-- Input Tanggal Koreksi --}}
    <div>
        <label for="date_to_correct" class="block text-gray-700 text-sm font-medium mb-2">
            📅 Tanggal Absensi yang Dikoreksi:
        </label>
        <input type="date" name="date_to_correct" id="date_to_correct"
            value="{{ old('date_to_correct', $dateToCorrect->format('Y-m-d')) }}"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50
                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                    text-gray-800 shadow-sm transition duration-150 ease-in-out">
        @error('date_to_correct')
            <p class="text-red-600 text-xs italic mt-1">{{ $message }}</p>
        @enderror
    </div>

    <hr class="border-t border-gray-200 my-4">

    <div class="text-center">
        <h4 class="text-xl font-semibold text-gray-800 mb-1">📝 Isi Data Koreksi</h4>
        <p class="text-sm text-gray-600">
            Nilai lama akan terisi otomatis. Hanya ubah kolom yang perlu dikoreksi.
        </p>
    </div>

    {{-- Jam Check-In/Out Baru --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="new_check_in" class="block text-gray-700 text-sm font-medium mb-2">Jam Check-In Baru:</label>
            <input type="time" name="new_check_in" id="new_check_in" value="{{ old('new_check_in', $oldCheckIn) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50
                            focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                            text-gray-800 shadow-sm transition duration-150 ease-in-out">
            @error('new_check_in')
                <p class="text-red-600 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="new_check_out" class="block text-gray-700 text-sm font-medium mb-2">Jam Check-Out Baru:</label>
            <input type="time" name="new_check_out" id="new_check_out"
                value="{{ old('new_check_out', $oldCheckOut) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50
                            focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                            text-gray-800 shadow-sm transition duration-150 ease-in-out">
            @error('new_check_out')
                <p class="text-red-600 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Judul Aktivitas --}}
    <div>
        <label for="new_activity_title" class="block text-gray-700 text-sm font-medium mb-2">Judul Aktivitas
            Baru:</label>
        <input type="text" name="new_activity_title" id="new_activity_title"
            value="{{ old('new_activity_title', $oldActivityTitle) }}" placeholder="Contoh: Meeting Proyek X"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50
                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                    text-gray-800 shadow-sm transition duration-150 ease-in-out">
        @error('new_activity_title')
            <p class="text-red-600 text-xs italic mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Deskripsi Aktivitas --}}
    <div>
        <label for="new_activity_description" class="block text-gray-700 text-sm font-medium mb-2">Deskripsi Aktivitas
            Baru:</label>
        <textarea name="new_activity_description" id="new_activity_description" rows="4"
            placeholder="Jelaskan detail aktivitas Anda... (opsional)"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50
                        focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                        text-gray-800 shadow-sm transition duration-150 ease-in-out">{{ old('new_activity_description', $oldActivityDescription) }}</textarea>
        @error('new_activity_description')
            <p class="text-red-600 text-xs italic mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Alasan Koreksi --}}
    <div>
        <label for="reason" class="block text-gray-700 text-sm font-medium mb-2">
            Alasan Koreksi <span class="text-red-500">*</span>:
        </label>
        <textarea name="reason" id="reason" rows="3" placeholder="Contoh: Lupa check-in, salah input jam, dll."
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50
                        focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                        text-gray-800 shadow-sm transition duration-150 ease-in-out">{{ old('reason') }}</textarea>
        @error('reason')
            <p class="text-red-600 text-xs italic mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Tombol Submit --}}
    <div class="flex justify-end pt-2">
        <button type="submit"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg
                        focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1
                        transition duration-200 ease-in-out shadow-md">
            🚀 Kirim Koreksi
        </button>
    </div>
</form> 