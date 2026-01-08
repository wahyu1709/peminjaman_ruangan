<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Pinjam | List Peminjaman</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{ asset('enno/assets/img/ui-icon.png') }}" rel="icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('enno/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/aos/aos.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('enno/assets/css/main.css') }}" rel="stylesheet">
  <style>
    .table-list {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .table-list thead th {
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.85em;
      letter-spacing: 0.5px;
    }
    .table-list tbody tr:hover {
      background-color: rgba(0,0,0,0.02);
    }
  </style>
</head>

<body class="list-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="{{ route('welcome') }}" class="logo d-flex align-items-center me-auto">
        <h1 class="sitename">Peminjaman Ruangan</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="{{ route('welcome') }}">Jadwal Peminjaman</a></li>
          <li><a href="{{ route('public.list') }}" class="active">List Peminjaman</a></li>
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
    <section class="section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-11">
            <h2 class="text-center mb-4">List Peminjaman Ruangan</h2>
            <p class="text-center mb-5">Daftar semua peminjaman ruangan yang telah disetujui.</p>

            @if($approvedBookings->isEmpty())
              <div class="text-center py-5">
                <p class="text-muted">Belum ada peminjaman yang disetujui.</p>
              </div>
            @else
            <div class="table-responsive">
                <table class="table table-list align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Hari</th>
                        <th>Tanggal</th>
                        <th>Ruangan</th>
                        <th>Jam</th>
                        <th>Keperluan</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($approvedBookings as $index => $booking)
                        <tr class="{{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isToday() ? 'table-success' : '' }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('dddd') }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($booking->tanggal_pinjam)->isoFormat('D MMMM Y') }}
                        </td>
                        <td>
                            <strong>{{ $booking->room->nama_ruangan ?? '-' }}</strong>
                            @if($booking->room?->kode_ruangan)
                              <br><small class="text-muted">{{ $booking->room->kode_ruangan }}</small>
                            @endif
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} –
                            {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                        </td>
                        <td>{{ $booking->keperluan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada peminjaman yang disetujui.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
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
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('enno/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('enno/assets/js/main.js') }}"></script>

</body>

</html>