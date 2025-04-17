<?php

// app/Http/Controllers/Auth/AuthController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * @class AuthController
 * @brief Mengelola autentikasi pengguna seperti login, register, dan logout.
 */
class AuthController extends Controller
{
    /**
     * @brief Menampilkan form login kepada pengguna.
     *
     * @return \Illuminate\View\View Tampilan halaman login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * @brief Memproses permintaan login pengguna.
     *
     * @param Request $request Data permintaan HTTP yang berisi email dan password.
     * @return \Illuminate\Http\RedirectResponse Redirect ke dashboard berdasarkan peran pengguna.
     */
    public function login(Request $request)
    {
        /**
         * @var array $credentials Menyimpan email dan password dari form.
         */
        $credentials = $request->only('email', 'password');

        // Coba autentikasi pengguna dengan kredensial
        if (Auth::attempt($credentials)) {
            /**
             * @var User $user Pengguna yang berhasil login.
             */
            $user = Auth::user();

            // Arahkan pengguna ke dashboard sesuai perannya
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('dashboard.admin');
                case 'kasir':
                    return redirect()->route('dashboard.kasir');
                case 'pemilik':
                    return redirect()->route('dashboard.pemilik');
                case 'member':
                    return redirect()->route('dashboard.member');
                default:
                    return redirect()->route('dashboard');
            }
        } else {
            // Jika gagal login, kembali ke form login dengan error
            return back()->withErrors([
                'email' => 'Email atau password salah.'
            ])->withInput();
        }
    }

    /**
     * @brief Menampilkan form registrasi pengguna.
     *
     * @return \Illuminate\View\View Tampilan halaman register.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * @brief Menangani proses pendaftaran pengguna baru.
     *
     * @param Request $request Data permintaan HTTP dari form register.
     * @return \Illuminate\Http\RedirectResponse Redirect ke dashboard sesuai peran pengguna.
     */
    public function register(Request $request)
    {
        /**
         * @var \Illuminate\Contracts\Validation\Validator $validator Objek untuk memvalidasi input.
         */
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,kasir,pemilik', // Peran yang diperbolehkan
        ]);

        // Jika validasi gagal, redirect kembali dengan error
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        /**
         * @var User $user Membuat user baru berdasarkan data input.
         */
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // Login otomatis setelah registrasi
        Auth::login($user);

        // Redirect ke dashboard sesuai role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('dashboard.admin');
            case 'kasir':
                return redirect()->route('dashboard.kasir');
            case 'pemilik':
                return redirect()->route('dashboard.pemilik');
            default:
                return redirect()->route('dashboard');
        }
    }

    /**
     * @brief Melakukan proses logout dan mengarahkan ke halaman login.
     *
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman login.
     */
    public function logout()
    {
        Auth::logout(); // Menghapus sesi login pengguna
        return redirect('/login'); // Kembali ke halaman login
    }
}
