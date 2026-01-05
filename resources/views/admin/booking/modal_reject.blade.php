<!-- Modal Konfirmasi Reject Booking -->
<div class="modal fade" id="rejectModal{{ $booking->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel{{ $booking->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel{{ $booking->id }}">
                    <i class="fas fa-times-circle mr-2"></i>
                    Tolak Peminjaman?
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin <strong class="text-danger">menolak</strong> peminjaman berikut?</p>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Pengaju:</strong><br>
                        {{ $booking->user->name }}
                    </div>
                    <div class="col-md-6">
                        <strong>Ruangan:</strong><br>
                        {{ $booking->room->nama_ruangan }}
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Tanggal:</strong><br>
                        {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->format('d F Y') }}
                    </div>
                    <div class="col-md-6">
                        <strong>Waktu:</strong><br>
                        {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                    </div>
                </div>
                <div class="mt-3">
                    <strong>Keperluan:</strong><br>
                    {{ $booking->keperluan }}
                </div>
                <p class="mt-4 text-danger">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Peminjaman yang ditolak tidak dapat dikembalikan ke status pending.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Batal
                </button>
                <form action="{{ route('bookingReject', $booking->id) }}" method="POST" style="display:inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times mr-1"></i>
                        Ya, Tolak Peminjaman
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>