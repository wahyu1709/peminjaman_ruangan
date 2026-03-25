<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 9pt;
    color: #1e293b;
    line-height: 1.5;
}

/* ── Page ─────────────────────────────────────────── */
.page { padding: 28px 32px 20px; }

/* ── Letterhead ───────────────────────────────────── */
.letterhead {
    border-bottom: 3px solid #0f2d5e;
    padding-bottom: 10px;
    margin-bottom: 18px;
}

.lh-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.lh-org {
    font-size: 8pt;
    color: #64748b;
    line-height: 1.6;
}

.lh-org strong {
    font-size: 10.5pt;
    color: #0f2d5e;
    display: block;
    margin-bottom: 1px;
}

.lh-doc {
    text-align: right;
    font-size: 7.5pt;
    color: #94a3b8;
}

.doc-title {
    font-size: 13pt;
    font-weight: bold;
    color: #0f2d5e;
    text-align: center;
    margin: 12px 0 4px;
    text-transform: uppercase;
    letter-spacing: .04em;
}

.doc-subtitle {
    font-size: 8.5pt;
    color: #64748b;
    text-align: center;
    margin-bottom: 0;
}

/* ── KPI summary row ──────────────────────────────── */
.kpi-row {
    display: flex;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 20px;
}

.kpi-cell {
    flex: 1;
    padding: 10px 8px;
    text-align: center;
    border-right: 1px solid #cbd5e1;
}
.kpi-cell:last-child { border-right: none; }

.kpi-cell .kv {
    font-size: 14pt;
    font-weight: bold;
    line-height: 1;
    margin-bottom: 3px;
}
.kpi-cell .kl {
    font-size: 6.5pt;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.kpi-blue   .kv { color: #1d4ed8; }
.kpi-green  .kv { color: #059669; }
.kpi-amber  .kv { color: #d97706; }
.kpi-red    .kv { color: #dc2626; }
.kpi-teal   .kv { color: #0891b2; }
.kpi-purple .kv { color: #7c3aed; }

/* ── Section ──────────────────────────────────────── */
.section {
    margin-bottom: 20px;
    page-break-inside: avoid;
}

.section-title {
    font-size: 9pt;
    font-weight: bold;
    color: #fff;
    background: #0f2d5e;
    padding: 5px 10px;
    border-radius: 3px 3px 0 0;
    letter-spacing: .02em;
}

/* ── Tables ───────────────────────────────────────── */
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8.5pt;
}

thead tr {
    background: #f1f5f9;
}

th {
    padding: 6px 9px;
    text-align: left;
    font-size: 7.5pt;
    font-weight: bold;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: .05em;
    border: 1px solid #cbd5e1;
}

td {
    padding: 5px 9px;
    border: 1px solid #e2e8f0;
    color: #334155;
}

tr:nth-child(even) td { background: #f8fafc; }

.tr-top td { background: #eff6ff !important; font-weight: bold; }

.td-r  { text-align: right; }
.td-c  { text-align: center; }
.td-n  { font-weight: 600; color: #0f172a; }
.td-muted { color: #94a3b8; }

/* ── Two col layout ───────────────────────────────── */
.two-col { display: flex; gap: 16px; }
.two-col > div { flex: 1; min-width: 0; }

/* ── Notes / footer ───────────────────────────────── */
.doc-footer {
    margin-top: 24px;
    padding-top: 8px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    font-size: 7pt;
    color: #94a3b8;
}

.note {
    font-size: 7.5pt;
    color: #64748b;
    margin-top: 5px;
    font-style: italic;
}

/* ── Status dots ──────────────────────────────────── */
.dot {
    display: inline-block;
    width: 8px; height: 8px;
    border-radius: 50%;
    margin-right: 4px;
    vertical-align: middle;
}
</style>
</head>
<body>
<div class="page">

{{-- ══════════════════════════════════════════════════
     KOP SURAT
══════════════════════════════════════════════════ --}}
<div class="letterhead">
    <div class="lh-top">
        <div class="lh-org">
            <strong>Fakultas Ilmu Keperawatan</strong>
            Universitas Indonesia<br>
            Sistem Manajemen Peminjaman Ruangan &amp; Barang
        </div>
        <div class="lh-doc">
            Digenerate: {{ $generated_at }}<br>
            Periode: <strong>{{ $period }}</strong>
        </div>
    </div>
    <div class="doc-title">Laporan Statistik Peminjaman</div>
    <div class="doc-subtitle">Rekap data peminjaman ruangan dan barang inventaris — {{ $period }}</div>
</div>

{{-- ══════════════════════════════════════════════════
     KPI RINGKASAN
══════════════════════════════════════════════════ --}}
@php
    $sd           = $status_data ?? [];
    $monthVals    = $monthly_data['data']   ?? [];
    $monthLabels  = $monthly_data['labels'] ?? [];
    $totalAll     = array_sum($monthVals);
    $approved     = $sd['approved']        ?? 0;
    $completed    = $sd['completed']       ?? 0;
    $pending      = $sd['pending']         ?? 0;
    $rejected     = $sd['rejected']        ?? 0;
    $inventoryOnly= $sd['inventory_only']  ?? 0;
    $revData      = $revenue_data['data']  ?? array_fill(0, 12, 0);
    $revTotal     = $revenue_data['total'] ?? array_sum($revData);
    $internal     = $user_type_data['internal'] ?? 0;
    $umum         = $user_type_data['umum']     ?? 0;
    $approvalRate = $totalAll > 0 ? round(($approved + $completed) / $totalAll * 100) : 0;

    function fRp($v) {
        if ($v >= 1000000) return 'Rp ' . number_format($v/1000000, 1, ',', '.') . ' jt';
        if ($v >= 1000)    return 'Rp ' . number_format($v/1000, 0, ',', '.') . ' rb';
        return 'Rp ' . number_format($v, 0, ',', '.');
    }
@endphp

<div class="kpi-row">
    <div class="kpi-cell kpi-blue">
        <div class="kv">{{ $totalAll }}</div>
        <div class="kl">Total Peminjaman</div>
    </div>
    <div class="kpi-cell kpi-green">
        <div class="kv">{{ $approved + $completed }}</div>
        <div class="kl">Disetujui</div>
    </div>
    <div class="kpi-cell kpi-amber">
        <div class="kv">{{ $pending }}</div>
        <div class="kl">Pending</div>
    </div>
    <div class="kpi-cell kpi-red">
        <div class="kv">{{ $rejected }}</div>
        <div class="kl">Ditolak</div>
    </div>
    <div class="kpi-cell kpi-teal">
        <div class="kv">{{ fRp($revTotal) }}</div>
        <div class="kl">Total Pendapatan</div>
    </div>
    <div class="kpi-cell kpi-purple">
        <div class="kv">{{ $approvalRate }}%</div>
        <div class="kl">Tingkat Persetujuan</div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     1. TREN PEMINJAMAN PER BULAN
══════════════════════════════════════════════════ --}}
<div class="section">
    <div class="section-title">1. Tren Peminjaman per Bulan — {{ $monthly_data['year'] }}</div>
    @php
        $peakIdx = array_search(max($monthVals), $monthVals);
    @endphp
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="td-r">Jumlah Peminjaman</th>
                <th class="td-r">% dari Total</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($monthLabels as $i => $label)
                @php
                    $v   = $monthVals[$i] ?? 0;
                    $pct = $totalAll > 0 ? round($v / $totalAll * 100, 1) : 0;
                    $isP = ($i === $peakIdx && $v > 0);
                @endphp
                <tr class="{{ $isP ? 'tr-top' : '' }}">
                    <td>{{ $label }}</td>
                    <td class="td-r td-n">{{ $v }}</td>
                    <td class="td-r">{{ $pct }}%</td>
                    <td>
                        @if ($isP)
                            ★ Bulan Tersibuk
                        @elseif ($v === 0)
                            <span class="td-muted">—</span>
                        @else
                            &nbsp;
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#f1f5f9;">
                <td><strong>Total</strong></td>
                <td class="td-r td-n"><strong>{{ $totalAll }}</strong></td>
                <td class="td-r"><strong>100%</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- ══════════════════════════════════════════════════
     2 & 3. STATUS + INTERNAL VS UMUM (dua kolom)
══════════════════════════════════════════════════ --}}
<div class="section">
    <div class="two-col">

        {{-- Distribusi Status --}}
        <div>
            <div class="section-title">2. Distribusi Status</div>
            @php
                $statusMap = [
                    'approved'         => ['Disetujui',           '#059669'],
                    'completed'        => ['Selesai',             '#0891b2'],
                    'pending'          => ['Pending',             '#d97706'],
                    'payment_uploaded' => ['Menunggu Verifikasi', '#7c3aed'],
                    'rejected'         => ['Ditolak',             '#dc2626'],
                    'cancelled'        => ['Dibatalkan',          '#94a3b8'],
                ];
                $stTotal = array_sum(array_map(fn($k) => $sd[$k] ?? 0, array_keys($statusMap)));
            @endphp
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th class="td-r">Jumlah</th>
                        <th class="td-r">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($statusMap as $key => $info)
                        @php
                            $v   = $sd[$key] ?? 0;
                            $pct = $stTotal > 0 ? round($v / $stTotal * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td>
                                <span class="dot" style="background:{{ $info[1] }};"></span>
                                {{ $info[0] }}
                            </td>
                            <td class="td-r td-n">{{ $v }}</td>
                            <td class="td-r">{{ $pct }}%</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f1f5f9;">
                        <td><strong>Total</strong></td>
                        <td class="td-r"><strong>{{ $stTotal }}</strong></td>
                        <td class="td-r"><strong>100%</strong></td>
                    </tr>
                </tfoot>
            </table>
            <p class="note">Tingkat persetujuan: {{ $approvalRate }}% (disetujui + selesai)</p>
        </div>

        {{-- Internal vs Umum --}}
        <div>
            <div class="section-title">3. Jenis Pengguna</div>
            @php
                $utTotal = $internal + $umum;
            @endphp
            <table>
                <thead>
                    <tr>
                        <th>Jenis Pengguna</th>
                        <th class="td-r">Jumlah</th>
                        <th class="td-r">%</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="tr-top">
                        <td>
                            <span class="dot" style="background:#0891b2;"></span>
                            Internal (Civitas FIK UI)
                        </td>
                        <td class="td-r td-n">{{ $internal }}</td>
                        <td class="td-r">{{ $utTotal > 0 ? round($internal/$utTotal*100, 1) : 0 }}%</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="dot" style="background:#db2777;"></span>
                            Umum
                        </td>
                        <td class="td-r td-n">{{ $umum }}</td>
                        <td class="td-r">{{ $utTotal > 0 ? round($umum/$utTotal*100, 1) : 0 }}%</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="dot" style="background:#8b5cf6;"></span>
                            Pinjam Barang Saja
                        </td>
                        <td class="td-r td-n">{{ $inventoryOnly }}</td>
                        <td class="td-r">
                            {{ $totalAll > 0 ? round($inventoryOnly/$totalAll*100, 1) : 0 }}%
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr style="background:#f1f5f9;">
                        <td><strong>Total</strong></td>
                        <td class="td-r"><strong>{{ $utTotal }}</strong></td>
                        <td class="td-r"><strong>100%</strong></td>
                    </tr>
                </tfoot>
            </table>
            <p class="note">*Pinjam barang saja dihitung dari total peminjaman</p>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════
     4. TOP RUANGAN
══════════════════════════════════════════════════ --}}
<div class="section">
    <div class="section-title">4. Top Ruangan Paling Sering Dipinjam</div>
    @php
        $rLabels = $top_rooms_data['labels'] ?? [];
        $rVals   = $top_rooms_data['data']   ?? [];
        $rColors = $top_rooms_data['colors'] ?? [];
        $rTotal  = array_sum($rVals);
    @endphp
    @if (count($rLabels) === 0)
        <table><tbody><tr><td class="td-muted" style="text-align:center;padding:16px;">
            Tidak ada data untuk periode ini.
        </td></tr></tbody></table>
    @else
        <table>
            <thead>
                <tr>
                    <th class="td-c">No</th>
                    <th>Nama Ruangan</th>
                    <th class="td-c">Tipe</th>
                    <th class="td-r">Jumlah Booking</th>
                    <th class="td-r">% dari Top {{ count($rLabels) }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rLabels as $i => $name)
                    @php
                        $v    = $rVals[$i]   ?? 0;
                        $col  = $rColors[$i] ?? '#4361ee';
                        $paid = ($col === '#ec4899' || $col === '#db2777');
                        $pct  = $rTotal > 0 ? round($v / $rTotal * 100, 1) : 0;
                    @endphp
                    <tr class="{{ $i === 0 ? 'tr-top' : '' }}">
                        <td class="td-c">
                            @if ($i === 0) ★
                            @elseif ($i === 1) 2
                            @elseif ($i === 2) 3
                            @else {{ $i + 1 }}
                            @endif
                        </td>
                        <td>{{ $name }}</td>
                        <td class="td-c">
                            <span class="dot" style="background:{{ $paid ? '#db2777' : '#1d4ed8' }};"></span>
                            {{ $paid ? 'Berbayar' : 'Gratis' }}
                        </td>
                        <td class="td-r td-n">{{ $v }}×</td>
                        <td class="td-r">{{ $pct }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f1f5f9;">
                    <td colspan="3"><strong>Total (Top {{ count($rLabels) }})</strong></td>
                    <td class="td-r"><strong>{{ $rTotal }}×</strong></td>
                    <td class="td-r"><strong>100%</strong></td>
                </tr>
            </tfoot>
        </table>
    @endif
</div>

{{-- ══════════════════════════════════════════════════
     5. PENDAPATAN PER BULAN
══════════════════════════════════════════════════ --}}
<div class="section">
    <div class="section-title">5. Pendapatan per Bulan — {{ $revenue_data['year'] ?? $monthly_data['year'] }}</div>
    @php
        $revLabels  = $revenue_data['labels'] ?? $monthLabels;
        $revAvg     = count(array_filter($revData)) > 0
                    ? $revTotal / count(array_filter($revData))
                    : 0;
        $revPeakIdx = array_search(max($revData), $revData);
    @endphp
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="td-r">Pendapatan</th>
                <th class="td-r">% dari Total</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($revLabels as $i => $lbl)
                @php
                    $v   = $revData[$i] ?? 0;
                    $pct = $revTotal > 0 ? round($v / $revTotal * 100, 1) : 0;
                    $isP = ($i === $revPeakIdx && $v > 0);
                @endphp
                <tr class="{{ $isP ? 'tr-top' : '' }}">
                    <td>{{ $lbl }}</td>
                    <td class="td-r td-n">
                        @if ($v > 0)
                            Rp {{ number_format($v, 0, ',', '.') }}
                        @else
                            <span class="td-muted">—</span>
                        @endif
                    </td>
                    <td class="td-r">{{ $v > 0 ? $pct.'%' : '—' }}</td>
                    <td>{{ $isP ? '★ Pendapatan Tertinggi' : '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#f1f5f9;">
                <td><strong>Total</strong></td>
                <td class="td-r"><strong>Rp {{ number_format($revTotal, 0, ',', '.') }}</strong></td>
                <td class="td-r"><strong>100%</strong></td>
                <td><strong>Rata-rata: {{ fRp(round($revAvg)) }}/bln</strong></td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- ══════════════════════════════════════════════════
     6. TOP BARANG INVENTARIS
══════════════════════════════════════════════════ --}}
<div class="section">
    <div class="section-title">6. Top Barang Inventaris Dipinjam</div>
    @php
        $iLabels  = $inventory_data['labels']   ?? [];
        $iQty     = $inventory_data['data']     ?? [];
        $iBooking = $inventory_data['bookings'] ?? [];
        $iTotal   = array_sum($iQty);
    @endphp
    @if (count($iLabels) === 0)
        <table><tbody><tr><td class="td-muted" style="text-align:center;padding:16px;">
            Tidak ada data inventaris untuk periode ini.
        </td></tr></tbody></table>
    @else
        <table>
            <thead>
                <tr>
                    <th class="td-c">No</th>
                    <th>Nama Barang</th>
                    <th class="td-r">Total Unit</th>
                    <th class="td-r">Jumlah Booking</th>
                    <th class="td-r">% dari Total Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($iLabels as $i => $name)
                    @php
                        $q   = $iQty[$i]     ?? 0;
                        $bk  = $iBooking[$i] ?? 0;
                        $pct = $iTotal > 0 ? round($q / $iTotal * 100, 1) : 0;
                    @endphp
                    <tr class="{{ $i === 0 ? 'tr-top' : '' }}">
                        <td class="td-c">{{ $i === 0 ? '★' : $i + 1 }}</td>
                        <td>{{ $name }}</td>
                        <td class="td-r td-n">{{ $q }} unit</td>
                        <td class="td-r">{{ $bk }}×</td>
                        <td class="td-r">{{ $pct }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f1f5f9;">
                    <td colspan="2"><strong>Total</strong></td>
                    <td class="td-r"><strong>{{ $iTotal }} unit</strong></td>
                    <td class="td-r"><strong>{{ array_sum($iBooking) }}×</strong></td>
                    <td class="td-r"><strong>100%</strong></td>
                </tr>
            </tfoot>
        </table>
    @endif
</div>

{{-- ══════════════════════════════════════════════════
     7. ANALISIS WAKTU
══════════════════════════════════════════════════ --}}
<div class="section">
    <div class="two-col">

        {{-- Distribusi per Jam --}}
        <div>
            <div class="section-title">7a. Distribusi per Jam</div>
            @php
                $hourly = $time_data['hourly'] ?? [];
                $hTotal = array_sum($hourly);
                $hMax   = max(array_merge(array_values($hourly), [1]));
                $avgDur = $time_data['avg_duration'] ?? 0;
            @endphp
            <table>
                <thead>
                    <tr>
                        <th>Jam</th>
                        <th class="td-r">Booking</th>
                        <th class="td-r">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hourly as $hr => $cnt)
                        @php
                            $pct  = $hTotal > 0 ? round($cnt / $hTotal * 100, 1) : 0;
                            $isPk = ($cnt === $hMax && $cnt > 0);
                        @endphp
                        <tr class="{{ $isPk ? 'tr-top' : '' }}">
                            <td>{{ $hr }}:00 – {{ $hr+1 }}:00 {{ $isPk ? '★' : '' }}</td>
                            <td class="td-r td-n">{{ $cnt }}</td>
                            <td class="td-r">{{ $cnt > 0 ? $pct.'%' : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f1f5f9;">
                        <td><strong>Total</strong></td>
                        <td class="td-r"><strong>{{ $hTotal }}</strong></td>
                        <td class="td-r"><strong>100%</strong></td>
                    </tr>
                </tfoot>
            </table>
            <p class="note">Durasi rata-rata: {{ $avgDur }} menit per sesi</p>
        </div>

        {{-- Distribusi per Hari --}}
        <div>
            <div class="section-title">7b. Distribusi per Hari</div>
            @php
                $weekday = $time_data['weekday'] ?? [];
                $wTotal  = array_sum($weekday);
                $wMax    = max(array_merge(array_values($weekday), [1]));
            @endphp
            <table>
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th class="td-r">Booking</th>
                        <th class="td-r">%</th>
                        <th class="td-c">Ket.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weekday as $day => $cnt)
                        @php
                            $pct    = $wTotal > 0 ? round($cnt / $wTotal * 100, 1) : 0;
                            $isPk   = ($cnt === $wMax && $cnt > 0);
                            $isWknd = in_array($day, ['Sabtu', 'Minggu']);
                        @endphp
                        <tr class="{{ $isPk ? 'tr-top' : '' }}">
                            <td style="{{ $isWknd ? 'color:#dc2626;' : '' }}">
                                {{ $day }}
                            </td>
                            <td class="td-r td-n">{{ $cnt }}</td>
                            <td class="td-r">{{ $cnt > 0 ? $pct.'%' : '—' }}</td>
                            <td class="td-c">
                                @if ($isPk) ★
                                @elseif ($isWknd)
                                    <span style="color:#dc2626;font-size:7pt;">+biaya</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f1f5f9;">
                        <td><strong>Total</strong></td>
                        <td class="td-r"><strong>{{ $wTotal }}</strong></td>
                        <td class="td-r"><strong>100%</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <p class="note">Merah = Sabtu/Minggu (dikenakan biaya tambahan)</p>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════ --}}
<div class="doc-footer">
    <span>Digenerate otomatis oleh Sistem Peminjaman Ruangan &amp; Barang — FIK Universitas Indonesia</span>
    <span>{{ $generated_at }}</span>
</div>

</div><!-- /.page -->
</body>
</html>