@extends('layouts/app')

@push('styles')
<style>
    :root {
        --card-r:  14px;
        --shadow:  0 2px 16px rgba(0,0,0,0.07);
        --shadow-h:0 8px 28px rgba(0,0,0,0.13);
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Page wrapper ────────────────────────────────────────── */
    .page-card {
        border: none;
        border-radius: var(--card-r);
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: fadeUp .4s ease both;
    }

    .page-card-header {
        padding: 13px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        background: linear-gradient(90deg, #4361ee, #4895ef);
        color: #fff;
        font-weight: 700;
        font-size: .9rem;
    }

    .page-card-header .header-title i { margin-right: 8px; }

    /* ── Table ───────────────────────────────────────────────── */
    .booking-table thead tr {
        background: #f8fafc;
    }

    .booking-table th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0;
        padding: 11px 12px;
        white-space: nowrap;
    }

    .booking-table td {
        padding: 11px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: .875rem;
        color: #334155;
    }

    .booking-table tbody tr:last-child td { border-bottom: none; }

    .booking-table tbody tr:hover td { background: #f8fafc; }

    /* ── User cell ───────────────────────────────────────────── */
    .user-name   { font-weight: 600; color: #0f172a; font-size: .875rem; }
    .user-type   { font-size: .72rem; color: #94a3b8; }

    /* ── Room cell ───────────────────────────────────────────── */
    .room-code   { font-weight: 700; color: #1e293b; }
    .room-name-s { font-size: .72rem; color: #64748b; }

    /* ── WA button ───────────────────────────────────────────── */
    .btn-wa {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #25d366;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 5px 10px;
        font-size: .78rem;
        font-weight: 600;
        text-decoration: none;
        transition: opacity .15s, transform .15s;
        white-space: nowrap;
    }
    .btn-wa:hover { opacity: .88; color: #fff; transform: translateY(-1px); text-decoration: none; }

    /* ── Time pill ───────────────────────────────────────────── */
    .time-pill {
        display: inline-block;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: .78rem;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 6px;
        white-space: nowrap;
    }

    /* ── Payment cell ────────────────────────────────────────── */
    .amount-text { font-weight: 700; color: #1d4ed8; font-size: .875rem; }

    /* ── Status badges ───────────────────────────────────────── */
    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .03em;
    }

    .sb-pending   { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
    .sb-verif     { background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; }
    .sb-approved  { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .sb-rejected  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    .sb-completed { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .sb-cancelled { background: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0; }

    /* ── Action buttons ──────────────────────────────────────── */
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 7px;
        padding: 4px 10px;
        font-size: .78rem;
        font-weight: 600;
        border: none;
        transition: opacity .15s, transform .15s;
        cursor: pointer;
        text-decoration: none;
    }
    .action-btn:hover { opacity: .85; transform: translateY(-1px); }
    .action-btn:active { transform: scale(.97); }

    .ab-approve  { background: #dcfce7; color: #166534; }
    .ab-reject   { background: #fee2e2; color: #991b1b; }
    .ab-cancel   { background: #f1f5f9; color: #475569; }
    .ab-extend   { background: #e0f2fe; color: #0369a1; text-decoration: none; }
    .ab-disabled { background: #f1f5f9; color: #cbd5e1; cursor: not-allowed; opacity: .7; }

    .ab-upload   { background: #fee2e2; color: #991b1b; }
    .ab-reupload { background: #fef9c3; color: #854d0e; }
    .ab-view-proof { background: #dcfce7; color: #166534; }
    .ab-invoice  { background: #e0f2fe; color: #0369a1; text-decoration: none; }

    /* ── Modal improvements ──────────────────────────────────── */
    .modal-header-blue    { background: linear-gradient(90deg, #4361ee, #4895ef); }
    .modal-header-green   { background: linear-gradient(90deg, #10b981, #34d399); }
    .modal-header-red     { background: linear-gradient(90deg, #ef4444, #fca5a5); }
    .modal-header-danger  { background: linear-gradient(90deg, #dc2626, #b91c1c); }

    .modal-detail-box {
        background: #f8fafc;
        border-radius: 10px;
        padding: 14px 16px;
        border-left: 3px solid #4361ee;
        margin-bottom: 14px;
    }

    .modal-detail-box .detail-row {
        display: flex;
        gap: 6px;
        margin-bottom: 6px;
        font-size: .875rem;
    }
    .modal-detail-box .detail-row:last-child { margin-bottom: 0; }
    .modal-detail-box .detail-label { font-weight: 600; color: #64748b; min-width: 110px; }
    .modal-detail-box .detail-value { color: #1e293b; }

    .bank-info-box {
        background: #fffbeb;
        border-radius: 10px;
        padding: 12px 16px;
        border-left: 3px solid #f59e0b;
        font-size: .875rem;
        margin-bottom: 14px;
    }

    .bank-info-box .bank-title { font-weight: 700; color: #92400e; margin-bottom: 8px; }
    .bank-info-box p { margin: 0 0 4px; color: #78350f; }
    .bank-info-box p:last-child { margin: 0; }

    .reason-box {
        background: #fff1f2;
        border-radius: 8px;
        padding: 12px 14px;
        border-left: 3px solid #ef4444;
        font-size: .875rem;
        color: #991b1b;
    }

    .comment-box {
        background: #f0fdf4;
        border-radius: 8px;
        padding: 12px 14px;
        border-left: 3px solid #10b981;
        font-size: .875rem;
        color: #166534;
    }

    /* ── Keperluan popover button ────────────────────────────── */
    .btn-keperluan {
        background: none;
        border: none;
        padding: 0;
        color: #4361ee;
        font-size: .78rem;
        font-weight: 600;
        cursor: pointer;
        display: block;
        margin-top: 2px;
        text-decoration: underline;
        text-decoration-style: dotted;
        text-underline-offset: 2px;
        line-height: 1.4;
    }
    .btn-keperluan:hover { color: #3a0ca3; }

    /* Popover styling */
    .popover {
        max-width: 320px;
        border: none;
        border-radius: 10px;
        box-shadow: 0 8px 28px rgba(0,0,0,.13);
    }
    .popover-header {
        background: linear-gradient(90deg, #4361ee, #4895ef);
        color: #fff;
        font-weight: 700;
        font-size: .82rem;
        border-radius: 10px 10px 0 0;
        border: none;
    }
    .popover-body {
        font-size: .875rem;
        color: #334155;
        line-height: 1.6;
        padding: 12px 14px;
    }

    /* ── Empty state ─────────────────────────────────────────── */
    .empty-booking {
        text-align: center;
        padding: 50px 20px;
        color: #94a3b8;
    }
    .empty-booking i   { font-size: 2.8rem; display: block; margin-bottom: 12px; opacity: .4; }
    .empty-booking p   { font-size: .875rem; margin: 0; }

    /* ── Alert flash ─────────────────────────────────────────── */
    .flash-alert {
        border: none;
        border-radius: var(--card-r);
        padding: 12px 18px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: .875rem;
        animation: fadeUp .3s ease both;
    }
    .flash-success { background: #f0fdf4; color: #166534; border-left: 4px solid #10b981; }
    .flash-error   { background: #fff1f2; color: #991b1b; border-left: 4px solid #ef4444; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ── Page Title ──────────────────────────────────────────── --}}
    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    {{-- ── Flash Messages ─────────────────────────────────────── --}}
    @if(session('success'))
    <div class="flash-alert flash-success">
        <i class="fas fa-check-circle fa-lg"></i>
        <div><strong>Berhasil!</strong> {{ session('success') }}</div>
        <button type="button" class="close ms-auto" data-dismiss="alert" style="background:none;border:none;font-size:1.1rem;color:inherit;opacity:.6;">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="flash-alert flash-error">
        <i class="fas fa-exclamation-triangle fa-lg"></i>
        <div><strong>Error!</strong> {{ session('error') }}</div>
        <button type="button" class="close ms-auto" data-dismiss="alert" style="background:none;border:none;font-size:1.1rem;color:inherit;opacity:.6;">&times;</button>
    </div>
    @endif

    {{-- ── Main Card ───────────────────────────────────────────── --}}
    <div class="page-card card">

        {{-- Header --}}
        <div class="page-card-header">
            <div class="header-title">
                <i class="fas fa-clipboard-list"></i>Data Peminjaman
            </div>
            <a href="{{ route('bookingCreate') }}" class="action-btn ab-approve" style="background:#fff;color:#4361ee;">
                <i class="fas fa-plus"></i> Pinjam Ruangan
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table booking-table mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:44px;">No</th>
                            <th>Pengaju</th>
                            <th class="text-center">No. HP</th>
                            <th>Ruangan</th>
                            <th>Tanggal</th>
                            <th class="text-center">Jam</th>
                            <th>Keperluan</th>
                            <th>Peran / Unit</th>
                            <th class="text-center">Pembayaran</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                        @php
                            $roomLabel = $booking->room
                                ? $booking->room->kode_ruangan . ' (' . $booking->room->nama_ruangan . ')'
                                : 'Barang Inventaris';
                        @endphp
                        <tr>
                            {{-- No --}}
                            <td class="text-center text-muted">{{ $loop->iteration }}</td>

                            {{-- Pengaju --}}
                            <td>
                                <div class="user-name">{{ $booking->user->name }}</div>
                                <div class="user-type">{{ $booking->user->jenis_pengguna }}</div>
                            </td>

                            {{-- No. HP --}}
                            <td class="text-center">
                                @if($booking->user->phone)
                                    @php
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $booking->user->phone);
                                        if (Str::startsWith($cleanPhone, '0'))         $cleanPhone = '62' . substr($cleanPhone, 1);
                                        elseif (Str::startsWith($cleanPhone, '+62'))    $cleanPhone = '62' . substr($cleanPhone, 3);
                                        elseif (!Str::startsWith($cleanPhone, '62') && strlen($cleanPhone) >= 10)
                                                                                        $cleanPhone = '62' . $cleanPhone;

                                        $konteks = $booking->room
                                            ? 'ruangan ' . $booking->room->kode_ruangan
                                            : 'barang inventaris';
                                        $waText = urlencode(
                                            "Halo {$booking->user->name}, saya dari admin FIK UI. " .
                                            "Tentang peminjaman {$konteks} tanggal {$booking->tanggal_pinjam}..."
                                        );
                                    @endphp
                                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}"
                                       target="_blank" class="btn-wa"
                                       title="Chat via WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                        <span class="d-none d-lg-inline">WhatsApp</span>
                                    </a>
                                @else
                                    <span style="font-size:.75rem;color:#94a3b8;">Tidak Ada</span>
                                @endif
                            </td>

                            {{-- Ruangan --}}
                            <td>
                                @if($booking->room)
                                    <div class="room-code">{{ $booking->room->kode_ruangan }}</div>
                                    <div class="room-name-s">{{ $booking->room->nama_ruangan }}</div>
                                @else
                                    <span style="color:#94a3b8;font-size:.85rem;">
                                        <i class="fas fa-box me-1"></i>Barang Saja
                                    </span>
                                @endif
                            </td>

                            {{-- Tanggal --}}
                            <td style="white-space:nowrap;">
                                {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('D MMM YYYY') }}
                            </td>

                            {{-- Jam --}}
                            <td class="text-center">
                                <span class="time-pill">
                                    {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                                </span>
                            </td>

                            {{-- Keperluan --}}
                            <td style="max-width:160px;">
                                @if(strlen($booking->keperluan) > 45)
                                    {{-- Teks panjang: tampilkan potongan + tombol baca selengkapnya --}}
                                    <span style="color:#334155;">{{ Str::limit($booking->keperluan, 45) }}</span>
                                    <button type="button"
                                            class="btn-keperluan"
                                            data-toggle="popover"
                                            data-trigger="focus"
                                            data-placement="left"
                                            data-content="{{ e($booking->keperluan) }}"
                                            title="Keperluan Lengkap"
                                            tabindex="0">
                                        ...selengkapnya
                                    </button>
                                @else
                                    {{ $booking->keperluan }}
                                @endif
                            </td>

                            {{-- Peran / Unit --}}
                            <td style="color:#64748b;font-size:.85rem;">
                                {{ $booking->role_unit ?? '—' }}
                            </td>

                            {{-- Pembayaran --}}
                            <td class="text-center">
                                @if($booking->total_amount > 0)
                                    <div class="amount-text mb-1">
                                        Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                                    </div>

                                    {{-- Invoice --}}
                                    @if($booking->invoice_path)
                                        <a href="{{ Storage::url($booking->invoice_path) }}"
                                           target="_blank" class="action-btn ab-invoice d-block mb-1">
                                            <i class="fas fa-file-invoice"></i> Invoice
                                        </a>
                                    @else
                                        <span class="status-badge sb-pending d-block mb-1">Invoice Pending</span>
                                    @endif

                                    {{-- Bukti --}}
                                    @if($booking->bukti_pembayaran)
                                        @if(pathinfo($booking->bukti_pembayaran, PATHINFO_EXTENSION) == 'pdf')
                                            <a href="{{ Storage::url($booking->bukti_pembayaran) }}"
                                               target="_blank" class="action-btn ab-view-proof d-block mb-1">
                                                <i class="fas fa-file-pdf"></i> Bukti PDF
                                            </a>
                                        @else
                                            <button class="action-btn ab-view-proof d-block mb-1 w-100"
                                                    data-toggle="modal"
                                                    data-target="#proofModal{{ $booking->id }}">
                                                <i class="fas fa-image"></i> Lihat Bukti
                                            </button>
                                        @endif
                                        @if(in_array($booking->status, ['pending','payment_uploaded']) && $booking->user_id === auth()->id())
                                            <button class="action-btn ab-reupload d-block w-100"
                                                    data-toggle="modal"
                                                    data-target="#uploadModal{{ $booking->id }}">
                                                <i class="fas fa-sync"></i> Re-upload
                                            </button>
                                        @endif
                                    @else
                                        @if($booking->status === 'pending' && $booking->user_id === auth()->id())
                                            <button class="action-btn ab-upload d-block w-100"
                                                    data-toggle="modal"
                                                    data-target="#uploadModal{{ $booking->id }}">
                                                <i class="fas fa-upload"></i> Upload Bukti
                                            </button>
                                        @else
                                            <span class="status-badge sb-rejected">Belum Upload</span>
                                        @endif
                                    @endif
                                @else
                                    <span style="color:#94a3b8;font-size:.82rem;">Gratis</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="text-center">
                                @php
                                    $statusMap = [
                                        'pending'          => ['sb-pending',   'Pending'],
                                        'payment_uploaded' => ['sb-verif',     'Menunggu Verifikasi'],
                                        'approved'         => ['sb-approved',  'Disetujui'],
                                        'rejected'         => ['sb-rejected',  'Ditolak'],
                                        'completed'        => ['sb-completed', 'Selesai'],
                                    ];
                                    [$sCls, $sLbl] = $statusMap[$booking->status] ?? ['sb-cancelled','Dibatalkan'];
                                @endphp
                                <span class="status-badge {{ $sCls }}">{{ $sLbl }}</span>

                                @if($booking->status == 'rejected' && $booking->rejected_reason)
                                    <br>
                                    <button class="btn btn-link text-danger p-0 mt-1"
                                            style="font-size:.75rem;font-weight:700;"
                                            data-toggle="modal"
                                            data-target="#reasonModal{{ $booking->id }}">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Alasan
                                    </button>
                                @elseif($booking->status == 'approved' && $booking->admin_comment)
                                    <br>
                                    <button class="btn btn-link text-success p-0 mt-1"
                                            style="font-size:.75rem;font-weight:700;"
                                            data-toggle="modal"
                                            data-target="#adminCommentModal{{ $booking->id }}">
                                        <i class="fas fa-info-circle me-1"></i>Komentar
                                    </button>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center">
                                @if(auth()->user()->role == 'admin' && in_array($booking->status, ['pending','payment_uploaded']))
                                    <div class="d-flex flex-column gap-1">
                                        @if($booking->total_amount > 0 && !$booking->bukti_pembayaran)
                                            <button class="action-btn ab-disabled w-100" disabled>
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        @else
                                            <button class="action-btn ab-approve w-100"
                                                    data-toggle="modal"
                                                    data-target="#exampleModal{{ $booking->id }}">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        @endif
                                        <button class="action-btn ab-reject w-100"
                                                data-toggle="modal"
                                                data-target="#rejectModal{{ $booking->id }}">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                @elseif($booking->status == 'approved')
                                    <button class="action-btn ab-cancel w-100"
                                            data-toggle="modal"
                                            data-target="#cancelModal{{ $booking->id }}">
                                        <i class="fas fa-ban"></i> Batalkan
                                    </button>

                                @elseif($booking->status == 'completed')
                                    <a href="{{ route('booking.extend', $booking->id) }}"
                                       class="action-btn ab-extend w-100">
                                        <i class="fas fa-clock"></i> Perpanjang
                                    </a>
                                @else
                                    <span style="color:#cbd5e1;font-size:.78rem;">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11">
                                <div class="empty-booking">
                                    <i class="fas fa-clipboard text-muted"></i>
                                    <p>Belum ada data peminjaman.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         MODALS
    ══════════════════════════════════════════════════════════ --}}
    @foreach ($bookings as $booking)
        @php
            $roomLabel = $booking->room
                ? $booking->room->kode_ruangan . ' (' . $booking->room->nama_ruangan . ')'
                : 'Barang Inventaris';
        @endphp

        {{-- Modal Approve & Reject (harus di luar tabel agar tidak di-corrupt DataTables) --}}
        @if(auth()->user()->role == 'admin' && in_array($booking->status, ['pending','payment_uploaded']))
            @include('admin.booking.modal_approve')
            @include('admin.booking.modal_reject')
        @endif

        {{-- Modal Upload Bukti --}}
        @if($booking->total_amount > 0 && $booking->user_id === auth()->id())
        <div class="modal fade" id="uploadModal{{ $booking->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header modal-header-green text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('booking.upload.proof', $booking->id) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">

                            {{-- Detail Peminjaman --}}
                            <div class="modal-detail-box">
                                <div style="font-size:.78rem;font-weight:700;color:#4361ee;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">
                                    <i class="fas fa-info-circle me-1"></i>Detail Peminjaman
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Ruangan</span>
                                    <span class="detail-value">{{ $roomLabel }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Tanggal</span>
                                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Waktu</span>
                                    <span class="detail-value">
                                        {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} –
                                        {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Total Bayar</span>
                                    <span class="detail-value" style="font-weight:700;color:#059669;font-size:1rem;">
                                        Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Info Rekening --}}
                            <div class="bank-info-box">
                                <div class="bank-title"><i class="fas fa-university me-2"></i>Informasi Transfer</div>
                                <p><strong>Bank:</strong> BNI Cabang Kampus UI Depok</p>
                                <p><strong>No. Rekening:</strong> 1273000535</p>
                                <p><strong>Atas Nama:</strong> Universitas Indonesia FIK Non Biaya Pendidikan</p>
                            </div>

                            {{-- Bukti lama --}}
                            @if($booking->bukti_pembayaran)
                            <div class="modal-detail-box" style="border-left-color:#94a3b8;">
                                <div style="font-size:.78rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">
                                    <i class="fas fa-history me-1"></i>Bukti Sebelumnya
                                </div>
                                @if(pathinfo($booking->bukti_pembayaran, PATHINFO_EXTENSION) == 'pdf')
                                    <a href="{{ Storage::url($booking->bukti_pembayaran) }}" target="_blank"
                                       class="action-btn ab-cancel">
                                        <i class="fas fa-file-pdf"></i> Lihat PDF Lama
                                    </a>
                                @else
                                    <img src="{{ Storage::url($booking->bukti_pembayaran) }}"
                                         class="img-thumbnail" style="max-height:140px;">
                                @endif
                            </div>
                            @endif

                            {{-- Upload field --}}
                            <div class="form-group">
                                <label class="font-weight-bold mb-2">
                                    Upload Bukti Transfer <span class="text-danger">*</span>
                                </label>
                                <input type="file"
                                       name="bukti_pembayaran"
                                       class="form-control-file @error('bukti_pembayaran') is-invalid @enderror"
                                       accept="image/*,.pdf"
                                       id="fileInput{{ $booking->id }}"
                                       required>
                                @error('bukti_pembayaran')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <small class="text-muted d-block mt-1">Format: JPG, PNG, PDF · Maks 5MB</small>
                            </div>

                            <div id="imagePreview{{ $booking->id }}" class="mt-2" style="display:none;">
                                <p class="font-weight-bold mb-1" style="font-size:.85rem;">Preview:</p>
                                <img id="preview{{ $booking->id }}" src=""
                                     class="img-fluid rounded" style="max-height:260px;border:1px solid #e2e8f0;">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="action-btn ab-cancel" data-dismiss="modal">Batal</button>
                            <button type="submit" class="action-btn ab-approve">
                                <i class="fas fa-check me-1"></i>Upload Bukti
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inp{{ $booking->id }} = document.getElementById('fileInput{{ $booking->id }}');
            const prv{{ $booking->id }} = document.getElementById('preview{{ $booking->id }}');
            const box{{ $booking->id }} = document.getElementById('imagePreview{{ $booking->id }}');
            if (inp{{ $booking->id }}) {
                inp{{ $booking->id }}.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const r = new FileReader();
                        r.onload = e => { prv{{ $booking->id }}.src = e.target.result; box{{ $booking->id }}.style.display='block'; };
                        r.readAsDataURL(file);
                    } else { box{{ $booking->id }}.style.display='none'; }
                });
            }
        });
        </script>
        @endif

        {{-- Modal Preview Bukti --}}
        @if($booking->bukti_pembayaran && pathinfo($booking->bukti_pembayaran, PATHINFO_EXTENSION) !== 'pdf')
        <div class="modal fade" id="proofModal{{ $booking->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header modal-header-green text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-image me-2"></i>Bukti Pembayaran — {{ $booking->user->name }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ Storage::url($booking->bukti_pembayaran) }}"
                             alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height:400px;">
                        <div class="modal-detail-box mt-3 text-left">
                            <div class="detail-row">
                                <span class="detail-label">Ruangan</span>
                                <span class="detail-value">{{ $roomLabel }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Total</span>
                                <span class="detail-value fw-bold">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Status</span>
                                <span class="detail-value">
                                    @php
                                        $spMap = ['payment_uploaded'=>['sb-verif','Menunggu Verifikasi'],'approved'=>['sb-approved','Terverifikasi']];
                                        [$sc,$sl] = $spMap[$booking->status] ?? ['sb-completed', ucfirst($booking->status)];
                                    @endphp
                                    <span class="status-badge {{ $sc }}">{{ $sl }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="action-btn ab-cancel" data-dismiss="modal">Tutup</button>
                        <a href="{{ Storage::url($booking->bukti_pembayaran) }}" target="_blank"
                           class="action-btn ab-extend">
                            <i class="fas fa-external-link-alt me-1"></i>Buka Tab Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Modal Alasan Penolakan --}}
        @if($booking->status == 'rejected' && $booking->rejected_reason)
        <div class="modal fade" id="reasonModal{{ $booking->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header modal-header-red text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-exclamation-triangle me-2"></i>Alasan Penolakan
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-detail-box">
                            <div class="detail-row"><span class="detail-label">Pengaju</span><span class="detail-value">{{ $booking->user->name }}</span></div>
                            <div class="detail-row"><span class="detail-label">Ruangan</span><span class="detail-value">{{ $roomLabel }}</span></div>
                            <div class="detail-row"><span class="detail-label">Tanggal</span><span class="detail-value">{{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}</span></div>
                            <div class="detail-row"><span class="detail-label">Waktu</span><span class="detail-value">{{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}</span></div>
                        </div>
                        <div class="reason-box">
                            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Alasan Penolakan</div>
                            {{ $booking->rejected_reason }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="action-btn ab-cancel" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Komentar Admin --}}
        @elseif($booking->status == 'approved' && $booking->admin_comment)
        <div class="modal fade" id="adminCommentModal{{ $booking->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header modal-header-green text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-info-circle me-2"></i>Komentar Admin
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-detail-box">
                            <div class="detail-row"><span class="detail-label">Pengaju</span><span class="detail-value">{{ $booking->user->name }}</span></div>
                            <div class="detail-row"><span class="detail-label">Ruangan</span><span class="detail-value">{{ $roomLabel }}</span></div>
                            <div class="detail-row"><span class="detail-label">Tanggal</span><span class="detail-value">{{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}</span></div>
                            <div class="detail-row"><span class="detail-label">Waktu</span><span class="detail-value">{{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}</span></div>
                        </div>
                        <div class="comment-box">
                            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Komentar Admin</div>
                            {{ $booking->admin_comment }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="action-btn ab-cancel" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Cancel --}}
        @elseif($booking->status == 'approved')
        <div class="modal fade" id="cancelModal{{ $booking->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header modal-header-danger text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Pembatalan
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-detail-box" style="border-left-color:#ef4444;">
                            <div style="font-size:.78rem;font-weight:700;color:#dc2626;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">
                                Yakin ingin membatalkan peminjaman ini?
                            </div>
                            <div class="detail-row"><span class="detail-label">Pengaju</span><span class="detail-value">{{ $booking->user->name }}</span></div>
                            <div class="detail-row"><span class="detail-label">Ruangan</span><span class="detail-value">{{ $roomLabel }}</span></div>
                            <div class="detail-row"><span class="detail-label">Tanggal</span><span class="detail-value">{{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}</span></div>
                            <div class="detail-row"><span class="detail-label">Waktu</span><span class="detail-value">{{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}</span></div>
                        </div>
                        <p class="text-danger mb-0" style="font-size:.82rem;">
                            <i class="fas fa-info-circle me-1"></i>Tindakan ini tidak dapat dikembalikan.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="action-btn ab-cancel" data-dismiss="modal">Batal</button>
                        <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="action-btn ab-reject">
                                <i class="fas fa-ban me-1"></i>Ya, Batalkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

    @endforeach

</div>
@endsection

@push('scripts')
<script>
// ── Fix: pindahkan semua modal ke <body> agar DataTables tidak mengganggu event-nya
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.modal').forEach(function (modal) {
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    // ── Aktifkan popover keperluan
    $('[data-toggle="popover"]').popover({
        html: false,
        sanitize: false,
    });

    // Tutup popover saat klik di luar
    $(document).on('click', function (e) {
        if (!$(e.target).closest('[data-toggle="popover"]').length) {
            $('[data-toggle="popover"]').popover('hide');
        }
    });
});
</script>
@endpush