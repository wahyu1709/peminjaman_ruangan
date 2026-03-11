<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginProses(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ], [
            'email.required' => 'Email wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter'
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard');
        } else {
            return redirect()->back()->with('error', 'Email atau Password salah!');
        }
    }

    // Langkah 1: Pilih jenis pengguna
    public function register()
    {
        return view('auth.register'); // register.blade.php
    }

    // Langkah 2A: Form Civitas FIK UI
    public function showRegisterStep1()
    {
        return view('auth.register-step1');
    }

    // Langkah 2B: Form Pihak Eksternal
    public function showRegisterStep2()
    {
        return view('auth.register-step2');
    }

    // Proses registrasi
    public function registerProses(Request $request)
    {
        // Validasi umum
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'jenis_pengguna' => 'required|in:mahasiswa,staff,dosen,umum',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'jenis_pengguna.required' => 'Jenis pengguna wajib dipilih',
        ]);

        // Validasi khusus berdasarkan jenis pengguna
        if ($request->jenis_pengguna !== 'umum') {
            // Civitas FIK UI
            $request->validate([
                'nim_nip' => 'required|string|max:50|unique:users,nim_nip',
            ], [
                'nim_nip.required' => 'NIM/NIP wajib diisi',
                'nim_nip.unique' => 'NIM/NIP sudah terdaftar',
            ]);
        } else {
            // Pihak Eksternal
            $request->validate([
                'instansi' => 'required|string|max:255',
                'phone' => 'required|regex:/^[\+]?[0-9]{10,13}$/',
            ], [
                'instansi.required' => 'Instansi wajib diisi',
                'phone.required' => 'No. HP wajib diisi',
                'phone.regex' => 'Format No. HP tidak valid (contoh: +6281234567890)',
            ]);
        }

        // Buat user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'jenis_pengguna' => $request->jenis_pengguna,
            'nim_nip' => $request->filled('nim_nip') ? $request->nim_nip : null,
            'instansi' => $request->filled('instansi') ? $request->instansi : null,
            'phone' => $request->filled('phone') ? $request->phone : null,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Selamat datang di sistem peminjaman ruangan FIK UI.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    // Forgot Password
    public function forgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPasswordForm(Request $request)
    {
        return view('auth.reset-password', [
            'token' => $request->token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}