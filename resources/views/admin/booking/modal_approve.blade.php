{{-- resources/views/admin/booking/modal_approve.blade.php --}}
@php
    $roomLabel = $booking->room
               ? $booking->room->kode_ruangan . ' (' . $booking->room->nama_ruangan . ')'
               : 'Barang Inventaris';
@endphp

<div class="modal fade" id="exampleModal{{ $booking->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">

            <form action="{{ route('bookingApprove', $booking->id) }}" method="POST">
                @csrf
                @method('PATCH')

                {{-- Header --}}
                <div class="modal-header text-white border-0"
                     style="background:linear-gradient(90deg,#10b981,#34d399);padding:14px 20px;">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.2);
                                    display:flex;align-items:center;justify-content:center;font-size:.95rem;
                                    flex-shrink:0;margin-right:8px;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h5 class="modal-title mb-0 font-weight-bold" style="font-size:.95rem;">
                            Setujui Peminjaman
                        </h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal"
                            style="opacity:.8;font-size:1.2rem;">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="padding:20px;">

                    {{-- Detail box --}}
                    <div style="background:#f8fafc;border-radius:10px;padding:14px 16px;
                                border-left:3px solid #10b981;margin-bottom:16px;">
                        <div style="font-size:.72rem;font-weight:700;color:#10b981;
                                    text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">
                            <i class="fas fa-info-circle mr-1"></i>Detail Peminjaman
                        </div>
                        <div class="row" style="font-size:.875rem;">
                            <div class="col-6 mb-2">
                                <div style="font-size:.7rem;color:#94a3b8;font-weight:700;
                                            text-transform:uppercase;letter-spacing:.05em;">Pengaju</div>
                                <div style="font-weight:600;color:#0f172a;">{{ $booking->user->name }}</div>
                            </div>
                            <div class="col-6 mb-2">
                                <div style="font-size:.7rem;color:#94a3b8;font-weight:700;
                                            text-transform:uppercase;letter-spacing:.05em;">Ruangan</div>
                                <div style="font-weight:600;color:#0f172a;">{{ $roomLabel }}</div>
                            </div>
                            <div class="col-6 mb-2">
                                <div style="font-size:.7rem;color:#94a3b8;font-weight:700;
                                            text-transform:uppercase;letter-spacing:.05em;">Tanggal</div>
                                <div style="font-weight:600;color:#0f172a;">
                                    {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div style="font-size:.7rem;color:#94a3b8;font-weight:700;
                                            text-transform:uppercase;letter-spacing:.05em;">Waktu</div>
                                <div style="font-weight:600;color:#0f172a;">
                                    {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} –
                                    {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div style="font-size:.7rem;color:#94a3b8;font-weight:700;
                                            text-transform:uppercase;letter-spacing:.05em;">Keperluan</div>
                                <div style="color:#334155;">{{ $booking->keperluan }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Komentar opsional --}}
                    <div class="form-group mb-0">
                        <label for="admin_comment{{ $booking->id }}"
                               style="font-size:.78rem;font-weight:700;color:#475569;
                                      text-transform:uppercase;letter-spacing:.05em;">
                            Komentar Admin
                            <span style="font-weight:400;color:#94a3b8;text-transform:none;
                                         letter-spacing:0;">(Opsional)</span>
                        </label>
                        <textarea name="admin_comment"
                                  id="admin_comment{{ $booking->id }}"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Catatan untuk pengaju, misal: info pembayaran, instruksi khusus..."
                                  style="border-radius:8px;border:1.5px solid #e2e8f0;
                                         font-size:.875rem;resize:none;"></textarea>
                        <small style="color:#94a3b8;font-size:.75rem;">
                            <i class="fas fa-eye mr-1"></i>Komentar ini akan terlihat oleh pengaju.
                        </small>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer border-0" style="padding:12px 20px;background:#f8fafc;">
                    <button type="button" class="btn btn-sm btn-light"
                            style="border-radius:8px;font-weight:600;border:1.5px solid #e2e8f0;"
                            data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-sm text-white font-weight-bold"
                            style="border-radius:8px;
                                   background:linear-gradient(135deg,#10b981,#059669);
                                   border:none;padding:7px 18px;">
                        <i class="fas fa-check mr-1"></i>Ya, Setujui
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>