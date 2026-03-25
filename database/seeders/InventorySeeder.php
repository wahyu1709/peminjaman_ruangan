<?php

namespace Database\Seeders;

use App\Models\Inventory;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run()
    {
        // Kategori: phantom_rjp
        Inventory::insert([
            ['name' => 'Phantom RJP Dewasa Half Body', 'category' => 'phantom_rjp', 'price_per_day' => 600000, 'stock' => 5, 'stock_available' => 5, 'is_active' => true],
            ['name' => 'Phantom RJP Pediatric', 'category' => 'phantom_rjp', 'price_per_day' => 600000, 'stock' => 3, 'stock_available' => 3, 'is_active' => true],
            ['name' => 'Phantom RJP Neonatus', 'category' => 'phantom_rjp', 'price_per_day' => 500000, 'stock' => 3, 'stock_available' => 3, 'is_active' => true],
        ]);

        // Kategori: phantom_airway
        Inventory::insert([
            ['name' => 'Phantom Airway Pediatrik', 'category' => 'phantom_airway', 'price_per_day' => 200000, 'stock' => 4, 'stock_available' => 4, 'is_active' => true],
            ['name' => 'Phantom Airway Dewasa', 'category' => 'phantom_airway', 'price_per_day' => 250000, 'stock' => 4, 'stock_available' => 4, 'is_active' => true],
        ]);

        // Kategori: phantom_lain
        Inventory::insert([
            ['name' => 'Phantom Full Body Dewasa', 'category' => 'phantom_lain', 'price_per_day' => 600000, 'stock' => 2, 'stock_available' => 2, 'is_active' => true],
            ['name' => 'Phantom Decubitus Area Gluteus 1 Set', 'category' => 'phantom_lain', 'price_per_day' => 200000, 'stock' => 3, 'stock_available' => 3, 'is_active' => true],
            ['name' => 'Phantom Pedis', 'category' => 'phantom_lain', 'price_per_day' => 100000, 'stock' => 5, 'stock_available' => 5, 'is_active' => true],
            ['name' => 'Phantom Perawatan Luka (Sally)', 'category' => 'phantom_lain', 'price_per_day' => 200000, 'stock' => 4, 'stock_available' => 4, 'is_active' => true],
            ['name' => 'Phantom Payudara', 'category' => 'phantom_lain', 'price_per_day' => 100000, 'stock' => 4, 'stock_available' => 4, 'is_active' => true],
            ['name' => 'Phantom Payudara Abnormal 1 Set', 'category' => 'phantom_lain', 'price_per_day' => 200000, 'stock' => 2, 'stock_available' => 2, 'is_active' => true],
        ]);

        // Kategori: defibrillator
        Inventory::insert([
            ['name' => 'Defibrillator', 'category' => 'defibrillator', 'price_per_day' => 600000, 'stock' => 2, 'stock_available' => 2, 'is_active' => true],
            ['name' => 'AED', 'category' => 'defibrillator', 'price_per_day' => 400000, 'stock' => 3, 'stock_available' => 3, 'is_active' => true],
        ]);

        // Kategori: bvm
        Inventory::insert([
            ['name' => 'BVM Dewasa', 'category' => 'bvm', 'price_per_day' => 20000, 'stock' => 10, 'stock_available' => 10, 'is_active' => true],
            ['name' => 'BVM Pediatrik', 'category' => 'bvm', 'price_per_day' => 20000, 'stock' => 8, 'stock_available' => 8, 'is_active' => true],
            ['name' => 'BVM Neonatus', 'category' => 'bvm', 'price_per_day' => 30000, 'stock' => 6, 'stock_available' => 6, 'is_active' => true],
        ]);

        // Kategori: laringoskop
        Inventory::insert([
            ['name' => 'Laringoskop Dewasa', 'category' => 'laringoskop', 'price_per_day' => 30000, 'stock' => 8, 'stock_available' => 8, 'is_active' => true],
            ['name' => 'Laringoskop Anak', 'category' => 'laringoskop', 'price_per_day' => 30000, 'stock' => 6, 'stock_available' => 6, 'is_active' => true],
        ]);

        // Kategori: alat_dasar
        Inventory::insert([
            ['name' => 'Stetoskop Dewasa', 'category' => 'alat_dasar', 'price_per_day' => 20000, 'stock' => 15, 'stock_available' => 15, 'is_active' => true],
            ['name' => 'Neck Collar', 'category' => 'alat_dasar', 'price_per_day' => 30000, 'stock' => 10, 'stock_available' => 10, 'is_active' => true],
        ]);

        // Kategori: stretcher
        Inventory::insert([
            ['name' => 'Scoop Stretcher', 'category' => 'stretcher', 'price_per_day' => 100000, 'stock' => 4, 'stock_available' => 4, 'is_active' => true],
            ['name' => 'Long Spine Board', 'category' => 'stretcher', 'price_per_day' => 100000, 'stock' => 5, 'stock_available' => 5, 'is_active' => true],
            ['name' => 'Short Spine Board', 'category' => 'stretcher', 'price_per_day' => 50000, 'stock' => 6, 'stock_available' => 6, 'is_active' => true],
        ]);

        // Kategori: bed
        Inventory::insert([
            ['name' => 'Bed Pasien Dewasa Manual', 'category' => 'bed', 'price_per_day' => 125000, 'stock' => 8, 'stock_available' => 8, 'is_active' => true],
            ['name' => 'Bed Pasien Dewasa Adjustable (Merk Paramount)', 'category' => 'bed', 'price_per_day' => 400000, 'stock' => 3, 'stock_available' => 3, 'is_active' => true],
            ['name' => 'Bed Pasien Dewasa Adjustable', 'category' => 'bed', 'price_per_day' => 150000, 'stock' => 5, 'stock_available' => 5, 'is_active' => true],
        ]);

        // Kategori: mobilitas
        Inventory::insert([
            ['name' => 'Brancard', 'category' => 'mobilitas', 'price_per_day' => 150000, 'stock' => 4, 'stock_available' => 4, 'is_active' => true],
            ['name' => 'Kruk', 'category' => 'mobilitas', 'price_per_day' => 30000, 'stock' => 12, 'stock_available' => 12, 'is_active' => true],
            ['name' => 'Kursi Roda', 'category' => 'mobilitas', 'price_per_day' => 50000, 'stock' => 8, 'stock_available' => 8, 'is_active' => true],
            ['name' => 'Tongkat Kaki Tiga / Empat', 'category' => 'mobilitas', 'price_per_day' => 15000, 'stock' => 10, 'stock_available' => 10, 'is_active' => true],
            ['name' => 'Walker', 'category' => 'mobilitas', 'price_per_day' => 15000, 'stock' => 8, 'stock_available' => 8, 'is_active' => true],
        ]);

        // Kategori: oksigen
        Inventory::insert([
            ['name' => 'Tabung Oksigen', 'category' => 'oksigen', 'price_per_day' => 50000, 'stock' => 6, 'stock_available' => 6, 'is_active' => true],
        ]);

        // Kategori: alat_kecil
        Inventory::insert([
            ['name' => 'Tiang Infus', 'category' => 'alat_kecil', 'price_per_day' => 25000, 'stock' => 20, 'stock_available' => 20, 'is_active' => true],
            ['name' => 'Set Bidai', 'category' => 'alat_kecil', 'price_per_day' => 35000, 'stock' => 15, 'stock_available' => 15, 'is_active' => true],
            ['name' => 'Set Mitela', 'category' => 'alat_kecil', 'price_per_day' => 20000, 'stock' => 15, 'stock_available' => 15, 'is_active' => true],
            ['name' => 'Tongue Spatel', 'category' => 'alat_kecil', 'price_per_day' => 5000, 'stock' => 30, 'stock_available' => 30, 'is_active' => true],
            ['name' => 'Pinset Anatomis', 'category' => 'alat_kecil', 'price_per_day' => 5000, 'stock' => 25, 'stock_available' => 25, 'is_active' => true],
            ['name' => 'Pinset Sirurgis', 'category' => 'alat_kecil', 'price_per_day' => 5000, 'stock' => 25, 'stock_available' => 25, 'is_active' => true],
            ['name' => 'Gunting Jaringan', 'category' => 'alat_kecil', 'price_per_day' => 5000, 'stock' => 20, 'stock_available' => 20, 'is_active' => true],
            ['name' => 'Gunting Bandage', 'category' => 'alat_kecil', 'price_per_day' => 5000, 'stock' => 20, 'stock_available' => 20, 'is_active' => true],
            ['name' => 'Kom/Bowl Medis', 'category' => 'alat_kecil', 'price_per_day' => 10000, 'stock' => 25, 'stock_available' => 25, 'is_active' => true],
            ['name' => 'Bengkok', 'category' => 'alat_kecil', 'price_per_day' => 10000, 'stock' => 25, 'stock_available' => 25, 'is_active' => true],
            ['name' => 'Bak Instrument', 'category' => 'alat_kecil', 'price_per_day' => 25000, 'stock' => 15, 'stock_available' => 15, 'is_active' => true],
            ['name' => 'Bak Injeksi', 'category' => 'alat_kecil', 'price_per_day' => 20000, 'stock' => 15, 'stock_available' => 15, 'is_active' => true],
        ]);

        // Kategori: monitoring
        Inventory::insert([
            ['name' => 'Tensi Meter Digital', 'category' => 'monitoring', 'price_per_day' => 100000, 'stock' => 8, 'stock_available' => 8, 'is_active' => true],
            ['name' => 'Gluco Check 3 in 1', 'category' => 'monitoring', 'price_per_day' => 50000, 'stock' => 6, 'stock_available' => 6, 'is_active' => true],
            ['name' => 'Oximeter', 'category' => 'monitoring', 'price_per_day' => 50000, 'stock' => 10, 'stock_available' => 10, 'is_active' => true],
            ['name' => 'Termometer Digital', 'category' => 'monitoring', 'price_per_day' => 10000, 'stock' => 15, 'stock_available' => 15, 'is_active' => true],
        ]);

        // Kategori: timbangan
        Inventory::insert([
            ['name' => 'Timbangan Berat Badan Injak', 'category' => 'timbangan', 'price_per_day' => 10000, 'stock' => 8, 'stock_available' => 8, 'is_active' => true],
            ['name' => 'Timbangan Berat Badan Digital + TB', 'category' => 'timbangan', 'price_per_day' => 100000, 'stock' => 4, 'stock_available' => 4, 'is_active' => true],
            ['name' => 'Pengukur Tinggi Badan', 'category' => 'timbangan', 'price_per_day' => 10000, 'stock' => 6, 'stock_available' => 6, 'is_active' => true],
        ]);

        // Kategori: alat_khusus
        Inventory::insert([
            ['name' => 'Food Model 10 Item', 'category' => 'alat_khusus', 'price_per_day' => 100000, 'stock' => 3, 'stock_available' => 3, 'is_active' => true],
            ['name' => 'Head Imobilizer', 'category' => 'alat_khusus', 'price_per_day' => 50000, 'stock' => 6, 'stock_available' => 6, 'is_active' => true],
            ['name' => 'KED (Kendrik Extrication Device)', 'category' => 'alat_khusus', 'price_per_day' => 150000, 'stock' => 4, 'stock_available' => 4, 'is_active' => true],
            ['name' => 'Margil Forcep', 'category' => 'alat_khusus', 'price_per_day' => 30000, 'stock' => 10, 'stock_available' => 10, 'is_active' => true],
            ['name' => 'Penlight', 'category' => 'alat_khusus', 'price_per_day' => 10000, 'stock' => 15, 'stock_available' => 15, 'is_active' => true],
            ['name' => 'Klem Kocher', 'category' => 'alat_khusus', 'price_per_day' => 30000, 'stock' => 10, 'stock_available' => 10, 'is_active' => true],
            ['name' => 'Suction Portable', 'category' => 'alat_khusus', 'price_per_day' => 50000, 'stock' => 6, 'stock_available' => 6, 'is_active' => true],
            ['name' => 'Refleks Hammer', 'category' => 'alat_khusus', 'price_per_day' => 10000, 'stock' => 12, 'stock_available' => 12, 'is_active' => true],
            ['name' => 'Head Lamp', 'category' => 'alat_khusus', 'price_per_day' => 30000, 'stock' => 8, 'stock_available' => 8, 'is_active' => true],
            ['name' => 'SDIDTK Set', 'category' => 'alat_khusus', 'price_per_day' => 50000, 'stock' => 5, 'stock_available' => 5, 'is_active' => true],
            ['name' => 'Nebulizer', 'category' => 'alat_khusus', 'price_per_day' => 30000, 'stock' => 8, 'stock_available' => 8, 'is_active' => true],
        ]);

        // Kategori: pakaian
        Inventory::insert([
            ['name' => 'Baju Scrub', 'category' => 'pakaian', 'price_per_day' => 15000, 'stock' => 30, 'stock_available' => 30, 'is_active' => true],
            ['name' => 'Baju Operasi', 'category' => 'pakaian', 'price_per_day' => 15000, 'stock' => 20, 'stock_available' => 20, 'is_active' => true],
            ['name' => 'Baju Pasien', 'category' => 'pakaian', 'price_per_day' => 15000, 'stock' => 25, 'stock_available' => 25, 'is_active' => true],
        ]);

        // Kategori: pompa
        Inventory::insert([
            ['name' => 'Infus Pump', 'category' => 'pompa', 'price_per_day' => 200000, 'stock' => 6, 'stock_available' => 6, 'is_active' => true],
            ['name' => 'Syringe Pump', 'category' => 'pompa', 'price_per_day' => 250000, 'stock' => 5, 'stock_available' => 5, 'is_active' => true],
        ]);

        // Kategori: simulator
        Inventory::insert([
            ['name' => 'Age Simulator', 'category' => 'simulator', 'price_per_day' => 400000, 'stock' => 2, 'stock_available' => 2, 'is_active' => true],
            ['name' => 'Emergo Train System Kit', 'category' => 'simulator', 'price_per_day' => 400000, 'stock' => 2, 'stock_available' => 2, 'is_active' => true],
            ['name' => 'Phantom CPR with SimPad', 'category' => 'simulator', 'price_per_day' => 700000, 'stock' => 3, 'stock_available' => 3, 'is_active' => true],
        ]);
    }
}