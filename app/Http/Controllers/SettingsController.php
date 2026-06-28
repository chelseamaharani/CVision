<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /**
     * Tampilkan halaman Settings (profil HRD)
     */
    public function index()
    {
        return view('pages.settings');
    }

    /**
     * Update nama, email, dan/atau password HRD
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'old_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;

        // Ganti password hanya jika kedua field diisi
        if ($request->filled('old_password') && $request->filled('new_password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withErrors([
                    'old_password' => 'The old password you entered is incorrect.',
                ]);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Changes saved successfully!');
    }
}