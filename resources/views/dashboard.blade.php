@extends('layouts.app')

@push('styles')
<style>
    :root {
        --card-r:  14px;
        --shadow:  0 2px 16px rgba(0,0,0,0.07);
        --shadow-h:0 8px 28px rgba(0,0,0,0.13);
    }

    /* ── KPI Cards (same style as statistik) ─────────────────── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }

    .kpi-card {
        border: none;
        border-radius: var(--card-r);
        padding: 18px 20px;
        box-shadow: var(--shadow);
        transition: transform .2s, box-shadow .2s;
        position: relative;
        overflow: hidden;
        color: #fff;
        animation: fadeUp .4s ease both;
        text-decoration: none;
        display: block;
    }

    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-h);
        color: #fff;
        text-decoration: none;
    }

    .kpi-card::after {
        content: '';
        position: absolute;
        right: -18px; top: -18px;
        width: 80px; height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,.1);
    }

    .kpi-card .kpi-icon  { font-size: 1.4rem; margin-bottom: 8px; opacity: .9; }
    .kpi-card .kpi-value { font-size: 1.9rem; font-weight: 800; line-height: 1; margin-bottom: 3px; letter-spacing: -.02em; }
    .kpi-card .kpi-label { font-size: 0.72rem; font-weight: 600; opacity: .85; text-transform: uppercase; letter-spacing: .06em; }
    .kpi-card .kpi-sub   { font-size: 0.7rem; opacity: .7; margin-top: 4px; }

    .kpi-blue   { background: linear-gradient(135deg, #4361ee, #3a0ca3); animation-delay:.05s; }
    .kpi-green  { background: linear-gradient(135deg, #10b981, #065f46); animation-delay:.10s; }
    .kpi-amber  { background: linear-gradient(135deg, #f59e0b, #b45309); animation-delay:.15s; }
    .kpi-red    { background: linear-gradient(135deg, #ef4444, #991b1b); animation-delay:.20s; }
    .kpi-teal   { background: linear-gradient(135deg, #06b6d4, #0e7490); animation-delay:.25s; }
    .kpi-purple { background: linear-gradient(135deg, #8b5cf6, #5b21b6); animation-delay:.30s; }
    .kpi-danger { background: linear-gradient(135deg, #dc2626, #7f1d1d); animation-delay:.05s; }
    .kpi-dark   { background: linear-gradient(135deg, #334155, #0f172a); animation-delay:.10s; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Alert ───────────────────────────────────────────────── */
    .alert-upload {
        border: none;
        border-radius: var(--card-r);
        background: linear-gradient(135deg, #fff7ed, #fef3c7);
        border-left: 4px solid #f59e0b;
        padding: 14px 18px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        box-shadow: var(--shadow);
        animation: fadeUp .35s ease both;
    }

    .alert-upload .alert-body {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.875rem;
        color: #92400e;
    }

    .alert-upload .alert-icon {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: #fbbf24;
        display: flex; align-items: center; justify-content: center;
        color: #fff;
        font-size: 1rem;
        flex-shrink: 0;
    }

    /* ── Urgent card (pending > 1 jam) ──────────────────────── */
    .kpi-card.kpi-urgent {
        background: linear-gradient(135deg, #dc2626, #7f1d1d);
        border: 2px solid #fca5a5;
        animation: urgentPulse 2s ease-in-out infinite, fadeUp .4s ease both;
    }

    @keyframes urgentPulse {
        0%, 100% { box-shadow: 0 2px 16px rgba(220,38,38,.3); }
        50%       { box-shadow: 0 4px 24px rgba(220,38,38,.6); }
    }

    /* ── Today table card ────────────────────────────────────── */
    .today-card {
        border: none;
        border-radius: var(--card-r);
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-top: 8px;
        animation: fadeUp .5s .3s ease both;
    }

    .today-card .today-header {
        background: linear-gradient(90deg, #0891b2, #06b6d4);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .today-card .today-header h6 {
        color: #fff;
        font-weight: 700;
        font-size: 0.9rem;
        margin: 0;
    }

    .today-card .today-header .date-badge {
        background: rgba(255,255,255,.2);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 10px;
    }

    /* Table styling */
    .today-table th {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 10px 12px;
    }

    .today-table td {
        padding: 10px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    .today-table tr:last-child td { border-bottom: none; }

    .today-table .tr-ongoing {
        background: linear-gradient(90deg, #fffbeb, #fff);
        border-left: 3px solid #f59e0b;
    }

    .room-cell .room-code { font-weight: 700; color: #1e293b; }
    .room-cell .room-name { font-size: 0.75rem; color: #64748b; }

    .time-badge {
        display: inline-block;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 6px;
    }

    .time-badge.ongoing {
        background: #fffbeb;
        color: #d97706;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }

    .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 10px; }
    .empty-state p { font-size: 0.875rem; margin: 0; }

    /* ── Page header ─────────────────────────────────────────── */
    .page-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .page-header-row h1 { margin: 0; }

    .greeting-text {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 2px;
    }

    .live-dot {
        display: inline-block;
        width: 8px; height: 8px;
        background: #10b981;
        border-radius: 50%;
        margin-right: 5px;
        animation: livePulse 1.5s ease-in-out infinite;
    }

    @keyframes livePulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: .3; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ── Page Header ────────────────────────────────────────── --}}
    <div class="page-header-row">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
            <div class="greeting-text">
                <span class="live-dot"></span>
                @if(auth()->user()->role == 'admin')
                    Selamat datang, Admin. Berikut ringkasan hari ini.
                @else
                    Halo, <strong>{{ auth()->user()->name }}</strong>. Berikut status peminjaman Anda.
                @endif
            </div>
        </div>
    </div>

    {{-- ── Alert: Upload Bukti Pembayaran ─────────────────────── --}}
    @if($pendingPaidBookings > 0)
    <div class="alert-upload">
        <div class="alert-body">
            <div class="alert-icon"><i class="fas fa-exclamation"></i></div>
            <div>
                Anda memiliki <strong>{{ $pendingPaidBookings }} peminjaman berbayar</strong>
                yang belum upload bukti pembayaran.
            </div>
        </div>
        <a href="{{ route('booking') }}" class="btn btn-warning btn-sm" style="border-radius:8px;">
            <i class="fas fa-arrow-right me-1"></i> Lihat &amp; Upload Bukti
        </a>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         KPI CARDS
    ══════════════════════════════════════════════════════════ --}}
    <div class="kpi-grid">

        @if(auth()->user()->role == 'admin')

            {{-- Urgent: pending > 1 jam --}}
            @if($pendingOver1Hour > 0)
            <a href="{{ route('booking') }}" class="kpi-card kpi-urgent">
                <div class="kpi-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="kpi-value">{{ $pendingOver1Hour }}</div>
                <div class="kpi-label">Butuh Persetujuan</div>
                <div class="kpi-sub">Pending &gt; 1 jam</div>
            </a>
            @endif

            <div class="kpi-card kpi-dark">
                <div class="kpi-icon"><i class="fas fa-door-open"></i></div>
                <div class="kpi-value">{{ $totalRuangan }}</div>
                <div class="kpi-label">Total Ruangan</div>
            </div>

            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="kpi-value">{{ $bookingsToday }}</div>
                <div class="kpi-label">Peminjaman Hari Ini</div>
                <div class="kpi-sub">Status disetujui</div>
            </div>

            <div class="kpi-card kpi-amber">
                <div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="kpi-value">{{ $bookingsPending }}</div>
                <div class="kpi-label">Menunggu Persetujuan</div>
            </div>

            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-value">{{ $bookingsApproved }}</div>
                <div class="kpi-label">Disetujui</div>
            </div>

            <div class="kpi-card kpi-red">
                <div class="kpi-icon"><i class="fas fa-times-circle"></i></div>
                <div class="kpi-value">{{ $bookingsRejected }}</div>
                <div class="kpi-label">Ditolak</div>
            </div>

        @else

            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="kpi-value">{{ $totalBookingSaya }}</div>
                <div class="kpi-label">Total Peminjaman Saya</div>
            </div>

            <div class="kpi-card kpi-amber">
                <div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="kpi-value">{{ $pendingSaya }}</div>
                <div class="kpi-label">Menunggu Persetujuan</div>
            </div>

            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-value">{{ $activeSaya }}</div>
                <div class="kpi-label">Peminjaman Aktif</div>
                <div class="kpi-sub">Sedang berlangsung</div>
            </div>

            <div class="kpi-card kpi-teal">
                <div class="kpi-icon"><i class="fas fa-history"></i></div>
                <div class="kpi-value">{{ $completedSaya }}</div>
                <div class="kpi-label">Selesai</div>
            </div>

        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════
         TABEL PEMINJAMAN HARI INI
    ══════════════════════════════════════════════════════════ --}}
    <div class="today-card card">
        <div class="today-header">
            <h6>
                <i class="fas fa-calendar-day me-2"></i>
                Peminjaman Hari Ini
            </h6>
            <span class="date-badge">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table today-table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:44px;">No</th>
                            @if(auth()->user()->role == 'admin')
                                <th>Pengaju</th>
                            @endif
                            <th>Ruangan / Tipe</th>
                            <th>Jam</th>
                            <th>Keperluan</th>
                            <th>Peran / Unit</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $bookingsTodayRows = auth()->user()->role == 'admin'
                                ? $bookingsTodayList
                                : ($bookingsTodayListSaya ?? collect());
                        @endphp

                        @forelse($bookingsTodayRows as $index => $booking)
                            @php
                                $date    = \Carbon\Carbon::parse($booking->tanggal_pinjam)->format('Y-m-d');
                                $start   = \Carbon\Carbon::parse($date . ' ' . $booking->waktu_mulai);
                                $end     = \Carbon\Carbon::parse($date . ' ' . $booking->waktu_selesai);
                                $isOngoing = now()->between($start, $end) && $booking->status === 'approved';

                                $statusMap = [
                                    'pending'          => ['badge-warning',   'Pending'],
                                    'payment_uploaded' => ['badge-info',      'Menunggu Verifikasi'],
                                    'approved'         => ['badge-success',   'Disetujui'],
                                    'rejected'         => ['badge-danger',    'Ditolak'],
                                    'completed'        => ['badge-secondary', 'Selesai'],
                                ];
                                [$badgeClass, $badgeLabel] = $statusMap[$booking->status]
                                    ?? ['badge-light', 'Dibatalkan'];
                            @endphp

                            <tr class="{{ $isOngoing ? 'tr-ongoing' : '' }}">
                                <td class="text-center text-muted">{{ $index + 1 }}</td>

                                @if(auth()->user()->role == 'admin')
                                    <td>
                                        <strong style="font-size:.875rem;">{{ $booking->user->name }}</strong><br>
                                        <small class="text-muted">{{ $booking->user->jenis_pengguna }}</small>
                                    </td>
                                @endif

                                <td>
                                    @if($booking->room)
                                        <div class="room-cell">
                                            <div class="room-code">{{ $booking->room->kode_ruangan }}</div>
                                            <div class="room-name">{{ $booking->room->nama_ruangan }}</div>
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size:.85rem;">
                                            <i class="fas fa-box me-1"></i> Barang Saja
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="time-badge {{ $isOngoing ? 'ongoing' : '' }}">
                                        {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }}
                                        –
                                        {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                                    </span>
                                    @if($isOngoing)
                                        <br>
                                        <small style="color:#d97706;font-size:.7rem;font-weight:600;">
                                            ● Sedang berlangsung
                                        </small>
                                    @endif
                                </td>

                                <td style="max-width:180px;">
                                    {{ Str::limit($booking->keperluan, 45) }}
                                </td>

                                <td style="color:#64748b;font-size:.85rem;">
                                    {{ $booking->role_unit ?: '—' }}
                                </td>

                                <td class="text-center">
                                    <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role == 'admin' ? 7 : 6 }}">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times text-muted"></i>
                                        <p>Tidak ada peminjaman hari ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection