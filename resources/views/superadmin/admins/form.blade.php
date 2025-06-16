@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ isset($admin) ? 'Edit Admin' : 'Tambah Admin Baru' }}</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
        <form action="{{ isset($admin) ? route('superadmin.admins.update', $admin) : route('superadmin.admins.store') }}" method="POST">
            @csrf
            @if (isset($admin))
                @method('PUT')
            @endif

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $admin->name ?? '') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $admin->email ?? '') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="bidang_id" class="block text-sm font-medium text-gray-700">Tugaskan ke Bidang</label>
                    <select name="bidang_id" id="bidang_id" required class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="" disabled {{ !isset($admin) ? 'selected' : '' }}>Pilih Bidang...</option>
                        @foreach($bidangs as $bidang)
                            <option value="{{ $bidang->id }}" {{ old('bidang_id', $admin->bidang_id ?? '') == $bidang->id ? 'selected' : '' }}>
                                {{ $bidang->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('bidang_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" {{ !isset($admin) ? 'required' : '' }} class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @if (isset($admin))<p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password.</p>@endif
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 mt-6">
                <a href="{{ route('superadmin.admins.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Batal</a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-indigo-700">
                    {{ isset($admin) ? 'Simpan Perubahan' : 'Buat Akun Admin' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection