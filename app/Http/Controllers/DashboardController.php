<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard HRD (halaman utama setelah login admin)
     */
    public function index()
    {
        return view('pages.dashboard');
    }
}