@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Upload Bukti Pembayaran</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-file-invoice-dollar me-2"></i>
                Detail Peminjaman
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Ruangan:</strong> {{ $booking->room->kode_ruangan }} - {{ $booking->room->nama_ruangan }}</p>
                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}</p>
                    <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total yang Harus Dibayar:</strong> <span class="text-danger fw-bold">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span></p>
                    <p><strong>Status:</strong> 
                        @if($booking->bukti_pembayaran)
                            <span class="badge bg-success text-white">Bukti Sudah Diupload</span>
                        @else
                            <span class="badge bg-warning">Menunggu Bukti Pembayaran</span>
                        @endif
                    </p>
                    @if($booking->invoice_path)
                        <a href="{{ Storage::url($booking->invoice_path) }}" target="_blank" class="btn btn-info btn-sm">
                            <i class="fas fa-file-pdf me-1"></i> Lihat Invoice
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(!$booking->bukti_pembayaran || $booking->status == 'pending')
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-upload me-2"></i>
                Upload Bukti Pembayaran
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('booking.upload.proof', $booking->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Bukti Pembayaran <span class="text-danger">*</span></label>
                    <input type="file" name="bukti_pembayaran" class="form-control @error('bukti_pembayaran') is-invalid @enderror" 
                           accept="image/*,.pdf" required>
                    @error('bukti_pembayaran')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <small class="text-muted">Format: JPG, PNG, PDF (Maksimal 5MB)</small>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-upload me-2"></i> Upload Bukti
                </button>
            </form>
        </div>
    </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Bukti pembayaran sudah diupload!</strong> Menunggu verifikasi dari admin.
        </div>
    @endif

    @if($booking->bukti_pembayaran)
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-image me-2"></i>
                    Bukti Pembayaran yang Diupload
                </h6>
            </div>
            <div class="card-body">
                @if(pathinfo($booking->bukti_pembayaran, PATHINFO_EXTENSION) == 'pdf')
                    <a href="{{ Storage::url($booking->bukti_pembayaran) }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-file-pdf me-1"></i> Buka PDF
                    </a>
                @else
                    <img src="{{ Storage::url($booking->bukti_pembayaran) }}" 
                         alt="Bukti Pembayaran" 
                         class="img-fluid rounded" 
                         style="max-height: 400px;">
                @endif
            </div>
        </div>
    @endif
</div>
@endsection