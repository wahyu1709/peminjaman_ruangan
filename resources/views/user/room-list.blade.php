@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Ruangan Tersedia</h1>
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