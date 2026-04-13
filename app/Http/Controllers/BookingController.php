<?php

namespace App\Http\Controllers;

use App\Events\BookingCreated;
use App\Models\Booking;
use App\Models\Inventory;
use App\Models\Room;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BookingController extends Controller
{
    /**
     * Display booking list (Admin: all bookings, User: own bookings)
     */
    public function index()
    {
        $isAdmin = Auth::user()->role === 'admin';
        
        $data = [
            'title' => 'Data Peminjaman Ruangan',
            'menuAdminBooking' => $isAdmin ? 'active' : '',
            'menuUserBooking' => !$isAdmin ? 'active' : '',
        ];

        $bookings = $isAdmin 
            ? Booking::with(['user', 'room', 'inventories'])->latest()->get()
            : Booking::with(['room', 'inventories'])->where('user_id', Auth::id())->latest()->get();

        $view = $isAdmin ? 'admin.booking.index' : 'user.booking.index';
        
        return view($view, array_merge($data, ['bookings' => $bookings]));
    }

    /**
     * Show booking creation form
     */
    public function create()
    {
        return view('admin.booking.create', [
            'title' => 'Form Pinjam Ruangan & Barang',
            'menuBooking' => 'active',
            'rooms' => Room::where('is_active', true)->get(),
            'inventories' => Inventory::available()->orderBy('category')->get(),
        ]);
    }

    /**
     * Store new booking with pricing logic based on SK No. 411
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id'               => 'nullable|exists:rooms,id',
            'tanggal_pinjam'        => 'required|date|after_or_equal:today',
            'waktu_mulai'           => 'required|date_format:H:i',
            'waktu_selesai'         => 'required|date_format:H:i|after:waktu_mulai',
            'keperluan'             => 'required|string|max:500',
            'role_unit'             => 'nullable|string|max:255',
            'selected_inventories'  => 'nullable|string',
            'is_urgent'             => 'nullable|boolean', // ← tambah
        ], [
            'room_id.required'          => 'Ruangan wajib dipilih',
            'tanggal_pinjam.after_or_equal' => 'Tanggal tidak boleh kemarin',
            'waktu_selesai.after'       => 'Waktu selesai harus setelah waktu mulai',
            'keperluan.required'        => 'Keperluan wajib diisi',
        ]);

        $user    = Auth::user();
        $isAdmin = $user->role === 'admin';
        $isUrgent = $request->boolean('is_urgent', false);

        $room = $request->filled('room_id') && $request->room_id
            ? Room::findOrFail($request->room_id)
            : null;

        $tanggal      = Carbon::parse($request->tanggal_pinjam);
        $waktuMulai   = Carbon::parse($request->tanggal_pinjam . ' ' . $request->waktu_mulai);
        $waktuSelesai = Carbon::parse($request->tanggal_pinjam . ' ' . $request->waktu_selesai);

        $selectedInventories = json_decode($request->selected_inventories ?? '[]', true) ?? [];

        // === VALIDASI KHUSUS NON-ADMIN =========================================
        if (!$isAdmin) {

            // 1. Jam operasional 07:00 - 22:00
            $jamBuka   = Carbon::parse($request->tanggal_pinjam . ' 07:00');
            $jamTutup  = Carbon::parse($request->tanggal_pinjam . ' 22:00');

            if ($waktuMulai->lt($jamBuka) || $waktuSelesai->gt($jamTutup)) {
                return back()->withErrors([
                    'waktu_mulai' => 'Jam peminjaman hanya diperbolehkan antara 07:00 - 22:00.'
                ])->withInput();
            }

            // 2. Minimum durasi 30 menit
            $durasiMenit = $waktuMulai->diffInMinutes($waktuSelesai);
            if ($durasiMenit < 30) {
                return back()->withErrors([
                    'waktu_selesai' => 'Durasi peminjaman minimal 30 menit.'
                ])->withInput();
            }

            // 3. Maksimal booking H+1 (hanya hari ini dan besok)
            $maksimalTanggal = Carbon::today()->addDay();
            if ($tanggal->gt($maksimalTanggal)) {
                return back()->withErrors([
                    'tanggal_pinjam' => 'Peminjaman hanya bisa dilakukan untuk hari ini atau besok.'
                ])->withInput();
            }

            // 4. H-1 jam sebelum waktu mulai (kecuali booking urgent)
            if (!$isUrgent) {
                $batasPesan = $waktuMulai->copy()->subHour();
                if (now()->gt($batasPesan)) {
                    return back()->withErrors([
                        'waktu_mulai' => 'Peminjaman harus dilakukan minimal 1 jam sebelum waktu mulai. ' .
                                        'Jika mendesak, gunakan fitur "Booking Urgent".'
                    ])->withInput();
                }
            }

            // 5. Maksimal 8 jam per hari
            $durasiJam = $waktuMulai->diffInHours($waktuSelesai, true);
            if ($durasiJam > 8) {
                return back()->withErrors([
                    'waktu_selesai' => 'Durasi peminjaman maksimal 8 jam per hari. ' .
                                    'Jika lebih dari 8 jam, hubungi admin.'
                ])->withInput();
            }
        }
        // ========================================================================

        // === VALIDASI UMUM (semua user) =========================================

        if (!$room && empty($selectedInventories)) {
            return back()->withErrors([
                'room_id' => 'Anda harus memilih minimal 1 ruangan atau 1 barang.'
            ])->withInput();
        }

        if ($room && $this->hasScheduleConflict($request)) {
            return back()->withErrors([
                'waktu_mulai' => 'Jadwal bentrok! Ruangan sudah dibooking pada waktu tersebut.'
            ])->withInput();
        }

        $inventoryErrors = $this->validateInventoryStock($selectedInventories);
        if (!empty($inventoryErrors)) {
            return back()->withErrors($inventoryErrors)->withInput();
        }

        if ($room && !$room->is_active) {
            return back()->withErrors(['room_id' => 'Ruangan tidak tersedia'])->withInput();
        }

        if ($room && $user->jenis_pengguna === 'umum' && $room->harga_sewa_per_hari == 0) {
            return back()->withErrors([
                'room_id' => 'Ruangan gratis hanya untuk Civitas Akademika FIK UI.'
            ])->withInput();
        }

        // === HITUNG TOTAL HARGA ================================================
        $totalAmount = $this->calculateTotalAmount($room, $tanggal, $user, $selectedInventories);

        // === SIMPAN BOOKING =====================================================
        $booking = Booking::create([
            'user_id'           => $user->id,
            'room_id'           => $request->room_id ?? null,
            'is_inventory_only' => !$request->room_id,
            'tanggal_pinjam'    => $request->tanggal_pinjam,
            'waktu_mulai'       => $request->waktu_mulai,
            'waktu_selesai'     => $request->waktu_selesai,
            'keperluan'         => $request->keperluan,
            'role_unit'         => $request->role_unit,
            'total_amount'      => $totalAmount,
            'status'            => 'pending',
            'is_urgent'         => $isUrgent, // ← tambah kolom ini
        ]);

        // === SIMPAN DATA BARANG =================================================
        if (!empty($selectedInventories)) {
            foreach ($selectedInventories as $item) {
                $inventory = Inventory::findOrFail($item['id']);
                $booking->inventories()->attach($inventory->id, [
                    'quantity'         => $item['quantity'],
                    'price_at_booking' => $item['price'],
                ]);
                $inventory->decrement('stock_available', $item['quantity']);
            }
        }

        event(new BookingCreated($booking));

        // === GENERATE INVOICE ===================================================
        if ($totalAmount > 0) {
            $this->generateInvoice($booking);
            return redirect()->route('dashboard')
                ->with('success', 'Peminjaman berhasil diajukan!')
                ->with('invoice_url', Storage::url($booking->invoice_path));
        }

        return redirect()->route('dashboard')
            ->with('success', 'Peminjaman berhasil diajukan. Tunggu persetujuan admin.');
    }

    /**
     * Approve booking
     */
    public function approve(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        
        $booking = Booking::with('room')->findOrFail($id);

        // Validate payment proof for paid bookings
        if ($booking->total_amount > 0 && !$booking->bukti_pembayaran) {
            return back()->withErrors(['status' => 'Booking berbayar harus memiliki bukti pembayaran!']);
        }

        $booking->update([
            'status' => 'approved',
            'admin_comment' => $request->admin_comment,
        ]);

        return back()->with('success', 'Booking telah disetujui');
    }

    /**
     * Reject booking
     */
    public function reject(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'rejected_reason' => 'required|string|max:255'
        ], [
            'rejected_reason.required' => 'Alasan penolakan wajib diisi'
        ]);

        $booking = Booking::findOrFail($id);

        if (!in_array($booking->status, ['pending', 'payment_uploaded'])) {
            return back()->withErrors([
                'status' => 'Booking ini tidak bisa ditolak (status: ' . $booking->status . ')'
            ]);
        }

        // Kembalikan stok barang
        foreach ($booking->inventories as $inventory) {
            $inventory->increment('stock_available', $inventory->pivot->quantity);
        }

        $booking->update([
            'status' => 'rejected',
            'rejected_reason' => $request->rejected_reason,
        ]);

        return back()->with('success', 'Booking telah ditolak');
    }

    /**
     * Cancel booking
     */
    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);

        $allowed = auth()->user()->role === 'admin' 
            || ($booking->user_id === auth()->id() && in_array($booking->status, ['pending', 'approved']));

        if (!$allowed) {
            abort(403, 'Tidak diizinkan membatalkan peminjaman ini.');
        }

        // Kembalikan stok barang
        foreach ($booking->inventories as $inventory) {
            $inventory->increment('stock_available', $inventory->pivot->quantity);
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Peminjaman berhasil dibatalkan.');
    }

    /**
     * Show extend booking form
     */
    public function extendForm($id)
    {
        $original = Booking::where('id', $id)
            ->where('status', 'completed')
            ->firstOrFail();

        // Check permission
        $allowed = $this->canExtendBooking($original);

        if (!$allowed) {
            return back()->withErrors(['error' => 'Anda tidak diizinkan mengajukan perpanjangan.']);
        }

        $data = [
            'room_id' => $original->room_id,
            'tanggal_pinjam' => $original->tanggal_pinjam,
            'waktu_mulai' => $original->waktu_mulai,
            'keperluan' => $original->keperluan,
            'role_unit' => $original->role_unit,
        ];

        return view('admin.booking.create', [
            'rooms' => Room::where('is_active', true)->orderBy('kode_ruangan')->get(),
            'inventories' => Inventory::available()->orderBy('category')->get(),
            'data' => $data,
            'title' => 'Ajukan Perpanjangan Peminjaman',
        ]);
    }

    /**
     * Show upload proof form
     */
    public function showUploadProof($id)
    {
        $booking = Booking::with('room', 'user')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        if ($booking->total_amount <= 0) {
            abort(404);
        }

        return view('booking.upload-proof', [
            'booking' => $booking,
            'title' => 'Upload Bukti Pembayaran',
        ]);
    }

    /**
     * Upload payment proof
     */
    public function uploadProof(Request $request, $id)
    {
        $booking = Booking::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ], [
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diupload',
            'bukti_pembayaran.mimes' => 'Format: jpeg, png, jpg, pdf',
            'bukti_pembayaran.max' => 'Ukuran maksimal 5MB',
        ]);

        // Delete old proof
        if ($booking->bukti_pembayaran) {
            Storage::disk('public')->delete($booking->bukti_pembayaran);
        }

        // Store new proof
        $path = $request->file('bukti_pembayaran')->store('payment_proofs', 'public');

        $booking->update([
            'bukti_pembayaran' => $path,
            'payment_uploaded_at' => now(),
            'status' => 'payment_uploaded',
        ]);

        return redirect()->route('booking')
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
    }

    /**
     * Public booking list
     */
    public function publicList()
    {
        $approvedBookings = Booking::with(['user', 'room'])
            ->where('status', 'approved')
            ->whereDate('tanggal_pinjam', '>=', now())
            ->orderBy('tanggal_pinjam')
            ->orderBy('waktu_mulai')
            ->limit(30)
            ->get();

        return view('booking.public-list', compact('approvedBookings'));
    }

    /**
     * Booking history page
     */
    public function history()
    {
        return view('admin.booking.history.index', [
            'title' => 'Riwayat Peminjaman Ruangan',
            'menuAdminHistory' => 'active',
        ]);
    }

    /**
     * Booking history data (DataTables)
     */
    public function historyData(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = Booking::with(['user', 'room', 'inventories']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }

        $query->orderBy('tanggal_pinjam', 'desc')->orderBy('waktu_mulai', 'desc');

        return DataTables::of($query)
            ->addColumn('ruangan', function ($booking) {           // ✅ tambah ini
                if ($booking->room) {
                    return $booking->room->kode_ruangan;
                }
                return '<span class="text-muted"><i class="fas fa-box me-1"></i> Barang Saja</span>';
            })
            ->addColumn('time_range', fn($booking) =>
                Carbon::parse($booking->waktu_mulai)->format('H:i') . ' - ' .
                Carbon::parse($booking->waktu_selesai)->format('H:i')
            )
            ->addColumn('status_badge', fn($booking) => $this->getStatusBadge($booking->status))
            ->addColumn('action', fn($booking) => '<span class="text-muted">—</span>')
            ->addIndexColumn()
            ->rawColumns(['ruangan', 'status_badge', 'action'])    // ✅ tambah 'ruangan'
            ->make(true);
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Check if schedule has conflict
     */
    private function hasScheduleConflict(Request $request): bool
    {
        // Hanya cek konflik jika ada ruangan yang dipilih
        if (!$request->filled('room_id') || !$request->room_id) {
            return false;
        }

        return Booking::where('room_id', $request->room_id)
            ->where('tanggal_pinjam', $request->tanggal_pinjam)
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->where(function ($query) use ($request) {
                $query->where('waktu_mulai', '<=', $request->waktu_selesai)
                    ->where('waktu_selesai', '>=', $request->waktu_mulai);
            })
            ->exists();
    }

    /**
     * Calculate total amount based on SK No. 411
     * 
     * @param Room|null $room
     * @param Carbon $tanggal
     * @param mixed $user
     * @param array $selectedInventories
     * @return float
     */
    private function calculateTotalAmount(?Room $room, Carbon $tanggal, $user, array $selectedInventories): float
    {
        // Step 1: Harga ruangan (dengan diskon 25% untuk internal)
        $hargaRuangan = $room ? ($room->harga_sewa_per_hari ?? 0) : 0;
        $diskon = ($user->jenis_pengguna !== 'umum') ? 0.25 : 0;
        $hargaSetelahDiskon = $hargaRuangan * (1 - $diskon);

        // Step 2: Harga barang
        $hargaBarang = 0;
        foreach ($selectedInventories as $item) {
            $hargaBarang += $item['price'] * $item['quantity'];
        }

        // Step 3: Biaya tambahan (Sabtu/Minggu)
        $biayaTambahan = 0;
        if ($tanggal->isSaturday()) {
            $biayaTambahan = 400000; // Kebersihan 100k + Teknisi 300k
        } elseif ($tanggal->isSunday()) {
            $biayaTambahan = 500000; // Kebersihan 200k + Teknisi 300k
        }

        // Step 4: Total akhir
        return $hargaSetelahDiskon + $hargaBarang + $biayaTambahan;
    }

    /**
     * Generate invoice PDF (dengan atau tanpa barang)
     */
    private function generateInvoice(Booking $booking): void
    {
        $invoiceNumber = 'INV/' . now()->format('Y') . '/' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
        
        $booking->update(['invoice_number' => $invoiceNumber]);
        $booking->load('room', 'inventories', 'user');

        // SATU VIEW UNTUK SEMUA KASUS
        $pdf = Pdf::loadView('booking.pdf.invoice', ['booking' => $booking])
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 5)
            ->setOption('margin-bottom', 5)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5);

        $invoicePath = 'invoices/invoice_' . $booking->id . '_' . time() . '.pdf';
        Storage::disk('public')->put($invoicePath, $pdf->output());
        $booking->update(['invoice_path' => $invoicePath]);
    }

    /**
     * Check if user can extend booking
     */
    private function canExtendBooking(Booking $booking): bool
    {
        if (auth()->user()->role === 'admin') {
            return true;
        }

        $endTime = Carbon::parse($booking->tanggal_pinjam . ' ' . $booking->waktu_selesai);
        $deadline = $endTime->addHour();

        return $booking->user_id === auth()->id() && now()->lte($deadline);
    }

    /**
     * Get status badge HTML
     */
    private function getStatusBadge(string $status): string
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'payment_uploaded' => '<span class="badge badge-info">Menunggu Verifikasi</span>',
            'approved' => '<span class="badge badge-success">Disetujui</span>',
            'rejected' => '<span class="badge badge-danger">Ditolak</span>',
            'completed' => '<span class="badge badge-info">Selesai</span>',
            'cancelled' => '<span class="badge badge-secondary">Dibatalkan</span>',
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary">Unknown</span>';
    }

    /**
     * Validate inventory stock availability
     */
    private function validateInventoryStock(array $selectedInventories): array
    {
        $errors = [];
        
        foreach ($selectedInventories as $item) {
            $inventory = Inventory::find($item['id'] ?? null);
            
            if (!$inventory || !$inventory->is_active) {
                $errors['inventory_' . ($item['id'] ?? 'unknown')] = 'Barang "' . ($item['name'] ?? 'Tidak diketahui') . '" tidak tersedia.';
                continue;
            }
            
            if ($inventory->stock_available < ($item['quantity'] ?? 1)) {
                $errors['inventory_' . $item['id']] = 
                    'Stok "' . $inventory->name . '" tidak mencukupi. Tersedia: ' . $inventory->stock_available;
            }
        }
        
        return $errors;
    }
}