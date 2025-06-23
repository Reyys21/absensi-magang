@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        {{-- Ukuran font diubah dari text-3xl menjadi text-xl --}}
        <h1 class="text-xl font-bold text-gray-800">Manajemen Admin</h1>
        <a href="{{ route('superadmin.admins.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fa-solid fa-plus mr-2"></i>
            Tambah Admin Baru
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-r-lg" role="alert"><p class="text-sm">{{ session('success') }}</p></div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-lg" role="alert"><p class="text-sm">{{ session('error') }}</p></div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bidang Ditugaskan</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($admins as $admin)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $admin->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $admin->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $admin->bidang->name ?? 'Belum Ditugaskan' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                        <a href="{{ route('superadmin.admins.edit', $admin) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                        
                        {{-- ▼▼▼ INI BAGIAN YANG DITAMBAHKAN/DIMODIFIKASI ▼▼▼ --}}
                        {{-- Form Demote: Muncul jika target BUKAN superadmin & BUKAN diri sendiri --}}
                        @if (Auth::id() !== $admin->id && !$admin->hasRole('superadmin'))
                            <form action="{{ route('superadmin.admins.demote', $admin) }}" method="POST" class="inline" onsubmit="return confirm('Anda yakin ingin mengubah role admin ini kembali menjadi User? Semua izin khusus akan dicabut.');">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900">Jadikan User</button>
                            </form>
                        @endif

                        {{-- Form Hapus: Muncul jika target BUKAN diri sendiri --}}
                        @if (Auth::id() !== $admin->id)
                            <form action="{{ route('superadmin.admins.destroy', $admin) }}" method="POST" class="inline ml-4" onsubmit="return confirm('Yakin ingin menghapus admin ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        @endif
                        {{-- ▲▲▲ AKHIR BAGIAN YANG DIMODIFIKASI ▲▲▲ --}}
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada akun admin yang dibuat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $admins->links() }}</div>
</div>
@endsection