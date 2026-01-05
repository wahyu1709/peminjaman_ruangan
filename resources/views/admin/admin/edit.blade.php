@extends('layouts/app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    {{ $title }}
</h1>

<div class="card">
    <div class="card-header bg-warning">
        <a href="{{ route('admin') }}" class="btn btn-sm btn-success">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('adminUpdate', $user->id) }}" method="post">
        @csrf
        @method('put')
        <div class="row">
            <div class="col-xl-6 mb-2">
                <label class="form-label">
                    <span class="text-danger">*</span>
                    Nama :
                </label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $user->name }}">
                @error('name')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror
            </div>
            <div class="col-xl-6">
                <label class="form-label">
                    <span class="text-danger">*</span>
                    Email :
                </label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $user->email }}">
                @error('email')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-xl-3 mb-2">
                <label class="form-label">
                    <span class="text-danger">*</span>
                    Jenis Pengguna :
                </label>
                <select name="jenis_pengguna" class="form-control @error('jenis_pengguna') is-invalid @enderror">
                    <option value="" selected disabled>---Pilih Jenis Pengguna---</option>
                    <option value="staff" {{ $user->jenis_pengguna == 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="dosen" {{ $user->jenis_pengguna == 'dosen' ? 'selected' : '' }}>Dosen</option>
                </select>
                @error('jenis_pengguna')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror
            </div>
            <div class="col-xl-3 mb-2">
                <label class="form-label">
                    <span class="text-danger">*</span>
                    Pilih Jenis Role :
                </label>
                <select name="role" class="form-control @error('role') is-invalid @enderror">
                    <option value="#" selected disabled>--Pilih Jenis Role--</option>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                </select>
                @error('role')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror
            </div>
            <div class="col-xl-6">
                <label class="form-label">
                    <span class="text-danger">*</span>
                    NIM/NIP :
                </label>
                <input type="text" name="nim_nip" class="form-control @error('nim_nip') is-invalid @enderror" value="{{ $user->nim_nip }}">
                @error('nim_nip')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-6 mb-2">
                <label class="form-label">
                    <span class="text-danger">*</span>
                    Password :
                </label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror
            </div>
            <div class="col-xl-6">
                <label class="form-label">
                    <span class="text-danger">*</span>
                    Konfirmasi Password :
                </label>
                <input type="password" name="password_confirmation" class="form-control @error('password') is-invalid @enderror">
            </div>
        </div>

        <div>
            <button type="submit" class="btn btn-warning mt-4">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </button>
        </div>
        </form>
    </div>
</div>

@endsection