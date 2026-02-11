<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    // 1. Tampilkan Halaman Login
    public function index()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        // Menggunakan view login KaiAdmin yang kita buat tadi
        return view('login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $throttleKey = 'login-attempt:' . Str::lower($request->input('email'));

        // 1. Cek limit percobaan
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Akun Anda ditangguhkan selama $minutes menit demi keamanan.",
            ])->withInput($request->except('password'));
        }

        // 2. Validasi Email, Password, & Google reCAPTCHA
        $request->validate([
            'email' => ['required', 'email'],
            'password' => [
                'required',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            // Tambahkan rule captcha di sini
            'g-recaptcha-response' => ['required', 'captcha'],
        ], [
            // Pesan error kustom untuk captcha
            'g-recaptcha-response.required' => 'Wajib mencentang kotak "I\'m not a robot".',
            'g-recaptcha-response.captcha' => 'Validasi captcha gagal, silakan muat ulang halaman.',
        ]);

        $credentials = $request->only('email', 'password');

        // 3. Coba proses login
        if (Auth::attempt($credentials, $request->remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user());
        }

        // 4. Jika gagal (Email/Password salah)
        RateLimiter::hit($throttleKey, 300);

        $retriesLeft = RateLimiter::remaining($throttleKey, 5);
        $errorMessage = 'Email atau password salah.';
        if ($retriesLeft > 0 && $retriesLeft <= 2) {
            $errorMessage .= " Sisa percobaan: $retriesLeft kali lagi.";
        }

        return back()->withErrors(['email' => $errorMessage])->onlyInput('email');
    }
    /**
     * Redirect langsung check role
     */
    protected function redirectByRole($user)
    {
        return redirect()->intended('/dashboard');
    }

    // 3. Proses Logout
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil keluar.');
    }
    public function edit()
    {
        return view('profile'); // Sesuaikan dengan nama file blade Anda
    }

    /**
     * Memperbarui data profil dan password.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // Update Nama dan Email
        $user->name = $request->name;
        $user->email = $request->email;

        // Update Password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
