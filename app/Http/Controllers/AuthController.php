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
    public function login(){
        return view('auth/login');
    }

    public function loginProses(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required|min:6'
        ],[
            'email.required' => 'email wajib diisi',
            'password.required' => 'password wajib diisi',
            'password.min' => 'password Minimal 6 Karakter'
        ]);

        $data = array(
            'email' => $request->email,
            'password' => $request->password
        );

        if(Auth::attempt($data)){
            return redirect()->route('dashboard');
        }else{
            return redirect()->back()->with('error', 'Email atau Password salah!');
        }
    }

    public function register(){
        return view('auth/register');
    }

    public function registerProses(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', 'unique:users,email', function($attribute, $value, $fail){
                $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'mail.com', 'ui.ac.id'];
                $domain = strtolower(substr(strrchr($value, "@"), 1));

                if(!in_array($domain, $allowedDomains)){
                    $fail('Email anda tidak valid. Gunakan email dengan domain: ' . implode(', ', $allowedDomains));
                }
            }],
            'nim_nip' => 'required|unique:users,nim_nip',
            'jenis_pengguna' => 'required',
            'password' => 'required|min:6|confirmed'
        ],[
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'nim_nip.required' => 'NIM/NIP wajib diisi',
            'nim_nip.unique' => 'NIM/NIP sudah terdaftar',
            'jenis_pengguna.required' => 'Jenis Pengguna wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password Minimal 6 Karakter',
            'password.confirmed' => 'Konfirmasi Password tidak sesuai'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'jenis_pengguna' => $request->jenis_pengguna,
            'nim_nip' => $request->nim_nip,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('login')->with('success', 'Registrasi berhasil');
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }

    public function forgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request){
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

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
