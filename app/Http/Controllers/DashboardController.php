<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        Booking::markCompletedBookings();

        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();

        // Default data untuk semua user
        $data = [
            'title' => 'Dashboard',
            'menuDashboard' => 'active',
            'today' => $today,
        ];

        if ($user->role == 'admin') { 
            // === DASHBOARD UNTUK ADMIN
            $pendingOver1Hour = Booking::where('status', 'pending')
            ->where('created_at', '<', $now->copy()->subHour())
            ->count();

            $data = array_merge($data, [
                'totalMahasiswa'    => User::where('jenis_pengguna', 'mahasiswa')->count(),
                'totalRuangan'      => Room::count(),
                'bookingsToday'     => Booking::whereDate('tanggal_pinjam', $today)->where('status', 'approved')->count(),
                'bookingsPending'   => Booking::where('status', 'pending')->count(),
                'bookingsApproved'  => Booking::where('status', 'approved')->count(),
                'bookingsRejected'  => Booking::where('status', 'rejected')->count(),
                'bookingsCompleted' => Booking::where('status', 'completed')->count(),
                'bookingsActive'    => Booking::where('status', 'approved')
                    ->where('tanggal_pinjam', '<=', $today)
                    ->where('waktu_selesai', '>=', $now)
                    ->count(),
                'bookingsTodayList' => Booking::with(['user', 'room'])
                    ->whereDate('tanggal_pinjam', $today)
                    ->orderBy('waktu_mulai', 'asc')
                    ->get(),
                 'pendingOver1Hour' => $pendingOver1Hour,
            ]);

        } else { 
            // === DASHBOARD UNTUK USER BIASA
            $data = array_merge($data, [
                'totalBookingSaya'     => $user->bookings()->count(),
                'pendingSaya'          => $user->bookings()->where('status', 'pending')->count(),
                'approvedSaya'         => $user->bookings()->where('status', 'approved')->count(),
                'completedSaya'        => $user->bookings()->where('status', 'completed')->count(),
                'activeSaya'           => $user->bookings()
                    ->where('status', 'approved')
                    ->whereDate('tanggal_pinjam', $today)
                    ->whereTime('waktu_mulai', '<=', $now)
                    ->whereTime('waktu_selesai', '>', $now)
                    ->count(),
                'bookingsTodayListSaya' => $user->bookings()
                    ->with('room')
                    ->whereDate('tanggal_pinjam', $today)
                    ->orderBy('waktu_mulai', 'asc')
                    ->get(),
            ]);
        }
        return view('dashboard', $data);
    }
}
