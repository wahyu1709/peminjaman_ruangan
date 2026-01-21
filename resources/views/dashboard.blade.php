@extends('layouts/app')

@section('content')

<h1 class="h3 mb-4 text-gray-800">
    {{ $title }}
</h1>

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
    {{-- <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-dark shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                            Peminjaman aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bookingsActive }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
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
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                            <td class="text-center">{!! $booking->status_badge !!}</td>
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

@endsection