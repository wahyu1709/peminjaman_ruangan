<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil SEMUA booking (tanpa filter status), urutkan berdasarkan waktu
        $bookings = Booking::with(['user', 'room'])
            ->whereIn('status', ['approved', 'pending', 'completed'])
            ->orderBy('tanggal_pinjam', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        $events = $bookings->map(function ($booking) {
            $kodeRuangan = $booking->room?->kode_ruangan ?? 'Ruangan';
            $namaRuangan = $booking->room?->nama_ruangan ?? 'Tidak diketahui';
            $pengaju = $booking->user?->name ?? 'Anonim';
            $lokasi = $booking->room->lokasi ?? 'Tidak diketahui';
            $roleUnit = $booking->role_unit ?? '-';

            // Gabungkan tanggal + waktu dalam format ISO 8601
            $start = $booking->tanggal_pinjam . 'T' . $booking->waktu_mulai;
            $end = $booking->tanggal_pinjam . 'T' . $booking->waktu_selesai;

            // Tentukan warna berdasarkan status
            switch ($booking->status) {
                case 'approved':
                    $color = '#28a745'; // hijau
                    $textColor = '#ffffff';
                    break;
                case 'pending':
                    $color = '#ffc107'; // kuning
                    $textColor = '#212529'; // hitam agar terbaca
                    break;
                case 'rejected':
                    $color = '#dc3545'; // merah
                    $textColor = '#ffffff';
                    break;
                default:
                    $color = '#6c757d';
                    $textColor = '#ffffff';
            }

            return [
                'title' => $kodeRuangan,
                'start' => $start,
                'end' => $end,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => $textColor,
                'extendedProps' => [
                    'kode_ruangan' => $kodeRuangan,
                    'nama_ruangan' => $namaRuangan,
                    'pengaju' => $pengaju,
                    'keperluan' => $booking->keperluan ?? '-',
                    'lokasi' => $lokasi,
                    'role_unit' => $roleUnit,
                    'status' => $booking->status,
                ]
            ];
        });

        return view('welcome', compact('events'));
    }

    public function ruangan(Request $request){
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

        return view('ruangan', compact('rooms', 'lokasiList'));
    }
}