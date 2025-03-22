<?php

// app/Http/Controllers/Auth/AuthController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Menangani proses login
    public function login(Request $request)
    {
        // Validasi dan autentikasi
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Mengambil role pengguna setelah login
            $user = Auth::user();

            // Redirect berdasarkan role pengguna
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('dashboard.admin'); // Dashboard untuk admin
                case 'kasir':
                    return redirect()->route('dashboard.kasir'); // Dashboard untuk kasir
                case 'pemilik':
                    return redirect()->route('dashboard.pemilik'); // Dashboard untuk pemilik
                case 'member':
                    return redirect()->route('dashboard.member'); // Dashboard untuk pemilik
                default:
                    return redirect()->route('dashboard'); // Default fallback jika role tidak dikenal
            }
        } else {
            // Login gagal
            return back()->withErrors(['email' => 'Email atau password salah.']);
        }
    }

    // Menampilkan form register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Menangani proses register
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,kasir,pemilik', // Menambahkan role
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Menyimpan data pengguna baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, // Menyimpan role pengguna
        ]);

        // Login otomatis setelah pendaftaran
        Auth::login($user);

        // Redirect berdasarkan role pengguna setelah pendaftaran
        switch ($user->role) {
            case 'admin':
                return redirect()->route('dashboard.admin'); // Dashboard untuk admin
            case 'kasir':
                return redirect()->route('dashboard.kasir'); // Dashboard untuk kasir
            case 'pemilik':
                return redirect()->route('dashboard.pemilik'); // Dashboard untuk pemilik
            default:
                return redirect()->route('dashboard'); // Default fallback jika role tidak dikenal
        }
    }

    // Logout pengguna
    public function logout()
    {
        Auth::logout();  // Logout user
        return redirect('/login');  // Arahkan pengguna ke halaman login setelah logout
    }
}
