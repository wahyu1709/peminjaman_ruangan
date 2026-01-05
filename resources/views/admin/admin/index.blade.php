@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    {{ $title }}
</h1>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
        <div class="mb-1 mr-2">
            <a href="{{ route('adminCreate') }}" class="btn btn-sm btn-primary">
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
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Jabatan</th>
                        <th>NIM/NIP</th>
                        <th>Email</th>
                        <th>
                            <i class="fas fa-cog"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                @if ($user->jenis_pengguna == 'staff')
                                    <span class="badge badge-dark">
                                        {{ $user->jenis_pengguna }}</td>
                                    </span>
                                @elseif ($user->jenis_pengguna == 'dosen')
                                    <span class="badge badge-primary">
                                        {{ $user->jenis_pengguna }}</td>
                                    </span>
                                @else
                                    <span class="badge badge-success">
                                        {{ $user->jenis_pengguna }}</td>
                                    </span>
                                @endif                                
                            </td>
                            <td>{{ $user->nim_nip }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $user->email }}</td>
                                </span>
                            <td class="text-center">
                                <a href="{{ route('adminEdit', $user->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exampleModal{{ $user->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @include('admin/admin/modal')
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection