<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialiteController extends Controller
{
    /**
     * Redirect ke halaman autentikasi Google.
     */
    public function redirectToGoogle()
    {
        if (request()->has('redirect')) {
            session(['url.intended' => request()->get('redirect')]);
        } else if (url()->previous() && !str_contains(url()->previous(), 'login')) {
            session(['url.intended' => url()->previous()]);
        }
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Menangani callback respon dari Google.
     */
    public function handleGoogleCallback()
    {
        try {
            // Ambil data user Google menggunakan mode stateless untuk kompatibilitas Vercel Serverless
            $googleUser = Socialite::driver('google')->stateless()->user();

            if (!$googleUser || !$googleUser->getEmail()) {
                return redirect()->route('home')->with('error', 'Gagal mengambil informasi profil dari akun Google Anda.');
            }

            // Cari user berdasarkan google_id atau email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // Update data google_id dan avatar jika belum terhubung
                $user->update([
                    'google_id' => $user->google_id ?? $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                // Buat user baru jika belum terdaftar
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Pengguna Google',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 'user',
                    'password' => bcrypt(\Illuminate\Support\Str::random(16)), // Password acak aman
                ]);
            }

            Auth::login($user, true);

            return redirect()->intended(route('home'))->with('success', 'Selamat datang, ' . $user->name . '! Anda berhasil login via Google.');
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Auth Error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('home')->with('error', 'Gagal login via Google: ' . $e->getMessage());
        }
    }
}
