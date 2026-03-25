@extends('layouts.app')

@push('styles')
<style>
    :root {
        --blue:   #4361ee; --teal:  #06b6d4; --green: #10b981;
        --amber:  #f59e0b; --red:   #ef4444; --purple:#8b5cf6;
        --pink:   #ec4899; --slate: #64748b; --indigo:#6366f1;
        --card-r: 14px;
        --shadow: 0 2px 16px rgba(0,0,0,0.07);
        --shadow-h: 0 8px 28px rgba(0,0,0,0.13);
    }

    /* ── Global Filter Bar ───────────────────────────────────── */
    .global-filter-bar {
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: var(--card-r);
        padding: 14px 20px;
        margin-bottom: 24px;
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .global-filter-bar .gf-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-right: 4px;
        white-space: nowrap;
    }

    .global-filter-bar select {
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        padding: 7px 12px;
        font-size: 0.875rem;
        color: #1e293b;
        background: #f8fafc;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        cursor: pointer;
    }

    .global-filter-bar select:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(67,97,238,.12);
        background: #fff;
    }

    .filter-active-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #eff6ff;
        color: var(--blue);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        border: 1px solid #bfdbfe;
    }

    .filter-active-badge .clear-btn {
        cursor: pointer;
        opacity: .6;
        font-size: 0.8rem;
        transition: opacity .15s;
        background: none;
        border: none;
        color: var(--blue);
        padding: 0;
        line-height: 1;
    }
    .filter-active-badge .clear-btn:hover { opacity: 1; }

    /* ── KPI Cards ───────────────────────────────────────────── */
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
    }

    .kpi-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-h); }
    .kpi-card::after {
        content: '';
        position: absolute;
        right: -18px; top: -18px;
        width: 80px; height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,.1);
    }

    .kpi-card .kpi-icon { font-size: 1.4rem; margin-bottom: 8px; opacity: .9; }
    .kpi-card .kpi-value { font-size: 1.75rem; font-weight: 800; line-height: 1; margin-bottom: 3px; letter-spacing: -.02em; }
    .kpi-card .kpi-label { font-size: 0.72rem; font-weight: 600; opacity: .85; text-transform: uppercase; letter-spacing: .06em; }

    .kpi-blue   { background: linear-gradient(135deg, #4361ee, #3a0ca3); animation-delay:.05s; }
    .kpi-green  { background: linear-gradient(135deg, #10b981, #065f46); animation-delay:.10s; }
    .kpi-amber  { background: linear-gradient(135deg, #f59e0b, #b45309); animation-delay:.15s; }
    .kpi-red    { background: linear-gradient(135deg, #ef4444, #991b1b); animation-delay:.20s; }
    .kpi-teal   { background: linear-gradient(135deg, #06b6d4, #0e7490); animation-delay:.25s; }
    .kpi-purple { background: linear-gradient(135deg, #8b5cf6, #5b21b6); animation-delay:.30s; }

    /* ── Stat Cards ──────────────────────────────────────────── */
    .stat-card {
        border: none;
        border-radius: var(--card-r);
        box-shadow: var(--shadow);
        margin-bottom: 20px;
        overflow: hidden;
        animation: fadeUp .4s ease both;
    }

    .stat-card-header {
        padding: 12px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 700;
        font-size: 0.875rem;
        color: #fff;
    }

    .stat-card-header i { font-size: 1rem; margin-right: 6px; }
    .stat-card-header .period-pill {
        font-size: 0.7rem;
        background: rgba(255,255,255,.2);
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 600;
    }

    .hdr-blue   { background: linear-gradient(90deg, #4361ee, #4895ef); }
    .hdr-green  { background: linear-gradient(90deg, #10b981, #34d399); }
    .hdr-amber  { background: linear-gradient(90deg, #d97706, #fbbf24); }
    .hdr-purple { background: linear-gradient(90deg, #8b5cf6, #c084fc); }
    .hdr-teal   { background: linear-gradient(90deg, #06b6d4, #67e8f9); color: #1e293b !important; }
    .hdr-pink   { background: linear-gradient(90deg, #ec4899, #f9a8d4); color: #1e293b !important; }
    .hdr-red    { background: linear-gradient(90deg, #ef4444, #fca5a5); color: #1e293b !important; }

    /* ── Chart wrappers ──────────────────────────────────────── */
    .chart-wrap { position: relative; width: 100%; }
    .chart-wrap.h-40 { height: 40vh; min-height: 240px; }
    .chart-wrap.h-35 { height: 35vh; min-height: 200px; }
    .chart-wrap.h-30 { height: 30vh; min-height: 180px; }
    .chart-wrap.pie  { max-width: 320px; height: 280px; margin: 0 auto; }

    /* ── Legend pills ────────────────────────────────────────── */
    .legend-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
    .legend-pill {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 0.72rem; font-weight: 600;
        padding: 3px 10px; border-radius: 20px;
    }
    .legend-pill .dot { width: 8px; height: 8px; border-radius: 50%; }

    /* ── Export btn ──────────────────────────────────────────── */
    .btn-export {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
        color: #fff; border: none; border-radius: 10px;
        padding: 9px 18px; font-weight: 700; font-size: .875rem;
        transition: opacity .2s, transform .15s;
        cursor: pointer;
    }
    .btn-export:hover  { opacity: .88; color: #fff; transform: translateY(-1px); }
    .btn-export:active { transform: scale(.97); }
    .btn-export:disabled { opacity: .6; cursor: not-allowed; transform: none; }

    /* ── Loading overlay ─────────────────────────────────────── */
    .chart-loading {
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.7);
        border-radius: 8px;
        z-index: 10;
        font-size: 0.8rem; color: #64748b;
        gap: 6px;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .spinner {
        width: 16px; height: 16px;
        border: 2px solid #e2e8f0;
        border-top-color: var(--blue);
        border-radius: 50%;
        animation: spin .6s linear infinite;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ── Page Header ─────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Statistik Peminjaman</h1>
            <small class="text-muted" id="filterDescription">Menampilkan semua data</small>
        </div>
        <button id="btnExportPdf" class="btn-export">
            <i class="fas fa-file-pdf me-2"></i>Export PDF
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         GLOBAL FILTER BAR — satu filter untuk semua section
    ══════════════════════════════════════════════════════════ --}}
    <div class="global-filter-bar">
        <span class="gf-label"><i class="fas fa-filter me-1"></i>Filter:</span>

        {{-- Tahun --}}
        <div class="d-flex align-items-center gap-2">
            <label class="mb-0" style="font-size:.8rem;font-weight:600;color:#475569;">Tahun</label>
            <select id="globalYear">
                @for ($y = now()->year - 4; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        {{-- Bulan (opsional) --}}
        <div class="d-flex align-items-center gap-2">
            <label class="mb-0" style="font-size:.8rem;font-weight:600;color:#475569;">Bulan</label>
            <select id="globalMonth">
                <option value="">Semua Bulan</option>
                @foreach([
                    '01'=>'Januari','02'=>'Februari','03'=>'Maret',
                    '04'=>'April','05'=>'Mei','06'=>'Juni',
                    '07'=>'Juli','08'=>'Agustus','09'=>'September',
                    '10'=>'Oktober','11'=>'November','12'=>'Desember'
                ] as $v => $l)
                    <option value="{{ $v }}">{{ $l }}</option>
                @endforeach
            </select>
        </div>

        {{-- Badge filter aktif --}}
        <div id="filterBadge" style="display:none;">
            <span class="filter-active-badge">
                <i class="fas fa-calendar-alt"></i>
                <span id="filterBadgeText"></span>
                <button class="clear-btn" id="clearMonthFilter" title="Hapus filter bulan">✕</button>
            </span>
        </div>

        <div class="ms-auto" style="font-size:.75rem;color:#94a3b8;">
            <i class="fas fa-sync-alt me-1"></i>Auto-reload saat filter berubah
        </div>
    </div>

    {{-- ── KPI Summary Cards ────────────────────────────────────── --}}
    <div class="kpi-grid">
        <div class="kpi-card kpi-blue">
            <div class="kpi-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="kpi-value" id="kpiTotal">—</div>
            <div class="kpi-label">Total Peminjaman</div>
        </div>
        <div class="kpi-card kpi-green">
            <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
            <div class="kpi-value" id="kpiApproved">—</div>
            <div class="kpi-label">Disetujui</div>
        </div>
        <div class="kpi-card kpi-amber">
            <div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="kpi-value" id="kpiPending">—</div>
            <div class="kpi-label">Pending</div>
        </div>
        <div class="kpi-card kpi-red">
            <div class="kpi-icon"><i class="fas fa-times-circle"></i></div>
            <div class="kpi-value" id="kpiRejected">—</div>
            <div class="kpi-label">Ditolak</div>
        </div>
        <div class="kpi-card kpi-teal">
            <div class="kpi-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="kpi-value" id="kpiRevenue">—</div>
            <div class="kpi-label">Total Pendapatan</div>
        </div>
        <div class="kpi-card kpi-purple">
            <div class="kpi-icon"><i class="fas fa-boxes"></i></div>
            <div class="kpi-value" id="kpiInventory">—</div>
            <div class="kpi-label">Pinjam Barang</div>
        </div>
    </div>

    {{-- ── Row 1: Tren + Status ─────────────────────────────────── --}}
    <div class="row">
        <div class="col-lg-8">
            <div class="stat-card card">
                <div class="stat-card-header hdr-blue">
                    <span><i class="fas fa-chart-line"></i>Tren Peminjaman</span>
                    <span class="period-pill" id="trendPeriodPill">—</span>
                </div>
                <div class="card-body">
                    {{-- Sub-filter hanya untuk daily mode --}}
                    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <label style="font-size:.78rem;font-weight:600;color:#64748b;margin:0;">Tampilan</label>
                            <select id="trendMode" style="border-radius:8px;border:1.5px solid #e2e8f0;padding:5px 10px;font-size:.85rem;">
                                <option value="monthly">Per Bulan</option>
                                <option value="daily">Per Hari</option>
                            </select>
                        </div>
                        <div id="filterDaysWrap" style="display:none;" class="d-flex align-items-center gap-2">
                            <label style="font-size:.78rem;font-weight:600;color:#64748b;margin:0;">Periode</label>
                            <select id="filterDays" style="border-radius:8px;border:1.5px solid #e2e8f0;padding:5px 10px;font-size:.85rem;">
                                <option value="7">7 hari</option>
                                <option value="14">14 hari</option>
                                <option value="30" selected>30 hari</option>
                                <option value="60">60 hari</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-wrap h-40" style="position:relative;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="stat-card card">
                <div class="stat-card-header hdr-purple">
                    <span><i class="fas fa-chart-pie"></i>Distribusi Status</span>
                    <span class="period-pill" id="statusPeriodPill">—</span>
                </div>
                <div class="card-body">
                    <div class="chart-wrap pie"><canvas id="statusChart"></canvas></div>
                    <div class="legend-pills" id="statusLegend"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Row 2: Top Ruangan + Internal vs Umum ───────────────── --}}
    <div class="row">
        <div class="col-lg-7">
            <div class="stat-card card">
                <div class="stat-card-header hdr-green">
                    <span><i class="fas fa-trophy"></i>Top Ruangan Paling Sering Dipinjam</span>
                    <span class="period-pill" id="roomsPeriodPill">—</span>
                </div>
                <div class="card-body">
                    <div class="chart-wrap h-35"><canvas id="topRoomsChart"></canvas></div>
                    <div class="legend-pills">
                        <span class="legend-pill" style="background:#e0e7ff;color:#3730a3;">
                            <span class="dot" style="background:#4361ee;"></span>Gratis
                        </span>
                        <span class="legend-pill" style="background:#fce7f3;color:#9d174d;">
                            <span class="dot" style="background:#ec4899;"></span>Berbayar
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="stat-card card">
                <div class="stat-card-header hdr-teal">
                    <span><i class="fas fa-users"></i>Internal vs Pengguna Umum</span>
                    <span class="period-pill" id="userTypePeriodPill">—</span>
                </div>
                <div class="card-body">
                    <div class="chart-wrap pie"><canvas id="userTypeChart"></canvas></div>
                    <div class="legend-pills mt-3" id="userTypeLegend"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Row 3: Pendapatan + Inventaris ──────────────────────── --}}
    <div class="row">
        <div class="col-lg-6">
            <div class="stat-card card">
                <div class="stat-card-header hdr-amber">
                    <span><i class="fas fa-money-bill-wave"></i>Pendapatan per Bulan</span>
                    <span class="period-pill" id="revenuePeriodPill">—</span>
                </div>
                <div class="card-body">
                    <div class="chart-wrap h-35"><canvas id="revenueChart"></canvas></div>
                    <div class="mt-3 p-3 rounded" style="background:#fffbeb;">
                        <small class="text-muted d-block">Total Pendapatan</small>
                        <strong class="fs-5 text-warning" id="totalRevenueLabel">—</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="stat-card card">
                <div class="stat-card-header hdr-pink">
                    <span><i class="fas fa-boxes"></i>Top Barang Inventaris</span>
                    <span class="period-pill" id="inventoryPeriodPill">—</span>
                </div>
                <div class="card-body">
                    <div class="chart-wrap h-35"><canvas id="inventoryChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Row 4: Analisis Waktu ────────────────────────────────── --}}
    <div class="stat-card card">
        <div class="stat-card-header hdr-red">
            <span><i class="fas fa-clock"></i>Analisis Waktu Penggunaan</span>
            <span class="period-pill" id="timePeriodPill">—</span>
        </div>
        <div class="card-body">
            <div class="mb-4 p-3 rounded d-inline-flex align-items-center gap-3"
                 style="background:#eff6ff;border-left:4px solid #4361ee;">
                <i class="fas fa-stopwatch fa-2x" style="color:#4361ee;"></i>
                <div>
                    <div class="text-muted" style="font-size:.72rem;font-weight:700;text-transform:uppercase;">Durasi Rata-rata</div>
                    <div class="fw-bold fs-4" id="avgDuration" style="color:#4361ee;">—</div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3" style="font-size:.85rem;color:#475569;">
                        <i class="fas fa-clock me-2"></i>Distribusi per Jam
                    </h6>
                    <div class="chart-wrap h-30"><canvas id="hourlyChart"></canvas></div>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3" style="font-size:.85rem;color:#475569;">
                        <i class="fas fa-calendar-week me-2"></i>Distribusi per Hari
                    </h6>
                    <div class="chart-wrap h-30"><canvas id="weekdayChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('sbadmin2/vendor/chart.js/Chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── State global filter ──────────────────────────────────────
    let G = {
        year:  new Date().getFullYear(),
        month: '',   // '' = semua bulan
    };

    const charts = {};

    // ── Colors ───────────────────────────────────────────────────
    const C = {
        blue:'#4361ee', teal:'#06b6d4', green:'#10b981',
        amber:'#f59e0b', red:'#ef4444', purple:'#8b5cf6',
        pink:'#ec4899', slate:'#64748b', indigo:'#6366f1',
    };

    const STATUS_COLORS = {
        approved:'#10b981', pending:'#f59e0b', payment_uploaded:'#06b6d4',
        rejected:'#ef4444', completed:'#64748b', cancelled:'#94a3b8',
    };

    const STATUS_LABELS = {
        approved:'Disetujui', pending:'Pending',
        payment_uploaded:'Menunggu Verifikasi',
        rejected:'Ditolak', completed:'Selesai', cancelled:'Dibatalkan',
    };

    const MONTH_NAMES = {
        '01':'Januari','02':'Februari','03':'Maret','04':'April',
        '05':'Mei','06':'Juni','07':'Juli','08':'Agustus',
        '09':'September','10':'Oktober','11':'November','12':'Desember'
    };

    // ── Helpers ──────────────────────────────────────────────────
    function hexAlpha(hex, a) {
        const r = parseInt(hex.slice(1,3),16);
        const g = parseInt(hex.slice(3,5),16);
        const b = parseInt(hex.slice(5,7),16);
        return `rgba(${r},${g},${b},${a})`;
    }

    function fmtRupiah(v) {
        if (v >= 1e9) return 'Rp ' + (v/1e9).toFixed(1) + 'M';
        if (v >= 1e6) return 'Rp ' + (v/1e6).toFixed(1) + ' jt';
        if (v >= 1e3) return 'Rp ' + (v/1e3).toFixed(0) + ' rb';
        return 'Rp ' + v;
    }

    function periodLabel() {
        if (G.month) return MONTH_NAMES[G.month] + ' ' + G.year;
        return 'Tahun ' + G.year;
    }

    function makeChart(id, config) {
        if (charts[id]) charts[id].destroy();
        const ctx = document.getElementById(id);
        if (!ctx) return;
        charts[id] = new Chart(ctx.getContext('2d'), config);
    }

    function api(url) {
        return fetch(url).then(r => r.json()).catch(e => { console.error(e); return {}; });
    }

    function buildUrl(base, extraParams = {}) {
        const params = new URLSearchParams({ year: G.year });
        if (G.month) params.set('month', G.month);
        Object.entries(extraParams).forEach(([k,v]) => params.set(k, v));
        return base + '?' + params.toString();
    }

    // Shared chart options
    function barOpts(horizontal = false) {
        return {
            responsive: true, maintainAspectRatio: false,
            indexAxis: horizontal ? 'y' : 'x',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,.85)',
                    titleFont:{size:12}, bodyFont:{size:11},
                    padding:9, cornerRadius:7,
                }
            },
            scales: {
                [horizontal?'x':'y']: {
                    beginAtZero: true,
                    ticks: { precision:0, stepSize:1 },
                    grid: { color:'rgba(0,0,0,.05)' }
                },
                [horizontal?'y':'x']: { grid: { display:false } }
            }
        };
    }

    function lineOpts() {
        return {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display:false },
                tooltip: {
                    backgroundColor:'rgba(15,23,42,.85)',
                    titleFont:{size:12}, bodyFont:{size:11},
                    padding:9, cornerRadius:7,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision:0, stepSize:1 },
                    grid: { color:'rgba(0,0,0,.05)' }
                },
                x: { grid: { display:false } }
            }
        };
    }

    // ── Update period pills ───────────────────────────────────────
    function updatePills() {
        const label = periodLabel();
        ['trendPeriodPill','statusPeriodPill','roomsPeriodPill',
         'userTypePeriodPill','revenuePeriodPill','inventoryPeriodPill',
         'timePeriodPill'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = label;
        });
        document.getElementById('filterDescription').textContent =
            G.month
            ? `Menampilkan data ${MONTH_NAMES[G.month]} ${G.year}`
            : `Menampilkan data tahun ${G.year}`;
    }

    // ── Update filter badge ──────────────────────────────────────
    function updateBadge() {
        const badge = document.getElementById('filterBadge');
        const text  = document.getElementById('filterBadgeText');
        if (G.month) {
            badge.style.display = '';
            text.textContent = MONTH_NAMES[G.month] + ' ' + G.year;
        } else {
            badge.style.display = 'none';
        }
    }

    // ════════════════════════════════════════════════════════════
    // LOAD FUNCTIONS — semua pakai G.year & G.month
    // ════════════════════════════════════════════════════════════

    function loadTrend() {
        const mode = document.getElementById('trendMode').value;
        document.getElementById('filterDaysWrap').style.display =
            mode === 'daily' ? '' : 'none';

        if (mode === 'monthly') {
            // Monthly: ikut global year (bulan diabaikan — tampilkan 12 bar)
            api(`{{ route('api.statistics.booking.per.month') }}?year=${G.year}&detail_status=1&user_type=1`)
                .then(d => {
                    if (!d.success) return;

                    const total = d.data.reduce((a,b) => a+b, 0);
                    document.getElementById('kpiTotal').textContent = total;

                    if (d.by_status) updateKPIFromStatus(d.by_status);
                    if (d.by_user_type) updateKPIUserType(d.by_user_type);

                    makeChart('trendChart', {
                        type: 'line',
                        data: {
                            labels: d.labels,
                            datasets: [{
                                label: 'Peminjaman',
                                data: d.data,
                                borderColor: C.blue,
                                backgroundColor: hexAlpha(C.blue, .08),
                                borderWidth: 3,
                                pointRadius: 5,
                                pointBackgroundColor: C.blue,
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                fill: true, tension: 0.35
                            }]
                        },
                        options: lineOpts()
                    });
                });
        } else {
            const days = document.getElementById('filterDays').value;
            api(`{{ route('api.statistics.booking.per.day') }}?days=${days}`)
                .then(d => {
                    if (!d.success) return;
                    makeChart('trendChart', {
                        type: 'bar',
                        data: {
                            labels: d.labels,
                            datasets: [{
                                label: 'Peminjaman',
                                data: d.data,
                                backgroundColor: d.colors || d.data.map(() => hexAlpha(C.blue,.6)),
                                borderRadius: 5,
                            }]
                        },
                        options: barOpts()
                    });
                });
        }
    }

    function loadStatus() {
        api(buildUrl(`{{ route('api.statistics.booking.per.month') }}`,
            { detail_status: 1 }))
            .then(d => {
                const by = d.by_status || {};
                const labels = Object.keys(by).map(k => STATUS_LABELS[k] || k);
                const values = Object.values(by);
                const colors = Object.keys(by).map(k => STATUS_COLORS[k] || C.slate);
                if (!values.length) return;

                updateKPIFromStatus(by);

                makeChart('statusChart', {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverOffset: 8,
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '60%',
                        plugins: {
                            legend: { display:false },
                            tooltip: {
                                backgroundColor:'rgba(15,23,42,.85)', padding:9, cornerRadius:7,
                                callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` }
                            }
                        }
                    }
                });

                const leg = document.getElementById('statusLegend');
                leg.innerHTML = labels.map((l,i) =>
                    `<span class="legend-pill" style="background:${hexAlpha(colors[i],.12)};color:${colors[i]};">
                        <span class="dot" style="background:${colors[i]};"></span>${l}: <b>${values[i]}</b>
                    </span>`
                ).join('');
            });
    }

    function loadTopRooms() {
        api(buildUrl(`{{ route('api.statistics.top.rooms') }}`))
            .then(d => {
                if (!d.success) return;
                makeChart('topRoomsChart', {
                    type: 'bar',
                    data: {
                        labels: d.labels,
                        datasets: [{
                            data: d.data,
                            backgroundColor: d.colors || d.data.map(() => C.blue),
                            borderRadius: 5,
                            borderSkipped: false,
                        }]
                    },
                    options: barOpts(true)
                });
            });
    }

    function loadUserType() {
        api(buildUrl(`{{ route('api.statistics.booking.per.month') }}`,
            { user_type: 1 }))
            .then(d => {
                const by = d.by_user_type || {};
                const labels = ['Internal (Civitas FIK UI)', 'Umum'];
                const values = [by.internal || 0, by.umum || 0];
                const colors = [C.teal, C.pink];

                updateKPIUserType(by);

                makeChart('userTypeChart', {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderWidth: 3, borderColor: '#fff', hoverOffset: 8,
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '60%',
                        plugins: {
                            legend: { display:false },
                            tooltip: {
                                backgroundColor:'rgba(15,23,42,.85)', padding:9, cornerRadius:7,
                                callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` }
                            }
                        }
                    }
                });

                const leg = document.getElementById('userTypeLegend');
                leg.innerHTML = labels.map((l,i) =>
                    `<span class="legend-pill" style="background:${hexAlpha(colors[i],.12)};color:${colors[i]};">
                        <span class="dot" style="background:${colors[i]};"></span>${l}: <b>${values[i]}</b>
                    </span>`
                ).join('');
            });
    }

    function loadRevenue() {
        api(buildUrl(`{{ route('api.statistics.revenue') }}`))
            .then(d => {
                if (!d.success) return;
                const total = d.total || d.data?.reduce((a,b)=>a+b,0) || 0;

                document.getElementById('kpiRevenue').textContent = fmtRupiah(total);
                document.getElementById('totalRevenueLabel').textContent =
                    new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0}).format(total);

                makeChart('revenueChart', {
                    type: 'bar',
                    data: {
                        labels: d.labels,
                        datasets: [{
                            data: d.data,
                            backgroundColor: hexAlpha(C.amber, .7),
                            borderColor: C.amber,
                            borderWidth: 2, borderRadius: 5,
                        }]
                    },
                    options: {
                        ...barOpts(),
                        plugins: {
                            legend: { display:false },
                            tooltip: {
                                backgroundColor:'rgba(15,23,42,.85)', padding:9, cornerRadius:7,
                                callbacks: {
                                    label: ctx => ' ' + new Intl.NumberFormat('id-ID',{
                                        style:'currency', currency:'IDR', minimumFractionDigits:0
                                    }).format(ctx.parsed.y)
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: v => fmtRupiah(v) },
                                grid: { color:'rgba(0,0,0,.05)' }
                            },
                            x: { grid: { display:false } }
                        }
                    }
                });
            });
    }

    function loadInventory() {
        api(buildUrl(`{{ route('api.statistics.inventory') }}`))
            .then(d => {
                if (!d.success) return;
                document.getElementById('kpiInventory').textContent =
                    d.data?.reduce((a,b)=>a+b,0) || '—';

                const palette = [C.pink,C.purple,C.blue,C.teal,C.green,C.amber];
                makeChart('inventoryChart', {
                    type: 'bar',
                    data: {
                        labels: d.labels,
                        datasets: [{
                            data: d.data,
                            backgroundColor: d.data.map((_,i) => hexAlpha(palette[i%palette.length],.75)),
                            borderRadius: 5, borderSkipped: false,
                        }]
                    },
                    options: barOpts(true)
                });
            });
    }

    function loadTimeAnalysis() {
        api(buildUrl(`{{ route('api.statistics.time.analysis') }}`))
            .then(d => {
                if (!d.success) return;
                document.getElementById('avgDuration').textContent = (d.avg_duration || 0) + ' menit';

                const hours = Object.keys(d.hourly || {}).map(h => h+':00');
                const hvals = Object.values(d.hourly || {});
                makeChart('hourlyChart', {
                    type: 'bar',
                    data: {
                        labels: hours,
                        datasets: [{
                            data: hvals,
                            backgroundColor: hvals.map(v => {
                                const m = Math.max(...hvals);
                                return hexAlpha(C.blue, 0.35 + (v/m)*0.6);
                            }),
                            borderRadius: 4,
                        }]
                    },
                    options: barOpts()
                });

                const days  = Object.keys(d.weekday || {});
                const dvals = Object.values(d.weekday || {});
                makeChart('weekdayChart', {
                    type: 'bar',
                    data: {
                        labels: days,
                        datasets: [{
                            data: dvals,
                            backgroundColor: dvals.map(v => {
                                const m = Math.max(...dvals);
                                return hexAlpha(C.indigo, 0.35 + (v/m)*0.6);
                            }),
                            borderRadius: 4,
                        }]
                    },
                    options: barOpts()
                });
            });
    }

    // ── KPI updaters ─────────────────────────────────────────────
    function updateKPIFromStatus(by) {
        document.getElementById('kpiApproved').textContent  = (by.approved||0) + (by.completed||0);
        document.getElementById('kpiPending').textContent   = by.pending  || 0;
        document.getElementById('kpiRejected').textContent  = by.rejected || 0;
    }

    function updateKPIUserType(by) {
        // tidak ada KPI khusus user type, tapi bisa dipakai nanti
    }

    // ── Master reload — panggil semua sekaligus ───────────────────
    function reloadAll() {
        updatePills();
        updateBadge();
        loadTrend();
        loadStatus();
        loadTopRooms();
        loadUserType();
        loadRevenue();
        loadInventory();
        loadTimeAnalysis();
    }

    // ══════════════════════════════════════════════════════════════
    // EVENT LISTENERS
    // ══════════════════════════════════════════════════════════════

    // Filter global — auto-reload
    document.getElementById('globalYear').addEventListener('change', function () {
        G.year = this.value;
        reloadAll();
    });

    document.getElementById('globalMonth').addEventListener('change', function () {
        G.month = this.value;
        reloadAll();
    });

    // Clear month filter
    document.getElementById('clearMonthFilter').addEventListener('click', function () {
        G.month = '';
        document.getElementById('globalMonth').value = '';
        reloadAll();
    });

    // Sub-filter tren (mode & days)
    document.getElementById('trendMode').addEventListener('change', loadTrend);
    document.getElementById('filterDays').addEventListener('change', loadTrend);

    // ── Export PDF ───────────────────────────────────────────────
    document.getElementById('btnExportPdf').addEventListener('click', function () {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';

        // Kirim parameter filter yang aktif ke backend
        let url = `{{ route('statistics.export.full') }}?year=${G.year}`;
        if (G.month) url += `&month=${G.month}`;

        window.open(url, '_blank');

        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-file-pdf me-2"></i>Export PDF';
        }, 3000);
    });

    // ── Initial load ─────────────────────────────────────────────
    reloadAll();
});
</script>
@endpush