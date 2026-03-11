<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookingController extends Controller
{
    use AuthorizesRequests;

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
            ? Booking::with(['user', 'room'])->latest()->get()
            : Booking::with('room')->where('user_id', Auth::id())->latest()->get();

        $view = $isAdmin ? 'admin.booking.index' : 'user.booking.index';
        
        return view($view, array_merge($data, ['bookings' => $bookings]));
    }

    /**
     * Show booking creation form
     */
    public function create()
    {
        return view('admin.booking.create', [
            'title' => 'Form Pinjam Ruangan',
            'menuBooking' => 'active',
            'rooms' => Room::where('is_active', true)->get(),
        ]);
    }

    /**
     * Store new booking with pricing logic based on SK No. 411
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'keperluan' => 'required|string|max:500',
            'role_unit' => 'nullable|string|max:255',
        ], [
            'room_id.required' => 'Ruangan wajib dipilih',
            'tanggal_pinjam.after_or_equal' => 'Tanggal tidak boleh kemarin',
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai',
            'keperluan.required' => 'Keperluan wajib diisi',
        ]);

        $user = Auth::user();
        $room = Room::findOrFail($request->room_id);
        $tanggal = Carbon::parse($request->tanggal_pinjam);

        // Check room availability
        if (!$room->is_active) {
            return back()->withErrors(['room_id' => 'Ruangan tidak tersedia'])->withInput();
        }

        // Check schedule conflict
        if ($this->hasScheduleConflict($request)) {
            return back()
                ->withErrors(['waktu_mulai' => 'Jadwal bentrok! Pilih waktu lain.'])
                ->withInput();
        }

        // Calculate total amount
        $totalAmount = $this->calculateTotalAmount($room, $tanggal, $user);

        // Validate: External users cannot book free rooms
        if ($user->jenis_pengguna === 'umum' && $room->harga_sewa_per_hari == 0) {
            return back()->withErrors([
                'room_id' => 'Ruangan gratis hanya untuk Civitas Akademika FIK UI. Pilih ruangan berbayar.'
            ])->withInput();
        }

        // Create booking
        $booking = Booking::create([
            'user_id' => $user->id,
            'room_id' => $request->room_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'keperluan' => $request->keperluan,
            'role_unit' => $request->role_unit,
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ]);

        // Generate invoice if paid
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

        if ($booking->status !== 'pending') {
            return back()->withErrors(['status' => 'Hanya booking pending yang bisa ditolak']);
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

        $query = Booking::with(['user', 'room']);

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
            ->addColumn('time_range', fn($booking) => 
                Carbon::parse($booking->waktu_mulai)->format('H:i') . ' - ' . 
                Carbon::parse($booking->waktu_selesai)->format('H:i')
            )
            ->addColumn('status_badge', fn($booking) => $this->getStatusBadge($booking->status))
            ->addColumn('action', fn($booking) => '<span class="text-muted">—</span>')
            ->addIndexColumn()
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Check if schedule has conflict
     */
    private function hasScheduleConflict(Request $request): bool
    {
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
     */
    private function calculateTotalAmount(Room $room, Carbon $tanggal, $user): float
    {
        // Step 1: Base price
        $hargaDasar = $room->harga_sewa_per_hari ?? 0;

        // Step 2: 25% discount for FIK UI (non-external)
        $diskon = ($user->jenis_pengguna !== 'umum') ? 0.25 : 0;
        $hargaSetelahDiskon = $hargaDasar * (1 - $diskon);

        // Step 3: Additional fees (Saturday/Sunday)
        $biayaTambahan = 0;
        
        if ($tanggal->isSaturday()) {
            $biayaTambahan = 100000 + 300000; // Cleaning + Technician
        } elseif ($tanggal->isSunday()) {
            $biayaTambahan = 200000 + 300000; // Cleaning + Technician
        }

        // Step 4: Total
        return $hargaSetelahDiskon + $biayaTambahan;
    }

    /**
     * Generate invoice PDF
     */
    private function generateInvoice(Booking $booking): void
    {
        $invoiceNumber = 'INV/' . now()->format('Y') . '/' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
        
        $booking->update(['invoice_number' => $invoiceNumber]);
        $booking->load('room');

        $pdf = Pdf::loadView('booking.pdf.invoice', ['booking' => $booking]);
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
}