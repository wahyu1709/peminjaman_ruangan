<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Pinjam | Daftar Ruangan</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{ asset('enno/assets/img/ui-icon.png') }}" rel="icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('enno/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('enno/assets/css/main.css') }}" rel="stylesheet">

  <style>
    .room-card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .room-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .room-image {
      height: 200px;
      object-fit: cover;
      border-radius: 8px 8px 0 0;
    }
    .room-title {
      font-weight: 600;
      color: #00bfa5;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .room-info {
      font-size: 0.9rem;
      color: #6c757d;
    }
    .btn-view {
      background-color: #00bfa5;
      color: white;
      border: none;
      border-radius: 20px;
      padding: 8px 20px;
      font-weight: 600;
      transition: background-color 0.2s ease;
    }
    .btn-view:hover {
      background-color: #00a88f;
    }
  </style>
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="#hero" class="logo d-flex align-items-center me-auto">
        <h1 class="sitename">Peminjaman Ruangan</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="{{ route('welcome') }}">Jadwal Peminjaman</a></li>
          <li><a href="{{ route('public.list') }}">List Peminjaman</a></li>
          <li><a href="{{ route('public.ruangan') }}" class="active">Daftar Ruangan</a></li>
          <li><a href="https://drive.google.com/file/d/19qr-KEJ_xOXvAi0IvcvpvneEHcgtn0hn/view?usp=drive_link" target="blank">Tutorial Pengguna</a></li>
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

    <!-- Hero Section -->
    <section id="hero" class="hero section light-background">
      <div class="container">
        <div class="row gy-4 justify-content-center">
          <div class="col-lg-10">
            <h1 class="text-center mb-5">Daftar Ruangan Tersedia</h1>

            <!-- Card Filter -->
            <div class="card shadow-lg border-0 mb-5">
              <div class="card-header bg-success py-3">
                <h6 class="m-0 font-weight-bold text-center text-white">
                  <i class="bi bi-filter-right me-2"></i>
                  Cari Ruangan Kosong
                </h6>
              </div>

              <div class="card-body">
                <form method="GET" action="{{ url('/ruangan') }}" class="row g-3">
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
                    <select name="lokasi" class="form-select">
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
                  <div class="col-md-3 col-sm-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                      <i class="bi bi-search me-2"></i>Cari Ruangan
                    </button>
                    <a href="{{ url('/ruangan') }}" class="btn btn-outline-secondary flex-fill">
                      <i class="bi bi-arrow-repeat me-2"></i>Reset
                    </a>
                  </div>
                </form>
              </div>
            </div>

            <div class="row">
              @foreach($rooms as $room)
                <div class="col-md-6 col-lg-4 mb-4">
                  <div class="card room-card shadow-sm h-100">
                    <img src="{{ $room->gambar ? asset('storage/' . $room->gambar) : asset('enno/assets/img/ruang-rapat.png') }}" 
                         class="room-image" alt="{{ $room->nama_ruangan }}">
                    <div class="card-body">
                      <h5 class="room-title">{{ $room->nama_ruangan }}</h5>
                      <p class="room-info">
                        <strong>Kode:</strong> {{ $room->kode_ruangan }}<br>
                        <strong>Lokasi:</strong> {{ $room->lokasi ?? '-' }}<br>
                        {{-- <strong>Lantai:</strong> {{ $room->lantai ?? '0' }} --}}
                      </p>
                      <a href="#" class="btn btn-view mt-3" data-bs-toggle="modal" data-bs-target="#roomModal{{ $room->id }}">
                        View Details
                      </a>
                    </div>
                  </div>
                </div>

                <!-- Modal Detail Ruangan -->
                <div class="modal fade" id="roomModal{{ $room->id }}" tabindex="-1" aria-labelledby="roomModalLabel{{ $room->id }}" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white" id="roomModalLabel{{ $room->id }}">
                          <i class="fas fa-door-open mr-2"></i>{{ $room->nama_ruangan }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <img src="{{ $room->gambar ? asset('storage/' . $room->gambar) : asset('enno/assets/img/ruang-rapat.png') }}"
                                 class="img-fluid rounded" alt="{{ $room->nama_ruangan }}">
                          </div>
                          <div class="col-md-6">
                            <h6><strong>Kode Ruangan:</strong> {{ $room->kode_ruangan }}</h6>
                            <h6><strong>Lokasi:</strong> {{ $room->lokasi ?? '-' }}</h6>
                            {{-- <h6><strong>Lantai:</strong> {{ $room->lantai ?? '0' }}</h6> --}}
                            <h6><strong>Kapasitas:</strong> {{ $room->kapasitas }} orang</h6>
                            {{-- @if($room->deskripsi)
                              <hr>
                              <p><strong>Deskripsi:</strong></p>
                              <p>{{ $room->deskripsi }}</p>
                            @endif --}}
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                      </div>
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
        </div>
      </div>
    </section>

  </main>

  <footer id="footer" class="footer">
    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">2026</strong> <span>FIK</span></p>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('enno/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset('enno/assets/js/main.js') }}"></script>

</body>

</html>