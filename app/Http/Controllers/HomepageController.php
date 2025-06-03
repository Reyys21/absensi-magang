<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function index()
    {
        // Pastikan file 'homepage.blade.php' ada di folder resources/views/
        return view('homepage');
    }
}