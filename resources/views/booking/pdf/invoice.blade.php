<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $booking->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 30px;
            font-size: 11px;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 0;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            /* color: white; */
            padding: 25px 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 20px;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        
        .header .university {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .header .address {
            font-size: 9px;
            opacity: 0.9;
            line-height: 1.4;
        }
        
        /* Content */
        .content {
            padding: 30px;
        }
        
        /* Invoice Info */
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        
        .invoice-info-left,
        .invoice-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .invoice-info-right {
            text-align: right;
        }
        
        .invoice-label {
            font-weight: 600;
            color: #667eea;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        
        .invoice-value {
            font-size: 12px;
            font-weight: 600;
            color: #333;
        }
        
        /* Section */
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 12px;
            padding-bottom: 5px;
            border-bottom: 2px solid #667eea;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table tr {
            border-bottom: 1px solid #f0f0f0;
        }
        
        table td {
            padding: 8px 0;
            font-size: 11px;
        }
        
        table td:first-child {
            color: #666;
            width: 40%;
        }
        
        table td:last-child {
            color: #333;
            font-weight: 500;
        }
        
        /* Breakdown Table */
        .breakdown-table {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        .breakdown-table table tr {
            border: none;
        }
        
        .breakdown-table td {
            padding: 6px 0;
        }
        
        .breakdown-row-base {
            color: #333;
        }
        
        .breakdown-row-discount {
            color: #28a745;
            font-weight: 600;
        }
        
        .breakdown-row-subtotal {
            font-weight: 700;
            border-top: 1px solid #ddd;
            padding-top: 8px !important;
        }
        
        .breakdown-row-additional {
            color: #fd7e14;
            font-weight: 600;
        }
        
        .breakdown-row-total {
            font-size: 13px;
            font-weight: 700;
            color: #667eea;
            border-top: 2px solid #667eea;
            padding-top: 10px !important;
        }
        
        /* Bank Info */
        .bank-info {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-top: 25px;
        }
        
        .bank-info h4 {
            color: #856404;
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .bank-info p {
            margin: 5px 0;
            font-size: 11px;
            color: #856404;
        }
        
        .bank-info strong {
            color: #664d03;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
        }
        
        .footer-note {
            font-size: 10px;
            color: #666;
            font-style: italic;
            margin-bottom: 8px;
        }
        
        .footer-stamp {
            font-size: 9px;
            color: #999;
        }
        
        /* Badge */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        /* Helpers */
        .text-right {
            text-align: right;
        }
        
        .text-bold {
            font-weight: 700;
        }
        
        .mt-20 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>INVOICE PEMINJAMAN RUANGAN</h1>
            <p class="university">Fakultas Ilmu Keperawatan Universitas Indonesia</p>
            <p class="address">
                Gedung A Lantai 2, RIK, Kampus UI Depok, Jl. Prof. Dr. Bahder Djohan,<br>
                Pondok Cina, Kec. Beji, Kota Depok, Jawa Barat 16424
            </p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Invoice Info -->
            <div class="invoice-info">
                <div class="invoice-info-left">
                    <div class="invoice-label">Nomor Invoice</div>
                    <div class="invoice-value">{{ $booking->invoice_number }}</div>
                </div>
                <div class="invoice-info-right">
                    <div class="invoice-label">Tanggal Terbit</div>
                    <div class="invoice-value">{{ \Carbon\Carbon::parse($booking->created_at)->isoFormat('D MMMM YYYY') }}</div>
                </div>
            </div>

            <!-- Data Peminjam -->
            <div class="section">
                <div class="section-title">Data Peminjam</div>
                <table>
                    <tr>
                        <td>Nama</td>
                        <td class="text-bold">{{ $booking->user->name }}</td>
                    </tr>
                    <tr>
                        <td>NIM/NIP</td>
                        <td>{{ $booking->user->nim_nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>{{ $booking->user->email }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Pengguna</td>
                        <td>
                            {{ ucfirst($booking->user->jenis_pengguna) }}
                            @if($booking->user->jenis_pengguna !== 'umum')
                                <span class="badge badge-success">Civitas FIK UI</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Instansi</td>
                        <td>
                            @if ($booking->user->jenis_pengguna === 'umum')
                                {{ $booking->user->instansi ?? '-' }}
                            @else
                                FIK UI 
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Detail Peminjaman -->
            <div class="section">
                <div class="section-title">Detail Peminjaman</div>
                <table>
                    <tr>
                        <td>Ruangan</td>
                        <td class="text-bold">{{ $booking->room->kode_ruangan }} - {{ $booking->room->nama_ruangan }}</td>
                    </tr>
                    <tr>
                        <td>Lokasi</td>
                        <td>{{ $booking->room->lokasi }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pinjam</td>
                        <td class="text-bold">{{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('dddd, D MMMM YYYY') }}</td>
                    </tr>
                    <tr>
                        <td>Waktu</td>
                        <td>{{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td>Keperluan</td>
                        <td>{{ $booking->keperluan }}</td>
                    </tr>
                    @if($booking->role_unit)
                    <tr>
                        <td>Unit Kerja</td>
                        <td>{{ $booking->role_unit }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- Rincian Biaya -->
            <div class="section">
                <div class="section-title">Rincian Biaya</div>
                
                @php
                    $hargaDasar = $booking->room->harga_sewa_per_hari ?? 0;
                    $tanggal = \Carbon\Carbon::parse($booking->tanggal_pinjam);
                    $isWeekend = $tanggal->isSaturday() || $tanggal->isSunday();
                    $namaHari = $tanggal->isoFormat('dddd');
                    
                    // Diskon 25% untuk Civitas FIK UI
                    $diskon = ($booking->user->jenis_pengguna !== 'umum') ? 0.25 : 0;
                    $nominalDiskon = $hargaDasar * $diskon;
                    $hargaSetelahDiskon = $hargaDasar - $nominalDiskon;
                    
                    // Biaya tambahan weekend
                    $biayaTambahan = 0;
                    if ($tanggal->isSaturday()) {
                        $biayaTambahan = 400000; // 100k cleaning + 300k technician
                    } elseif ($tanggal->isSunday()) {
                        $biayaTambahan = 500000; // 200k cleaning + 300k technician
                    }
                @endphp
                
                <div class="breakdown-table">
                    <table>
                        <tr class="breakdown-row-base">
                            <td>Harga Sewa Dasar ({{ $namaHari }})</td>
                            <td class="text-right">Rp {{ number_format($hargaDasar, 0, ',', '.') }}</td>
                        </tr>
                        
                        @if($diskon > 0)
                        <tr class="breakdown-row-discount">
                            <td>Diskon Civitas FIK UI (25%)</td>
                            <td class="text-right">- Rp {{ number_format($nominalDiskon, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="breakdown-row-subtotal">
                            <td>Subtotal Setelah Diskon</td>
                            <td class="text-right">Rp {{ number_format($hargaSetelahDiskon, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        
                        @if($biayaTambahan > 0)
                        <tr class="breakdown-row-additional">
                            <td>Biaya Tambahan Hari {{ $namaHari }}</td>
                            <td class="text-right">+ Rp {{ number_format($biayaTambahan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="font-size: 9px; color: #666; padding-top: 3px;">
                                <em>* Biaya kebersihan dan teknisi untuk hari libur</em>
                            </td>
                        </tr>
                        @endif
                        
                        <tr class="breakdown-row-total">
                            <td>TOTAL PEMBAYARAN</td>
                            <td class="text-right">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Informasi Pembayaran -->
            <div class="bank-info">
                <h4>Informasi Pembayaran</h4>
                <p><strong>Bank BNI Cabang Kampus UI Depok</strong></p>
                <p>Nomor Rekening: <strong>1273000535</strong></p>
                <p>Atas Nama: <strong>Universitas Indonesia FIK Non Biaya Pendidikan</strong></p>
                <p style="margin-top: 10px; font-size: 10px;">
                    <em>Transfer sesuai nominal di atas, lalu upload bukti pembayaran melalui sistem.</em>
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p class="footer-note">
                    Invoice ini berlaku selama <strong>3 hari sejak tanggal penerbitan</strong>.<br>
                    Pembayaran yang terlambat akan dikenakan pembatalan otomatis.
                </p>
                <p class="footer-stamp">
                    Dicetak pada: {{ now()->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }} WIB<br>
                    Sistem Peminjaman Ruangan FIK UI - {{ config('app.url') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>