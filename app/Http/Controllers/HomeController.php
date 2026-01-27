<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil SEMUA booking yang relevan, urutkan berdasarkan waktu
        $bookings = Booking::with(['user', 'room'])
            ->whereIn('status', ['approved', 'pending', 'completed'])
            ->orderBy('tanggal_pinjam', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        $events = $bookings->map(function ($booking) {
            $kodeRuangan = $booking->room?->kode_ruangan ?? 'Ruangan';
            $namaRuangan = $booking->room?->nama_ruangan ?? 'Tidak diketahui';
            $pengaju     = $booking->user?->name ?? 'Anonim';
            $lokasi      = $booking->room->lokasi ?? 'Tidak diketahui';
            $roleUnit    = $booking->role_unit ?? '-';

            // Format tanggal + waktu untuk FullCalendar (ISO 8601)
            $start = $booking->tanggal_pinjam . 'T' . $booking->waktu_mulai;
            $end   = $booking->tanggal_pinjam . 'T' . $booking->waktu_selesai;

            // Warna berdasarkan status
            switch ($booking->status) {
                case 'approved':
                    $color = '#28a745'; // hijau
                    $textColor = '#ffffff';
                    break;
                case 'pending':
                    $color = '#ffc107'; // kuning
                    $textColor = '#212529';
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
                'title'         => $kodeRuangan,
                'start'         => $start,
                'end'           => $end,
                'backgroundColor' => $color,
                'borderColor'   => $color,
                'textColor'     => $textColor,
                'extendedProps' => [
                    'kode_ruangan' => $kodeRuangan,
                    'nama_ruangan' => $namaRuangan,
                    'pengaju'      => $pengaju,
                    'keperluan'    => $booking->keperluan ?? '-',
                    'lokasi'       => $lokasi,
                    'role_unit'    => $roleUnit,
                    'status'       => $booking->status,
                ]
            ];
        });

        return view('welcome', compact('events'));
    }

    public function ruangan(Request $request)
    {
        $query = Room::where('is_active', true);

        // Filter: Ruangan kosong berdasarkan tanggal & jam
        if ($request->filled(['tanggal', 'jam_mulai', 'jam_selesai'])) {
            $tanggal     = $request->tanggal;
            $jam_mulai   = $request->jam_mulai;
            $jam_selesai = $request->jam_selesai;

            $query->whereDoesntHave('bookings', function ($q) use ($tanggal, $jam_mulai, $jam_selesai) {
                $q->where('tanggal_pinjam', $tanggal)
                  ->where('status', 'approved') // Hanya booking yang sudah disetujui
                  ->where(function ($q) use ($jam_mulai, $jam_selesai) {
                      $q->where('waktu_mulai', '<', $jam_selesai)
                        ->where('waktu_selesai', '>', $jam_mulai);
                  });
            });
        }

        // Filter Lokasi (jika dipilih)
        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }

        // Pengurutan: PASCA muncul paling awal, lalu lokasi lain alfabetis, lalu kode ruangan
        $query->orderByRaw("CASE WHEN lokasi = 'PASCA' THEN 0 ELSE 1 END")
              ->orderBy('lokasi', 'asc')
              ->orderBy('kode_ruangan', 'asc');

        $rooms = $query->get();

        // Daftar lokasi unik untuk dropdown filter
        $lokasiList = Room::where('is_active', true)
                          ->pluck('lokasi')
                          ->unique()
                          ->sort()
                          ->values();

        return view('ruangan', compact('rooms', 'lokasiList'));
    }
}