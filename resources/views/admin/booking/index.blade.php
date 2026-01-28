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
                        <th>Pembayaran</th>
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
                        
                        <!-- Kolom PEMBAYARAN (kolom ke-8) -->
                        <td class="text-center align-middle">
                            @if($booking->total_amount > 0)
                                <!-- Ruangan Berbayar -->
                                <div class="mb-2">
                                    <small class="text-muted">Total: Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</small>
                                </div>
                                
                                @if($booking->invoice_path)
                                    <a href="{{ Storage::url($booking->invoice_path) }}" target="_blank" class="btn btn-info btn-sm mb-2">
                                        <i class="fas fa-file-invoice me-1"></i> Invoice
                                    </a>
                                @endif
                                
                                @if($booking->bukti_pembayaran)
                                    @if(pathinfo($booking->bukti_pembayaran, PATHINFO_EXTENSION) == 'pdf')
                                        <a href="{{ Storage::url($booking->bukti_pembayaran) }}" target="_blank" class="btn btn-success btn-sm">
                                            <i class="fas fa-file-pdf me-1"></i> Bukti
                                        </a>
                                    @else
                                        <!-- Untuk gambar, tampilkan thumbnail -->
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#proofModal{{ $booking->id }}">
                                            <i class="fas fa-image me-1"></i> Bukti
                                        </button>
                                        
                                        <!-- Modal untuk gambar besar -->
                                        <div class="modal fade" id="proofModal{{ $booking->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Bukti Pembayaran - {{ $booking->user->name }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ Storage::url($booking->bukti_pembayaran) }}" 
                                                            alt="Bukti Pembayaran" 
                                                            class="img-fluid">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <span class="badge badge-danger">Belum Upload</span>
                                @endif
                            @else
                                <!-- Ruangan Gratis -->
                                <span class="text-muted">Gratis</span>
                            @endif
                        </td>
                        
                        <!-- Kolom STATUS (kolom ke-9) -->
                        <td class="text-center align-middle">
                            @switch($booking->status)
                                @case('pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @break
                                @case('payment_uploaded')
                                    <span class="badge badge-info">Menunggu Verifikasi</span>
                                    @break
                                @case('approved')
                                    <span class="badge badge-success">Disetujui</span>
                                    @break
                                @case('rejected')
                                    <span class="badge badge-danger">Ditolak</span>
                                    @break
                                @case('completed')
                                    <span class="badge badge-secondary">Selesai</span>
                                    @break
                                @default
                                    <span class="badge badge-light">Dibatalkan</span>
                            @endswitch

                            <!-- Tombol Alasan Penolakan / Komentar Admin -->
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
                        
                        <!-- Kolom AKSI (kolom ke-10) -->
                        <td class="text-center">
                            @if(auth()->user()->role == 'admin' && in_array($booking->status, ['pending', 'payment_uploaded']))
                                <!-- Validasi: hanya bisa approve jika bukti sudah diupload (untuk booking berbayar) -->
                                @if($booking->total_amount > 0 && !$booking->bukti_pembayaran)
                                    <button type="button" class="btn btn-success btn-sm" disabled>
                                        Approve
                                    </button>
                                @else
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#exampleModal{{ $booking->id }}">
                                        Approve
                                    </button>
                                @endif
                                
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $booking->id }}">
                                    Reject
                                </button>
                                
                                @include('admin.booking.modal_approve')
                                @include('admin.booking.modal_reject')
                                
                            @elseif($booking->status == 'approved')
                                <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-secondary" 
                                            data-toggle="modal" data-target="#cancelModal{{ $booking->id }}">
                                        <i class="fas fa-ban"></i> Batalkan
                                    </button>
                                </form>
                            @elseif ($booking->status == 'completed')
                                <a href="{{ route('booking.extend', $booking->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-clock"></i> Perpanjangan
                                </a>
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
                                    <p><strong>Ruangan:</strong> {{ $booking->room->kode_ruangan }} ({{ $booking->room->nama_ruangan }})</p>
                                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}</p>
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
                                    <p><strong>Ruangan:</strong> {{ $booking->room->kode_ruangan }} ({{ $booking->room->nama_ruangan }})</p>
                                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}</p>
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
                
                {{-- Modal Cancel --}}
                @elseif($booking->status == 'approved')
                    <div class="modal fade" id="cancelModal{{ $booking->id }}" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel{{ $booking->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="cancelModalLabel{{ $booking->id }}">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Konfirmasi Pembatalan
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Apakah Anda yakin ingin membatalkan peminjaman ini?</strong></p>
                                    <p><strong>Pengaju:</strong> {{ $booking->user->name }}</p>
                                    <p><strong>Ruangan:</strong> {{ $booking->room->kode_ruangan }} ({{ $booking->room->nama_ruangan }})</p>
                                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}</p>
                                    <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}</p>
                                    <hr>
                                    <p class="text-danger small mb-0">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Tindakan ini tidak dapat dikembalikan.
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-check mr-1"></i> Ya, Batalkan
                                        </button>
                                    </form>
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