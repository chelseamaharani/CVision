<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        if (! $user) {
            $user = User::create([
                'name'      => $googleUser->getName() ?? $googleUser->getNickname(),
                'email'     => $googleUser->getEmail(),
                'password'  => bcrypt(Str::random(24)),
                'role'      => 'pelamar',
                'google_id' => $googleUser->getId(),
            ]);
        } elseif (! $user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        Auth::login($user, true);

        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        }

        return redirect()->route('landing');
    }
}