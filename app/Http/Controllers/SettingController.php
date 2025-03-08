<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string',
            'currency_symbol' => 'required|string|max:5',
            'warning_quantity' => 'required|numeric|min:1',
        ]);
        
        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
