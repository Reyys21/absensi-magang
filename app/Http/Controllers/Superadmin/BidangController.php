<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\User; // Import User model
use App\Models\CorrectionRequest; // Import CorrectionRequest model
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BidangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Bidang::orderBy('name');

        // Tambahkan logika pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%');
        }

        $bidangs = $query->paginate(10)->appends($request->query());
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
     * Display the specified resource.
     *
     * @param  \App\Models\Bidang  $bidang
     * @return \Illuminate\View\View
     */
    public function show(Bidang $bidang, Request $request) // Tambahkan Request $request
    {
        // Untuk tab "Users"
        $usersQuery = User::where('bidang_id', $bidang->id)
                          ->whereHas('roles', function($q){ $q->where('name', 'user'); }); // Hanya user biasa
        
        if ($request->filled('user_search')) {
            $search = $request->input('user_search');
            $usersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                  ->orWhere('email', 'like', '%'.$search.'%');
            });
        }
        $users = $usersQuery->paginate(10, ['*'], 'users_page')->appends(['user_search' => $request->input('user_search')]); //

        // Untuk tab "Permintaan Koreksi"
        $correctionsQuery = CorrectionRequest::with('user')
                                           ->whereHas('user', function ($q) use ($bidang) {
                                               $q->where('bidang_id', $bidang->id);
                                           })
                                           ->where('status', 'pending'); // Hanya yang pending
        
        if ($request->filled('correction_search')) {
            $search = $request->input('correction_search');
            $correctionsQuery->where(function($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%'.$search.'%')
                              ->orWhere('email', 'like', '%'.$search.'%');
                })->orWhere('attendance_date', 'like', '%'.$search.'%');
            });
        }
        $corrections = $correctionsQuery->paginate(10, ['*'], 'corrections_page')->appends(['correction_search' => $request->input('correction_search')]); //

        // Untuk tab "Roles" (Admin di bidang ini)
        $adminsQuery = User::where('bidang_id', $bidang->id)
                           ->whereHas('roles', function($q){ $q->where('name', 'admin'); }); // Hanya admin
        
        if ($request->filled('admin_search')) {
            $search = $request->input('admin_search');
            $adminsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                  ->orWhere('email', 'like', '%'.$search.'%');
            });
        }
        $admins = $adminsQuery->paginate(10, ['*'], 'admins_page')->appends(['admin_search' => $request->input('admin_search')]); //


        return view('superadmin.bidang.show', compact('bidang', 'users', 'corrections', 'admins')); // Kirim data ke view
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