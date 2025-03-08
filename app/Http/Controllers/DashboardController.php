<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Untuk Admin
    public function index()
    {
        return view('dashboard.admin'); // Tampilan dashboard admin
    }

    // Untuk Kasir
    public function kasirDashboard()
    {
        return view('dashboard.kasir'); // Tampilan dashboard kasir
    }

    // Untuk Pemilik
    public function pemilikDashboard()
    {
        return view('dashboard.pemilik'); // Tampilan dashboard pemilik
    }
}
