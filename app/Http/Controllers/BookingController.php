<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function index(){
        if (Auth::user()->role == 'admin') {
            $data = array(
                'title' => 'Data Peminjaman Ruangan',
                'menuAdminBooking' => 'active'
            );
            $bookings = Booking::with(['user', 'room'])->latest()->get();
            return view('admin.booking.index', array_merge($data, ['bookings' => $bookings]));
        } else {
            $data = array(
                'title' => 'Data Peminjaman Ruangan',
                'menuUserBooking' => 'active'
            );
            $bookings = Booking::with('room')
                               ->where('user_id', Auth::id())
                               ->latest()->get();
            return view('user.booking.index', array_merge($data, ['bookings' => $bookings]));
        }
    }

    public function create(){
        $data = array(
            'title' => 'Form Pinjam Ruangan',
            'menuBooking' => 'active',
            'rooms' => Room::where('is_active', 1)->get(),
        );
        
        return view('admin.booking.create', $data);
    }

    public function store(Request $request){
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'keperluan' => 'required|string',
            'role_unit' => 'nullable|string|max:255',
        ],[
            'room_id.required'       => 'Ruangan wajib dipilih',
            'tanggal_pinjam.required'=> 'Tanggal pinjam wajib diisi',
            'tanggal_pinjam.after_or_equal' => 'Tanggal tidak boleh kemarin',
            'waktu_selesai.after'    => 'Waktu selesai harus setelah waktu mulai',
            'keperluan.required'     => 'Keperluan wajib diisi',
        ]);

        // Cek apakah ruangan aktif
        $room = Room::find($request->room_id);
        if (!$room->is_active) {
            return back()->withErrors(['room_id' => 'Ruangan ini tidak tersedia untuk booking'])->withInput();
        }

        // Cek konflik
        $conflict = Booking::where('room_id', $request->room_id)
            ->where('tanggal_pinjam', $request->tanggal_pinjam)
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->where(function ($query) use ($request) {
                $query->where('waktu_mulai', '<=', $request->waktu_selesai)
                    ->where('waktu_selesai', '>=', $request->waktu_mulai);
            })
            ->exists();

        if ($conflict) {
            return back()
                ->withErrors(['waktu_mulai' => 'Jadwal bentrok! Ruangan sudah dibooking pada waktu tersebut. Silakan pilih jam lain.'])
                ->withInput();
        }

        $totalAmount = $room->harga_sewa_per_hari ?? 0;

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'room_id' => $request->room_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'keperluan' => $request->keperluan,
            'role_unit' => $request->role_unit,
            'total_amount' => $totalAmount, 
        ]);

        // Generate invoice jika berbayar
        if ($totalAmount > 0) {
            $invoiceNumber = 'INV/' . now()->format('Y') . '/' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
            
            // SIMPAN DULU KE DATABASE
            $booking->update([
                'invoice_number' => $invoiceNumber,
            ]);
            
            // LOAD ULANG DATA AGAR INVOICE_NUMBER TERISI
            $booking->load('room'); // atau refresh()
            
            // BARU GENERATE PDF
            $pdf = Pdf::loadView('booking.pdf.invoice', ['booking' => $booking]);
            $invoicePath = 'invoices/invoice_' . $booking->id . '_' . time() . '.pdf';
            Storage::disk('public')->put($invoicePath, $pdf->output());
            
            // UPDATE PATH
            $booking->update(['invoice_path' => $invoicePath]);
        }

        // Redirect
        if ($totalAmount > 0) {
            return redirect()->route('dashboard')
                ->with('success', 'Peminjaman berhasil diajukan!')
                ->with('invoice_url', Storage::url($booking->invoice_path));
        } else {
            return redirect()->route('dashboard')
                ->with('success', 'Peminjaman berhasil diajukan. Tunggu persetujuan admin.');
        }
    }

    public function approve(Request $request,$id)
    {
        $booking = Booking::with('room')->findOrFail($id);
    
        // Hanya admin yang boleh approve
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Validasi untuk booking berbayar
        if ($booking->total_amount > 0 && !$booking->bukti_pembayaran) {
            return back()->withErrors([
                'status' => 'Booking berbayar harus memiliki bukti pembayaran sebelum disetujui!'
            ]);
        }

        $booking->update([
            'status' => 'approved',
            'admin_comment' => $request->admin_comment ?? null,
        ]);
        
        return back()->with('success', 'Booking telah disetujui');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejected_reason' => 'required|string|max:255'
        ],[
            'rejected_reason.required' => 'Alasan penolakan wajib diisi'
        ]);

        $booking = Booking::findOrFail($id);

        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        if ($booking->status !== 'pending'){
            return back()->withErrors(['status' => 'Booking tidak dapat ditolak karena statusnya bukan pending']);
        }

        $booking->update([
            'status' => 'rejected',
            'rejected_reason' => $request->rejected_reason,
            ]);

        return back()->with('success', 'Booking telah ditolak');
    }

    public function publicList()
    {
        $approvedBookings = \App\Models\Booking::with(['user', 'room'])
            ->where('status', 'approved')
            ->whereDate('tanggal_pinjam', '>=', now()->toDateString())
            ->orderBy('tanggal_pinjam', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->limit(30)
            ->get();

        return view('booking.public-list', compact('approvedBookings'));
    }

    public function history(){
        $data = [
            'title' => 'Riwayat Peminjaman Ruangan',
            'menuAdminHistory' => 'active'
        ];

        return view('admin.booking.history.index', $data);
    }

    public function historyData(Request $request)
    {
        // Hanya admin yang boleh akses
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = Booking::with(['user', 'room']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal (single date)
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }

        $query->orderBy('tanggal_pinjam', 'desc')
                ->orderBy('waktu_mulai', 'desc'); 

        return DataTables::of($query)
        ->addColumn('time_range', function ($booking) {
            return \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') . ' - ' . 
                \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i');
        })
        ->addColumn('status_badge', function ($booking) {
            switch ($booking->status) {
                case 'pending': return '<span class="badge badge-warning">Pending</span>';
                case 'approved': return '<span class="badge badge-success">Disetujui</span>';
                case 'rejected': return '<span class="badge badge-danger">Ditolak</span>';
                case 'completed': return '<span class="badge badge-info">Selesai</span>';
                default: return '<span class="badge badge-secondary">Dibatalkan</span>';
            }
        })
        ->addColumn('action', function ($booking) {
            // Tidak ada aksi approve/reject di riwayat (karena hanya history)
            return '<span class="text-muted">—</span>';
        })
        ->addIndexColumn()
        ->rawColumns(['status_badge', 'action'])
        ->make(true);
    }

    public function cancel($id){
        $booking = Booking::findOrFail($id);

        // cek akses
        if(auth()->user()->role === 'admin'){
            // admin boleh batalkan semua booking
            $allowed = true;
        } else{
            // user hanya boleh batalkan booking miliknya
            $allowed = ($booking->user_id === auth()->id() && in_array($booking->status, ['pending', 'approved']));
        }

        if (!$allowed){
            abort(403, 'Tidak diizinkan membatalkan peminjaman ini.');
        }

        $booking->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Peminjaman berhasil dibatalkan.');
    }

    public function extendForm($id){
        $original = Booking::where('id', $id)
                    ->where('status', 'completed')
                    ->firstOrFail();
        
        // cek akses
        if(auth()->user()->role === 'admin'){
            // admin boleh perpanjang kapan saja
            $allowed = true;
        } else{
            // user hanya boleh perpanjang booking miliknya & dalam 1 jam setelah selesai
            $endtime = \Carbon\Carbon::parse($original->tanggal_pinjam . ' ' . $original->waktu_selesai);
            $deadline = $endtime->copy()->addHour();
            $allowed = (
                $original->user_id === auth()->id() &&
                now()->lte($deadline)
            );
        }

        if(!$allowed){
            return redirect()->back()->withErrors([
                'error' => 'Anda tidak diizinkan untuk mengajukan perpanjangan untuk peminjaman ini.'
            ]);
        }
        
        $data = [
            'room_id' => $original->room_id,
            'tanggal_pinjam' => $original->tanggal_pinjam,
            'waktu_mulai' => $original->waktu_mulai,
            'keperluan' => $original->keperluan,
            'role_unit' => $original->role_unit,
        ];

        $rooms = Room::where('is_active', true)->orderBy('kode_ruangan')->get();

        $title = 'Ajukan Perpanjangan Peminjaman';

        return view('admin.booking.create', compact('rooms', 'data', 'title'));
    }

    public function showUploadProof($id)
    {
        $booking = Booking::with('room', 'user')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        // Pastikan ini booking berbayar
        if (!$booking->total_amount || $booking->total_amount <= 0) {
            abort(404);
        }

        $title = 'Upload Bukti Pembayaran';

        return view('booking.upload-proof', compact(['booking', 'title']));
    }

    public function uploadProof(Request $request, $id)
    {
        $booking = Booking::where('user_id', auth()->id())->findOrFail($id);

        // Validasi
        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ], [
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diupload',
            'bukti_pembayaran.file' => 'File harus berupa gambar atau PDF',
            'bukti_pembayaran.mimes' => 'Format file harus: jpeg, png, jpg, pdf',
            'bukti_pembayaran.max' => 'Ukuran file maksimal 5MB'
        ]);

        // Hapus bukti lama jika ada
        if ($booking->bukti_pembayaran) {
            Storage::disk('public')->delete($booking->bukti_pembayaran);
        }

        // Simpan bukti baru
        $path = $request->file('bukti_pembayaran')->store('payment_proofs', 'public');

        $booking->update([
            'bukti_pembayaran' => $path,
            'payment_uploaded_at' => now(),
            'status' => 'payment_uploaded' // ubah status
        ]);

        return redirect()->route('booking.upload.proof.show', $booking->id)
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi dari admin.');
    }
}