{{-- resources/views/admin/booking/modal_reject.blade.php --}}
@php
    $roomLabel = $booking->room
               ? $booking->room->kode_ruangan . ' (' . $booking->room->nama_ruangan . ')'
               : 'Barang Inventaris';
@endphp

<div class="modal fade" id="rejectModal{{ $booking->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">

            <form action="{{ route('bookingReject', $booking->id) }}" method="POST">
                @csrf
                @method('PATCH')

                {{-- Header --}}
                <div class="modal-header text-white border-0"
                     style="background:linear-gradient(90deg,#dc2626,#b91c1c);padding:14px 20px;">
                    <div class="d-flex align-items-center">
                        <div style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.2);
                                    display:flex;align-items:center;justify-content:center;font-size:.95rem;
                                    flex-shrink:0;margin-right:10px;">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <h5 class="modal-title mb-0 font-weight-bold" style="font-size:.95rem;">
                            Tolak Peminjaman Ruangan
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
                                border-left:3px solid #dc2626;margin-bottom:16px;">
                        <div style="font-size:.72rem;font-weight:700;color:#dc2626;
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

                    {{-- Textarea alasan --}}
                    <div class="form-group mb-2">
                        <label for="rejected_reason{{ $booking->id }}"
                               style="font-size:.78rem;font-weight:700;color:#dc2626;
                                      text-transform:uppercase;letter-spacing:.05em;">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea name="rejected_reason"
                                  id="rejected_reason{{ $booking->id }}"
                                  class="form-control @error('rejected_reason') is-invalid @enderror"
                                  rows="4"
                                  required
                                  placeholder="Masukkan alasan penolakan secara jelas..."
                                  style="border-radius:8px;border:1.5px solid #fca5a5;
                                         font-size:.875rem;resize:none;"></textarea>
                        @error('rejected_reason')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Warning note --}}
                    <div style="background:#fff1f2;border-radius:8px;padding:10px 14px;
                                border-left:3px solid #ef4444;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-exclamation-triangle" style="color:#ef4444;flex-shrink:0;"></i>
                        <span style="font-size:.8rem;color:#991b1b;">
                            Peminjaman yang ditolak <strong>tidak dapat dikembalikan</strong> ke status pending.
                        </span>
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
                                   background:linear-gradient(135deg,#dc2626,#b91c1c);
                                   border:none;padding:7px 18px;">
                        <i class="fas fa-times mr-1"></i>Ya, Tolak Peminjaman
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>