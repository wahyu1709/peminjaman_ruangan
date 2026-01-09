<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index(){
        $user = Auth::user();
        $data = array(
            'title' => 'Pengaturan'
        );
        return view('settings.index', compact('user'), $data);
    }

    public function updateProfile(Request $request){
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nim_nip' => 'nullable|string|max:255'
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile berhasil diupdate');
    }

    public function updatePassword(Request $request){
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::default(),'min:6', 'confirmed']
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diupdate');
    }
}
