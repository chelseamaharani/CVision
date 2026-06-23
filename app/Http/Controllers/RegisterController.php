<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    // Tampilkan form register
    public function index()
    {
        return view('pages.register');
    }

    // Proses register (hanya pelamar)
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255|unique:users,name',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'pelamar',  // default role pelamar
        ]);

        Auth::login($user);

        return redirect()->route('landing');  // Pelamar → landing page
    }
}