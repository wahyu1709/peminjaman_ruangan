<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

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
            'title' => 'Form pinjam Ruangan',
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
            'keperluan' => 'required|string'
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
            ->where('status', '!=', 'rejected')
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

        Booking::create([
            'user_id' => Auth::id(),
            'room_id' => $request->room_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'keperluan' => $request->keperluan,
        ]);

        return redirect()->route('booking')->with('success', 'Peminjaman ruangan berhasil diajukan');
    }

    public function approve($id)
    {
        $booking = Booking::findOrFail($id);

        // Hanya admin yang boleh approve
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $booking->update(['status' => 'approved']);

        return back()->with('success', 'Booking telah disetujui');
    }

    public function reject($id)
    {
        $booking = Booking::findOrFail($id);

        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $booking->update(['status' => 'rejected']);

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
                default: return '<span class="badge badge-secondary">Selesai</span>';
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
}
