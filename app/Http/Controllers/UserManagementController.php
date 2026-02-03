<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport; // Nanti buat ini

class UserManagementController extends Controller
{
    public function index()
    {
        // Hanya admin yang boleh akses
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $data = [
            'title' => 'Manajemen Pengguna',
            'menuAdminUsers' => 'active'
        ];

        return view('admin.users.index', $data);
    }

    public function getData(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Query semua user kecuali yang role admin
        $query = User::where('role', 'user');

        // Filter berdasarkan jenis pengguna
        if ($request->filled('jenis_pengguna')) {
            $query->where('jenis_pengguna', $request->jenis_pengguna);
        }

        $query->orderBy('id', 'asc');

        return DataTables::of($query)
            ->addColumn('jenis_badge', function ($user) {
                $badges = [
                    'mahasiswa' => '<span class="badge badge-primary"><i class="fas fa-user-graduate mr-1"></i>Mahasiswa</span>',
                    'dosen' => '<span class="badge badge-success"><i class="fas fa-chalkboard-teacher mr-1"></i>Dosen</span>',
                    'staff' => '<span class="badge badge-info"><i class="fas fa-user-tie mr-1"></i>Staff</span>',
                    'umum' => '<span class="badge badge-warning"><i class="fas fa-user-friends mr-1"></i>Umum</span>',
                ];
                return $badges[$user->jenis_pengguna] ?? '<span class="badge badge-secondary">-</span>';
            })
            ->addColumn('action', function ($user) {
                return '
                    <button class="btn btn-sm btn-warning btn-edit" 
                            data-id="'.$user->id.'"
                            data-name="'.e($user->name).'"
                            data-email="'.e($user->email).'"
                            data-nim_nip="'.e($user->nim_nip).'"
                            data-jenis="'.$user->jenis_pengguna.'">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="'.$user->id.'">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->addIndexColumn()
            ->rawColumns(['jenis_badge', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'jenis_pengguna' => 'required|in:mahasiswa,dosen,staff,umum',
            'nim_nip' => 'nullable|string|max:50|unique:users,nim_nip',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'jenis_pengguna.required' => 'Jenis pengguna harus dipilih',
            'nim_nip.unique' => 'NIM/NIP sudah terdaftar',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'jenis_pengguna' => $request->jenis_pengguna,
            'nim_nip' => $request->nim_nip,
        ]);

        return response()->json(['success' => true, 'message' => 'Pengguna berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'jenis_pengguna' => 'required|in:mahasiswa,dosen,staff,umum',
            'nim_nip' => ['nullable', 'string', 'max:50', Rule::unique('users', 'nim_nip')->ignore($id)],
            'password' => 'nullable|min:6|confirmed',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'nim_nip.unique' => 'NIM/NIP sudah terdaftar',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'jenis_pengguna' => $request->jenis_pengguna,
            'nim_nip' => $request->nim_nip,
        ]);

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return response()->json(['success' => true, 'message' => 'Pengguna berhasil diupdate']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Cek apakah user punya booking
        if ($user->bookings()->count() > 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Tidak bisa menghapus user yang memiliki riwayat booking'
            ], 400);
        }

        $user->delete();
        return response()->json(['success' => true, 'message' => 'Pengguna berhasil dihapus']);
    }

    public function excel(Request $request)
    {
        $jenis = $request->get('jenis', 'all');
        $filename = 'DataPengguna_' . ucfirst($jenis) . '_' . now()->format('d-m-Y_H-i-s');

        $query = User::where('role', 'user');
        if ($jenis !== 'all') {
            $query->where('jenis_pengguna', $jenis);
        }

        $query->orderBy('id', 'asc');
        
        return Excel::download(new UsersExport($jenis), $filename . '.xlsx');
    }

    public function pdf(Request $request)
    {
        $jenis = $request->get('jenis', 'all');
        
        $query = User::where('role', 'user');
        if ($jenis !== 'all') {
            $query->where('jenis_pengguna', $jenis);
        }

        $query->orderBy('id', 'asc');
        
        $data = [
            'users' => $query->latest()->get(),
            'title' => 'Data Pengguna ' . ucfirst($jenis),
        ];
        
        $filename = 'DataPengguna_' . ucfirst($jenis) . '_' . now()->format('d-m-Y_H-i-s');
        $pdf = Pdf::loadView('admin.users.pdf', $data);
        return $pdf->stream($filename . '.pdf');
    }
}