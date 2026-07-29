<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OrganizerRegistrationController extends Controller
{
    /**
     * Menampilkan form pendaftaran akun Kepanitiaan/HIMA baru.
     */
    public function showRegistrationForm()
    {
        return view('auth.organizer-register');
    }

    /**
     * Menampilkan form login khusus Kepanitiaan/HIMA.
     */
    public function showLoginForm()
    {
        return view('auth.organizer-login');
    }

    /**
     * Memproses login khusus Kepanitiaan/HIMA.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === 'organizer' || $user->organizer) {
                $request->session()->regenerate();
                return redirect()->route('organizer.dashboard')->with('success', 'Selamat datang kembali di Dashboard Organisasi.');
            }
            if ($user->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, Superadmin.');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau Password Kepanitiaan/HIMA tidak valid.',
        ])->onlyInput('email');
    }

    /**
     * Memproses pendaftaran organisasi baru.
     */
    public function register(Request $request)
    {
        $request->validate([
            'organization_name' => 'required|string|max:255|unique:organizers,name',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'description' => 'nullable|string|max:1000',
        ]);

        // 1. Buat Akun User untuk Organisasi
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(50)");
        } catch (\Throwable $e) {
            // Ignore if fails
        }

        $user = User::create([
            'name' => $request->organization_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'organizer',
        ]);

        // 2. Buat Rekaman Organisasi (Tenant)
        $organizer = Organizer::create([
            'user_id' => $user->id,
            'name' => $request->organization_name,
            'slug' => Str::slug($request->organization_name) . '-' . Str::random(4),
            'description' => $request->description,
            'status' => 'approved',
            'is_verified' => true,
        ]);

        // 3. Auto Login dan redirect ke Dashboard Tenant
        Auth::login($user);

        return redirect()->route('organizer.dashboard')->with('success', 'Selamat! Akun Kepanitiaan/HIMA ' . $organizer->name . ' berhasil terdaftar.');
    }
}
