@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    {{ $title }}
</h1>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
        <div class="mb-1 mr-2">
            <a href="{{ route('roomCreate') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Tambah Data
            </a>
        </div>

        <div>
            <a href="#" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel mr-2"></i>
                Excel
            </a>
            <a href="#" class="btn btn-sm btn-danger">
                <i class="fas fa-file-pdf mr-2"></i>
                PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>No</th>
                        <th>Kode Ruangan</th>
                        <th>Nama Ruangan</th>
                        <th>Lokasi</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                        <th>
                            <i class="fas fa-cog"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rooms as $room)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $room->kode_ruangan }}</td>
                            <td>{{ $room->nama_ruangan }}</td>
                            <td>{{ $room->lokasi }}</td>
                            <td>{{ $room->kapasitas }}</td>
                            <td class="text-center">
                                @if($room->is_active == 1)
                                    <span class="badge badge-success">Tersedia</span>
                                @else
                                    <span class="badge badge-danger">Tidak Tersedia</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('roomEdit', $room->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exampleModal{{ $room->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @include('admin/room/modal')
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection