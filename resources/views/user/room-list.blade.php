@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>

    <!-- Card Filter -->
    <div class="card shadow-lg border-0 mb-5">
        <div class="card-header bg-primary py-3">
            <h6 class="m-0 font-weight-bold text-center text-white">
                <i class="bi bi-filter-right me-2"></i>
                Cari Ruangan Kosong
            </h6>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ url('/ruangan-user') }}" class="row g-3">
                <!-- Tanggal -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-bold">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" 
                            value="{{ request('tanggal') }}" required>
                </div>

                <!-- Jam Mulai -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-bold">Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control" 
                            value="{{ request('jam_mulai') }}" required step="1800">
                </div>

                <!-- Jam Selesai -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-bold">Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-control" 
                            value="{{ request('jam_selesai') }}" required step="1800">
                </div>

                <!-- Lokasi -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-bold">Lokasi</label>
                    <select name="lokasi" class="form-control">
                        <option value="">Semua Lokasi</option>
                        @foreach($lokasiList as $lok)
                        <option value="{{ $lok }}" {{ request('lokasi') == $lok ? 'selected' : '' }}>
                            {{ $lok }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kapasitas Minimal -->
                {{-- <div class="col-md-3 col-sm-6">
                <label class="form-label fw-bold">Kapasitas Minimal</label>
                <input type="number" name="kapasitas_min" class="form-control" 
                        value="{{ request('kapasitas_min') }}" min="1" placeholder="Contoh: 20">
                </div> --}}

                <!-- Tombol Cari & Reset -->
                <div class="col-md-3 col-sm-6 align-items-end gap-2 mt-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search me-2"></i>Cari Ruangan
                    </button>
                    <a href="{{ url('/ruangan-user') }}" class="btn btn-outline-secondary flex-fill">
                        <i class="bi bi-arrow-repeat me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @foreach($rooms as $room)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="{{ $room->gambar ? asset('storage/' . $room->gambar) : asset('enno/assets/img/ruang-rapat.png') }}" 
                         class="card-img-top" alt="{{ $room->nama_ruangan }}" style="height: 180px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">
                            <span class="badge bg-primary text-white">{{ $room->kode_ruangan }}</span>
                        </h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ $room->nama_ruangan }}</h6>
                        <p class="card-text">
                            <i class="fas fa-users me-2"></i> Kapasitas: <strong>{{ $room->kapasitas }} orang</strong><br>
                            @if($room->lokasi)
                                <i class="fas fa-map-marker-alt me-2"></i> {{ $room->lokasi }}
                            @endif
                            {{-- @if($room->lantai)
                                <br><i class="fas fa-building me-2"></i> Lantai: {{ $room->lantai }}
                            @endif --}}
                        </p>
                        <a href="{{ route('bookingCreate') }}" class="btn btn-primary btn-block">
                            Pinjam Ruangan
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($rooms->count() == 0)
        <div class="alert alert-info text-center mt-5">
            <i class="fas fa-info-circle me-2"></i> Belum ada ruangan tersedia.
        </div>
    @endif
</div>
@endsection