<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Pinjam | Daftar Ruangan</title>

  <link href="{{ asset('enno/assets/img/ui-icon.png') }}" rel="icon">

  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link href="{{ asset('enno/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/css/main.css') }}" rel="stylesheet">

  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ── Filter ──────────────────────────────────────────────────── */
    .filter-card {
      border: none;
      border-radius: 20px;
      box-shadow: 0 4px 24px rgba(0,191,165,0.12);
      overflow: hidden;
    }
    .filter-card .card-header {
      background: linear-gradient(135deg, #00bfa5 0%, #00897b 100%);
      padding: 16px 24px;
    }
    .filter-card .form-control,
    .filter-card .form-select {
      border-radius: 10px;
      border: 1.5px solid #dee2e6;
      font-size: 0.88rem;
      padding: 9px 12px;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
      border-color: #00bfa5;
      box-shadow: 0 0 0 3px rgba(0,191,165,0.15);
    }
    .filter-card .form-label {
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #495057;
      margin-bottom: 5px;
    }

    /* ── Room Card ───────────────────────────────────────────────── */
    .room-card {
      border: none;
      border-radius: 18px;
      overflow: hidden;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      box-shadow: 0 2px 14px rgba(0,0,0,0.07);
    }
    .room-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 16px 36px rgba(0,0,0,0.12);
    }

    /* Skeleton shimmer saat gambar belum load */
    .room-img-wrapper {
      position: relative;
      height: 195px;
      overflow: hidden;
      background: #e9ecef;
    }
    .room-img-wrapper::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg,
        transparent 0%, rgba(255,255,255,0.55) 50%, transparent 100%);
      background-size: 200% 100%;
      animation: shimmer 1.4s infinite;
      z-index: 1;
    }
    .room-img-wrapper.loaded::before { display: none; }

    @keyframes shimmer {
      0%   { background-position: -200% 0; }
      100% { background-position:  200% 0; }
    }

    .room-img-wrapper img {
      width: 100%; height: 100%;
      object-fit: cover;
      opacity: 0;
      transition: transform 0.4s ease, opacity 0.35s ease;
      position: relative;
      z-index: 2;
    }
    .room-img-wrapper img.img-loaded { opacity: 1; }
    .room-card:hover .room-img-wrapper img { transform: scale(1.06); }

    /* Badge overlay */
    .room-code-badge {
      position: absolute; top: 12px; left: 12px; z-index: 3;
      background: rgba(0,137,123,0.9);
      color: #fff; font-size: 0.72rem; font-weight: 700;
      padding: 4px 12px; border-radius: 20px;
      text-transform: uppercase; letter-spacing: 0.05em;
      backdrop-filter: blur(4px);
    }
    .room-price-badge {
      position: absolute; top: 12px; right: 12px; z-index: 3;
      background: rgba(255,255,255,0.92);
      color: #00897b; font-size: 0.72rem; font-weight: 700;
      padding: 4px 10px; border-radius: 20px;
      backdrop-filter: blur(4px);
    }

    .room-card .card-body { padding: 16px 18px 18px; }

    .room-name {
      font-size: 0.95rem; font-weight: 700;
      color: #212529; margin-bottom: 10px; line-height: 1.35;
    }

    .room-chips { display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 14px; }
    .room-chip {
      display: inline-flex; align-items: center; gap: 4px;
      background: #e8f5f4; color: #00796b;
      font-size: 0.75rem; font-weight: 600;
      padding: 3px 10px; border-radius: 20px;
    }

    .btn-view {
      width: 100%;
      background: linear-gradient(135deg, #00bfa5, #00897b);
      color: #fff; border: none; border-radius: 10px;
      padding: 9px; font-weight: 600; font-size: 0.88rem;
      transition: opacity 0.2s, transform 0.15s;
    }
    .btn-view:hover  { opacity: 0.9; color: #fff; }
    .btn-view:active { transform: scale(0.97); }

    /* ── Modal Detail ────────────────────────────────────────────── */
    .modal-room-img {
      width: 100%; height: 220px;
      object-fit: cover; border-radius: 12px;
      background: #e9ecef;
      opacity: 0; transition: opacity 0.3s ease;
    }
    .modal-room-img.img-loaded { opacity: 1; }

    .detail-row {
      display: flex; align-items: flex-start; gap: 10px;
      padding: 10px 0; border-bottom: 1px solid #f0f0f0;
      font-size: 0.9rem;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-icon {
      width: 32px; height: 32px; border-radius: 8px;
      background: #e8f5f4; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
      color: #00897b; font-size: 0.9rem;
    }
    .detail-label {
      font-size: 0.72rem; font-weight: 700;
      text-transform: uppercase; color: #adb5bd;
      letter-spacing: 0.04em; display: block;
    }
    .detail-value { font-weight: 600; color: #212529; }

    /* ── Empty state ─────────────────────────────────────────────── */
    .empty-state { text-align: center; padding: 60px 20px; color: #6c757d; }
    .empty-state i { font-size: 3.5rem; color: #ced4da; display: block; margin-bottom: 16px; }

    /* ── Animasi kartu masuk ─────────────────────────────────────── */
    @keyframes cardFadeUp {
      from { opacity: 0; transform: translateY(18px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .room-card-wrapper { animation: cardFadeUp 0.35s ease both; }
    @for ($i = 1; $i <= 24; $i++)
      .room-card-wrapper:nth-child({{ $i }}) { animation-delay: {{ ($i - 1) * 0.05 }}s; }
    @endfor
  </style>
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">
      <a href="#" class="logo d-flex align-items-center me-auto">
        <h1 class="sitename">Peminjaman Ruangan</h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="{{ route('welcome') }}">Jadwal Peminjaman</a></li>
          <li><a href="{{ route('public.list') }}">List Peminjaman</a></li>
          <li><a href="{{ route('public.ruangan') }}" class="active">Daftar Ruangan</a></li>
          <li><a href="https://drive.google.com/file/d/19qr-KEJ_xOXvAi0IvcvpvneEHcgtn0hn/view?usp=drive_link" target="_blank">Tutorial Pengguna</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
      @auth
        <a class="btn-getstarted" href="{{ route('dashboard') }}">Dashboard</a>
      @else
        <a class="btn-getstarted" href="{{ route('login') }}">Login</a>
      @endauth
    </div>
  </header>

  <main class="main">
    <section id="hero" class="hero section light-background">
      <div class="container">
        <div class="row gy-4 justify-content-center">
          <div class="col-lg-11">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
              <h1 class="mb-0">Daftar Ruangan Tersedia</h1>
              @if(request()->hasAny(['tanggal','jam_mulai','jam_selesai','lokasi']))
                <span class="badge bg-success px-3 py-2 fs-6">
                  <i class="bi bi-funnel me-1"></i>{{ $rooms->count() }} hasil
                </span>
              @endif
            </div>

            {{-- ── Filter ──────────────────────────────────────────── --}}
            <div class="card filter-card mb-5">
              <div class="card-header">
                <h6 class="m-0 fw-bold text-white text-center">
                  <i class="bi bi-search me-2"></i>Cari Ruangan Kosong
                </h6>
              </div>
              <div class="card-body p-4">
                <form method="GET" action="{{ url('/ruangan') }}">
                  <div class="row g-3 align-items-end">
                    <div class="col-md-3 col-sm-6">
                      <label class="form-label">Tanggal</label>
                      <input type="date" name="tanggal" class="form-control"
                             value="{{ request('tanggal') }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-2 col-sm-6">
                      <label class="form-label">Jam Mulai</label>
                      <input type="time" name="jam_mulai" class="form-control"
                             value="{{ request('jam_mulai') }}" step="1800" required>
                    </div>
                    <div class="col-md-2 col-sm-6">
                      <label class="form-label">Jam Selesai</label>
                      <input type="time" name="jam_selesai" class="form-control"
                             value="{{ request('jam_selesai') }}" step="1800" required>
                    </div>
                    <div class="col-md-2 col-sm-6">
                      <label class="form-label">Lokasi</label>
                      <select name="lokasi" class="form-select">
                        <option value="">Semua Lokasi</option>
                        @foreach($lokasiList as $lok)
                          <option value="{{ $lok }}" {{ request('lokasi') == $lok ? 'selected' : '' }}>
                            {{ $lok }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-3 col-sm-6">
                      <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success flex-fill rounded-3 fw-bold">
                          <i class="bi bi-search me-1"></i> Cari
                        </button>
                        <a href="{{ url('/ruangan') }}" class="btn btn-outline-secondary rounded-3" title="Reset">
                          <i class="bi bi-arrow-repeat"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            {{-- ── Grid Ruangan ────────────────────────────────────── --}}
            @if($rooms->count() > 0)
              <div class="row g-4">
                @foreach($rooms as $room)
                  @php
                    $gambar     = $room->gambar
                                ? asset('storage/' . $room->gambar)
                                : asset('enno/assets/img/ruang-rapat.png');
                    $harga      = $room->harga_sewa_per_hari ?? 0;
                    $hargaLabel = $harga > 0
                                ? 'Rp ' . number_format($harga, 0, ',', '.')
                                : 'Gratis';
                  @endphp

                  <div class="col-md-6 col-lg-4 room-card-wrapper">
                    <div class="card room-card h-100">

                      {{-- Foto: lazy load via IntersectionObserver --}}
                      <div class="room-img-wrapper">
                        <img
                          src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                          data-src="{{ $gambar }}"
                          alt="{{ $room->nama_ruangan }}"
                          class="lazy-img">
                        <span class="room-code-badge">{{ $room->kode_ruangan }}</span>
                        <span class="room-price-badge">{{ $hargaLabel }}/hari</span>
                      </div>

                      <div class="card-body d-flex flex-column">
                        <h5 class="room-name">{{ $room->nama_ruangan }}</h5>
                        <div class="room-chips">
                          <span class="room-chip">
                            <i class="bi bi-people-fill"></i> {{ $room->kapasitas }} orang
                          </span>
                          @if($room->lokasi)
                            <span class="room-chip">
                              <i class="bi bi-geo-alt-fill"></i> {{ $room->lokasi }}
                            </span>
                          @endif
                          <span class="room-chip" style="background:#e8f5e9;color:#2e7d32;">
                            <i class="bi bi-check-circle-fill"></i> Tersedia
                          </span>
                        </div>
                        <div class="mt-auto">
                          <button class="btn btn-view"
                                  data-bs-toggle="modal"
                                  data-bs-target="#roomModal{{ $room->id }}">
                            <i class="bi bi-eye me-1"></i> Lihat Detail
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  {{-- Modal: gambar di-load saat modal dibuka --}}
                  <div class="modal fade" id="roomModal{{ $room->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                      <div class="modal-content border-0 shadow">
                        <div class="modal-header" style="background:linear-gradient(135deg,#00bfa5,#00897b);">
                          <h5 class="modal-title text-white fw-bold">
                            <i class="bi bi-door-open me-2"></i>{{ $room->nama_ruangan }}
                          </h5>
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                          <div class="row g-4">
                            <div class="col-md-6">
                              <img
                                src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                                data-src="{{ $gambar }}"
                                class="modal-room-img lazy-img"
                                alt="{{ $room->nama_ruangan }}">
                            </div>
                            <div class="col-md-6">
                              <div class="detail-row">
                                <div class="detail-icon"><i class="bi bi-hash"></i></div>
                                <div>
                                  <span class="detail-label">Kode Ruangan</span>
                                  <span class="detail-value">{{ $room->kode_ruangan }}</span>
                                </div>
                              </div>
                              <div class="detail-row">
                                <div class="detail-icon"><i class="bi bi-geo-alt"></i></div>
                                <div>
                                  <span class="detail-label">Lokasi</span>
                                  <span class="detail-value">{{ $room->lokasi ?? '-' }}</span>
                                </div>
                              </div>
                              <div class="detail-row">
                                <div class="detail-icon"><i class="bi bi-people"></i></div>
                                <div>
                                  <span class="detail-label">Kapasitas</span>
                                  <span class="detail-value">{{ $room->kapasitas }} orang</span>
                                </div>
                              </div>
                              <div class="detail-row">
                                <div class="detail-icon"><i class="bi bi-tag"></i></div>
                                <div>
                                  <span class="detail-label">Harga Sewa</span>
                                  <span class="detail-value">{{ $hargaLabel }}/hari</span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                          @auth
                            <a href="{{ route('bookingCreate') }}" class="btn btn-success fw-bold">
                              <i class="bi bi-calendar-plus me-1"></i> Pinjam Sekarang
                            </a>
                          @else
                            <a href="{{ route('login') }}" class="btn btn-success fw-bold">
                              <i class="bi bi-box-arrow-in-right me-1"></i> Login untuk Meminjam
                            </a>
                          @endauth
                        </div>
                      </div>
                    </div>
                  </div>

                @endforeach
              </div>

            @else
              <div class="empty-state">
                <i class="bi bi-door-closed"></i>
                <h5 class="fw-bold">Tidak ada ruangan tersedia</h5>
                <p>
                  @if(request()->hasAny(['tanggal','jam_mulai','jam_selesai']))
                    Semua ruangan sudah dipesan pada waktu tersebut. Coba tanggal atau jam lain.
                  @else
                    Belum ada ruangan yang terdaftar.
                  @endif
                </p>
                @if(request()->hasAny(['tanggal','jam_mulai','jam_selesai']))
                  <a href="{{ url('/ruangan') }}" class="btn btn-outline-success mt-2">
                    <i class="bi bi-arrow-repeat me-1"></i> Lihat Semua Ruangan
                  </a>
                @endif
              </div>
            @endif

          </div>
        </div>
      </div>
    </section>
  </main>

  <footer id="footer" class="footer">
    <div class="container copyright text-center mt-4">
      <p>© {{ date('Y') }} Fakultas Ilmu Keperawatan Universitas Indonesia</p>
    </div>
  </footer>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>
  <div id="preloader"></div>

  <script src="{{ asset('enno/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('enno/assets/js/main.js') }}"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {

    // ── 1. Lazy load gambar kartu (IntersectionObserver) ──────────────────────
    // Gambar hanya di-fetch saat mendekati viewport (rootMargin 200px)
    // sehingga halaman pertama kali load jauh lebih cepat.
    const lazyImgs = document.querySelectorAll('img.lazy-img');

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
      // Fallback browser lama: load semua sekaligus
      lazyImgs.forEach(loadImg);
    }

    function loadImg(img) {
      if (!img.dataset.src) return;
      img.src = img.dataset.src;
      img.addEventListener('load', function () {
        img.classList.add('img-loaded');
        const wrapper = img.closest('.room-img-wrapper');
        if (wrapper) wrapper.classList.add('loaded');
      }, { once: true });
    }

    // ── 2. Lazy load gambar di dalam modal (baru load saat modal dibuka) ──────
    // Ini mencegah foto resolusi tinggi di-download sebelum user membuka modal.
    document.querySelectorAll('.modal').forEach(function (modal) {
      modal.addEventListener('show.bs.modal', function () {
        modal.querySelectorAll('img.lazy-img').forEach(function (img) {
          if (img.dataset.src && img.getAttribute('src') !== img.dataset.src) {
            loadImg(img);
          }
        });
      }, { once: true }); // cukup sekali per modal
    });

  });
  </script>

</body>
</html>