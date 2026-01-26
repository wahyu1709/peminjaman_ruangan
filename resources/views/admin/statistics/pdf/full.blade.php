<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statistik Peminjaman Ruangan</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
            font-style: italic;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }
        .section-title {
            color: #00bfa5;
            margin: 0 0 15px 0;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .chart-placeholder {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Statistik Peminjaman Ruangan</h1>
        <p>Fakultas Ilmu Keperawatan Universitas Indonesia</p>
        <p>Generated at: {{ $generated_at }}</p>
    </div>
    
    <div class="content">
        <!-- Section 1: Statistik Peminjaman Ruangan -->
        <div class="section">
            <h2 class="section-title">Analisis Peminjaman Ruangan</h2>
            
            <div class="chart-placeholder">
                Grafik: Line Chart Peminjaman per Bulan Tahun {{ $monthly_data['year'] }}
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Jumlah Peminjaman</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthly_data['labels'] as $index => $month)
                        <tr>
                            <td>{{ $month }}</td>
                            <td>{{ $monthly_data['data'][$index] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="chart-placeholder mt-4">
                Grafik: Bar Chart Peminjaman per Hari ({{ $daily_data['period'] }})
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jumlah Peminjaman</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($daily_data['labels'] as $index => $date)
                        <tr>
                            <td>{{ $date }}</td>
                            <td>{{ $daily_data['data'][$index] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Section 2: Top Ruangan -->
        <div class="section">
            <h2 class="section-title">Top Ruangan Paling Sering Dipinjam - {{ $period }}</h2>
            
            <div class="chart-placeholder">
                Grafik: Horizontal Bar Chart Top Ruangan
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Ruangan</th>
                        <th>Jumlah Peminjaman</th>
                        <th>Tipe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($top_rooms_data['labels'] as $index => $room)
                        <tr>
                            <td>{{ $room }}</td>
                            <td>{{ $top_rooms_data['data'][$index] }}</td>
                            <td>{{ $top_rooms_data['is_paid'][$index] ? 'Berbayar' : 'Gratis' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Section 3: Analisis Waktu -->
        <div class="section">
            <h2 class="section-title">Analisis Waktu Penggunaan Ruangan - {{ $period }}</h2>
            
            <div style="margin-bottom: 20px;">
                <strong>Durasi Rata-rata Peminjaman:</strong> {{ $time_analysis_data['avg_duration'] }} menit
            </div>

            <h3>Jam Paling Sering Dipinjam</h3>
            <table>
                <thead>
                    <tr>
                        <th>Jam</th>
                        <th>Jumlah Peminjaman</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($time_analysis_data['hourly'] as $hour => $count)
                        <tr>
                            <td>{{ $hour }}:00</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h3>Hari Paling Sibuk</h3>
            <table>
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Jumlah Peminjaman</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($time_analysis_data['weekday'] as $day => $count)
                        <tr>
                            <td>{{ $day }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="footer">
        Laporan ini dihasilkan secara otomatis oleh sistem peminjaman ruangan.
    </div>
</body>
</html>