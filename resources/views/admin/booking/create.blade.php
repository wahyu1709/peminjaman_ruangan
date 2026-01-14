@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    {{ $title }}
</h1>

<div class="card">
    <div class="card-header bg-primary">
        <a href="{{ route('booking') }}" class="btn btn-sm btn-success">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('bookingStore') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-xl-6 mb-2">
                    <div class="form-label">
                        <label>Ruangan <span class="text-danger">*</span></label>
                        <select name="room_id" class="form-control @error('room_id') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->kode_ruangan }} - {{ $room->nama_ruangan }} ({{ $room->kapasitas }} orang)</option>
                            @endforeach
                        </select>
                        @error('room_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="form-label">
                        <label>Tanggal Pinjam <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pinjam" class="form-control @error('tanggal_pinjam') is-invalid @enderror" min="{{ date('Y-m-d') }}" required>
                        @error('tanggal_pinjam') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6 mb-2">
                    <div class="form-label">
                        <label for="role_unit">Peran / Unit Kerja <span class="text-muted">(Opsional)</span></label>
                        <select name="role_unit" id="role_unit" class="form-control">
                            <option value="" disabled selected>Pilih Peran / Unit Kerja</option>
                            <option value="Panitia Kegiatan">Panitia Kegiatan</option>
                            <option value="Koordinator MK">Koordinator MK</option>
                            <option value="Pengelola PS S1">Pengelola PS S1</option>
                            <option value="Pengelola PS S2">Pengelola PS S2</option>
                            <option value="Pengelola PS S3">Pengelola PS S3</option>
                            <option value="Departemen Kep Dasar">Departemen Kep Dasar</option>
                        </select>
                    </div>
                </div>
                <div class="col-xl-3 mb-2">
                    <div class="form-label">
                        <label>Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="waktu_mulai" class="form-control @error('waktu_mulai') is-invalid @enderror" required>
                        @error('waktu_mulai') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="col-xl-3 mb-2">
                    <div class="form-label">
                        <label>Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" name="waktu_selesai" class="form-control @error('waktu_selesai') is-invalid @enderror" required>
                        @error('waktu_selesai') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="form-label">
                        <label>Keperluan <span class="text-danger">*</span></label>
                        <textarea name="keperluan" class="form-control @error('keperluan') is-invalid @enderror" rows="4" required></textarea>
                        @error('keperluan') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-primary mt-4">
                    <i class="fas fa-save mr-2"></i> Submit Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection