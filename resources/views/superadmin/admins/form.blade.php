@extends('layouts.app')

@section('content')
<div class="flex-1 flex flex-col">
    <header class="bg-white flex flex-row justify-between items-center py-2 px-4 sm:px-6 md:px-8 border-b border-gray-200 sticky top-0 z-10">
        <div>
            <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">
                {{ $admin->exists ? 'Edit Admin' : 'Tambah Admin Baru' }}
            </h1>
        </div>
        @include('layouts.profile')
    </header>

    <main class="flex-1 p-4 sm:p-6 md:p-8 bg-gray-50/50">
        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ $admin->exists ? route('superadmin.admins.update', $admin) : route('superadmin.admins.store') }}" method="POST">
                @csrf
                @if($admin->exists)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            required>
                        @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            required>
                        @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            {{ $admin->exists ? '' : 'required' }}>
                        @if ($admin->exists)
                            <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password.</p>
                        @endif
                        @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            {{ $admin->exists ? '' : 'required' }}>
                        @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bidang_id" class="block text-sm font-medium text-gray-700">Bidang</label>
                        <select name="bidang_id" id="bidang_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm 
                            @if(Auth::user()->hasRole('admin') && !Auth::user()->hasRole('superadmin')) bg-gray-200 cursor-not-allowed @endif"
                            @if(Auth::user()->hasRole('admin') && !Auth::user()->hasRole('superadmin')) disabled @endif
                            required>
                            @foreach ($bidangs as $bidang)
                                <option value="{{ $bidang->id }}"
                                    {{ old('bidang_id', $admin->bidang_id ?? Auth::user()->bidang_id) == $bidang->id ? 'selected' : '' }}>
                                    {{ $bidang->name }}
                                </option>
                            @endforeach
                        </select>
                        {{-- Jika select dinonaktifkan, kita butuh input tersembunyi untuk mengirim nilainya --}}
                        @if(Auth::user()->hasRole('admin') && !Auth::user()->hasRole('superadmin'))
                            <input type="hidden" name="bidang_id" value="{{ Auth::user()->bidang_id }}">
                        @endif
                        @error('bidang_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" id="role"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            required>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}"
                                    {{ (old('role', $admin->roles->first()->name ?? '')) == $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <a href="{{ route('superadmin.admins.index') }}" class="bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                        Batal
                    </a>
                    <button type="submit"
                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ $admin->exists ? 'Perbarui Admin' : 'Simpan Admin' }}
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection