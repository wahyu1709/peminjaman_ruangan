@extends('layouts.app')

@push('styles')
<style>
    :root {
        --room-radius: 14px;
        --room-shadow: 0 4px 20px rgba(0,0,0,0.08);
        --room-shadow-hover: 0 12px 36px rgba(0,0,0,0.15);
        --transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ── Filter ── */
    .filter-card {
        border: none;
        border-radius: var(--room-radius);
        box-shadow: var(--room-shadow);
        overflow: hidden;
    }
    .filter-card .card-header {
        background: linear-gradient(135deg, #1a56db 0%, #0e3fa0 100%);
        padding: 1rem 1.5rem;
        border: none;
    }
    .filter-card .form-control,
    .filter-card select.form-control {
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        padding: 0.55rem 0.85rem;
        font-size: 0.9rem;
        transition: border-color var(--transition), box-shadow var(--transition);
    }
    .filter-card .form-control:focus {
        border-color: #1a56db;
        box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.12);
    }

    /* ── Room Card ── */
    .room-card {
        border: none;
        border-radius: var(--room-radius);
        box-shadow: var(--room-shadow);
        transition: transform var(--transition), box-shadow var(--transition);
        overflow: hidden;
        height: 100%;
    }
    .room-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--room-shadow-hover);
    }

    .room-card__img-wrap {
        position: relative;
        height: 190px;
        overflow: hidden;
    }
    .room-card__img-wrap img {
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .room-card:hover .room-card__img-wrap img {
        transform: scale(1.05);
    }
    .room-card__img-overlay {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 55%;
        background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 100%);
        pointer-events: none;
    }

    /* Badge di atas gambar */
    .room-card__kode {
        position: absolute;
        top: 12px; left: 12px;
        background: #1a56db;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 4px 10px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(26,86,219,0.4);
    }
    .room-card__avail {
        position: absolute;
        top: 12px; right: 12px;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
    }

    /* Body */
    .room-card__body { padding: 1.1rem 1.25rem 1.25rem; }

    .room-card__name {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.6rem;
        line-height: 1.35;
    }

    .room-card__meta {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 0.85rem;
    }
    .room-card__meta-item {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 0.85rem;
        color: #64748b;
    }
    .room-card__meta-item i {
        font-size: 0.85rem;
        color: #94a3b8;
        width: 16px;
        text-align: center;
        flex-shrink: 0;
    }

    /* Harga pill */
    .room-card__price {
        display: inline-block;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 4px 11px;
        border-radius: 20px;
        margin-bottom: 1rem;
        background: #ecfdf5;
        color: #065f46;
    }
    .room-card__price.is-paid {
        background: #fff7ed;
        color: #9a3412;
    }

    /* CTA button */
    .room-card__btn {
        display: block;
        width: 100%;
        padding: 0.6rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        background: linear-gradient(135deg, #1a56db 0%, #0e3fa0 100%);
        border: none;
        color: #fff;
        text-align: center;
        text-decoration: none;
        transition: opacity var(--transition), transform var(--transition);
    }
    .room-card__btn:hover {
        opacity: 0.88;
        color: #fff;
        transform: translateY(-1px);
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 5rem 1rem;
        color: #94a3b8;
    }
    .empty-state .empty-icon {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        display: block;
        opacity: 0.35;
    }

    /* ── Lazy load skeleton shimmer ── */
    .room-card__img-wrap {
        background: #e9ecef; /* placeholder color */
    }
    .room-card__img-wrap::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg,
            transparent 0%, rgba(255,255,255,0.55) 50%, transparent 100%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
        z-index: 1;
        pointer-events: none;
    }
    .room-card__img-wrap.loaded::before { display: none; }

    @keyframes shimmer {
        0%   { background-position: -200% 0; }
        100% { background-position:  200% 0; }
    }

    .room-card__img-wrap img {
        opacity: 0;
        transition: transform 0.4s ease, opacity 0.35s ease;
        position: relative;
        z-index: 2;
    }
    .room-card__img-wrap img.img-loaded { opacity: 1; }

    /* ── Staggered animation ── */
    .room-col { animation: fadeUp 0.4s ease both; }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .room-col:nth-child(1)  { animation-delay: 0.00s; }
    .room-col:nth-child(2)  { animation-delay: 0.06s; }
    .room-col:nth-child(3)  { animation-delay: 0.12s; }
    .room-col:nth-child(4)  { animation-delay: 0.18s; }
    .room-col:nth-child(5)  { animation-delay: 0.24s; }
    .room-col:nth-child(6)  { animation-delay: 0.30s; }
    .room-col:nth-child(7)  { animation-delay: 0.36s; }
    .room-col:nth-child(8)  { animation-delay: 0.42s; }
    .room-col:nth-child(9)  { animation-delay: 0.48s; }
    .room-col:nth-child(10) { animation-delay: 0.54s; }
    .room-col:nth-child(11) { animation-delay: 0.60s; }
    .room-col:nth-child(12) { animation-delay: 0.66s; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Judul --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>

    {{-- ── Filter Card ── --}}
    <div class="filter-card card mb-5">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-center text-white">
                <i class="bi bi-search me-2"></i>Cari Ruangan Kosong
            </h6>
        </div>
        <div class="card-body py-4">
            <form method="GET" action="{{ url('/ruangan-user') }}">
                <div class="row g-3 align-items-end">

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fw-bold small">Tanggal</label>
                        <input type="date" name="tanggal"
                               class="form-control"
                               value="{{ request('tanggal') }}"
                               min="{{ date('Y-m-d') }}"
                               required>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label fw-bold small">Jam Mulai</label>
                        <input type="time" name="jam_mulai"
                               class="form-control"
                               value="{{ request('jam_mulai') }}"
                               step="1800"
                               required>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label fw-bold small">Jam Selesai</label>
                        <input type="time" name="jam_selesai"
                               class="form-control"
                               value="{{ request('jam_selesai') }}"
                               step="1800"
                               required>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label fw-bold small">Lokasi</label>
                        <select name="lokasi" class="form-control">
                            <option value="">Semua Lokasi</option>
                            @foreach($lokasiList as $lok)
                                <option value="{{ $lok }}" @selected(request('lokasi') == $lok)>
                                    {{ $lok }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-search me-1"></i>Cari
                            </button>
                            <a href="{{ url('/ruangan-user') }}" class="btn btn-outline-secondary flex-fill">
                                <i class="bi bi-arrow-repeat me-1"></i>Reset
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- ── Hasil ── --}}
    @if($rooms->count() > 0)

        {{-- Info hasil filter --}}
        @if(request()->hasAny(['tanggal', 'jam_mulai', 'jam_selesai']))
            <p class="text-muted mb-3 small">
                <i class="bi bi-check-circle-fill text-success me-1"></i>
                <strong>{{ $rooms->count() }}</strong> ruangan tersedia
                @if(request('tanggal'))
                    pada <strong>{{ \Carbon\Carbon::parse(request('tanggal'))->isoFormat('D MMMM YYYY') }}</strong>
                @endif
                @if(request('jam_mulai') && request('jam_selesai'))
                    pukul <strong>{{ request('jam_mulai') }} – {{ request('jam_selesai') }}</strong>
                @endif
            </p>
        @endif

        <div class="row">
            @foreach($rooms as $room)
                @php
                    $isGratis = !$room->harga_sewa_per_hari || $room->harga_sewa_per_hari == 0;
                    $gambar   = $room->gambar
                                ? asset('storage/' . $room->gambar)
                                : asset('enno/assets/img/ruang-rapat.png');
                    $isFiltered = request()->hasAny(['tanggal', 'jam_mulai', 'jam_selesai']);
                @endphp

                <div class="col-md-6 col-lg-4 mb-4 room-col">
                    <div class="room-card card">

                        {{-- Gambar --}}
                        <div class="room-card__img-wrap">
                            <img
                                src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                                data-src="{{ $gambar }}"
                                alt="{{ $room->nama_ruangan }}"
                                class="lazy-img">
                            <div class="room-card__img-overlay"></div>
                            <span class="room-card__kode">{{ $room->kode_ruangan }}</span>
                            @if($isFiltered)
                                <span class="room-card__avail badge bg-success">
                                    <i class="bi bi-check-lg me-1"></i>Tersedia
                                </span>
                            @endif
                        </div>

                        {{-- Body --}}
                        <div class="room-card__body">
                            <div class="room-card__name">{{ $room->nama_ruangan }}</div>

                            <div class="room-card__meta">
                                <div class="room-card__meta-item">
                                    <i class="fas fa-users"></i>
                                    <span>Kapasitas <strong>{{ $room->kapasitas }}</strong> orang</span>
                                </div>
                                @if($room->lokasi)
                                    <div class="room-card__meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $room->lokasi }}</span>
                                    </div>
                                @endif
                            </div>

                            <span class="room-card__price {{ $isGratis ? '' : 'is-paid' }}">
                                @if($isGratis)
                                    <i class="bi bi-gift me-1"></i>Gratis
                                @else
                                    <i class="bi bi-tag me-1"></i>Rp {{ number_format($room->harga_sewa_per_hari, 0, ',', '.') }}/hari
                                @endif
                            </span>

                            <a href="{{ route('bookingCreate') }}" class="room-card__btn">
                                <i class="bi bi-calendar-plus me-1"></i>Pinjam Ruangan Ini
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

    @else

        {{-- Empty state --}}
        <div class="empty-state">
            <i class="bi bi-building-slash empty-icon"></i>
            @if(request()->hasAny(['tanggal', 'jam_mulai', 'jam_selesai']))
                <p class="fw-semibold text-dark mb-1">Tidak ada ruangan tersedia untuk jadwal ini</p>
                <p class="small">Coba ubah tanggal atau jam peminjaman.</p>
            @else
                <p>Belum ada ruangan yang terdaftar.</p>
            @endif
        </div>

    @endif

</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lazyImgs = document.querySelectorAll('img.lazy-img');

    function loadImg(img) {
        if (!img.dataset.src) return;
        img.src = img.dataset.src;
        img.addEventListener('load', function () {
            img.classList.add('img-loaded');
            const wrapper = img.closest('.room-card__img-wrap');
            if (wrapper) wrapper.classList.add('loaded');
        }, { once: true });
    }

    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                loadImg(entry.target);
                io.unobserve(entry.target);
            });
        }, { rootMargin: '200px 0px' });

        lazyImgs.forEach(function (img) { io.observe(img); });
    } else {
        // Fallback browser lama
        lazyImgs.forEach(loadImg);
    }
});
</script>
@endpush

@endsection