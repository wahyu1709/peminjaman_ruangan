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
                Pinjam Ruangan
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
                        <th>Peran / Unit Kerja</th>
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
                        <td>{{ $booking->role_unit ?? '-' }}</td>
                        <td class="text-center align-middle">
                            {!! $booking->status_badge !!}

                            @if($booking->status == 'rejected' && $booking->rejected_reason)
                                <br>
                                <button type="button" 
                                        class="btn btn-link text-danger p-0 small font-weight-bold"
                                        data-toggle="modal" 
                                        data-target="#reasonModal{{ $booking->id }}">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Alasan Penolakan
                                </button>
                            @elseif($booking->status == 'approved' && $booking->admin_comment)
                                <br>
                                <button type="button"
                                        class="btn btn-link text-success p-0 small font-weight-bold"
                                        data-toggle="modal"
                                        data-target="#adminCommentModal{{ $booking->id }}">
                                    <i class="fas fa-info-circle mr-1"></i> Lihat Komentar Admin
                                </button>
                            @endif

                        </td>
                        <td class="text-center">
                             <!-- Tombol approve/reject untuk admin -->
                            @if(auth()->user()->role == 'admin' && $booking->status == 'pending')
                                <!-- Tombol Approve dengan Modal -->
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#exampleModal{{ $booking->id }}">
                                    Approve
                                </button>
                                @include('admin.booking.modal_approve')

                                <!-- Tombol Reject dengan Modal -->
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $booking->id }}">
                                    Reject
                                </button>
                                @include('admin.booking.modal_reject')
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            @foreach ($bookings as $booking)
            <!-- Modal Alasan Penolakan -->
                @if($booking->status == 'rejected' && $booking->rejected_reason)
                    <div class="modal fade" id="reasonModal{{ $booking->id }}" tabindex="-1" role="dialog" aria-labelledby="reasonModalLabel{{ $booking->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="reasonModalLabel{{ $booking->id }}">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Alasan Penolakan
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Pengaju:</strong> {{ $booking->user->name }}</p>
                                    <p><strong>Ruangan:</strong> {{ $booking->room->nama_ruangan }}</p>
                                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->format('d F Y') }}</p>
                                    <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}</p>
                                    <hr>
                                    <p class="font-weight-bold text-danger mb-1">Alasan Penolakan:</p>
                                    <p class="mb-0">{{ $booking->rejected_reason }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>

            <!-- Modal Komentar Admin -->
                @elseif ($booking->status == 'approved' && $booking->admin_comment)
                    <div class="modal fade" id="adminCommentModal{{ $booking->id }}" tabindex="-1" role="dialog" aria-labelledby="adminCommentModalLabel{{ $booking->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="adminCommentModalLabel{{ $booking->id }}">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Kommentar Admin
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Pengaju:</strong> {{ $booking->user->name }}</p>
                                    <p><strong>Ruangan:</strong> {{ $booking->room->nama_ruangan }}</p>
                                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->format('d F Y') }}</p>
                                    <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}</p>
                                    <hr>
                                    <p class="font-weight-bold text-success mb-1">Komentar Admin:</p>
                                    <p class="mb-0">{{ $booking->admin_comment }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

@endsection