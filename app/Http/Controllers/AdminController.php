<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Admin',
            'menuAdminAdmin' => 'active', // untuk highlight menu sidebar
            'users' => User::whereIn('jenis_pengguna', ['staff', 'dosen'])
                           ->latest()->get(),
        ];

        return view('admin.admin.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Data Admin',
            'menuAdminAdmin' => 'active',
        ];

        return view('admin.admin.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'jenis_pengguna'=> ['required', Rule::in(['staff', 'dosen'])],
            'nim_nip'       => 'required|unique:users,nim_nip',
            'password'      => 'required|confirmed|min:6',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'email.unique'   => 'Email sudah terdaftar',
            'jenis_pengguna.required' => 'Jenis pengguna harus dipilih',
            'jenis_pengguna.in' => 'Jenis pengguna hanya boleh Staff atau Dosen',
            'nim_nip.required' => 'NIM/NIP tidak boleh kosong',
            'nim_nip.unique'   => 'NIM/NIP sudah terdaftar',
            'password.required' => 'Password tidak boleh kosong',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.min'      => 'Password minimal 6 karakter',
        ]);

        User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'jenis_pengguna' => $request->jenis_pengguna,
            'nim_nip'        => $request->nim_nip,
            'password'       => Hash::make($request->password),
        ]);

        return redirect()->route('admin')->with('success', 'Data admin berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        // Pastikan hanya staff/dosen yang bisa diedit dari menu ini
        if (!in_array($user->jenis_pengguna, ['staff', 'dosen'])) {
            abort(404);
        }

        $data = [
            'title' => 'Edit Data Admin',
            'menuAdminAdmin' => 'active',
            'user'  => $user,
        ];

        return view('admin.admin.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => ['required', 'email', Rule::unique('users','email')->ignore($id)],
            'jenis_pengguna'=> ['required', Rule::in(['staff', 'dosen'])],
            'role'=> ['required', Rule::in(['admin', 'user'])],
            'nim_nip'       => ['required', Rule::unique('users','nim_nip')->ignore($id)],
            'password'      => 'nullable|confirmed|min:6',
        ]);

        $user->name           = $request->name;
        $user->email          = $request->email;
        $user->jenis_pengguna = $request->jenis_pengguna;
        $user->nim_nip        = $request->nim_nip;
        $user->role           = $request->role;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('admin')->with('success', 'Data admin berhasil diubah');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (!in_array($user->jenis_pengguna, ['staff', 'dosen'])) {
            abort(404);
        }

        $user->delete();

        return redirect()->route('admin')->with('success', 'Data admin berhasil dihapus');
    }
}