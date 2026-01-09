<?php

namespace App\Http\Controllers;

use App\Models\Booking;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil SEMUA booking (tanpa filter status), urutkan berdasarkan waktu
        $bookings = Booking::with(['user', 'room'])
            ->orderBy('tanggal_pinjam', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        $events = $bookings->map(function ($booking) {
            $kodeRuangan = $booking->room?->kode_ruangan ?? 'Ruangan';
            $namaRuangan = $booking->room?->nama_ruangan ?? 'Tidak diketahui';
            $pengaju = $booking->user?->name ?? 'Anonim';
            $lokasi = $booking->room->lokasi ?? 'Tidak diketahui';

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
                    'status' => $booking->status,
                ]
            ];
        });

        return view('welcome', compact('events'));
    }
}