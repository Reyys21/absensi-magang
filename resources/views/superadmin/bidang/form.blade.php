@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        {{ isset($bidang) ? 'Edit Bidang' : 'Tambah Bidang Baru' }}
    </h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
        <form action="{{ isset($bidang) ? route('superadmin.bidang.update', $bidang) : route('superadmin.bidang.store') }}" method="POST">
            @csrf
            @if (isset($bidang))
                @method('PUT')
            @endif

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Bidang</label>
                <input type="text" name="name" id="name" value="{{ old('name', $bidang->name ?? '') }}" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       required autofocus>
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('superadmin.bidang.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Batal</a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ isset($bidang) ? 'Simpan Perubahan' : 'Tambah Bidang' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection