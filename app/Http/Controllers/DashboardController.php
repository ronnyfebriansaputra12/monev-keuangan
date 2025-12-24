<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Method untuk PLO
    public function index()
    {
        return view('dashboard', [
            'title' => 'Dashboard Utama',
            'user' => Auth::user()
        ]);
    }

    // Method untuk Verifikator
    public function verifikatorIndex()
    {
        return view('dashboard', [
            'title' => 'Dashboard Verifikator',
            'role_display' => 'Tim Verifikator'
        ]);
    }

    // Method untuk Bendahara
    public function bendaharaIndex()
    {
        return view('dashboard', [
            'title' => 'Dashboard Bendahara',
            'role_display' => 'Bendahara Pengeluaran'
        ]);
    }
}
