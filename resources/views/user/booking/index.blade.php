@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    {{ $title }}
</h1>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
        <div class="mb-1 mr-2">
            <a href="{{ route('bookingCreate') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Tambah Data
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>No</th>
                        <th>Pengaju</th>
                        <th>Ruangan</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>
                            <i class="fas fa-cog"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ $booking->room->nama_ruangan }}</td>
                        <td>{{ $booking->tanggal_pinjam }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                        </td>
                        <td>{{ $booking->keperluan }}</td>
                        <td>{!! $booking->status_badge !!}</td>
                        <td class="text-center">
                             <!-- Tombol approve/reject untuk admin -->
                            @if(auth()->user()->role == 'admin' && $booking->status == 'pending')
                                <!-- Tombol Approve dengan Modal -->
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#exampleModal{{ $booking->id }}">
                                    Approve
                                </button>
                                @include('booking.modal_approve')

                                <!-- Tombol Reject dengan Modal -->
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $booking->id }}">
                                    Reject
                                </button>
                                @include('booking.modal_reject')
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection