<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Method ini bisa digunakan untuk dashboard admin
    public function adminDashboard()
    {
        // Logika spesifik untuk dashboard admin
        return view('admin.dashboard');
    }

    // Method ini bisa digunakan untuk dashboard superadmin
    public function superadminDashboard()
    {
        // Logika spesifik untuk dashboard superadmin
        return view('superadmin.dashboard');
    }
}