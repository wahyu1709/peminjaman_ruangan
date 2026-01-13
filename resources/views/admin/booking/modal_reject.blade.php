<!-- Modal Konfirmasi Reject Booking -->
<div class="modal fade" id="rejectModal{{ $booking->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel{{ $booking->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel{{ $booking->id }}">
                    <i class="fas fa-times-circle mr-2"></i>
                    Tolak Peminjaman Ruangan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('bookingReject', $booking->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="modal-body">
                    <!-- Detail Booking -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Pengaju:</strong><br>
                            {{ $booking->user->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Ruangan:</strong><br>
                            {{ $booking->room->kode_ruangan }} ({{ $booking->room->nama_ruangan }})
                        </div>
                    </div>

                    <div class="row mb-3">
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

                    <div class="mb-3">
                        <strong>Keperluan:</strong><br>
                        {{ $booking->keperluan }}
                    </div>

                    <hr>

                    <!-- Alasan Penolakan (WAJIB) -->
                    <div class="form-group">
                        <label for="rejected_reason{{ $booking->id }}" class="font-weight-bold text-danger">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea name="rejected_reason" id="rejected_reason{{ $booking->id }}" 
                                  class="form-control @error('rejected_reason') is-invalid @enderror" 
                                  rows="4" required 
                                  placeholder="Masukkan alasan penolakan secara jelas..."></textarea>

                        @error('rejected_reason')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <p class="text-danger small mt-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Peminjaman yang ditolak tidak dapat dikembalikan ke status pending.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times mr-1"></i>
                        Ya, Tolak Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>