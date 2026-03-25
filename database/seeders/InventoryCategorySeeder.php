<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryCategory;

class InventoryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // ── Peralatan Lab / Keperawatan ─────────────── sort 1xx
            ['key'=>'phantom_rjp',       'name'=>'Phantom RJP',                  'icon'=>'fas fa-heartbeat',    'sort_order'=>101],
            ['key'=>'phantom_airway',    'name'=>'Phantom Airway',               'icon'=>'fas fa-wind',         'sort_order'=>102],
            ['key'=>'phantom_lain',      'name'=>'Phantom Lainnya',              'icon'=>'fas fa-user-injured', 'sort_order'=>103],
            ['key'=>'defibrillator',     'name'=>'Defibrillator & AED',          'icon'=>'fas fa-bolt',         'sort_order'=>104],
            ['key'=>'bvm',               'name'=>'BVM (Bag Valve Mask)',         'icon'=>'fas fa-lungs',        'sort_order'=>105],
            ['key'=>'laringoskop',       'name'=>'Laringoskop',                  'icon'=>'fas fa-microscope',   'sort_order'=>106],
            ['key'=>'alat_dasar',        'name'=>'Alat Dasar',                   'icon'=>'fas fa-stethoscope',  'sort_order'=>107],
            ['key'=>'stretcher',         'name'=>'Stretcher & Spine Board',      'icon'=>'fas fa-procedures',   'sort_order'=>108],
            ['key'=>'bed',               'name'=>'Bed Pasien',                   'icon'=>'fas fa-bed',          'sort_order'=>109],
            ['key'=>'mobilitas',         'name'=>'Alat Mobilitas',               'icon'=>'fas fa-wheelchair',   'sort_order'=>110],
            ['key'=>'oksigen',           'name'=>'Tabung Oksigen',               'icon'=>'fas fa-flask',        'sort_order'=>111],
            ['key'=>'alat_kecil',        'name'=>'Alat Medis Kecil',             'icon'=>'fas fa-syringe',      'sort_order'=>112],
            ['key'=>'monitoring',        'name'=>'Alat Monitoring',              'icon'=>'fas fa-chart-line',   'sort_order'=>113],
            ['key'=>'timbangan',         'name'=>'Timbangan',                    'icon'=>'fas fa-weight',       'sort_order'=>114],
            ['key'=>'alat_khusus',       'name'=>'Alat Khusus',                  'icon'=>'fas fa-tools',        'sort_order'=>115],
            ['key'=>'pakaian',           'name'=>'Pakaian Medis',                'icon'=>'fas fa-tshirt',       'sort_order'=>116],
            ['key'=>'pompa',             'name'=>'Pompa (Infus/Syringe)',        'icon'=>'fas fa-tint',         'sort_order'=>117],
            ['key'=>'simulator',         'name'=>'Simulator',                    'icon'=>'fas fa-vr-cardboard', 'sort_order'=>118],

            // ── Transportasi ─────────────────────────────── sort 2xx
            ['key'=>'kendaraan_roda4',   'name'=>'Kendaraan Roda 4 (Mobil)',     'icon'=>'fas fa-car',          'sort_order'=>201],
            ['key'=>'kendaraan_roda2',   'name'=>'Kendaraan Roda 2',             'icon'=>'fas fa-motorcycle',   'sort_order'=>202],

            // ── Elektronik & AV ──────────────────────────── sort 3xx
            ['key'=>'infocus',           'name'=>'Proyektor / Infocus',          'icon'=>'fas fa-film',         'sort_order'=>301],
            ['key'=>'audio',             'name'=>'Peralatan Audio',              'icon'=>'fas fa-microphone',   'sort_order'=>302],
            ['key'=>'kamera',            'name'=>'Kamera & Aksesoris',           'icon'=>'fas fa-camera',       'sort_order'=>303],
            ['key'=>'komputer',          'name'=>'Komputer / Laptop / Tablet',   'icon'=>'fas fa-laptop',       'sort_order'=>304],
            ['key'=>'baterai_acc',       'name'=>'Baterai & Aksesori Elektronik','icon'=>'fas fa-battery-full', 'sort_order'=>305],

            // ── Perlengkapan Umum ─────────────────────────── sort 4xx
            ['key'=>'mebel',             'name'=>'Mebel & Perlengkapan Ruangan', 'icon'=>'fas fa-chair',        'sort_order'=>401],
            ['key'=>'alat_kebersihan',   'name'=>'Alat Kebersihan',              'icon'=>'fas fa-broom',        'sort_order'=>402],
            ['key'=>'perlengkapan_acara','name'=>'Perlengkapan Acara',           'icon'=>'fas fa-star',         'sort_order'=>403],

            // ── Lainnya ───────────────────────────────────── sort 999
            ['key'=>'lainnya',           'name'=>'Lainnya',                      'icon'=>'fas fa-box',          'sort_order'=>999],
        ];

        foreach ($categories as $cat) {
            InventoryCategory::updateOrCreate(['key' => $cat['key']], $cat);
        }
    }
}