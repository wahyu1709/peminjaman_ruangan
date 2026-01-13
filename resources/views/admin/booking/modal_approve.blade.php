<!-- Modal Konfirmasi Approve Booking -->
<div class="modal fade" id="exampleModal{{ $booking->id }}" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel{{ $booking->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <!-- FORM DIBUNGKUS DI SINI -->
            <form action="{{ route('bookingApprove', $booking->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="approveModalLabel{{ $booking->id }}">
                        <i class="fas fa-check-circle mr-2"></i>
                        Setujui Peminjaman?
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p>Apakah Anda yakin ingin <strong class="text-success">menyetujui</strong> peminjaman berikut?</p>
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

                    <!-- Komentar Admin (Opsional) -->
                    <div class="form-group mt-4">
                        <label for="admin_comment{{ $booking->id }}" class="font-weight-bold text-success">
                            Komentar / Informasi Tambahan <span class="text-muted">(Opsional)</span>
                        </label>
                        <textarea name="admin_comment" id="admin_comment{{ $booking->id }}" 
                                  class="form-control" 
                                  rows="4" 
                                  placeholder="Isi komentar jika peminjaman ruangan ini berbayar"></textarea>
                        <p class="text-muted small mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Komentar ini akan dilihat oleh pengaju. Kosongkan jika tidak diperlukan.
                        </p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-1"></i>
                        Ya, Setujui Peminjaman
                    </button>
                </div>

            </form> <!-- Tutup form di sini -->

        </div>
    </div>
</div>