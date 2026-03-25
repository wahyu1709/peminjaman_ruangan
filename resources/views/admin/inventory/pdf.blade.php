<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:8.5pt; color:#1e293b; }

.letterhead {
    border-bottom:3px solid #7c3aed; padding-bottom:10px; margin-bottom:16px;
}
.lh-top { display:flex; justify-content:space-between; align-items:flex-start; }
.lh-org strong { font-size:10.5pt; color:#5b21b6; display:block; margin-bottom:2px; }
.lh-org { font-size:8pt; color:#64748b; line-height:1.6; }
.lh-doc { text-align:right; font-size:7.5pt; color:#94a3b8; }

.doc-title { font-size:13pt; font-weight:bold; color:#5b21b6; text-align:center; margin:10px 0 4px; text-transform:uppercase; letter-spacing:.04em; }
.doc-subtitle { font-size:8pt; color:#64748b; text-align:center; margin-bottom:14px; }

table { width:100%; border-collapse:collapse; font-size:8pt; }
thead tr { background:#5b21b6; }
th { padding:7px 9px; text-align:left; font-weight:bold; color:#fff; font-size:7.5pt; text-transform:uppercase; letter-spacing:.04em; }
td { padding:6px 9px; border-bottom:1px solid #f1f5f9; color:#334155; }
tr:nth-child(even) td { background:#faf5ff; }
tr:last-child td { border-bottom:none; }

.td-r  { text-align:right; }
.td-c  { text-align:center; }
.td-bold { font-weight:600; color:#0f172a; }

.badge-active   { background:#dcfce7; color:#166534; padding:2px 7px; border-radius:10px; font-size:7pt; font-weight:bold; }
.badge-inactive { background:#f1f5f9; color:#475569; padding:2px 7px; border-radius:10px; font-size:7pt; font-weight:bold; }

tfoot tr td { background:#f1f5f9; font-weight:bold; border-top:2px solid #e2e8f0; }

.doc-footer { margin-top:16px; padding-top:8px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; font-size:7pt; color:#94a3b8; }

.section-title {
    font-size:8pt; font-weight:bold; color:#5b21b6;
    text-transform:uppercase; letter-spacing:.05em;
    margin:12px 0 6px; padding-bottom:4px;
    border-bottom:1.5px solid #ede9fe;
}
</style>
</head>
<body>

<div class="letterhead">
    <div class="lh-top">
        <div class="lh-org">
            <strong>Fakultas Ilmu Keperawatan</strong>
            Universitas Indonesia<br>
            Sistem Manajemen Peminjaman Ruangan &amp; Barang
        </div>
        <div class="lh-doc">
            Digenerate: {{ $period }}<br>
            @if($category !== 'all')
                Kategori: <strong>{{ $category }}</strong>
            @else
                Semua Kategori
            @endif
        </div>
    </div>
    <div class="doc-title">Laporan Data Inventaris</div>
    <div class="doc-subtitle">Daftar barang inventaris — per {{ $period }}</div>
</div>

@php
    $grouped     = $items->groupBy('category');
    $totalStok   = $items->sum('stock');
    $totalAvail  = $items->sum('stock_available');
    $totalUsed   = $totalStok - $totalAvail;
@endphp

{{-- Summary --}}
<table style="margin-bottom:14px;width:auto;">
    <tr>
        <td style="padding:6px 14px;background:#ede9fe;border-radius:6px;margin-right:8px;">
            <div style="font-size:6.5pt;color:#5b21b6;font-weight:700;text-transform:uppercase;">Total Jenis</div>
            <div style="font-size:13pt;font-weight:bold;color:#5b21b6;">{{ $items->count() }}</div>
        </td>
        <td style="width:10px;"></td>
        <td style="padding:6px 14px;background:#dcfce7;border-radius:6px;">
            <div style="font-size:6.5pt;color:#166534;font-weight:700;text-transform:uppercase;">Total Stok</div>
            <div style="font-size:13pt;font-weight:bold;color:#166534;">{{ $totalStok }}</div>
        </td>
        <td style="width:10px;"></td>
        <td style="padding:6px 14px;background:#fef9c3;border-radius:6px;">
            <div style="font-size:6.5pt;color:#854d0e;font-weight:700;text-transform:uppercase;">Tersedia</div>
            <div style="font-size:13pt;font-weight:bold;color:#854d0e;">{{ $totalAvail }}</div>
        </td>
        <td style="width:10px;"></td>
        <td style="padding:6px 14px;background:#fee2e2;border-radius:6px;">
            <div style="font-size:6.5pt;color:#991b1b;font-weight:700;text-transform:uppercase;">Dipinjam</div>
            <div style="font-size:13pt;font-weight:bold;color:#991b1b;">{{ $totalUsed }}</div>
        </td>
    </tr>
</table>

{{-- Tabel per kategori --}}
@foreach($grouped as $cat => $catItems)
    @php $catItem = $catItems->first(); @endphp
    <div class="section-title">{{ $catItem->category_name }}</div>
    <table style="margin-bottom:12px;">
        <thead>
            <tr>
                <th style="width:30px;" class="td-c">No</th>
                <th>Nama Barang</th>
                <th class="td-r">Harga/Hari</th>
                <th class="td-c">Stok Total</th>
                <th class="td-c">Tersedia</th>
                <th class="td-c">Dipinjam</th>
                <th class="td-c">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($catItems as $i => $item)
            <tr>
                <td class="td-c">{{ $i + 1 }}</td>
                <td class="td-bold">{{ $item->name }}</td>
                <td class="td-r">
                    @if($item->price_per_day > 0)
                        Rp {{ number_format($item->price_per_day, 0, ',', '.') }}
                    @else
                        <span style="color:#94a3b8;">Gratis</span>
                    @endif
                </td>
                <td class="td-c">{{ $item->stock }}</td>
                <td class="td-c" style="color:#10b981;font-weight:600;">{{ $item->stock_available }}</td>
                <td class="td-c" style="color:#f59e0b;font-weight:600;">{{ $item->stock - $item->stock_available }}</td>
                <td class="td-c">
                    @if($item->is_active)
                        <span class="badge-active">Aktif</span>
                    @else
                        <span class="badge-inactive">Nonaktif</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="td-r">Subtotal {{ $catItems->count() }} item:</td>
                <td class="td-c">{{ $catItems->sum('stock') }}</td>
                <td class="td-c" style="color:#10b981;">{{ $catItems->sum('stock_available') }}</td>
                <td class="td-c" style="color:#f59e0b;">{{ $catItems->sum('stock') - $catItems->sum('stock_available') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
@endforeach

<div class="doc-footer">
    <span>FIK Universitas Indonesia — Sistem Peminjaman Ruangan &amp; Barang</span>
    <span>{{ $period }}</span>
</div>

</body>
</html>