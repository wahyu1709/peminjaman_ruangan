<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $booking->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #00bfa5;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #00bfa5;
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .content {
            margin-top: 20px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details th, .invoice-details td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .total {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
        }
        .bank-info {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE PEMINJAMAN RUANGAN</h1>
        <p>Fakultas Ilmu Keperawatan Universitas Indonesia</p>
        <p>Gedung A Lantai 2, Rumpun Ilmu Kesehatan (RIK), Kampus UI Depok,
Jl. Prof. Dr. Bahder Djohan, Kampus UI Depok, Pondok Cina, Kecamatan Beji, Kota Depok, Jawa Barat 16424, Indonesia.</p>
    </div>
    
    <div class="content">
        <!-- Nomor & Tanggal -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <div>
                <strong>Nomor Invoice:</strong><br>
                {{ $booking->invoice_number }}
            </div>
            <div>
                <strong>Tanggal:</strong><br>
                {{ \Carbon\Carbon::parse($booking->created_at)->isoFormat('D MMMM YYYY') }}
            </div>
        </div>

        <!-- Data Peminjam -->
        <div class="invoice-details">
            <h3>Data Peminjam</h3>
            <table>
                <tr>
                    <td><strong>Nama:</strong></td>
                    <td>{{ $booking->user->name }}</td>
                </tr>
                <tr>
                    <td><strong>NIM:</strong></td>
                    <td>{{ $booking->user->nim_nip }}</td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td>{{ $booking->user->email }}</td>
                </tr>
            </table>
        </div>

        <!-- Detail Peminjaman -->
        <div class="invoice-details">
            <h3>Detail Peminjaman</h3>
            <table>
                <tr>
                    <td><strong>Ruangan:</strong></td>
                    <td>{{ $booking->room->kode_ruangan }} - {{ $booking->room->nama_ruangan }}</td>
                </tr>
                <tr>
                    <td><strong>Lokasi:</strong></td>
                    <td>{{ $booking->room->lokasi }}</td>
                </tr>
                <tr>
                    <td><strong>Tanggal Pinjam:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('dddd, D MMMM YYYY') }}</td>
                </tr>
                <tr>
                    <td><strong>Waktu:</strong></td>
                    <td>{{ $booking->waktu_mulai }} - {{ $booking->waktu_selesai }}</td>
                </tr>
                <tr>
                    <td><strong>Keperluan:</strong></td>
                    <td>{{ $booking->keperluan }}</td>
                </tr>
            </table>
        </div>

        <!-- Rincian Biaya -->
        <div class="invoice-details">
            <h3>Rincian Biaya</h3>
            <table>
                <tr>
                    <td>Harga Sewa/Hari</td>
                    <td>Rp {{ number_format($booking->room->harga_sewa_per_hari, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Durasi</td>
                    <td>1 hari</td>
                </tr>
                <tr>
                    <td><strong>Total yang Harus Dibayar</strong></td>
                    <td><strong>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Total -->
        <div class="total">
            TOTAL: Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
        </div>

        <!-- Informasi Bank -->
        <div class="bank-info">
            <h4>Informasi Pembayaran</h4>
            <p><strong>Bank XYZ</strong><br>
            Atas Nama: Fakultas Ilmu Keperawatan UI<br>
            Nomor Rekening: 1234567890</p>
            <p><em>Silakan transfer sesuai nominal di atas dan upload bukti pembayaran.</em></p>
        </div>

        <div class="footer">
            Invoice ini berlaku selama 3 hari sejak tanggal penerbitan.
        </div>
    </div>
</body>
</html>