<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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

    public function statistics()
    {
        // Ambil data peminjaman per bulan untuk tahun ini
        $bookingsPerMonth = Booking::select(
            DB::raw('MONTH(tanggal_pinjam) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('tanggal_pinjam', Carbon::now()->format('Y'))
        ->where('status', '!=', 'cancelled')
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month');

        // Siapkan data untuk 12 bulan
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[$i] = $bookingsPerMonth->get($i, 0);
        }

        $data = [
            'title' => 'Statistik Peminjaman Ruangan',
            'menuAdminStatistics' => 'active',
            'monthlyData' => $monthlyData,
            'currentYear' => Carbon::now()->format('Y')
        ];

        return view('admin.statistics.index', $data);
    }

    public function bookingPerMonth(Request $request)
    {
        $year = $request->input('year', Carbon::now()->format('Y'));

        $bookingsPerMonth = Booking::select(
            DB::raw('MONTH(tanggal_pinjam) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('tanggal_pinjam', $year)
        ->where('status', '!=', 'cancelled')
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month');

        // Siapkan data untuk 12 bulan
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[$i] = $bookingsPerMonth->get($i, 0);
        }

        return response()->json([
            'success' => true,
            'data' => array_values($monthlyData),
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'year' => $year
        ]);
    }

    public function bookingPerDay(Request $request)
    {
        $days = $request->input('days', 30);
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);

        $bookingsPerDay = Booking::select(
            DB::raw('DATE(tanggal_pinjam) as date'),
            DB::raw('COUNT(*) as total')
        )
        ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
        ->where('status', '!=', 'cancelled')
        ->groupBy('date')
        ->pluck('total', 'date');

        // Siapkan data untuk semua hari
        $dailyData = [];
        $labels = [];
        $colors = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $endDate->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $label = $date->format('d M');
            
            $dailyData[] = $bookingsPerDay->get($dateStr, 0);
            $labels[] = $label;
            
            // Highlight weekend
            $dayOfWeek = $date->dayOfWeek; // 0 = Minggu, 6 = Sabtu
            $colors[] = ($dayOfWeek == 0 || $dayOfWeek == 6) ? '#ff6b6b' : '#4e73df';
        }

        return response()->json([
            'success' => true,
            'data' => $dailyData,
            'labels' => $labels,
            'colors' => $colors,
            'period' => "$days hari terakhir"
        ]);
    }

    // Top Ruangan
    public function topRooms(Request $request)
    {
        $year = $request->input('year', Carbon::now()->format('Y'));
        $month = $request->input('month'); // Opsional

        $query = Booking::select('room_id', DB::raw('COUNT(*) as total'))
            ->where('status', '!=', 'cancelled')
            ->with('room')
            ->groupBy('room_id')
            ->orderByDesc('total')
            ->limit(10);

        // Filter berdasarkan tahun
        $query->whereYear('tanggal_pinjam', $year);

        // Jika bulan dipilih, filter juga berdasarkan bulan
        if ($month) {
            $query->whereMonth('tanggal_pinjam', $month);
        }

        $topRooms = $query->get();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($topRooms as $booking) {
            $room = $booking->room;
            if (!$room) continue;

            $labels[] = $room->kode_ruangan . ' - ' . $room->nama_ruangan;
            $data[] = $booking->total;
            $colors[] = $room->is_paid ? '#ff6b6b' : '#4e73df';
        }

        // Tentukan periode untuk judul
        $period = $month ? 
            Carbon::createFromDate($year, $month, 1)->isoFormat('MMMM YYYY') : 
            $year;

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
            'period' => $period
        ]);
    }

    public function timeAnalysis(Request $request)
    {
        $year = $request->input('year', Carbon::now()->format('Y'));
        $month = $request->input('month');

        $query = Booking::where('status', '!=', 'cancelled')
            ->whereYear('tanggal_pinjam', $year);

        if ($month) {
            $query->whereMonth('tanggal_pinjam', $month);
        }

        // 1. Jam Paling Sering Dipinjam (per jam)
        $hourlyData = $query->clone()
            ->select(DB::raw('HOUR(waktu_mulai) as hour'), DB::raw('COUNT(*) as total'))
            ->groupBy('hour')
            ->pluck('total', 'hour');

        // Inisialisasi jam 7-20 dengan 0
        $hours = [];
        for ($h = 7; $h <= 20; $h++) {
            $hours[$h] = $hourlyData->get($h, 0); // default 0 jika tidak ada
        }

        // 2. Hari Paling Sibuk (Senin=1, Minggu=7)
        $dailyData = $query->clone()
            ->select(DB::raw('DAYOFWEEK(tanggal_pinjam) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->pluck('total', 'day');

        $days = [
            2 => 'Senin',    // DAYOFWEEK: Senin=2
            3 => 'Selasa',
            4 => 'Rabu',
            5 => 'Kamis',
            6 => 'Jumat',
            7 => 'Sabtu',
            1 => 'Minggu'     // DAYOFWEEK: Minggu=1
        ];

        $weekdayData = [];
        foreach ($days as $num => $name) {
            $weekdayData[$name] = $dailyData->get($num, 0); // default 0
        }

        // 3. Durasi Rata-rata (dalam menit)
        $durationData = $query->clone()
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, waktu_mulai, waktu_selesai)) as avg_duration'))
            ->first();

        $avgDuration = round($durationData->avg_duration ?? 0);

        return response()->json([
            'success' => true,
            'hourly' => $hours,
            'weekday' => $weekdayData,
            'avg_duration' => $avgDuration,
            'period' => $month ? 
                Carbon::createFromDate($year, $month, 1)->isoFormat('MMMM YYYY') : 
                $year
        ]);
    }

    public function exportFullPdf(Request $request)
    {
        $year = $request->input('year', now()->format('Y'));
        $month = $request->input('month');
        $days = $request->input('days', 30);

        // Ambil semua data
        $monthlyData = $this->getMonthlyData($year);
        $dailyData = $this->getDailyData($days);
        $topRoomsData = $this->getTopRoomsData($year, $month);
        $timeAnalysisData = $this->getTimeAnalysisData($year, $month);

        // Tentukan periode untuk judul
        $period = $month ? 
            \Carbon\Carbon::createFromDate($year, $month, 1)->isoFormat('MMMM YYYY') : 
            $year;

        $pdf = Pdf::loadView('admin.statistics.pdf.full', [
            'monthly_data' => $monthlyData,
            'daily_data' => $dailyData,
            'top_rooms_data' => $topRoomsData,
            'time_analysis_data' => $timeAnalysisData,
            'period' => $period,
            'generated_at' => now()->isoFormat('D MMMM YYYY HH:mm')
        ]);

        return $pdf->download("statistik_lengkap_" . now()->format('Y-m-d') . ".pdf");
    }

    // Helper methods (dipindah ke dalam controller)
    private function getMonthlyData($year)
    {
        $bookingsPerMonth = Booking::select(
            DB::raw('MONTH(tanggal_pinjam) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('tanggal_pinjam', $year)
        ->where('status', '!=', 'cancelled')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[$i] = 0;
        }

        foreach ($bookingsPerMonth as $booking) {
            $monthlyData[$booking->month] = $booking->total;
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'data' => array_values($monthlyData),
            'year' => $year
        ];
    }

    private function getDailyData($days)
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays($days - 1);

        $bookingsPerDay = Booking::select(
            DB::raw('DATE(tanggal_pinjam) as date'),
            DB::raw('COUNT(*) as total')
        )
        ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
        ->where('status', '!=', 'cancelled')
        ->groupBy('date')
        ->pluck('total', 'date');

        $dailyData = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $endDate->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $label = $date->format('d M');

            $dailyData[] = $bookingsPerDay->get($dateStr, 0);
            $labels[] = $label;
        }

        return [
            'labels' => $labels,
            'data' => $dailyData,
            'period' => "$days hari terakhir"
        ];
    }

    private function getTopRoomsData($year, $month = null)
    {
        $query = Booking::select('room_id', DB::raw('COUNT(*) as total'))
            ->where('status', '!=', 'cancelled')
            ->with('room')
            ->groupBy('room_id')
            ->orderByDesc('total')
            ->limit(10);

        $query->whereYear('tanggal_pinjam', $year);
        if ($month) {
            $query->whereMonth('tanggal_pinjam', $month);
        }

        $topRooms = $query->get();

        $labels = [];
        $data = [];
        $is_paid = [];

        foreach ($topRooms as $booking) {
            $room = $booking->room;
            if (!$room) continue;

            $labels[] = $room->kode_ruangan . ' - ' . $room->nama_ruangan;
            $data[] = $booking->total;
            $is_paid[] = $room->is_paid;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'is_paid' => $is_paid
        ];
    }

    private function getTimeAnalysisData($year, $month = null)
    {
        $query = Booking::where('status', '!=', 'cancelled')
            ->whereYear('tanggal_pinjam', $year);

        if ($month) {
            $query->whereMonth('tanggal_pinjam', $month);
        }

        // Jam paling sering
        $hourlyData = $query->clone()
            ->select(DB::raw('HOUR(waktu_mulai) as hour'), DB::raw('COUNT(*) as total'))
            ->groupBy('hour')
            ->pluck('total', 'hour');

        $hours = [];
        for ($h = 7; $h <= 20; $h++) {
            $hours[$h] = $hourlyData->get($h, 0);
        }

        // Hari paling sibuk
        $dailyData = $query->clone()
            ->select(DB::raw('DAYOFWEEK(tanggal_pinjam) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->pluck('total', 'day');

        $days = [2 => 'Senin', 3 => 'Selasa', 4 => 'Rabu', 5 => 'Kamis', 6 => 'Jumat', 7 => 'Sabtu', 1 => 'Minggu'];
        $weekdayData = [];
        foreach ($days as $num => $name) {
            $weekdayData[$name] = $dailyData->get($num, 0);
        }

        // Durasi rata-rata
        $durationData = $query->clone()
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, waktu_mulai, waktu_selesai)) as avg_duration'))
            ->first();

        return [
            'hourly' => $hours,
            'weekday' => $weekdayData,
            'avg_duration' => round($durationData->avg_duration ?? 0)
        ];
    }
}
