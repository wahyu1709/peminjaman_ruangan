<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Wahyu',
            'role' => 'admin',
            'jenis_pengguna' => 'staff',
            'nim_nip' => '836491287',
            'email' => 'admin@example.com',
            'password' => Hash::make('123123123')
        ]);

        User::create([
            'name' => 'Agus',
            'role' => 'user',
            'jenis_pengguna' => 'mahasiswa',
            'nim_nip' => '091274722',
            'email' => 'agus@example.com',
            'password' => Hash::make('123123123')
        ]);

        User::create([
            'name' => 'Eko',
            'role' => 'admin',
            'jenis_pengguna' => 'dosen',
            'nim_nip' => '764617831',
            'email' => 'eko@example.com',
            'password' => Hash::make('123123123')
        ]);

        Room::create([
            'nama_ruangan' => 'Lab Komputer 1',
            'kode_ruangan' => 'LAB01',
            'lokasi' => 'Gedung A, Lantai 2',
            'kapasitas' => 30,
            'is_active' => true
        ]);

        Room::create([
            'nama_ruangan' => 'Ruang Kelas 1',
            'kode_ruangan' => 'RK01',
            'lokasi' => 'Gedung B, Lantai 1',
            'kapasitas' => 25,
            'is_active' => false
        ]);
    }
}
