<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use App\Models\Inventory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use function Symfony\Component\Clock\now;

class DashboardController extends Controller
{
    public function index()
    {
        Booking::markCompletedBookings();

        $user  = Auth::user();
        $today = Carbon::today();
        $now   = Carbon::now();

        $data = [
            'title'         => 'Dashboard',
            'menuDashboard' => 'active',
            'today'         => $today,
        ];

        if ($user->role == 'admin') {
            $pendingOver1Hour = Booking::where('status', 'pending')
                ->where('created_at', '<', $now->copy()->subHour())
                ->count();

            $data = array_merge($data, [
                'totalMahasiswa'    => User::where('jenis_pengguna', 'mahasiswa')->count(),
                'totalRuangan'      => Room::count(),
                'bookingsToday'     => Booking::whereDate('tanggal_pinjam', $today)->where('status', 'approved')->count(),
                'bookingsPending'   => Booking::where('status', 'pending')->count(),
                'bookingsApproved'  => Booking::where('status', 'approved')->count(),
                'bookingsRejected'  => Booking::whereDate('tanggal_pinjam', $today)->where('status', 'rejected')->count(),
                'bookingsCompleted' => Booking::where('status', 'completed')->count(),
                'bookingsActive'    => Booking::where('status', 'approved')
                    ->where('tanggal_pinjam', '<=', $today)
                    ->where('waktu_selesai', '>=', $now)
                    ->count(),
                'bookingsTodayList' => Booking::with(['user', 'room'])
                    ->whereDate('tanggal_pinjam', $today)
                    ->orderBy('waktu_mulai', 'asc')
                    ->get(),
                'pendingOver1Hour'    => $pendingOver1Hour,
                'pendingPaidBookings' => $user->bookings()
                    ->where('total_amount', '>', 0)
                    ->where('status', 'pending')
                    ->whereNull('bukti_pembayaran')
                    ->count(),
            ]);
        } else {
            $data = array_merge($data, [
                'totalBookingSaya' => $user->bookings()->count(),
                'pendingSaya'      => $user->bookings()->where('status', 'pending')->count(),
                'approvedSaya'     => $user->bookings()->where('status', 'approved')->count(),
                'completedSaya'    => $user->bookings()->where('status', 'completed')->count(),
                'activeSaya'       => $user->bookings()
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
                'pendingPaidBookings' => $user->bookings()
                    ->where('total_amount', '>', 0)
                    ->where('status', 'pending')
                    ->whereNull('bukti_pembayaran')
                    ->count(),
            ]);
        }

        return view('dashboard', $data);
    }

    public function statistics()
    {
        return view('admin.statistics.index', [
            'title'               => 'Statistik Peminjaman Ruangan',
            'menuAdminStatistics' => 'active',
            'currentYear'         => Carbon::now()->format('Y'),
        ]);
    }

    // =========================================================
    // API: Booking per Bulan
    // Mendukung param tambahan: detail_status=1, user_type=1
    // =========================================================
    public function bookingPerMonth(Request $request)
    {
        $year  = $request->input('year', Carbon::now()->format('Y'));
        $month = $request->input('month'); // ← tambah ini

        // ── Data bulanan utama ────────────────────────────────
        $query = Booking::select(
                DB::raw('MONTH(tanggal_pinjam) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('tanggal_pinjam', $year)
            ->where('status', '!=', 'cancelled');

        // ← filter bulan jika ada
        if ($month) {
            $query->whereMonth('tanggal_pinjam', $month);
        }

        $bookingsPerMonth = $query->groupBy('month')->pluck('total', 'month');

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[$i] = $bookingsPerMonth->get($i, 0);
        }

        $response = [
            'success' => true,
            'data'    => array_values($monthlyData),
            'labels'  => ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
            'year'    => $year,
        ];

        // ── Distribusi status ─────────────────────────────────
        if ($request->boolean('detail_status')) {
            $statusQuery = Booking::select('status', DB::raw('COUNT(*) as total'))
                ->whereYear('tanggal_pinjam', $year)
                ->groupBy('status');

            // ← filter bulan jika ada
            if ($month) {
                $statusQuery->whereMonth('tanggal_pinjam', $month);
            }

            $statusCounts = $statusQuery->pluck('total', 'status');

            $response['by_status'] = [
                'approved'         => $statusCounts->get('approved', 0),
                'pending'          => $statusCounts->get('pending', 0),
                'payment_uploaded' => $statusCounts->get('payment_uploaded', 0),
                'rejected'         => $statusCounts->get('rejected', 0),
                'completed'        => $statusCounts->get('completed', 0),
                'cancelled'        => $statusCounts->get('cancelled', 0),
            ];

            $response['by_status']['inventory_only'] = Booking::whereYear('tanggal_pinjam', $year)
                ->when($month, fn($q) => $q->whereMonth('tanggal_pinjam', $month))
                ->where('is_inventory_only', true)
                ->count();
        }

        // ── Internal vs umum ──────────────────────────────────
        if ($request->boolean('user_type')) {
            $userTypeQuery = Booking::select('users.jenis_pengguna', DB::raw('COUNT(*) as total'))
                ->join('users', 'bookings.user_id', '=', 'users.id')
                ->whereYear('bookings.tanggal_pinjam', $year)
                ->where('bookings.status', '!=', 'cancelled')
                ->groupBy('users.jenis_pengguna');

            // ← filter bulan jika ada
            if ($month) {
                $userTypeQuery->whereMonth('bookings.tanggal_pinjam', $month);
            }

            $userTypeCounts = $userTypeQuery->pluck('total', 'jenis_pengguna');

            $internal = 0;
            $umum     = 0;
            foreach ($userTypeCounts as $type => $count) {
                $type === 'umum' ? $umum += $count : $internal += $count;
            }

            $response['by_user_type'] = [
                'internal' => $internal,
                'umum'     => $umum,
            ];
        }

        return response()->json($response);
    }

    // =========================================================
    // API: Booking per Hari
    // =========================================================
    public function bookingPerDay(Request $request)
    {
        $days    = $request->input('days', 30);
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

        $dailyData = [];
        $labels    = [];
        $colors    = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date    = $endDate->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');

            $dailyData[] = $bookingsPerDay->get($dateStr, 0);
            $labels[]    = $date->format('d M');
            $colors[]    = in_array($date->dayOfWeek, [0, 6]) ? '#ef4444' : '#4361ee';
        }

        return response()->json([
            'success' => true,
            'data'    => $dailyData,
            'labels'  => $labels,
            'colors'  => $colors,
            'period'  => "$days hari terakhir",
        ]);
    }

    // =========================================================
    // API: Top Ruangan
    // =========================================================
    public function topRooms(Request $request)
    {
        $year  = $request->input('year', Carbon::now()->format('Y'));
        $month = $request->input('month');

        $query = Booking::select('room_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('room_id')          // abaikan booking barang saja
            ->where('status', '!=', 'cancelled')
            ->whereYear('tanggal_pinjam', $year)
            ->groupBy('room_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('room');

        if ($month) {
            $query->whereMonth('tanggal_pinjam', $month);
        }

        $topRooms = $query->get();

        $labels = [];
        $data   = [];
        $colors = [];

        foreach ($topRooms as $booking) {
            $room = $booking->room;
            if (!$room) continue;

            $labels[] = $room->kode_ruangan . ' - ' . $room->nama_ruangan;
            $data[]   = $booking->total;
            $colors[] = $room->harga_sewa_per_hari ? '#ec4899' : '#4361ee';
        }

        return response()->json([
            'success' => true,
            'labels'  => $labels,
            'data'    => $data,
            'colors'  => $colors,
            'period'  => $month
                ? Carbon::createFromDate($year, $month, 1)->isoFormat('MMMM YYYY')
                : $year,
        ]);
    }

    // =========================================================
    // API: Analisis Waktu
    // =========================================================
    public function timeAnalysis(Request $request)
    {
        $year  = $request->input('year', Carbon::now()->format('Y'));
        $month = $request->input('month');

        $query = Booking::where('status', '!=', 'cancelled')
            ->whereYear('tanggal_pinjam', $year);

        if ($month) {
            $query->whereMonth('tanggal_pinjam', $month);
        }

        // Jam paling sering
        $hourlyRaw = $query->clone()
            ->select(DB::raw('HOUR(waktu_mulai) as hour'), DB::raw('COUNT(*) as total'))
            ->groupBy('hour')
            ->pluck('total', 'hour');

        $hours = [];
        for ($h = 7; $h <= 20; $h++) {
            $hours[$h] = $hourlyRaw->get($h, 0);
        }

        // Hari paling sibuk
        $dailyRaw = $query->clone()
            ->select(DB::raw('DAYOFWEEK(tanggal_pinjam) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->pluck('total', 'day');

        $dayMap = [2=>'Senin',3=>'Selasa',4=>'Rabu',5=>'Kamis',6=>'Jumat',7=>'Sabtu',1=>'Minggu'];
        $weekdayData = [];
        foreach ($dayMap as $num => $name) {
            $weekdayData[$name] = $dailyRaw->get($num, 0);
        }

        // Durasi rata-rata
        $avgDuration = round(
            $query->clone()
                ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, waktu_mulai, waktu_selesai)) as avg'))
                ->first()
                ->avg ?? 0
        );

        return response()->json([
            'success'      => true,
            'hourly'       => $hours,
            'weekday'      => $weekdayData,
            'avg_duration' => $avgDuration,
            'period'       => $month
                ? Carbon::createFromDate($year, $month, 1)->isoFormat('MMMM YYYY')
                : $year,
        ]);
    }

    // =========================================================
    // API: Pendapatan per Bulan  ← BARU
    // =========================================================
    public function revenuePerMonth(Request $request)
    {
        $year  = $request->input('year', Carbon::now()->format('Y'));
        $month = $request->input('month'); // ← tambah ini

        $query = Booking::select(
                DB::raw('MONTH(tanggal_pinjam) as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->whereYear('tanggal_pinjam', $year)
            ->whereIn('status', ['approved', 'completed', 'payment_uploaded'])
            ->where('total_amount', '>', 0);

        // ← filter bulan jika ada
        if ($month) {
            $query->whereMonth('tanggal_pinjam', $month);
        }

        $revenuePerMonth = $query->groupBy('month')->pluck('revenue', 'month');

        // Jika filter bulan aktif, kembalikan data per hari bukan per bulan
        if ($month) {
            // Ambil data per hari dalam bulan tersebut
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
            $dailyRevenue = Booking::select(
                    DB::raw('DAY(tanggal_pinjam) as day'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->whereYear('tanggal_pinjam', $year)
                ->whereMonth('tanggal_pinjam', $month)
                ->whereIn('status', ['approved', 'completed', 'payment_uploaded'])
                ->where('total_amount', '>', 0)
                ->groupBy('day')
                ->pluck('revenue', 'day');

            $data   = [];
            $labels = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $data[]   = (float) ($dailyRevenue->get($d, 0));
                $labels[] = $d;
            }

            return response()->json([
                'success' => true,
                'labels'  => $labels,
                'data'    => $data,
                'total'   => array_sum($data),
                'year'    => $year,
                'month'   => $month,
            ]);
        }

        // Jika tidak ada filter bulan, kembalikan data per bulan seperti biasa
        $monthlyRevenue = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyRevenue[$i] = (float) ($revenuePerMonth->get($i, 0));
        }

        return response()->json([
            'success' => true,
            'labels'  => ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
            'data'    => array_values($monthlyRevenue),
            'total'   => array_sum($monthlyRevenue),
            'year'    => $year,
        ]);
    }

    // =========================================================
    // API: Top Barang Inventaris  ← BARU
    // =========================================================
    public function topInventory(Request $request)
    {
        $year  = $request->input('year', Carbon::now()->format('Y'));
        $month = $request->input('month');

        $query = DB::table('inventory_bookings')
            ->join('inventories', 'inventory_bookings.inventory_id', '=', 'inventories.id')
            ->join('bookings', 'inventory_bookings.booking_id', '=', 'bookings.id')
            ->select(
                'inventories.name',
                'inventories.category',
                DB::raw('SUM(inventory_bookings.quantity) as total_qty'),
                DB::raw('COUNT(DISTINCT inventory_bookings.booking_id) as total_booking')
            )
            ->whereYear('bookings.tanggal_pinjam', $year)
            ->where('bookings.status', '!=', 'cancelled')
            ->groupBy('inventories.id', 'inventories.name', 'inventories.category')
            ->orderByDesc('total_qty')
            ->limit(10);

        if ($month) {
            $query->whereMonth('bookings.tanggal_pinjam', $month);
        }

        $results = $query->get();

        return response()->json([
            'success' => true,
            'labels'  => $results->pluck('name')->toArray(),
            'data'    => $results->pluck('total_qty')->map(fn($v) => (int)$v)->toArray(),
            'bookings'=> $results->pluck('total_booking')->map(fn($v) => (int)$v)->toArray(),
            'period'  => $month
                ? Carbon::createFromDate($year, $month, 1)->isoFormat('MMMM YYYY')
                : $year,
        ]);
    }

    // =========================================================
    // Export PDF
    // =========================================================
    public function exportFullPdf(Request $request)
    {
        if(Auth::user()->role !== 'admin'){
            abort(403);
        }

        $year = $request->input('year', now()->format('Y'));
        $month = $request->input('month');

        // Monthly data
        $monthlyData = $this->getMonthlyData($year, $month);

        // Top rooms
        $topRoomsData = $this->getTopRoomsData($year, $month);

        // Time analysis
        $timeData = $this->getTimeAnalysisData($year, $month);

        // Status distribution
        $statusQuery = Booking::select('status', DB::raw('COUNT(*) as total'))
                        ->whereYear('tanggal_pinjam', $year)
                        ->groupBy('status');
        
        if ($month) {
            $statusQuery->whereMonth('tanggal_pinjam', $month);
        }

        $statusCounts = $statusQuery->pluck('total', 'status');

        $statusData = [
            'approved' => $statusCounts->get('approved',0),
            'pending' => $statusCounts->get('pending',0),
            'payment_uploaded' => $statusCounts->get('payment_uploaded',0),
            'rejected' => $statusCounts->get('rejected',0),
            'completed' => $statusCounts->get('completed',0),
            'cancelled' => $statusCounts->get('cancelled',0),
        ];

        // Internal VS Umum
        $userTypeQuery = Booking::select('users.jenis_pengguna', DB::raw('COUNT(*) as total'))
        ->join('users', 'bookings.user_id', '=', 'users.id')
        ->whereYear('bookings.tanggal_pinjam', $year)
        ->where('bookings.status', '!=', 'cancelled')
        ->groupBy('users.jenis_pengguna');

        if ($month){
            $userTypeQuery->whereMonth('bookings.tanggal_pinjam', $month);
        }

        $userTypeCounts = $userTypeQuery->pluck('total', 'jenis_pengguna');

        $internal = 0;
        $umum = 0;
        foreach ($userTypeCounts as $type => $count) {
            $type === 'umum' ? $umum += $count : $internal += $count;
        }
        $userTypeData = ['internal' => $internal, 'umum' => $umum];

        // Revenue
        $revenueQuery = Booking::select(
                DB::raw('MONTH(tanggal_pinjam) as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->whereYear('tanggal_pinjam', $year)
            ->whereIn('status', ['approved', 'completed', 'payment_uploaded'])
            ->where('total_amount', '>', 0)
            ->groupBy('month');

        if ($month) {
            $revenueQuery->whereMonth('tanggal_pinjam', $month);
        }

        $revenueRaw = $revenueQuery->pluck('revenue', 'month');

        if ($month) {
            // Filter bulan aktif → data per hari
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
            $dailyRevenue = Booking::select(
                    DB::raw('DAY(tanggal_pinjam) as day'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->whereYear('tanggal_pinjam', $year)
                ->whereMonth('tanggal_pinjam', $month)
                ->whereIn('status', ['approved', 'completed', 'payment_uploaded'])
                ->where('total_amount', '>', 0)
                ->groupBy('day')
                ->pluck('revenue', 'day');

            $revData = [];
            $revLabels = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $revData[] = (float) ($dailyRevenue->get($d, 0));
                $revLabels[] = $d . ' ' . Carbon::createFromDate($year, $month, $d)->isoFormat('MMM');
            }

            $revenueData = [
                'labels' => $revLabels,
                'data' => $revData,
                'total' => array_sum($revData),
                'year' => $year,
                'month' => $month,
            ];
        } else {
            // Tidak ada filter bulan → data per bulan
            $monthlyRevenue = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlyRevenue[$i] = (float) ($revenueRaw->get($i, 0));
            }
            $revenueData = [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                'data' => array_values($monthlyRevenue),
                'total' => array_sum($monthlyRevenue),
                'year' => $year,
            ];
        }

        // Top inventory
        $invQuery = DB::table('inventory_bookings')
            ->join('inventories', 'inventory_bookings.inventory_id', '=', 'inventories.id')
            ->join('bookings', 'inventory_bookings.booking_id', '=', 'bookings.id')
            ->select(
                'inventories.name',
                DB::raw('SUM(inventory_bookings.quantity) as total_qty'),
                DB::raw('COUNT(DISTINCT inventory_bookings.booking_id) as total_booking')
            )
            ->whereYear('bookings.tanggal_pinjam', $year)
            ->where('bookings.status', '!=', 'cancelled')
            ->groupBy('inventories.id', 'inventories.name')
            ->limit(10);
        
        if ($month) {
            $invQuery->whereMonth('bookings.tanggal_pinjam', $month);
        }

        $invResults = $invQuery->get();
        $inventoryData = [
            'labels' => $invResults->pluck('name')->toArray(),
            'data' => $invResults->pluck('total_qty')->map(fn($v) => (int)$v)->toArray(),
            'bookings' => $invResults->pluck('total_booking')->map(fn($v) => (int)$v)->toArray()
        ];

        // Periode label
        $period = $month
            ? Carbon::createFromDate($year, $month, 1)->isoFormat('MMMM YYYY')
            : 'Tahun ' . $year;

        // Generate PDF
        $pdf = Pdf::loadView('admin.statistics.pdf.full', [
            'monthly_data'   => $monthlyData,
            'top_rooms_data' => $topRoomsData,
            'time_data'      => $timeData,
            'status_data'    => $statusData,
            'user_type_data' => $userTypeData,
            'revenue_data'   => $revenueData,
            'inventory_data' => $inventoryData,
            'period'         => $period,
            'generated_at'   => Carbon::now()->isoFormat('D MMMM YYYY, HH:mm'),
        ])
        ->setPaper('a4', 'portrait')
        ->setOption('margin-top',    8)
        ->setOption('margin-bottom', 8)
        ->setOption('margin-left',   0)
        ->setOption('margin-right',  0)
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', false);


        $filename = 'statistik_peminjaman_' . $year . ($month ? '_'.$month : '') . '.pdf';
        return $pdf->download($filename);
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================
    private function getMonthlyData($year, $month = null): array
    {
        $query = Booking::select(
                DB::raw('MONTH(tanggal_pinjam) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('tanggal_pinjam', $year)
            ->where('status', '!=', 'cancelled');
        
        if ($month) {
            $query->whereMonth('tanggal_pinjam', $month);
        }

        $raw = $query->groupBy('month')->pluck('total', 'month');

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[$i] = $raw->get($i, 0);
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'data' => array_values($data),
            'year' => $year,
            'month' => $month,
        ];
    }

    private function getDailyData($days): array
    {
        $endDate   = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);

        $raw = Booking::select(
                DB::raw('DATE(tanggal_pinjam) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->groupBy('date')
            ->pluck('total', 'date');

        $data   = [];
        $labels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date    = $endDate->copy()->subDays($i);
            $data[]  = $raw->get($date->format('Y-m-d'), 0);
            $labels[]= $date->format('d M');
        }

        return ['labels' => $labels, 'data' => $data, 'period' => "$days hari terakhir"];
    }

    private function getTopRoomsData($year, $month = null): array
    {
        $query = Booking::select('room_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('room_id')
            ->where('status', '!=', 'cancelled')
            ->whereYear('tanggal_pinjam', $year)
            ->with('room')
            ->groupBy('room_id')
            ->orderByDesc('total')
            ->limit(10);

        if ($month) {
            $query->whereMonth('tanggal_pinjam', $month);
        }

        $labels = [];
        $data   = [];
        $isPaid = [];

        foreach ($query->get() as $b) {
            if (!$b->room) continue;
            $labels[] = $b->room->kode_ruangan . ' - ' . $b->room->nama_ruangan;
            $data[]   = $b->total;
            $isPaid[] = (bool) $b->room->harga_sewa_per_hari;
        }

        return ['labels' => $labels, 'data' => $data, 'is_paid' => $isPaid];
    }

    private function getTimeAnalysisData($year, $month = null): array
    {
        $query = Booking::where('status', '!=', 'cancelled')
            ->whereYear('tanggal_pinjam', $year);

        if ($month) {
            $query->whereMonth('tanggal_pinjam', $month);
        }

        $hourlyRaw = $query->clone()
            ->select(DB::raw('HOUR(waktu_mulai) as hour'), DB::raw('COUNT(*) as total'))
            ->groupBy('hour')
            ->pluck('total', 'hour');

        $hours = [];
        for ($h = 7; $h <= 20; $h++) {
            $hours[$h] = $hourlyRaw->get($h, 0);
        }

        $dailyRaw  = $query->clone()
            ->select(DB::raw('DAYOFWEEK(tanggal_pinjam) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->pluck('total', 'day');

        $dayMap = [2=>'Senin',3=>'Selasa',4=>'Rabu',5=>'Kamis',6=>'Jumat',7=>'Sabtu',1=>'Minggu'];
        $weekday = [];
        foreach ($dayMap as $num => $name) {
            $weekday[$name] = $dailyRaw->get($num, 0);
        }

        $avg = round(
            $query->clone()
                ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, waktu_mulai, waktu_selesai)) as avg'))
                ->first()
                ->avg ?? 0
        );

        return ['hourly' => $hours, 'weekday' => $weekday, 'avg_duration' => $avg];
    }
}