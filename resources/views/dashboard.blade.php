@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>

    <!-- Alert Invoice (dipindahkan ke sini) -->
    @if(session('invoice_url'))
        <div class="alert alert-success mb-4">
            <i class="fas fa-file-invoice mr-2"></i>
            <strong>Invoice Anda sudah siap!</strong><br>
            <a href="{{ session('invoice_url') }}" target="_blank" class="btn btn-primary btn-sm mt-2">
                <i class="fas fa-download mr-1"></i> Download Invoice
            </a>
        </div>
    @endif

    <!-- Card Upload Bukti (untuk user) -->
    @if(auth()->user()->role != 'admin' && $pendingPaidBookings > 0)
        <div class="alert alert-warning mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Anda memiliki <strong>{{ $pendingPaidBookings }}</strong> peminjaman ruangan berbayar yang belum upload bukti pembayaran!
            
            @if($pendingPaidBookings == 1)
                @php
                    $firstBooking = Auth::user()->bookings()
                        ->where('total_amount', '>', 0)
                        ->where('status', 'pending')
                        ->whereNull('bukti_pembayaran')
                        ->first();
                @endphp
                <a href="{{ route('booking.upload.proof.show', $firstBooking->id) }}" 
                class="btn btn-warning btn-sm ms-2">
                    <i class="fas fa-upload me-1"></i> Upload Bukti
                </a>
            @else
                <a href="{{ route('booking') }}" class="btn btn-warning btn-sm ms-2">
                    <i class="fas fa-list me-1"></i> Lihat Semua
                </a>
            @endif
        </div>
    @endif

    <div class="row">
        {{-- Admin Dashboard card --}}
        @if (auth()->user()->role == 'admin')
            <!-- Card Peringatan Booking Pending Lama -->
            @if($pendingOver1Hour > 0)
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Butuh Persetujuan Segera
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $pendingOver1Hour }} Booking
                                </div>
                                <small class="text-muted">Pending lebih dari 1 jam</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Total Ruangan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRuangan }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-door-open fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Peminjaman Hari ini</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bookingsToday }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Menunggu Persetujuan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bookingsPending }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Peminjaman yang ditolak</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bookingsRejected }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- User dashboard card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Total Peminjaman saya</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBookingSaya }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Menunggu Persetujuan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingSaya }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Peminjaman aktif</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeSaya }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Tabel Peminjaman Hari Ini -->
    <div class="card shadow mb-4 mt-4">
        <div class="card-header py-3 bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-calendar-day mr-2"></i>
                Peminjaman Ruangan Hari Ini 
                <span class="badge badge-light">{{ $today->isoFormat('D MMMM YYYY') }}</span>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered w-100" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            @if(auth()->user()->role == 'admin')
                                <th>Pengaju</th>
                            @endif
                            <th>Ruangan</th>
                            <th>Jam</th>
                            <th>Keperluan</th>
                            <th>Peran / Unit Kerja</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $bookingsToday = auth()->user()->role == 'admin'
                                ? $bookingsTodayList 
                                : $bookingsTodayListSaya ?? collect();
                        @endphp

                        @forelse($bookingsToday as $index => $booking)
                            @php
                                $start = \Carbon\Carbon::parse($booking->tanggal_pinjam . ' ' . $booking->waktu_mulai);
                                $end   = \Carbon\Carbon::parse($booking->tanggal_pinjam . ' ' . $booking->waktu_selesai);
                                $isOngoing = now()->between($start, $end);
                                $highlightClass = ($isOngoing && $booking->status === 'approved') ? 'table-warning' : '';
                                
                                // Status badge
                                switch ($booking->status) {
                                    case 'pending': 
                                        $statusBadge = '<span class="badge badge-warning">Pending</span>'; break;
                                    case 'payment_uploaded': 
                                        $statusBadge = '<span class="badge badge-info">Menunggu Verifikasi</span>'; break;
                                    case 'approved': 
                                        $statusBadge = '<span class="badge badge-success">Disetujui</span>'; break;
                                    case 'rejected': 
                                        $statusBadge = '<span class="badge badge-danger">Ditolak</span>'; break;
                                    case 'completed': 
                                        $statusBadge = '<span class="badge badge-secondary">Selesai</span>'; break;
                                    default: 
                                        $statusBadge = '<span class="badge badge-light">Dibatalkan</span>';
                                }
                            @endphp

                            <tr class="{{ $highlightClass }}">
                                <td class="text-center">{{ $index + 1 }}</td>
                                @if(auth()->user()->role == 'admin')
                                    <td>{{ $booking->user->name }}</td>
                                @endif
                                <td>
                                    <strong>{{ $booking->room->kode_ruangan }}</strong><br>
                                    <small class="text-muted">{{ $booking->room->nama_ruangan }}</small>
                                </td>
                                <td class="text-center">
                                    <strong>
                                        {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                                    </strong>
                                </td>
                                <td>{{ Str::limit($booking->keperluan, 40) }}</td>
                                <td>{{ $booking->role_unit ? $booking->role_unit : '-' }}</td>
                                <td class="text-center">{!! $statusBadge !!}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role == 'admin' ? '7' : '6' }}" 
                                    class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Tidak ada peminjaman ruangan hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
</div>
@endsection