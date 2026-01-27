<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(){
        $data = array(
            'title' => 'Data Ruangan',
            'menuAdminRoom' => 'active',
            'rooms' => Room::get()
        );

        return view('admin/room/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Data Ruangan',
            'menuAdminRoom' => 'active',
        ];

        return view('admin.room.create', $data);
    }

    public function store(Request $request){
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kode_ruangan' => 'required|string|unique:rooms,kode_ruangan',
            'lokasi' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'kapasitas' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ],[
            'nama_ruangan.required' => 'Nama Ruangan wajib diisi',
            'kode_ruangan.required' => 'Kode Ruangan wajib diisi',
            'kode_ruangan.unique' => 'Kode Ruangan sudah digunakan',
            'lokasi.required' => 'Lokasi wajib diisi',
            'kapasitas.required' => 'Kapasitas wajib diisi',
            'kapasitas.integer' => 'Kapasitas harus berupa angka',
            'kapasitas.min' => 'Kapasitas minimal 1',
            'is_active.required' => 'Ketersediaan wajib diisi',
            'is_active.boolean' => 'Ketersediaan tidak valid',
            'gambar.image' => 'File harus berupa gambar',
            'gambar.mimes' => 'Format gambar harus: jpeg, png, jpg, gif',
            'gambar.max' => 'Ukuran gambar maksimal 5MB'
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('ruangan', 'public');
        }

        Room::create([
            'nama_ruangan' => $request->nama_ruangan,
            'kode_ruangan' => $request->kode_ruangan,
            'lokasi' => $request->lokasi,
            'kapasitas' => $request->kapasitas,
            'is_active' => $request->is_active,
            'gambar' => $gambarPath,
        ]);

        return redirect()->route('room')->with('success', 'Data ruangan berhasil ditambahkan');
    }

    public function edit($id){
        $data = array(
            'title' => 'Edit Data Room',
            'menuAdminRoom' => 'active',
            'room' => Room::findorFail($id)
        );
        return view('admin/room/edit', $data);
    }

    public function update(Request $request, $id){
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kode_ruangan' => 'required|string|unique:rooms,kode_ruangan,' . $id,
            'lokasi' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'kapasitas' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ],[
            'nama_ruangan.required' => 'Nama Ruangan wajib diisi',
            'kode_ruangan.required' => 'Kode Ruangan wajib diisi',
            'kode_ruangan.unique' => 'Kode Ruangan sudah digunakan',
            'lokasi.required' => 'Lokasi wajib diisi',
            'kapasitas.required' => 'Kapasitas wajib diisi',
            'kapasitas.integer' => 'Kapasitas harus berupa angka',
            'kapasitas.min' => 'Kapasitas minimal 1',
            'is_active.required' => 'Ketersediaan wajib diisi',
            'is_active.boolean' => 'Ketersediaan tidak valid',
            'gambar.image' => 'File harus berupa gambar',
            'gambar.mimes' => 'Format gambar harus: jpeg, png, jpg, gif',
            'gambar.max' => 'Ukuran gambar maksimal 5MB',
        ]);

        $room = Room::findOrFail($id);
        $room->nama_ruangan = $request->nama_ruangan;
        $room->kode_ruangan = $request->kode_ruangan;
        $room->lokasi = $request->lokasi;
        $room->kapasitas = $request->kapasitas;
        $room->is_active = $request->is_active;

        // Update gambar jika ada
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($room->gambar) {
                Storage::disk('public')->delete($room->gambar);
            }
            $room->gambar = $request->file('gambar')->store('ruangan', 'public');
        }
        

        $room->save();

        return redirect()->route('room')->with('success', 'Data ruangan berhasil diubah');

    }

    public function destroy($id){
        $room = Room::findOrFail($id);
        $room->delete();

        return redirect()->route('room')->with('success', 'Data berhasil dihapus');
    }

    public function userList(Request $request){
        $query = Room::where('is_active', true);

        // Filter Tanggal + Jam Mulai + Jam Selesai (untuk cek ruangan kosong)
        if ($request->filled(['tanggal', 'jam_mulai', 'jam_selesai'])) {
            $tanggal     = $request->tanggal;
            $jam_mulai   = $request->jam_mulai;
            $jam_selesai = $request->jam_selesai;

            $query->whereDoesntHave('bookings', function ($q) use ($tanggal, $jam_mulai, $jam_selesai) {
                $q->where('tanggal_pinjam', $tanggal)
                  ->where('status', 'approved') // hanya booking yang sudah disetujui
                  ->where(function ($q) use ($jam_mulai, $jam_selesai) {
                      $q->where('waktu_mulai', '<', $jam_selesai)
                        ->where('waktu_selesai', '>', $jam_mulai);
                  });
            });
        }

        // Filter Lokasi (langsung dari kolom lokasi di tabel rooms)
        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }

        // Filter Kapasitas Minimal
        // if ($request->filled('kapasitas_min')) {
        //     $query->where('kapasitas', '>=', $request->kapasitas_min);
        // }

        // Urutkan berdasarkan kode_ruangan
        $rooms = $query->orderBy('kode_ruangan', 'asc')->get();

        // Ambil daftar lokasi unik untuk dropdown filter
        $lokasiList = Room::where('is_active', true)
                          ->pluck('lokasi')
                          ->unique()
                          ->sort()
                          ->values();
        
        $title = 'Daftar Ruangan Tersedia';

        return view('user.room-list', compact('rooms', 'lokasiList', 'title'));
    }
}