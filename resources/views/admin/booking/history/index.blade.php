@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

<div class="card">
    <div class="card-header bg-light border-bottom">
        <div class="row align-items-end g-3">
            <!-- Filter Status -->
            <div class="col-md-3 col-sm-6">
                <label for="filter_status" class="form-label fw-medium text-muted small mb-1">
                    Status Booking
                </label>
                <select id="filter_status" class="custom-select custom-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>

            <!-- Filter Tanggal Mulai -->
            <div class="col-md-3 col-sm-6">
                <label for="filter_start_date" class="form-label fw-medium text-muted small mb-1">
                    Tanggal Mulai
                </label>
                <input type="date" id="filter_start_date" class="form-control form-control-sm">
            </div>

            <!-- Filter Tanggal Selesai -->
            <div class="col-md-3 col-sm-6">
                <label for="filter_end_date" class="form-label fw-medium text-muted small mb-1">
                    Tanggal Selesai
                </label>
                <input type="date" id="filter_end_date" class="form-control form-control-sm">
            </div>

            <!-- Tombol Aksi -->
            <div class="col-md-3 col-sm-6 d-flex gap-2">
                <button id="apply-filter" class="btn btn-primary btn-sm flex-fill">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
                <button id="reset-filter" class="btn btn-outline-secondary btn-sm flex-fill">
                    <i class="fas fa-undo me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="bookingHistoryTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>No</th>
                        <th>Pengaju</th>
                        <th>Ruangan</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data dimuat oleh DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Pastikan jQuery siap
    if (typeof jQuery === 'undefined') {
        console.error('jQuery tidak dimuat!');
        return;
    }

    const table = $('#bookingHistoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('booking.history.data') }}",
            data: function (d) {
                // Ambil nilai saat request dikirim
                d.status = $('#filter_status').val() || '';
                d.start_date = $('#filter_start_date').val();
                d.end_date = $('#filter_end_date').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'user.name', name: 'user.name' },
            { data: 'room.nama_ruangan', name: 'room.nama_ruangan' },
            { data: 'tanggal_pinjam', name: 'tanggal_pinjam' },
            { data: 'time_range', name: 'time_range' },
            { data: 'keperluan', name: 'keperluan' },
            { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        }
    });

    // Pastikan event listener dipasang setelah DataTable siap
    function reloadTable() {
        table.ajax.reload(null, false); // false = jangan reset paging
    }

    $('#apply-filter').on('click', reloadTable);
    $('#reset-filter').on('click', function () {
        $('#filter_status').val('');
        $('#filter_start_date').val('');
        $('#filter_end_date').val('');
        reloadTable();
    });
    $('#filter_status, #filter_start_date, #filter_end_date').on('change', reloadTable);
});
</script>

@endsection
