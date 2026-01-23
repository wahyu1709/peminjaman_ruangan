@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Statistik Peminjaman Ruangan</h1>
    </div>

    <!-- Card Statistik -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-chart-line me-2"></i>
                Analisis Peminjaman
            </h6>
        </div>
        <div class="card-body">
            <!-- Tab Navigation (Bootstrap 4) -->
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

            <!-- Tab Content (Bootstrap 4) -->
            <div class="tab-content" id="statsTabContent">
                <!-- Monthly Tab -->
                <div class="tab-pane fade show active" id="monthly" role="tabpanel">
                    <!-- Isi grafik per bulan -->
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
</div>
@endsection

<!-- Bootstrap Core JavaScript -->
<script src="{{ asset('sbadmin2/vendor/chart.js/Chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let monthlyChart = null;
    let dailyChart = null;

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

    // === EVENT LISTENERS ===
    document.getElementById('filterYear').addEventListener('change', function() {
        loadMonthlyData(this.value);
    });

    document.getElementById('filterDays').addEventListener('change', function() {
        loadDailyData(this.value);
    });

    // Load initial data
    loadMonthlyData(new Date().getFullYear());
    loadDailyData(30);
});
</script>