<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Tampilkan form login
    public function index()
    {
        return view('pages.login');
    }

    // Proses login (pelamar maupun HRD/admin)
    public function login(Request $request)
    {
        $isHrd = $request->boolean('hrd_login');

        // Ambil email & password sesuai form mana yang dikirim
        $email    = $isHrd ? $request->email_hrd    : $request->email;
        $password = $isHrd ? $request->password_hrd : $request->password;

        $request->merge(['email' => $email, 'password' => $password]);

        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'email'    => $email,
            'password' => $password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Validasi tambahan: kalau form HRD tapi role bukan admin → tolak
            if ($isHrd && $user->role !== 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun ini bukan akun HRD.',
                ])->withInput(['hrd_login' => true]);
            }

            // Kalau role nya admin → ke dashboard
            if ($user->role === 'admin') {
                return redirect()->route('dashboard');
            }

            // Pelamar → halaman landing pelamar (Upload CV)
            return redirect()->route('landing');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($isHrd ? ['hrd_login' => true] : []);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}