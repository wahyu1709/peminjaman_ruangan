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
        $bookings = Booking::with(['user', 'room', 'inventories'])
            ->whereIn('status', ['approved', 'pending', 'completed'])
            ->orderBy('tanggal_pinjam', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        $events = $bookings->map(function ($booking) {
            $isInventoryOnly = $booking->is_inventory_only || !$booking->room;

            // Title di kalender
            $title       = $isInventoryOnly ? '📦 Pinjam Barang' : ($booking->room->kode_ruangan ?? 'Ruangan');
            $kodeRuangan = $isInventoryOnly ? null : ($booking->room->kode_ruangan ?? null);
            $namaRuangan = $isInventoryOnly ? null : ($booking->room->nama_ruangan ?? null);
            $lokasi      = $isInventoryOnly ? null : ($booking->room->lokasi ?? null);

            // Daftar barang yang dipinjam
            $inventoryList = $booking->inventories->map(function ($inv) {
                return [
                    'name'     => $inv->name,
                    'quantity' => $inv->pivot->quantity,
                    'category' => $inv->category_name,
                ];
            })->toArray();

            $start = $booking->tanggal_pinjam . 'T' . $booking->waktu_mulai;
            $end   = $booking->tanggal_pinjam . 'T' . $booking->waktu_selesai;

            switch ($booking->status) {
                case 'approved':  $color = '#28a745'; $textColor = '#ffffff'; break;
                case 'pending':   $color = '#ffc107'; $textColor = '#212529'; break;
                case 'rejected':  $color = '#dc3545'; $textColor = '#ffffff'; break;
                default:          $color = '#6c757d'; $textColor = '#ffffff';
            }

            return [
                'title'           => $title,
                'start'           => $start,
                'end'             => $end,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => $textColor,
                'extendedProps'   => [
                    'is_inventory_only' => $isInventoryOnly,
                    'kode_ruangan'      => $kodeRuangan,
                    'nama_ruangan'      => $namaRuangan,
                    'pengaju'           => $booking->user?->name ?? 'Anonim',
                    'keperluan'         => $booking->keperluan ?? '-',
                    'lokasi'            => $lokasi,
                    'role_unit'         => $booking->role_unit ?? '-',
                    'status'            => $booking->status,
                    'inventories'       => $inventoryList,
                ]
            ];
        });

        return view('welcome', compact('events'));
    }

    public function ruangan(Request $request)
    {
        $query = Room::where('is_active', true);

        if ($request->filled(['tanggal', 'jam_mulai', 'jam_selesai'])) {
            $tanggal     = $request->tanggal;
            $jam_mulai   = $request->jam_mulai;
            $jam_selesai = $request->jam_selesai;

            $query->whereDoesntHave('bookings', function ($q) use ($tanggal, $jam_mulai, $jam_selesai) {
                $q->where('tanggal_pinjam', $tanggal)
                  ->where('status', 'approved')
                  ->where(function ($q) use ($jam_mulai, $jam_selesai) {
                      $q->where('waktu_mulai', '<', $jam_selesai)
                        ->where('waktu_selesai', '>', $jam_mulai);
                  });
            });
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }

        $query->orderByRaw("CASE WHEN lokasi = 'PASCA' THEN 0 ELSE 1 END")
              ->orderBy('lokasi', 'asc')
              ->orderBy('kode_ruangan', 'asc');

        $rooms = $query->get();

        $lokasiList = Room::where('is_active', true)
                          ->pluck('lokasi')
                          ->unique()
                          ->sort()
                          ->values();

        return view('ruangan', compact('rooms', 'lokasiList'));
    }
}