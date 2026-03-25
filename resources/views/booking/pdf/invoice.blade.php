<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $booking->invoice_number }}</title>
    <style>
        @page {
            margin: 25mm 15mm;
            size: A4;
        }
        body {
            font-family: 'DejaVu Sans', 'Nunito', sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 12px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #1a4780;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header img {
            height: 60px;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #1a4780;
            font-size: 24px;
            margin: 5px 0;
            font-weight: bold;
        }
        .header h2 {
            color: #d62828;
            font-size: 18px;
            margin: 5px 0;
            font-weight: bold;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
        }
        .info-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 25px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        .divider {
            height: 1px;
            background: #1a4780;
            margin: 25px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #1a4780;
            color: white;
            text-align: left;
            padding: 10px 12px;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 11px;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-primary {
            color: #1a4780;
            font-weight: bold;
        }
        .text-success {
            color: #28a745;
            font-weight: bold;
        }
        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }
        .text-warning {
            color: #ffc107;
            font-weight: bold;
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .payment-info {
            background: #e3f2fd;
            border-left: 4px solid #1a4780;
            padding: 15px;
            margin: 25px 0;
            border-radius: 0 4px 4px 0;
        }
        .payment-info h4 {
            color: #1a4780;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 45%;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-weight: bold;
            font-size: 12px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
            font-size: 10px;
            color: #6c757d;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .sk-reference {
            background: #fff8e1;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 12px;
            margin: 20px 0;
            font-size: 11px;
        }
        .sk-reference p {
            margin: 5px 0;
        }
        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Fakultas Ilmu Keperawatan</h1>
            <h2>Universitas Indonesia</h2>
            <p>Universitas Indonesia Gedung A Lantai 2, Rumpun Ilmu Kesehatan (RIK)</p>
            <p>Jl. Prof. Dr. Bahder Djohan, Kampus UI Depok, Pondok Cina, Kecamatan Beji, Kota Depok, Jawa Barat 16424, Indonesia.</p>
            <p>Email: wd-sdm.fik@ui.ac.id | Website: nursing.ui.ac.id</p>
        </div>

        <!-- Invoice Info -->
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Nomor Invoice:</span>
                <span class="text-primary">{{ $booking->invoice_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal:</span>
                <span>{{ \Carbon\Carbon::parse($booking->created_at)->isoFormat('D MMMM YYYY') }}</span>
            </div>
        </div>

        <!-- SK Reference -->
        <div class="sk-reference">
            <p><strong>Dasar Hukum:</strong> Surat Keputusan Dekan FIK UI No. 411/SK/F12.D/UI/2026</p>
            <p><strong>Tanggal:</strong> 10 Februari 2026</p>
            <p><strong>Tentang:</strong> Tarif Sewa Ruangan dan Peralatan Laboratorium untuk Kegiatan Akademik dan Non-Akademik</p>
        </div>

        <!-- Pemohon Info -->
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Nama Pemohon:</span>
                <span>{{ $booking->user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Jenis Pengguna:</span>
                <span>
                    @if($booking->user->jenis_pengguna === 'mahasiswa')
                        <span>Mahasiswa FIK UI</span>
                    @elseif($booking->user->jenis_pengguna === 'dosen')
                        <span>Dosen FIK UI</span>
                    @elseif($booking->user->jenis_pengguna === 'staff')
                        <span>Staff FIK UI</span>
                    @else
                        <span>Pihak Eksternal</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Instansi:</span>
                <span>{{ $booking->user->instansi ?? 'FIK UI' }}</span>
            </div>
            @if(auth()->user()->jenis_pengguna !== 'umum')
                <div class="info-row">
                    <span class="info-label">NIM/NIP:</span>
                    <span>{{ $booking->user->nim_nip ?? '-' }}</span>
                </div>
            @endif
            <div class="info-row">
                <span class="info-label">No. HP:</span>
                <span>{{ $booking->user->phone ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span>{{ $booking->user->email }}</span>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Ruangan:</span>
                <span>
                    @if($booking->room)
                        <strong>{{ $booking->room->kode_ruangan }} - {{ $booking->room->nama_ruangan }}</strong><br>
                        <small class="text-muted">{{ $booking->room->lokasi }}</small>
                    @else
                        <span class="text-danger"><strong>TANPA RUANGAN</strong> (Pinjam Barang Saja)</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Pinjam:</span>
                <span>{{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('dddd, D MMMM YYYY') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Waktu:</span>
                <span>{{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }} WIB</span>
            </div>
            <div class="info-row">
                <span class="info-label">Peran/Unit:</span>
                <span>{{ $booking->role_unit ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Keperluan:</span>
                <span>{{ $booking->keperluan }}</span>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Price Breakdown -->
        <h3 style="color: #1a4780; margin: 20px 0 15px; font-size: 16px;">Rincian Biaya</h3>

        <table>
            <thead>
                <tr>
                    <th width="50%">Keterangan</th>
                    <th width="20%" class="text-center">Qty</th>
                    <th width="30%" class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <!-- Ruangan -->
                @if($booking->room)
                    <tr>
                        <td>
                            Sewa Ruangan:<br>
                            <strong>{{ $booking->room->kode_ruangan }} - {{ $booking->room->nama_ruangan }}</strong><br>
                            <small>{{ $booking->room->lokasi }}</small>
                        </td>
                        <td class="text-center">1 hari</td>
                        <td class="text-right">{{ number_format($booking->room->harga_sewa_per_hari ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    
                    <!-- Diskon -->
                    @php
                        $hargaDasar = $booking->room->harga_sewa_per_hari ?? 0;
                        $diskon = ($booking->user->jenis_pengguna !== 'umum') ? 0.25 : 0;
                        $nominalDiskon = $hargaDasar * $diskon;
                        $hargaSetelahDiskon = $hargaDasar - $nominalDiskon;
                    @endphp
                    
                    @if($nominalDiskon > 0)
                    <tr>
                        <td>
                            <span class="text-danger">Diskon 25% Civitas FIK UI</span><br>
                            <small>Sesuai SK No. 411 Pasal Ketiga</small>
                        </td>
                        <td class="text-center">-</td>
                        <td class="text-right text-danger">- {{ number_format($nominalDiskon, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                @endif

                <!-- Barang Inventaris -->
                @if($booking->inventories->count() > 0)
                    @if($booking->room)
                        <tr>
                            <td colspan="3" style="padding: 8px 0; font-weight: bold; background-color: #f8f9fa;">
                                BARANG INVENTARIS
                            </td>
                        </tr>
                    @endif
                    
                    @foreach($booking->inventories as $inventory)
                        <tr>
                            <td>
                                {{ $inventory->name }}<br>
                                {{-- <small class="text-muted">Kategori: {{ $inventory->category }}</small> --}}
                            </td>
                            <td class="text-center">{{ $inventory->pivot->quantity }}</td>
                            <td class="text-right">
                                {{ number_format($inventory->pivot->price_at_booking * $inventory->pivot->quantity, 0, ',', '.') }}
                                <br>
                                <small class="text-muted">
                                    (Rp {{ number_format($inventory->pivot->price_at_booking, 0, ',', '.') }}/hari)
                                </small>
                            </td>
                        </tr>
                    @endforeach
                @endif

                <!-- Biaya Tambahan -->
                @php
                    $tanggal = \Carbon\Carbon::parse($booking->tanggal_pinjam);
                    $biayaTambahan = 0;
                    $namaHari = '';
                    
                    if ($tanggal->isSaturday()) {
                        $biayaTambahan = 400000;
                        $namaHari = 'Sabtu';
                    } elseif ($tanggal->isSunday()) {
                        $biayaTambahan = 500000;
                        $namaHari = 'Minggu';
                    }
                @endphp
                
                @if($biayaTambahan > 0)
                    <tr>
                        <td>
                            Biaya Tambahan Hari {{ $namaHari }}<br>
                            <small>Kebersihan (Rp {{ number_format($namaHari === 'Sabtu' ? 100000 : 200000, 0, ',', '.') }}) + Teknisi (Rp 300.000)</small>
                        </td>
                        <td class="text-center">1 hari</td>
                        <td class="text-right text-warning">+ {{ number_format($biayaTambahan, 0, ',', '.') }}</td>
                    </tr>
                @endif

                <!-- Total -->
                <tr class="total-row">
                    <td><strong>TOTAL YANG HARUS DIBAYAR</strong></td>
                    <td class="text-center"></td>
                    <td class="text-right text-primary" style="font-size: 16px;">
                        Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Instructions -->
        <div class="payment-info">
            <h4><i class="fas fa-money-bill-wave mr-2"></i>PETUNJUK PEMBAYARAN</h4>
            <p><strong>Bank:</strong> BNI Cabang Kampus UI Depok</p>
            <p><strong>No. Rekening:</strong> <span class="text-primary" style="font-size: 14px; letter-spacing: 2px;">1273000535</span></p>
            <p><strong>Atas Nama:</strong> Universitas Indonesia FIK – Non Biaya Pendidikan</p>
            <p><strong>Catatan:</strong> Transfer dengan nominal <strong>tepat</strong> sesuai invoice. Cantumkan nomor invoice di berita transfer.</p>
            <p><strong>Batas Waktu:</strong> 3x24 jam setelah persetujuan booking. Booking dibatalkan otomatis jika melebihi batas waktu.</p>
        </div>

        <!-- SK Reminder -->
        <div class="sk-reference">
            <p><i class="fas fa-exclamation-triangle mr-2"></i> <strong>PENTING:</strong> Sesuai Pasal Keenam SK No. 411, permohonan harus diajukan secara tertulis minimal 2 minggu sebelum pelaksanaan kegiatan.</p>
            <p><i class="fas fa-shield-alt mr-2"></i> Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat BSrE (Balai Sertifikasi Elektronik).</p>
        </div>

        <!-- Signature -->
        {{-- <div class="signature">
            <div class="signature-box">
                <p>Depok, {{ \Carbon\Carbon::parse($booking->created_at)->isoFormat('D MMMM YYYY') }}</p>
                <p>Pemohon,</p>
                <div class="signature-line" style="margin-top: 60px;">
                    {{ $booking->user->name }}
                </div>
            </div>
            <div class="signature-box">
                <p>Mengetahui,</p>
                <p>Wakil Dekan Bidang Sumber Daya,<br>Ventura & Administrasi Umum</p>
                <div class="signature-line" style="margin-top: 40px;">
                    FIK UI
                </div>
            </div>
        </div> --}}

        <!-- Footer -->
        <div class="footer">
            <p>Invoice ini dibuat secara elektronik melalui Sistem Peminjaman Ruangan FIK UI</p>
            <p>© {{ date('Y') }} Fakultas Ilmu Keperawatan Universitas Indonesia</p>
            <p class="no-print" style="margin-top: 10px; color: #d62828; font-weight: bold;">
                ⚠️ INI ADALAH DOKUMEN RESMI. HARAP SIMPAN DENGAN BAIK SEBAGAI BUKTI PEMBAYARAN.
            </p>
        </div>
    </div>
</body>
</html>