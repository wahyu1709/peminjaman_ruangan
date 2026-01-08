<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Exports\UserExport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(){
        $data = array(
            'title' => 'Data Mahasiswa',
            'menuAdminUser' => 'active',
            'users' => User::where('jenis_pengguna', 'mahasiswa')->get(),
        );

        return view('admin/user/index', $data);
    }

    public function create(){
        $data = array(
            'title' => 'Tambah Data User',
            'menuAdminUser' => 'active',
        );

        return view('admin/user/create', $data);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'jenis_pengguna' => 'required',
            'nim_nip' => 'required|unique:users,nim_nip',
            'password' => 'required|confirmed|min:6'
        ],[
            'name.required' => 'Nama tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'email.unique' => 'Email sudah terdaftar',
            'jenis_pengguna.required' => 'Harus memilih jenis pengguna',
            'nim_nip.required' => 'NIM/NIP tidak boleh kosong',
            'nim_nip.unique' => 'NIM?NIP sudah terdaftar',
            'password.required' => 'Password tidak boleh kosong',
            'password.confirmed' => 'Password tidak sama',
            'password.min' => 'Password minimal 6 karakter'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->jenis_pengguna = $request->jenis_pengguna;
        $user->nim_nip = $request->nim_nip;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('user')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id){
        $data = array(
            'title' => 'Edit Data User',
            'menuAdminUser' => 'active',
            'user' => User::findorFail($id)
        );
        return view('admin/user/edit', $data);
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'email' => ['required', Rule::unique('users','email')->ignore($id)],
            'jenis_pengguna' => 'required',
            'nim_nip' => ['required', Rule::unique('users','nim_nip')->ignore($id)],
            'password' => 'nullable|confirmed|min:6'
        ],[
            'name.required' => 'Nama tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'email.unique' => 'Email sudah terdaftar',
            'jenis_pengguna.required' => 'Harus memilih jenis pengguna',
            'nim_nip.required' => 'NIM/NIP tidak boleh kosong',
            'nim_nip.unique' => 'NIM/NIP sudah terdaftar',
            'password.confirmed' => 'Password tidak sama',
            'password.min' => 'Password minimal 6 karakter'
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->jenis_pengguna = $request->jenis_pengguna;
        $user->nim_nip = $request->nim_nip;
        if($request->password) $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('user')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id){
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user')->with('success', 'Data berhasil dihapus');
    }

    public function excel(){
        $filename = now()->format('d-m-Y_H-i-s');
        return Excel::download(new UserExport, 'DataUser_'.$filename.'.xlsx');
    }

    public function pdf(){
        $filename = now()->format('d-m-Y_H-i-s');
        $data = array(
            'users' => User::where('jenis_pengguna', 'mahasiswa')
                 ->latest()->get(),
        );
        
        $pdf = Pdf::loadView('admin/user/pdf', $data);
        return $pdf->stream('DataUser_'.$filename.'.pdf');
    }
}
