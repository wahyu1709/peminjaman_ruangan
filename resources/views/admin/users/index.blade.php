@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

<div class="card shadow mb-4">
    <!-- Card Header dengan Tab -->
    <div class="card-header py-3 bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <!-- Tabs Filter -->
            <ul class="nav nav-pills mb-2 mb-md-0" id="userTypeTabs" role="tablist">
                <li class="nav-item mr-1">
                    <a class="nav-link active py-2 px-3" id="tab-all" data-filter="" href="#">
                        <i class="fas fa-users mr-1"></i> Semua
                    </a>
                </li>
                <li class="nav-item mr-1">
                    <a class="nav-link py-2 px-3" id="tab-mahasiswa" data-filter="mahasiswa" href="#">
                        <i class="fas fa-user-graduate mr-1"></i> Mahasiswa
                    </a>
                </li>
                <li class="nav-item mr-1">
                    <a class="nav-link py-2 px-3" id="tab-dosen" data-filter="dosen" href="#">
                        <i class="fas fa-chalkboard-teacher mr-1"></i> Dosen
                    </a>
                </li>
                <li class="nav-item mr-1">
                    <a class="nav-link py-2 px-3" id="tab-staff" data-filter="staff" href="#">
                        <i class="fas fa-user-tie mr-1"></i> Staff
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3" id="tab-umum" data-filter="umum" href="#">
                        <i class="fas fa-user-friends mr-1"></i> Umum
                    </a>
                </li>
            </ul>

            <!-- Tombol Aksi -->
            <div class="d-flex gap-2">
                <div class="dropdown mr-2">
                    <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-download mr-1"></i> Export
                    </button>
                    <div class="dropdown-menu" aria-labelledby="exportDropdown">
                        <a class="dropdown-item" href="{{ route('users.excel') }}" id="exportExcel">
                            <i class="fas fa-file-excel text-success mr-2"></i> Excel
                        </a>
                        <a class="dropdown-item" href="{{ route('users.pdf') }}" id="exportPdf">
                            <i class="fas fa-file-pdf text-danger mr-2"></i> PDF
                        </a>
                    </div>
                </div>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUserModal">
                    <i class="fas fa-plus mr-1"></i> Tambah Pengguna
                </button>
            </div>
        </div>
    </div>

    <!-- Card Body -->
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="usersTable" width="100%">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>NIM/NIP</th>
                        <th width="12%">Jenis</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data dimuat via DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus mr-2"></i> Tambah Pengguna
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addUserForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Jenis Pengguna <span class="text-danger">*</span></label>
                        <select name="jenis_pengguna" class="form-control" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="dosen">Dosen</option>
                            <option value="staff">Staff</option>
                            <option value="umum">Umum</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>NIM/NIP <span class="text-muted">(Opsional)</span></label>
                        <input type="text" name="nim_nip" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i> Edit Pengguna
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password <span class="text-muted">(Kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" class="form-control" minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Jenis Pengguna <span class="text-danger">*</span></label>
                        <select name="jenis_pengguna" id="edit_jenis" class="form-control" required>
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="dosen">Dosen</option>
                            <option value="staff">Staff</option>
                            <option value="umum">Umum</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>NIM/NIP</label>
                        <input type="text" name="nim_nip" id="edit_nim_nip" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save mr-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentFilter = '';

    // Inisialisasi DataTable
    const table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('users.data') }}",
            data: function(d) {
                d.jenis_pengguna = currentFilter;
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'nim_nip', name: 'nim_nip', defaultContent: '-' },
            { data: 'jenis_badge', name: 'jenis_badge', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
            processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><br>Memuat data...'
        }
    });

    // Tab Filter
    $('#userTypeTabs a').on('click', function(e) {
        e.preventDefault();
        $('#userTypeTabs a').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        table.ajax.reload();
    });

    // Export Excel
    $('#exportExcel').on('click', function(e) {
        e.preventDefault();
        const jenis = currentFilter || 'all';
        window.location.href = "{{ route('users.excel') }}?jenis=" + jenis;
    });

    // Export PDF
    $('#exportPdf').on('click', function(e) {
        e.preventDefault();
        const jenis = currentFilter || 'all';
        window.open("{{ route('users.pdf') }}?jenis=" + jenis, '_blank');
    });

    // Submit Form Tambah User
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        
        // Tampilkan loading
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('users.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#addUserModal').modal('hide');
                        table.ajax.reload();
                        $('#addUserForm')[0].reset();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Terjadi kesalahan saat menambahkan data'
                    });
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan sistem';
                
                if (xhr.responseJSON?.errors) {
                    // Ambil error pertama dari validasi Laravel
                    const errors = xhr.responseJSON.errors;
                    errorMsg = Object.values(errors)[0][0];
                } else if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMsg
                });
            }
        });
    });

    // Tombol Edit
    $(document).on('click', '.btn-edit', function() {
        const data = $(this).data();
        $('#edit_user_id').val(data.id);
        $('#edit_name').val(data.name);
        $('#edit_email').val(data.email);
        $('#edit_nim_nip').val(data.nim_nip);
        $('#edit_jenis').val(data.jenis);
        $('#editUserModal').modal('show');
    });

    // Submit Form Edit User
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        const userId = $('#edit_user_id').val();
        
        // Tampilkan loading
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ url('/users') }}/" + userId,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#editUserModal').modal('hide');
                        table.ajax.reload();
                        $('#editUserForm')[0].reset();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Terjadi kesalahan saat update data'
                    });
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan sistem';
                
                if (xhr.responseJSON?.errors) {
                    // Ambil error pertama
                    const errors = xhr.responseJSON.errors;
                    errorMsg = Object.values(errors)[0][0];
                } else if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMsg
                });
            }
        });
    });

    // Tombol Hapus
    $(document).on('click', '.btn-delete', function() {
        const userId = $(this).data('id');
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Yakin ingin menghapus pengguna ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/users') }}/" + userId,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Terhapus!', response.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Terjadi kesalahan sistem', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush