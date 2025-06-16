<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BidangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bidangs = Bidang::orderBy('name')->paginate(10);
        return view('superadmin.bidang.index', compact('bidangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.bidang.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:bidangs,name',
        ]);

        Bidang::create($request->all());

        return redirect()->route('superadmin.bidang.index')
                         ->with('success', 'Bidang baru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bidang $bidang)
    {
        return view('superadmin.bidang.form', compact('bidang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bidang $bidang)
    {
        $request->validate([
            'name' => ['required','string','max:255', Rule::unique('bidangs')->ignore($bidang->id)],
        ]);

        $bidang->update($request->all());

        return redirect()->route('superadmin.bidang.index')
                         ->with('success', 'Nama bidang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bidang $bidang)
    {
        // Tambahkan validasi jika perlu, misalnya jangan hapus jika masih ada user di dalamnya
        if ($bidang->users()->count() > 0) {
            return redirect()->route('superadmin.bidang.index')
                             ->with('error', 'Gagal menghapus! Masih ada user atau admin di dalam bidang ini.');
        }

        $bidang->delete();

        return redirect()->route('superadmin.bidang.index')
                         ->with('success', 'Bidang berhasil dihapus.');
    }
}