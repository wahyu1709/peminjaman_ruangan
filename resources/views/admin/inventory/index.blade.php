@extends('layouts.app')

@push('styles')
<style>
    :root {
        --card-r:  14px;
        --shadow:  0 2px 16px rgba(0,0,0,0.07);
        --shadow-h:0 8px 28px rgba(0,0,0,0.13);
    }

    @keyframes fadeUp {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* ── Page card ───────────────────────────────────────────── */
    .page-card {
        border: none; border-radius: var(--card-r);
        box-shadow: var(--shadow); overflow: hidden;
        animation: fadeUp .4s ease both;
    }

    .page-card-header {
        padding: 14px 20px;
        background: linear-gradient(90deg, #8b5cf6, #7c3aed);
        display: flex; align-items: center;
        justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }

    /* ── KPI mini cards ──────────────────────────────────────── */
    .kpi-mini-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .kpi-mini {
        border: none; border-radius: var(--card-r);
        padding: 14px 16px; color: #fff;
        box-shadow: var(--shadow);
        transition: transform .2s, box-shadow .2s;
        position: relative; overflow: hidden;
        animation: fadeUp .4s ease both;
    }

    .kpi-mini:hover { transform: translateY(-3px); box-shadow: var(--shadow-h); }
    .kpi-mini::after {
        content:''; position:absolute; right:-14px; top:-14px;
        width:64px; height:64px; border-radius:50%;
        background:rgba(255,255,255,.1);
    }

    .kpi-mini .kv { font-size:1.5rem; font-weight:800; line-height:1; margin-bottom:3px; }
    .kpi-mini .kl { font-size:.68rem; font-weight:600; opacity:.82; text-transform:uppercase; letter-spacing:.06em; }

    .km-purple { background:linear-gradient(135deg,#8b5cf6,#5b21b6); animation-delay:.05s; }
    .km-blue   { background:linear-gradient(135deg,#4361ee,#3a0ca3); animation-delay:.10s; }
    .km-green  { background:linear-gradient(135deg,#10b981,#065f46); animation-delay:.15s; }
    .km-amber  { background:linear-gradient(135deg,#f59e0b,#b45309); animation-delay:.20s; }
    .km-red    { background:linear-gradient(135deg,#ef4444,#991b1b); animation-delay:.25s; }

    /* ── Filter tabs ─────────────────────────────────────────── */
    .filter-tabs { display:flex; gap:6px; flex-wrap:wrap; }

    .filter-tab {
        display:inline-flex; align-items:center; gap:5px;
        padding:5px 13px; border-radius:20px;
        font-size:.78rem; font-weight:600;
        border:1.5px solid rgba(255,255,255,.35);
        color:rgba(255,255,255,.8); background:transparent;
        cursor:pointer; transition:all .2s; text-decoration:none;
    }
    .filter-tab:hover { background:rgba(255,255,255,.15); color:#fff; text-decoration:none; }
    .filter-tab.active { background:#fff; color:#7c3aed; border-color:#fff; font-weight:700; }

    /* ── Header actions ──────────────────────────────────────── */
    .btn-hdr {
        display:inline-flex; align-items:center; gap:5px;
        padding:6px 14px; border-radius:8px; font-size:.8rem;
        font-weight:700; border:none; cursor:pointer;
        transition:opacity .15s, transform .15s; text-decoration:none;
        white-space:nowrap;
    }
    .btn-hdr:hover  { opacity:.88; transform:translateY(-1px); text-decoration:none; }
    .btn-hdr:active { transform:scale(.97); }
    .btn-hdr-white  { background:#fff; color:#7c3aed; }
    .btn-hdr-export { background:rgba(255,255,255,.15); color:#fff; border:1.5px solid rgba(255,255,255,.4); }
    .btn-hdr-export:hover { background:rgba(255,255,255,.25); color:#fff; }

    .export-dropdown { position:relative; display:inline-block; }
    .export-menu {
        position:absolute; top:calc(100% + 6px); right:0;
        background:#fff; border-radius:10px;
        box-shadow:var(--shadow-h); min-width:150px;
        z-index:999; overflow:hidden; display:none;
    }
    .export-menu.open { display:block; animation:fadeUp .15s ease; }
    .export-menu a {
        display:flex; align-items:center; gap:8px;
        padding:10px 14px; font-size:.82rem; font-weight:600;
        color:#334155; text-decoration:none; transition:background .15s;
    }
    .export-menu a:hover { background:#f8fafc; }

    /* ── Table ───────────────────────────────────────────────── */
    .inv-table thead tr { background:#f8fafc; }
    .inv-table th {
        font-size:.72rem; font-weight:700; text-transform:uppercase;
        letter-spacing:.06em; color:#64748b;
        border-bottom:2px solid #e2e8f0; padding:11px 12px; white-space:nowrap;
    }
    .inv-table td {
        padding:11px 12px; vertical-align:middle;
        border-bottom:1px solid #f1f5f9; font-size:.875rem; color:#334155;
    }
    .inv-table tbody tr:last-child td { border-bottom:none; }
    .inv-table tbody tr:hover td { background:#faf5ff; }

    /* ── Item name cell ──────────────────────────────────────── */
    .item-img {
        width:38px; height:38px; border-radius:8px;
        object-fit:cover; margin-right:10px; flex-shrink:0;
        border:1px solid #e2e8f0;
    }
    .item-img-placeholder {
        width:38px; height:38px; border-radius:8px;
        background:linear-gradient(135deg,#ede9fe,#ddd6fe);
        display:inline-flex; align-items:center; justify-content:center;
        color:#7c3aed; font-size:.9rem; flex-shrink:0; margin-right:10px;
    }
    .item-name { font-weight:600; color:#0f172a; }
    .item-price { font-size:.72rem; color:#94a3b8; }

    /* ── Category badge ──────────────────────────────────────── */
    .cat-badge {
        display:inline-block; padding:3px 9px; border-radius:20px;
        font-size:.7rem; font-weight:600;
        background:#ede9fe; color:#5b21b6; border:1px solid #ddd6fe;
    }

    /* ── Stock bar ───────────────────────────────────────────── */
    .stock-bar-wrap { min-width:100px; }
    .stock-nums { display:flex; justify-content:space-between; font-size:.75rem; margin-bottom:4px; }
    .stock-avail { font-weight:700; color:#0f172a; }
    .stock-total { color:#94a3b8; }
    .stock-track { height:6px; background:#f1f5f9; border-radius:3px; overflow:hidden; }
    .stock-fill  { height:100%; border-radius:3px; transition:width .3s ease; }

    /* ── Status badges ───────────────────────────────────────── */
    .status-badge {
        display:inline-block; padding:3px 10px; border-radius:20px;
        font-size:.72rem; font-weight:700; letter-spacing:.03em;
    }
    .sb-approved  { background:#dcfce7; color:#166534; border:1px solid #86efac; }
    .sb-cancelled { background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; }

    /* ── Action buttons ──────────────────────────────────────── */
    .action-btn {
        display:inline-flex; align-items:center; gap:4px;
        border-radius:7px; padding:4px 9px;
        font-size:.75rem; font-weight:600; border:none;
        transition:opacity .15s, transform .15s; cursor:pointer;
        text-decoration:none; white-space:nowrap;
    }
    .action-btn:hover  { opacity:.85; transform:translateY(-1px); }
    .action-btn:active { transform:scale(.97); }
    .ab-edit    { background:#ede9fe; color:#5b21b6; }
    .ab-stock   { background:#dbeafe; color:#1d4ed8; }
    .ab-toggle  { background:#fef9c3; color:#854d0e; }
    .ab-delete  { background:#fee2e2; color:#991b1b; }

    /* ── Modal styles ────────────────────────────────────────── */
    .modal-content {
        border:none !important; border-radius:14px !important;
        overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.15) !important;
    }
    .modal-hdr-purple { background:linear-gradient(90deg,#8b5cf6,#7c3aed); }
    .modal-hdr-blue   { background:linear-gradient(90deg,#4361ee,#4895ef); }
    .modal-hdr-amber  { background:linear-gradient(90deg,#d97706,#fbbf24); }
    .modal-hdr-danger { background:linear-gradient(90deg,#dc2626,#b91c1c); }
    .modal-hdr-green  { background:linear-gradient(90deg,#10b981,#34d399); }

    .modal-body   { padding:20px !important; }
    .modal-footer { padding:12px 20px !important; background:#f8fafc; border-top:1px solid #f1f5f9 !important; }

    .field-group  { margin-bottom:14px; }
    .field-label  { font-size:.75rem; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:.05em; margin-bottom:5px; display:block; }
    .field-input  {
        width:100%; border-radius:8px; border:1.5px solid #e2e8f0;
        padding:8px 12px; font-size:.875rem; color:#1e293b;
        transition:border-color .2s, box-shadow .2s; outline:none;
    }
    .field-input:focus { border-color:#7c3aed; box-shadow:0 0 0 3px rgba(124,58,237,.12); }
    .field-hint   { font-size:.72rem; color:#94a3b8; margin-top:4px; }
    .form-row-2   { display:flex; gap:12px; }
    .form-row-2 > div { flex:1; min-width:0; }

    .modal-section-title {
        font-size:.72rem; font-weight:700; color:#94a3b8;
        text-transform:uppercase; letter-spacing:.08em;
        margin:14px 0 10px; padding-bottom:6px; border-bottom:1px solid #f1f5f9;
    }

    /* Stock type toggle */
    .stock-type-toggle { display:flex; gap:8px; }
    .stock-type-btn {
        flex:1; padding:8px; border-radius:8px; border:1.5px solid #e2e8f0;
        font-size:.82rem; font-weight:600; cursor:pointer;
        background:#f8fafc; color:#64748b; transition:all .15s; text-align:center;
    }
    .stock-type-btn.active-add    { background:#dcfce7; color:#166534; border-color:#86efac; }
    .stock-type-btn.active-reduce { background:#fee2e2; color:#991b1b; border-color:#fca5a5; }

    /* Image preview */
    .img-preview-box {
        width:100%; height:120px; border-radius:8px;
        border:2px dashed #e2e8f0; display:flex;
        align-items:center; justify-content:center;
        overflow:hidden; cursor:pointer; background:#f8fafc;
        transition:border-color .2s;
    }
    .img-preview-box:hover { border-color:#7c3aed; }
    .img-preview-box img  { width:100%; height:100%; object-fit:cover; }
    .img-preview-box .placeholder { color:#94a3b8; font-size:.82rem; text-align:center; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>

    {{-- ── KPI Mini Cards ──────────────────────────────────────── --}}
    <div class="kpi-mini-grid" id="kpiMiniGrid">
        <div class="kpi-mini km-purple">
            <div class="kv" id="kpiTotal">—</div>
            <div class="kl">Total Jenis Barang</div>
        </div>
        <div class="kpi-mini km-green">
            <div class="kv" id="kpiActive">—</div>
            <div class="kl">Barang Aktif</div>
        </div>
        <div class="kpi-mini km-blue">
            <div class="kv" id="kpiStokTotal">—</div>
            <div class="kl">Total Stok</div>
        </div>
        <div class="kpi-mini km-amber">
            <div class="kv" id="kpiStokAv">—</div>
            <div class="kl">Stok Tersedia</div>
        </div>
        <div class="kpi-mini km-red">
            <div class="kv" id="kpiStokUsed">—</div>
            <div class="kl">Sedang Dipinjam</div>
        </div>
    </div>

    {{-- ── Main Card ───────────────────────────────────────────── --}}
    <div class="page-card card">

        <div class="page-card-header">
            {{-- Filter tabs --}}
            <div class="filter-tabs" id="categoryTabs">
                <a href="#" class="filter-tab active" data-category="">
                    <i class="fas fa-th"></i> Semua
                </a>
                @foreach($categories as $category)
                    <a href="#" class="filter-tab" data-category="{{ $category->key }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="d-flex gap-2 align-items-center">
                <div class="export-dropdown">
                    <button class="btn-hdr btn-hdr-export" id="toggleExport">
                        <i class="fas fa-download"></i> Export
                        <i class="fas fa-chevron-down" style="font-size:.65rem;margin-left:2px;"></i>
                    </button>
                    <div class="export-menu" id="exportMenu">
                        <a href="#" id="doExportExcel">
                            <i class="fas fa-file-excel" style="color:#10b981;"></i> Excel
                        </a>
                        <a href="#" id="doExportPdf">
                            <i class="fas fa-file-pdf" style="color:#ef4444;"></i> PDF
                        </a>
                    </div>
                </div>
                <button class="btn-hdr btn-hdr-export" data-toggle="modal" data-target="#manageCatModal">
                    <i class="fas fa-tags"></i> Kelola Kategori
                </button>
                <button class="btn-hdr btn-hdr-white" data-toggle="modal" data-target="#addItemModal">
                    <i class="fas fa-plus"></i> Tambah Barang
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table inv-table mb-0" id="inventoryTable" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:44px;">No</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th class="text-center">Harga/Hari</th>
                            <th>Stok</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width:140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         MODAL TAMBAH BARANG
    ══════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-hdr-purple text-white border-0" style="padding:14px 20px;">
                    <div class="d-flex align-items-center">
                        <div style="width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.2);
                                    display:flex;align-items:center;justify-content:center;margin-right:10px;">
                            <i class="fas fa-plus" style="font-size:.85rem;"></i>
                        </div>
                        <h5 class="modal-title mb-0 font-weight-bold" style="font-size:.95rem;">
                            Tambah Barang Inventaris
                        </h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" style="opacity:.8;">&times;</button>
                </div>
                <form id="addItemForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row-2">
                            <div style="flex:2;">
                                <div class="field-group">
                                    <label class="field-label">Nama Barang <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="field-input" required placeholder="Contoh: Phantom RJP Adult">
                                </div>
                                <div class="form-row-2">
                                    <div class="field-group">
                                        <label class="field-label">Kategori <span class="text-danger">*</span></label>
                                        <select name="category" class="field-input" required>
                                            <option value="">-- Pilih --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->key }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field-group">
                                        <label class="field-label">Status</label>
                                        <select name="is_active" class="field-input" required>
                                            <option value="1">Aktif</option>
                                            <option value="0">Nonaktif</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row-2">
                                    <div class="field-group">
                                        <label class="field-label">Harga/Hari (Rp) <span class="text-danger">*</span></label>
                                        <input type="number" name="price_per_day" class="field-input" required min="0" placeholder="0">
                                        <span class="field-hint">0 = gratis</span>
                                    </div>
                                    <div class="field-group">
                                        <label class="field-label">Jumlah Stok <span class="text-danger">*</span></label>
                                        <input type="number" name="stock" class="field-input" required min="0" placeholder="0">
                                    </div>
                                </div>
                            </div>
                            {{-- <div style="flex:1;">
                                <div class="field-group">
                                    <label class="field-label">Foto Barang</label>
                                    <div class="img-preview-box" id="addImgBox" onclick="document.getElementById('addImgInput').click()">
                                        <div class="placeholder">
                                            <i class="fas fa-image d-block mb-1" style="font-size:1.5rem;opacity:.4;"></i>
                                            Klik untuk upload
                                        </div>
                                    </div>
                                    <input type="file" name="image" id="addImgInput" accept="image/*" style="display:none;">
                                    <span class="field-hint">JPG/PNG, maks 2MB</span>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light"
                                style="border-radius:8px;font-weight:600;border:1.5px solid #e2e8f0;"
                                data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm text-white font-weight-bold"
                                style="border-radius:8px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);border:none;padding:7px 18px;">
                            <i class="fas fa-save mr-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         MODAL EDIT BARANG
    ══════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="editItemModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-hdr-amber text-dark border-0" style="padding:14px 20px;">
                    <div class="d-flex align-items-center">
                        <div style="width:30px;height:30px;border-radius:50%;background:rgba(0,0,0,.1);
                                    display:flex;align-items:center;justify-content:center;margin-right:10px;">
                            <i class="fas fa-edit" style="font-size:.85rem;"></i>
                        </div>
                        <h5 class="modal-title mb-0 font-weight-bold" style="font-size:.95rem;">Edit Barang</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" style="opacity:.7;">&times;</button>
                </div>
                <form id="editItemForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_item_id">
                    <div class="modal-body">
                        <div class="form-row-2">
                            <div style="flex:2;">
                                <div class="field-group">
                                    <label class="field-label">Nama Barang <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="edit_name" class="field-input" required>
                                </div>
                                <div class="form-row-2">
                                    <div class="field-group">
                                        <label class="field-label">Kategori <span class="text-danger">*</span></label>
                                        <select name="category" id="edit_category" class="field-input" required>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->key }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field-group">
                                        <label class="field-label">Status</label>
                                        <select name="is_active" id="edit_is_active" class="field-input" required>
                                            <option value="1">Aktif</option>
                                            <option value="0">Nonaktif</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row-2">
                                    <div class="field-group">
                                        <label class="field-label">Harga/Hari (Rp)</label>
                                        <input type="number" name="price_per_day" id="edit_price" class="field-input" min="0">
                                    </div>
                                    <div class="field-group">
                                        <label class="field-label">Total Stok</label>
                                        <input type="number" name="stock" id="edit_stock" class="field-input" min="0">
                                        <span class="field-hint" id="edit_stock_hint"></span>
                                    </div>
                                </div>
                            </div>
                            <div style="flex:1;">
                                <div class="field-group">
                                    <label class="field-label">Foto Barang</label>
                                    <div class="img-preview-box" id="editImgBox" onclick="document.getElementById('editImgInput').click()">
                                        <div class="placeholder" id="editImgPlaceholder">
                                            <i class="fas fa-image d-block mb-1" style="font-size:1.5rem;opacity:.4;"></i>
                                            Klik untuk ganti
                                        </div>
                                    </div>
                                    <input type="file" name="image" id="editImgInput" accept="image/*" style="display:none;">
                                    <span class="field-hint">Kosongkan jika tidak diganti</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light"
                                style="border-radius:8px;font-weight:600;border:1.5px solid #e2e8f0;"
                                data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm text-dark font-weight-bold"
                                style="border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);border:none;padding:7px 18px;">
                            <i class="fas fa-save mr-1"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         MODAL KELOLA STOK
    ══════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="stockModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-hdr-blue text-white border-0" style="padding:14px 20px;">
                    <div class="d-flex align-items-center">
                        <div style="width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.2);
                                    display:flex;align-items:center;justify-content:center;margin-right:10px;">
                            <i class="fas fa-boxes" style="font-size:.85rem;"></i>
                        </div>
                        <h5 class="modal-title mb-0 font-weight-bold" style="font-size:.95rem;">
                            Kelola Stok — <span id="stockItemName"></span>
                        </h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" style="opacity:.8;">&times;</button>
                </div>
                <form id="stockForm">
                    <input type="hidden" id="stock_item_id">
                    <div class="modal-body">

                        {{-- Info stok saat ini --}}
                        <div style="background:#f8fafc;border-radius:10px;padding:12px 16px;border-left:3px solid #4361ee;margin-bottom:16px;">
                            <div style="font-size:.7rem;font-weight:700;color:#4361ee;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">
                                Stok Saat Ini
                            </div>
                            <div class="d-flex gap-4">
                                <div>
                                    <div style="font-size:.7rem;color:#94a3b8;">Total</div>
                                    <div style="font-weight:700;font-size:1.2rem;color:#0f172a;" id="stockCurrentTotal">—</div>
                                </div>
                                <div>
                                    <div style="font-size:.7rem;color:#94a3b8;">Tersedia</div>
                                    <div style="font-weight:700;font-size:1.2rem;color:#10b981;" id="stockCurrentAvail">—</div>
                                </div>
                                <div>
                                    <div style="font-size:.7rem;color:#94a3b8;">Dipinjam</div>
                                    <div style="font-weight:700;font-size:1.2rem;color:#f59e0b;" id="stockCurrentUsed">—</div>
                                </div>
                            </div>
                        </div>

                        {{-- Jenis perubahan --}}
                        <div class="field-group">
                            <label class="field-label">Tipe Perubahan</label>
                            <div class="stock-type-toggle">
                                <div class="stock-type-btn active-add" id="btnAdd" onclick="setStockType('add')">
                                    <i class="fas fa-plus-circle mr-1"></i> Tambah Stok
                                </div>
                                <div class="stock-type-btn" id="btnReduce" onclick="setStockType('reduce')">
                                    <i class="fas fa-minus-circle mr-1"></i> Kurangi Stok
                                </div>
                            </div>
                            <input type="hidden" name="type" id="stock_type" value="add">
                        </div>

                        <div class="form-row-2">
                            <div class="field-group">
                                <label class="field-label">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="stock_amount" class="field-input" required min="1" placeholder="0">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Catatan <span style="font-weight:400;color:#94a3b8;">(Opsional)</span></label>
                                <input type="text" name="note" class="field-input" placeholder="Alasan penyesuaian stok...">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light"
                                style="border-radius:8px;font-weight:600;border:1.5px solid #e2e8f0;"
                                data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm text-white font-weight-bold" id="stockSubmitBtn"
                                style="border-radius:8px;background:linear-gradient(135deg,#4361ee,#3a0ca3);border:none;padding:7px 18px;">
                            <i class="fas fa-check mr-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@include('admin.inventory.partials.modal-categories')
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    let currentCategory = '';

    // ── DataTable ──────────────────────────────────────────────
    const table = $('#inventoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('inventory.data') }}",
            data: function (d) {
                d.category = currentCategory;
            }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            {
                data: null,
                name: 'name',
                orderable: true,
                searchable: true,
                render: function (d) {
                    const img = d.image
                        ? `<img src="/storage/${d.image}" class="item-img">`
                        : `<div class="item-img-placeholder"><i class="fas fa-box"></i></div>`;
                    return `<div class="d-flex align-items-center">
                        ${img}
                        <div>
                            <div class="item-name">${d.name}</div>
                            <div class="item-price">${d.price_fmt}/hari</div>
                        </div>
                    </div>`;
                }
            },
            {
                data: 'category_label',
                name: 'category',
                render: d => `<span class="cat-badge">${d}</span>`
            },
            {
                data: 'price_fmt',
                name: 'price_per_day',
                className: 'text-center',
                orderable: true
            },
            {
                data: 'stok_info',
                name: 'stock',
                orderable: false,
                render: function (d) {
                    return `<div class="stock-bar-wrap">
                        <div class="stock-nums">
                            <span class="stock-avail">${d.available} tersedia</span>
                            <span class="stock-total">/ ${d.total}</span>
                        </div>
                        <div class="stock-track">
                            <div class="stock-fill" style="width:${d.pct}%;background:${d.color};"></div>
                        </div>
                    </div>`;
                }
            },
            {
                data: 'status_badge',
                name: 'is_active',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
            processing: '<div style="padding:16px;color:#64748b;"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</div>'
        },
        drawCallback: updateKpi,
    });

    // ── Filter tabs ────────────────────────────────────────────
    $('#categoryTabs a').on('click', function (e) {
        e.preventDefault();
        $('#categoryTabs a').removeClass('active');
        $(this).addClass('active');
        currentCategory = $(this).data('category');
        table.ajax.reload();
    });

    // ── KPI update dari DataTables info ───────────────────────
    function updateKpi() {
        $.get("{{ route('inventory.data') }}", { category: currentCategory, kpi: 1 }, function (d) {
            if (!d.kpi) return;
            $('#kpiTotal').text(d.kpi.total);
            $('#kpiActive').text(d.kpi.active);
            $('#kpiStokTotal').text(d.kpi.stok_total);
            $('#kpiStokAv').text(d.kpi.stok_available);
            $('#kpiStokUsed').text(d.kpi.stok_used);
        });
    }
    updateKpi();

    // ── Export dropdown ────────────────────────────────────────
    $('#toggleExport').on('click', function (e) {
        e.stopPropagation();
        $('#exportMenu').toggleClass('open');
    });

    $(document).on('click', function () {
        $('#exportMenu').removeClass('open');
    });

    $('#doExportExcel').on('click', function (e) {
        e.preventDefault();
        window.location.href = "{{ route('inventory.export.excel') }}?category=" + (currentCategory || 'all');
    });

    $('#doExportPdf').on('click', function (e) {
        e.preventDefault();
        window.open("{{ route('inventory.export.pdf') }}?category=" + (currentCategory || 'all'), '_blank');
    });

    // ── Image preview (tambah) ────────────────────────────────
    $('#addImgInput').on('change', function () {
        previewImg(this, 'addImgBox');
    });

    // ── Image preview (edit) ──────────────────────────────────
    $('#editImgInput').on('change', function () {
        previewImg(this, 'editImgBox');
    });

    function previewImg(input, boxId) {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const box = document.getElementById(boxId);
            box.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
        };
        reader.readAsDataURL(file);
    }

    // ── Tambah Barang ──────────────────────────────────────────
    $('#addItemForm').on('submit', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        const fd = new FormData(this);
        $.ajax({
            url: "{{ route('inventory.store') }}",
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#addItemModal').modal('hide');
                        table.ajax.reload();
                        $('#addItemForm')[0].reset();
                        document.getElementById('addImgBox').innerHTML = '<div class="placeholder"><i class="fas fa-image d-block mb-1" style="font-size:1.5rem;opacity:.4;"></i>Klik untuk upload</div>';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            },
            error: function (xhr) {
                const errors = xhr.responseJSON?.errors;
                const msg = errors ? Object.values(errors)[0][0] : (xhr.responseJSON?.message || 'Terjadi kesalahan');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: msg
                });
            }
        });
    });

    // ── Edit Barang — buka modal ───────────────────────────────
    $(document).on('click', '.btn-edit-item', function () {
        const id = $(this).data('id');
        $.get("{{ url('/inventory') }}/" + id, function (res) {
            if (!res.success) return;
            const d = res.data;
            $('#edit_item_id').val(d.id);
            $('#edit_name').val(d.name);
            $('#edit_category').val(d.category);
            $('#edit_price').val(d.price_per_day);
            $('#edit_stock').val(d.stock);
            $('#edit_is_active').val(d.is_active ? '1' : '0');
            $('#edit_stock_hint').text(`Stok tersedia saat ini: ${d.stock_available}`);
            // Tampilkan gambar lama
            const box = document.getElementById('editImgBox');
            if (d.image) {
                box.innerHTML = `<img src="/storage/${d.image}" style="width:100%;height:100%;object-fit:cover;">`;
            } else {
                box.innerHTML = '<div class="placeholder"><i class="fas fa-image d-block mb-1" style="font-size:1.5rem;opacity:.4;"></i>Klik untuk ganti</div>';
            }
            $('#editItemModal').modal('show');
        });
    });

    // ── Edit Barang — submit ───────────────────────────────────
    $('#editItemForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#edit_item_id').val();
        Swal.fire({
            title: 'Memperbarui...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        const fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: "{{ url('/inventory') }}/" + id,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#editItemModal').modal('hide');
                        table.ajax.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            },
            error: function (xhr) {
                const errors = xhr.responseJSON?.errors;
                const msg = errors ? Object.values(errors)[0][0] : (xhr.responseJSON?.message || 'Terjadi kesalahan');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: msg
                });
            }
        });
    });

    // ── Kelola Stok — buka modal ───────────────────────────────
    $(document).on('click', '.btn-stock', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const tot = $(this).data('total');
        const av = $(this).data('available');
        $('#stock_item_id').val(id);
        $('#stockItemName').text(name);
        $('#stockCurrentTotal').text(tot);
        $('#stockCurrentAvail').text(av);
        $('#stockCurrentUsed').text(tot - av);
        $('#stock_amount').val('');
        setStockType('add');
        $('#stockModal').modal('show');
    });

    // ── Kelola Stok — submit ───────────────────────────────────
    $('#stockForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#stock_item_id').val();
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        $.ajax({
            url: "{{ url('/inventory') }}/" + id + "/stock",
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                type: $('#stock_type').val(),
                amount: $('#stock_amount').val(),
                note: $('input[name="note"]').val(),
            },
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#stockModal').modal('hide');
                        table.ajax.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                });
            }
        });
    });

    // ── Toggle Status ──────────────────────────────────────────
    $(document).on('click', '.btn-toggle-status', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        Swal.fire({
            title: 'Ubah Status?',
            html: `Ubah status aktif/nonaktif untuk <strong>${name}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Ubah',
            cancelButtonText: 'Batal',
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: "{{ url('/inventory') }}/" + id + "/toggle",
                method: 'PATCH',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        table.ajax.reload(null, false);
                    }
                },
                error: () => Swal.fire('Error!', 'Terjadi kesalahan', 'error')
            });
        });
    });

    // ── Hapus ──────────────────────────────────────────────────
    $(document).on('click', '.btn-delete-item', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        Swal.fire({
            title: 'Hapus Barang?',
            html: `Yakin ingin menghapus <strong>${name}</strong>?<br><small class="text-muted">Data yang dihapus tidak bisa dikembalikan.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: "{{ url('/inventory') }}/" + id,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Bisa Dihapus',
                            text: res.message
                        });
                    }
                },
                error: () => Swal.fire('Error!', 'Terjadi kesalahan', 'error')
            });
        });
    });

    // ════════════════════════════════════════════════════════════
    // CATEGORY MANAGEMENT
    // ════════════════════════════════════════════════════════════

    // ── Load daftar kategori ───────────────────────────────────
    function loadCategories() {
        $.get("{{ route('inventory.categories') }}", function (res) {
            if (!res.success) return;
            const cats = res.data;
            $('#catCount').text('(' + cats.length + ' kategori)');
            if (!cats.length) {
                $('#catTableBody').html(
                    '<tr><td colspan="4" style="text-align:center;padding:24px;color:#94a3b8;">Belum ada kategori.</td></tr>'
                );
                return;
            }
            const rows = cats.map(function (c) {
                const activeBadge = c.is_active
                    ? '<span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:10px;font-size:.7rem;font-weight:700;">Aktif</span>'
                    : '<span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:10px;font-size:.7rem;font-weight:700;">Nonaktif</span>';
                return `<tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 12px;font-weight:600;color:#0f172a;">
                        <i class="fas fa-box" style="color:#7c3aed;margin-right:7px;"></i>${c.name}
                    </td>
                    <td style="padding:10px 12px;">
                        <code style="background:#f1f5f9;padding:2px 7px;border-radius:4px;font-size:.75rem;color:#475569;">${c.key}</code>
                    </td>
                    <td style="padding:10px 12px;text-align:center;">${activeBadge}</td>
                    <td style="padding:10px 12px;text-align:center;">
                        <button class="action-btn ab-edit btn-edit-cat mr-1"
                        data-id="${c.id}"
                        data-name="${c.name}"
                        data-active="${c.is_active ? 1 : 0}"
                        title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn ab-delete btn-delete-cat"
                        data-id="${c.id}"
                        data-name="${c.name}"
                        title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
            }).join('');
            $('#catTableBody').html(rows);
        });
    }

    // Buka modal → langsung load
    $('#manageCatModal').on('show.bs.modal', loadCategories);

    // ── Tambah kategori ────────────────────────────────────────
    $('#addCatForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('inventory.categories.store') }}",
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', name: $('#newCatName').val() },
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false
                    });
                    $('#newCatName').val('').focus();
                    loadCategories();
                    reloadCategoryTabs();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.errors
                    ? Object.values(xhr.responseJSON.errors)[0][0]
                    : (xhr.responseJSON?.message || 'Terjadi kesalahan');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: msg
                });
            }
        });
    });

    // ── Edit kategori — buka modal ─────────────────────────────
    $(document).on('click', '.btn-edit-cat', function () {
        const d = $(this).data();
        $('#editCatId').val(d.id);
        $('#editCatName').val(d.name);
        $('#editCatActive').val(d.active);
        $('#editCatModal').modal('show');
    });

    // ── Edit kategori — submit ─────────────────────────────────
    $('#editCatForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#editCatId').val();
        $.ajax({
            url: "{{ url('/inventory/categories') }}/" + id,
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                name: $('#editCatName').val(),
                is_active: $('#editCatActive').val(),
            },
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false
                    });
                    $('#editCatModal').modal('hide');
                    loadCategories();
                    reloadCategoryTabs();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.errors
                    ? Object.values(xhr.responseJSON.errors)[0][0]
                    : (xhr.responseJSON?.message || 'Terjadi kesalahan');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: msg
                });
            }
        });
    });

    // ── Hapus kategori ─────────────────────────────────────────
    $(document).on('click', '.btn-delete-cat', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        Swal.fire({
            title: 'Hapus Kategori?',
            html: `Yakin hapus kategori <strong>${name}</strong>?<br>
                <small class="text-muted">Kategori yang masih memiliki barang tidak bisa dihapus.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: "{{ url('/inventory/categories') }}/" + id,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: res.message,
                            timer: 1800,
                            showConfirmButton: false
                        });
                        loadCategories();
                        reloadCategoryTabs();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Bisa Dihapus',
                            text: res.message
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                    });
                }
            });
        });
    });

    // ── Reload filter tabs setelah kategori berubah ────────────
    function reloadCategoryTabs() {
        $.get("{{ route('inventory.categories') }}", function (res) {
            if (!res.success) return;
            const activeCats = res.data.filter(function (c) {
                return c.is_active;
            });
            let html = `<a href="#" class="filter-tab ${currentCategory === '' ? 'active' : ''}" data-category="">
                <i class="fas fa-th"></i> Semua
            </a>`;
            activeCats.forEach(function (c) {
                const isActive = currentCategory === c.key ? 'active' : '';
                html += `<a href="#" class="filter-tab ${isActive}" data-category="${c.key}">
                    <i class="fas fa-box"></i> ${c.name}
                </a>`;
            });
            $('#categoryTabs').html(html);
            // Re-attach event
            $('#categoryTabs a').off('click').on('click', function (e) {
                e.preventDefault();
                $('#categoryTabs a').removeClass('active');
                $(this).addClass('active');
                currentCategory = $(this).data('category');
                table.ajax.reload();
            });
            // Reload dropdown di form tambah/edit barang
            const opts = activeCats.map(function (c) {
                return `<option value="${c.key}">${c.name}</option>`;
            }).join('');
            $('select[name="category"]').each(function () {
                const cur = $(this).val();
                $(this).html('<option value="">-- Pilih Kategori --</option>' + opts);
                if (cur) $(this).val(cur);
            });
        });
    }
});

// ── Stock type toggle ──────────────────────────────────────
function setStockType(type) {
    document.getElementById('stock_type').value = type;
    const addBtn = document.getElementById('btnAdd');
    const reduceBtn = document.getElementById('btnReduce');
    const submitBtn = document.getElementById('stockSubmitBtn');
    if (type === 'add') {
        addBtn.classList.add('active-add');
        addBtn.classList.remove('active-reduce');
        reduceBtn.classList.remove('active-add', 'active-reduce');
        submitBtn.style.background = 'linear-gradient(135deg,#10b981,#059669)';
    } else {
        reduceBtn.classList.add('active-reduce');
        reduceBtn.classList.remove('active-add');
        addBtn.classList.remove('active-add', 'active-reduce');
        submitBtn.style.background = 'linear-gradient(135deg,#ef4444,#dc2626)';
    }
}
</script>
@endpush