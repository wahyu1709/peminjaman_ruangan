@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Statistik Peminjaman Ruangan</h1>
    </div>

    <!-- Section 1: Statistik Peminjaman Ruangan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-chart-line me-2"></i>
                Analisis Peminjaman Ruangan
            </h6>
        </div>
        <div class="card-body">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-4" id="statsTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="monthly-tab" data-toggle="tab" href="#monthly" role="tab">
                        Per Bulan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="daily-tab" data-toggle="tab" href="#daily" role="tab">
                        Per Hari
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="statsTabContent">
                <!-- Monthly Tab -->
                <div class="tab-pane fade show active" id="monthly" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tahun</label>
                            <select id="filterYear" class="form-control">
                                @for ($y = now()->year - 5; $y <= now()->year + 2; $y++)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height:40vh; width:100%">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <!-- Daily Tab -->
                <div class="tab-pane fade" id="daily" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Periode</label>
                            <select id="filterDays" class="form-control">
                                <option value="7">7 hari terakhir</option>
                                <option value="14">14 hari terakhir</option>
                                <option value="30" selected>30 hari terakhir</option>
                                <option value="60">60 hari terakhir</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height:40vh; width:100%">
                        <canvas id="dailyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Top Ruangan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-trophy me-2"></i>
                Top Ruangan Paling Sering Dipinjam
            </h6>
        </div>
        <div class="card-body">
            <!-- Filter Tahun & Bulan -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tahun</label>
                    <select id="filterYearTopRooms" class="form-control">
                        @for ($y = now()->year - 5; $y <= now()->year + 2; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Bulan (Opsional)</label>
                    <select id="filterMonthTopRooms" class="form-control">
                        <option value="">Semua Bulan</option>
                        <option value="01">Januari</option>
                        <option value="02">Februari</option>
                        <option value="03">Maret</option>
                        <option value="04">April</option>
                        <option value="05">Mei</option>
                        <option value="06">Juni</option>
                        <option value="07">Juli</option>
                        <option value="08">Agustus</option>
                        <option value="09">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
            </div>

            <!-- Grafik -->
            <div class="chart-container" style="position: relative; height:50vh; width:100%">
                <canvas id="topRoomsChart"></canvas>
            </div>
            
            <!-- Legend -->
            <div class="mt-3 text-center">
                <span class="badge bg-primary text-white me-2">Gratis</span>
                <span class="badge bg-danger text-white">Berbayar</span>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="{{ asset('sbadmin2/vendor/chart.js/Chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let monthlyChart = null;
    let dailyChart = null;
    let topRoomsChart = null;

    // === MONTHLY CHART ===
    function renderMonthlyChart(data) {
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        if (monthlyChart) monthlyChart.destroy();

        monthlyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: data.data,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 3,
                    pointRadius: 5,
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: getChartOptions()
        });
    }

    // === DAILY CHART ===
    function renderDailyChart(data) {
        const ctx = document.getElementById('dailyChart').getContext('2d');
        if (dailyChart) dailyChart.destroy();

        dailyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: data.data,
                    backgroundColor: data.colors,
                    borderColor: data.colors.map(color => color),
                    borderWidth: 1
                }]
            },
            options: {
                ...getChartOptions(),
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // === TOP ROOMS CHART ===
    function renderTopRoomsChart(data) {
        const ctx = document.getElementById('topRoomsChart').getContext('2d');
        if (topRoomsChart) topRoomsChart.destroy();

        topRoomsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: data.data,
                    backgroundColor: data.colors,
                    borderColor: data.colors.map(color => color),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Horizontal bar chart
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return 'Peminjaman: ' + context.parsed.x;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // === TOP ROOMS MONTHLY CHART ===
    function renderTopRoomsChartMonthly(data) {
        const ctx = document.getElementById('topRoomsChartMonthly').getContext('2d');
        if (topRoomsChartMonthly) topRoomsChartMonthly.destroy();

        topRoomsChartMonthly = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: data.data,
                    backgroundColor: data.colors,
                    borderColor: data.colors.map(color => color),
                    borderWidth: 1
                }]
            },
            options: getTopRoomsChartOptions()
        });
    }

    // === TOP ROOMS DAILY CHART ===
    function renderTopRoomsChartDaily(data) {
        const ctx = document.getElementById('topRoomsChartDaily').getContext('2d');
        if (topRoomsChartDaily) topRoomsChartDaily.destroy();

        topRoomsChartDaily = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: data.data,
                    backgroundColor: data.colors,
                    borderColor: data.colors.map(color => color),
                    borderWidth: 1
                }]
            },
            options: getTopRoomsChartOptions()
        });
    }

    // === OPTIONS UNTUK TOP ROOMS ===
    function getTopRoomsChartOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return 'Peminjaman: ' + context.parsed.x;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 }
                },
                y: {
                    grid: { display: false }
                }
            }
        };
    }

    // === OPTIONS SHARED ===
    function getChartOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return 'Peminjaman: ' + context.parsed.y;
                        }
                    }
                }
            }
        };
    }

    // === LOAD DATA ===
    function loadMonthlyData(year) {
        fetch(`{{ route('api.statistics.booking.per.month') }}?year=${year}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderMonthlyChart(data);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function loadDailyData(days) {
        fetch(`{{ route('api.statistics.booking.per.day') }}?days=${days}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderDailyChart(data);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // === LOAD TOP ROOMS DATA ===
    function loadTopRoomsData(year, month = null) {
        let url = `{{ route('api.statistics.top.rooms') }}?year=${year}`;
        if (month) {
            url += `&month=${month}`;
        }
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderTopRoomsChart(data);
                    // Update judul jika diperlukan
                    console.log('Periode:', data.period);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // === EVENT LISTENERS ===
    document.getElementById('filterYear').addEventListener('change', function() {
        loadMonthlyData(this.value);
    });

    document.getElementById('filterDays').addEventListener('change', function() {
        loadDailyData(this.value);
    });

    // Event listener untuk top rooms
    document.getElementById('filterYearTopRooms').addEventListener('change', function() {
        const year = this.value;
        const month = document.getElementById('filterMonthTopRooms').value;
        loadTopRoomsData(year, month);
    });

    document.getElementById('filterMonthTopRooms').addEventListener('change', function() {
        const year = document.getElementById('filterYearTopRooms').value;
        const month = this.value || null;
        loadTopRoomsData(year, month);
    });

    // Load initial data
    loadMonthlyData(new Date().getFullYear());
    loadDailyData(30);
    loadTopRoomsData(new Date().getFullYear());
});
</script>