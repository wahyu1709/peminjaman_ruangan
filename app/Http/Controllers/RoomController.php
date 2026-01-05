<?php

namespace App\Http\Controllers;

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
            'kapasitas' => 'required|integer|min:1',
            'is_active' => 'required|boolean'
        ],[
            'nama_ruangan.required' => 'Nama Ruangan wajib diisi',
            'kode_ruangan.required' => 'Kode Ruangan wajib diisi',
            'kode_ruangan.unique' => 'Kode Ruangan sudah digunakan',
            'lokasi.required' => 'Lokasi wajib diisi',
            'kapasitas.required' => 'Kapasitas wajib diisi',
            'kapasitas.integer' => 'Kapasitas harus berupa angka',
            'kapasitas.min' => 'Kapasitas minimal 1',
            'is_active.required' => 'Ketersediaan wajib diisi',
            'is_active.boolean' => 'Ketersediaan tidak valid'
        ]);

        Room::create([
            'nama_ruangan' => $request->nama_ruangan,
            'kode_ruangan' => $request->kode_ruangan,
            'lokasi' => $request->lokasi,
            'kapasitas' => $request->kapasitas,
            'is_active' => $request->is_active,
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
            'kapasitas' => 'required|integer|min:1',
            'is_active' => 'required|boolean'
        ],[
            'nama_ruangan.required' => 'Nama Ruangan wajib diisi',
            'kode_ruangan.required' => 'Kode Ruangan wajib diisi',
            'kode_ruangan.unique' => 'Kode Ruangan sudah digunakan',
            'lokasi.required' => 'Lokasi wajib diisi',
            'kapasitas.required' => 'Kapasitas wajib diisi',
            'kapasitas.integer' => 'Kapasitas harus berupa angka',
            'kapasitas.min' => 'Kapasitas minimal 1',
            'is_active.required' => 'Ketersediaan wajib diisi',
            'is_active.boolean' => 'Ketersediaan tidak valid'
        ]);

        $room = Room::findOrFail($id);
        $room->nama_ruangan = $request->nama_ruangan;
        $room->kode_ruangan = $request->kode_ruangan;
        $room->lokasi = $request->lokasi;
        $room->kapasitas = $request->kapasitas;
        $room->is_active = $request->is_active;

        $room->save();

        return redirect()->route('room')->with('success', 'Data ruangan berhasil diubah');

    }

    public function destroy($id){
        $room = Room::findOrFail($id);
        $room->delete();

        return redirect()->route('room')->with('success', 'Data berhasil dihapus');
    }
}
