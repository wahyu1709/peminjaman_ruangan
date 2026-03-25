@extends('layouts.app')

@push('styles')
<style>
    :root {
        --card-r:  14px;
        --shadow:  0 2px 16px rgba(0,0,0,0.07);
        --shadow-h:0 8px 28px rgba(0,0,0,0.13);
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Page card ───────────────────────────────────────────── */
    .page-card {
        border: none;
        border-radius: var(--card-r);
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: fadeUp .4s ease both;
    }

    .page-card-header {
        padding: 14px 20px;
        background: linear-gradient(90deg, #4361ee, #4895ef);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    /* ── Filter tabs ─────────────────────────────────────────── */
    .filter-tabs {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .filter-tab {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 13px;
        border-radius: 20px;
        font-size: .78rem;
        font-weight: 600;
        border: 1.5px solid rgba(255,255,255,.35);
        color: rgba(255,255,255,.8);
        background: transparent;
        cursor: pointer;
        transition: all .2s;
        text-decoration: none;
    }

    .filter-tab:hover {
        background: rgba(255,255,255,.15);
        color: #fff;
        text-decoration: none;
    }

    .filter-tab.active {
        background: #fff;
        color: #4361ee;
        border-color: #fff;
        font-weight: 700;
    }

    /* ── Header right buttons ────────────────────────────────── */
    .header-actions { display: flex; gap: 8px; align-items: center; }

    .btn-hdr {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: .8rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: opacity .15s, transform .15s;
        text-decoration: none;
        white-space: nowrap;
    }
    .btn-hdr:hover  { opacity: .88; transform: translateY(-1px); text-decoration: none; }
    .btn-hdr:active { transform: scale(.97); }

    .btn-hdr-white  { background: #fff; color: #4361ee; }
    .btn-hdr-green  { background: #10b981; color: #fff; }
    .btn-hdr-export { background: rgba(255,255,255,.15); color: #fff; border: 1.5px solid rgba(255,255,255,.4); }
    .btn-hdr-export:hover { background: rgba(255,255,255,.25); color: #fff; }

    /* ── Table ───────────────────────────────────────────────── */
    .user-table thead tr { background: #f8fafc; }

    .user-table th {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0;
        padding: 11px 12px;
        white-space: nowrap;
    }

    .user-table td {
        padding: 11px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: .875rem;
        color: #334155;
    }

    .user-table tbody tr:last-child td { border-bottom: none; }
    .user-table tbody tr:hover td { background: #f8fafc; }

    /* ── User name cell ──────────────────────────────────────── */
    .user-avatar {
        width: 34px; height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4361ee, #4895ef);
        color: #fff;
        font-weight: 700;
        font-size: .85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-right: 10px;
    }

    .user-name   { font-weight: 600; color: #0f172a; }
    .user-nimnip { font-size: .72rem; color: #94a3b8; margin-top: 1px; }

    /* ── Jenis badges ────────────────────────────────────────── */
    .jenis-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .03em;
    }
    .jb-mahasiswa { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
    .jb-dosen     { background:#f0fdf4; color:#166534; border:1px solid #86efac; }
    .jb-staff     { background:#fef9c3; color:#854d0e; border:1px solid #fde047; }
    .jb-umum      { background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; }
    .jb-admin     { background:#fce7f3; color:#9d174d; border:1px solid #f9a8d4; }

    /* ── Action buttons ──────────────────────────────────────── */
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 7px;
        padding: 4px 10px;
        font-size: .78rem;
        font-weight: 600;
        border: none;
        transition: opacity .15s, transform .15s;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
    }
    .action-btn:hover  { opacity: .85; transform: translateY(-1px); }
    .action-btn:active { transform: scale(.97); }

    .ab-edit   { background:#fef9c3; color:#854d0e; }
    .ab-delete { background:#fee2e2; color:#991b1b; }

    /* ── Dropdown export ─────────────────────────────────────── */
    .export-dropdown {
        position: relative;
        display: inline-block;
    }
    .export-menu {
        position: absolute;
        top: calc(100% + 6px);
        right: 0;
        background: #fff;
        border-radius: 10px;
        box-shadow: var(--shadow-h);
        min-width: 150px;
        z-index: 999;
        overflow: hidden;
        display: none;
    }
    .export-menu.open { display: block; }
    .export-menu a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        font-size: .82rem;
        font-weight: 600;
        color: #334155;
        text-decoration: none;
        transition: background .15s;
    }
    .export-menu a:hover { background: #f8fafc; }

    /* ── Modal styles ────────────────────────────────────────── */
    .modal-header-blue   { background: linear-gradient(90deg,#4361ee,#4895ef); }
    .modal-header-amber  { background: linear-gradient(90deg,#d97706,#fbbf24); }
    .modal-header-danger { background: linear-gradient(90deg,#dc2626,#b91c1c); }

    .modal-content {
        border: none !important;
        border-radius: 14px !important;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,.15) !important;
    }

    .modal-body { padding: 20px !important; }
    .modal-footer {
        padding: 12px 20px !important;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9 !important;
    }

    /* Form fields in modal */
    .field-group { margin-bottom: 14px; }
    .field-label {
        font-size: .75rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 5px;
        display: block;
    }
    .field-input {
        width: 100%;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        padding: 8px 12px;
        font-size: .875rem;
        color: #1e293b;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }
    .field-input:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 3px rgba(67,97,238,.12);
    }
    .field-hint { font-size: .72rem; color: #94a3b8; margin-top: 4px; }

    /* Two column form */
    .form-row-2 { display: flex; gap: 12px; }
    .form-row-2 > div { flex: 1; min-width: 0; }

    /* Section divider in modal */
    .modal-section-title {
        font-size: .72rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin: 16px 0 12px;
        padding-bottom: 6px;
        border-bottom: 1px solid #f1f5f9;
    }

    /* ── Empty state ─────────────────────────────────────────── */
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #94a3b8;
    }
    .empty-state i { font-size: 2.8rem; display: block; margin-bottom: 12px; opacity: .4; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>

    <div class="page-card card">

        {{-- ── Header: Filter Tabs + Tombol ──────────────────── --}}
        <div class="page-card-header">

            {{-- Filter Tabs --}}
            <div class="filter-tabs" id="userTypeTabs">
                <a href="#" class="filter-tab active" data-filter="">
                    <i class="fas fa-users"></i> Semua
                </a>
                <a href="#" class="filter-tab" data-filter="mahasiswa">
                    <i class="fas fa-user-graduate"></i> Mahasiswa
                </a>
                <a href="#" class="filter-tab" data-filter="dosen">
                    <i class="fas fa-chalkboard-teacher"></i> Dosen
                </a>
                <a href="#" class="filter-tab" data-filter="staff">
                    <i class="fas fa-user-tie"></i> Staff
                </a>
                <a href="#" class="filter-tab" data-filter="umum">
                    <i class="fas fa-user-friends"></i> Umum
                </a>
            </div>

            {{-- Action Buttons --}}
            <div class="header-actions">
                {{-- Export dropdown --}}
                <div class="export-dropdown">
                    <button class="btn-hdr btn-hdr-export" id="toggleExport">
                        <i class="fas fa-download"></i> Export
                        <i class="fas fa-chevron-down" style="font-size:.65rem;margin-left:2px;"></i>
                    </button>
                    <div class="export-menu" id="exportMenu">
                        <a href="#" id="exportExcel">
                            <i class="fas fa-file-excel" style="color:#10b981;"></i> Excel
                        </a>
                        <a href="#" id="exportPdf">
                            <i class="fas fa-file-pdf" style="color:#ef4444;"></i> PDF
                        </a>
                    </div>
                </div>

                <button class="btn-hdr btn-hdr-white" data-toggle="modal" data-target="#addUserModal">
                    <i class="fas fa-plus"></i> Tambah Pengguna
                </button>
            </div>
        </div>

        {{-- ── Tabel ──────────────────────────────────────────── --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table user-table mb-0" id="usersTable" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:44px;">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No. HP</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-center" style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Diisi DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         MODAL TAMBAH PENGGUNA
    ══════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-blue text-white border-0" style="padding:14px 20px;">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.2);
                                    display:flex;align-items:center;justify-content:center;margin-right:8px;">
                            <i class="fas fa-user-plus" style="font-size:.85rem;"></i>
                        </div>
                        <h5 class="modal-title mb-0 font-weight-bold" style="font-size:.95rem;">
                            Tambah Pengguna Baru
                        </h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal"
                            style="opacity:.8;">&times;</button>
                </div>

                <form id="addUserForm">
                    @csrf
                    <div class="modal-body">

                        <div class="form-row-2">
                            <div class="field-group">
                                <label class="field-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="field-input" required placeholder="Nama lengkap">
                            </div>
                            <div class="field-group">
                                <label class="field-label">NIM / NIP <span style="font-weight:400;color:#94a3b8;">(Opsional)</span></label>
                                <input type="text" name="nim_nip" class="field-input" placeholder="Nomor identitas">
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="field-input" required placeholder="email@example.com">
                        </div>

                        <div class="form-row-2">
                            <div class="field-group">
                                <label class="field-label">No. HP <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="field-input" required placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Jenis Pengguna <span class="text-danger">*</span></label>
                                <select name="jenis_pengguna" class="field-input" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="mahasiswa">Mahasiswa</option>
                                    <option value="dosen">Dosen</option>
                                    <option value="staff">Staff</option>
                                    <option value="umum">Umum</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-section-title">Keamanan Akun</div>

                        <div class="form-row-2">
                            <div class="field-group">
                                <label class="field-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="field-input" required minlength="6"
                                       placeholder="Min. 6 karakter">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="field-input" required minlength="6"
                                       placeholder="Ulangi password">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light"
                                style="border-radius:8px;font-weight:600;border:1.5px solid #e2e8f0;"
                                data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm text-white font-weight-bold"
                                style="border-radius:8px;background:linear-gradient(135deg,#4361ee,#3a0ca3);
                                       border:none;padding:7px 18px;">
                            <i class="fas fa-save mr-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         MODAL EDIT PENGGUNA
    ══════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-amber text-dark border-0" style="padding:14px 20px;">
                    <div class="d-flex align-items-center">
                        <div style="width:30px;height:30px;border-radius:50%;background:rgba(0,0,0,.1);
                                    display:flex;align-items:center;justify-content:center;margin-right:8px;">
                            <i class="fas fa-edit" style="font-size:.85rem;"></i>
                        </div>
                        <h5 class="modal-title mb-0 font-weight-bold" style="font-size:.95rem;">
                            Edit Data Pengguna
                        </h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal"
                            style="opacity:.7;">&times;</button>
                </div>

                <form id="editUserForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="user_id" id="edit_user_id">

                    <div class="modal-body">

                        <div class="form-row-2">
                            <div class="field-group">
                                <label class="field-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit_name" class="field-input" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">NIM / NIP</label>
                                <input type="text" name="nim_nip" id="edit_nim_nip" class="field-input">
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="field-input" required>
                        </div>

                        <div class="form-row-2">
                            <div class="field-group">
                                <label class="field-label">No. HP <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" id="edit_phone" class="field-input" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Jenis Pengguna <span class="text-danger">*</span></label>
                                <select name="jenis_pengguna" id="edit_jenis" class="field-input" required>
                                    <option value="mahasiswa">Mahasiswa</option>
                                    <option value="dosen">Dosen</option>
                                    <option value="staff">Staff</option>
                                    <option value="umum">Umum</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-section-title">Ubah Password <span style="font-weight:400;color:#94a3b8;font-size:.75rem;">(Kosongkan jika tidak diubah)</span></div>

                        <div class="form-row-2">
                            <div class="field-group">
                                <label class="field-label">Password Baru</label>
                                <input type="password" name="password" class="field-input" minlength="6"
                                       placeholder="Min. 6 karakter">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="field-input" minlength="6"
                                       placeholder="Ulangi password baru">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light"
                                style="border-radius:8px;font-weight:600;border:1.5px solid #e2e8f0;"
                                data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm text-dark font-weight-bold"
                                style="border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);
                                       border:none;padding:7px 18px;">
                            <i class="fas fa-save mr-1"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentFilter = '';

    // ── DataTable ──────────────────────────────────────────────
    const table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('users.data') }}",
            data: function(d) { d.jenis_pengguna = currentFilter; }
        },
        columns: [
            { data: 'DT_RowIndex',  name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name',         name: 'name' },
            { data: 'email',        name: 'email' },
            { data: 'phone',        name: 'phone' },
            { data: 'jenis_badge',  name: 'jenis_badge', orderable: false, className: 'text-center' },
            { data: 'action',       name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
            processing: '<div style="padding:16px;color:#64748b;"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</div>'
        }
    });

    // ── Filter tabs ────────────────────────────────────────────
    $('#userTypeTabs a').on('click', function(e) {
        e.preventDefault();
        $('#userTypeTabs a').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        table.ajax.reload();
    });

    // ── Export dropdown toggle ────────────────────────────────
    $('#toggleExport').on('click', function(e) {
        e.stopPropagation();
        $('#exportMenu').toggleClass('open');
    });
    $(document).on('click', function() {
        $('#exportMenu').removeClass('open');
    });

    $('#exportExcel').on('click', function(e) {
        e.preventDefault();
        window.location.href = "{{ route('users.excel') }}?jenis=" + (currentFilter || 'all');
    });

    $('#exportPdf').on('click', function(e) {
        e.preventDefault();
        window.open("{{ route('users.pdf') }}?jenis=" + (currentFilter || 'all'), '_blank');
    });

    // ── Tambah User ────────────────────────────────────────────
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();

        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: "{{ route('users.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    Swal.fire({ icon:'success', title:'Berhasil!', text: res.message, timer:2000, showConfirmButton:false })
                        .then(() => { $('#addUserModal').modal('hide'); table.ajax.reload(); $('#addUserForm')[0].reset(); });
                } else {
                    Swal.fire({ icon:'error', title:'Gagal!', text: res.message || 'Terjadi kesalahan' });
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                const msg = errors ? Object.values(errors)[0][0] : (xhr.responseJSON?.message || 'Terjadi kesalahan sistem');
                Swal.fire({ icon:'error', title:'Error!', text: msg });
            }
        });
    });

    // ── Edit User ──────────────────────────────────────────────
    $(document).on('click', '.btn-edit', function() {
        const d = $(this).data();
        $('#edit_user_id').val(d.id);
        $('#edit_name').val(d.name);
        $('#edit_email').val(d.email);
        $('#edit_nim_nip').val(d.nim_nip);
        $('#edit_phone').val(d.phone);
        $('#edit_jenis').val(d.jenis);
        $('#editUserModal').modal('show');
    });

    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        const userId = $('#edit_user_id').val();

        Swal.fire({ title: 'Memperbarui...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: "{{ url('/users') }}/" + userId,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    Swal.fire({ icon:'success', title:'Berhasil!', text: res.message, timer:2000, showConfirmButton:false })
                        .then(() => { $('#editUserModal').modal('hide'); table.ajax.reload(); });
                } else {
                    Swal.fire({ icon:'error', title:'Gagal!', text: res.message || 'Terjadi kesalahan' });
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                const msg = errors ? Object.values(errors)[0][0] : (xhr.responseJSON?.message || 'Terjadi kesalahan sistem');
                Swal.fire({ icon:'error', title:'Error!', text: msg });
            }
        });
    });

    // ── Hapus User ─────────────────────────────────────────────
    $(document).on('click', '.btn-delete', function() {
        const userId = $(this).data('id');
        const userName = $(this).data('name') || 'pengguna ini';

        Swal.fire({
            title: 'Hapus Pengguna?',
            html: `Yakin ingin menghapus <strong>${userName}</strong>?<br><small class="text-muted">Data yang dihapus tidak bisa dikembalikan.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/users') }}/" + userId,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({ icon:'success', title:'Terhapus!', text: res.message, timer:2000, showConfirmButton:false });
                            table.ajax.reload();
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan sistem', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush