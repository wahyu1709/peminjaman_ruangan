<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Pinjam | Beranda</title>
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
    /* Atasi modal "+X more" FullCalendar tertutup footer */
    .fc-popover {
      z-index: 1050 !important; /* lebih tinggi dari footer (biasanya z-index 1000 atau kurang) */
    }

    .fc-popover-header {
      background-color: #343a40 !important;
      color: white !important;
    }

    .fc-popover-body {
      max-height: 400px;
      overflow-y: auto;
    }

    /* Agar modal tidak ketutup footer sticky */
    body {
      padding-bottom: 100px; /* beri ruang bawah agar konten tidak tertutup footer */
    }

    /* Optional: buat event di popover lebih cantik */
    .fc-more-popover .fc-event {
      margin: 4px 0;
      padding: 6px 8px;
      border-radius: 6px;
      color: white;
      font-size: 0.9em;
    }

      /* Truncate event content agar tidak melewati kotak, gunakan line-clamp */
    .fc-event-custom {
      display: block;
      line-height: 1.3;
      width: 100%;
    }
    .fc-event-title-custom {
      font-weight: 600;
      display: block;
      margin-bottom: 3px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .fc-event-time-custom {
      font-size: 0.85em;
      opacity: 0.95;
      display: block;
      margin-bottom: 3px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .fc-event-keperluan-custom {
      font-size: 0.85em;
      opacity: 0.9;
      display: block;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      line-clamp: 2;
    }
    /* Fallback untuk browser tanpa -webkit-line-clamp */
    @supports not (-webkit-line-clamp: 1) {
      .fc-event-custom .fc-event-title,
      .fc-event-custom .fc-event-keperluan {
        white-space: nowrap;
      }
    }

    /* Week/Day View - Event styling */
    .fc-timegrid-event {
      border-radius: 6px !important;
      padding: 6px 8px !important;
      font-size: 0.9em !important;
      box-shadow: 0 2px 4px rgba(0,0,0,0.15) !important;
      overflow: hidden !important;
    }

    .fc-timegrid-event-harness {
      margin: 0 !important;
    }

    /* Week view specific styling */
    .fc-timegrid .fc-event-custom {
      padding: 2px 0;
    }

    .fc-timegrid .fc-event-title-custom {
      font-size: 0.95em;
    }

    .fc-timegrid .fc-event-time-custom {
      font-size: 0.8em;
    }

    .fc-timegrid .fc-event-keperluan-custom {
      font-size: 0.8em;
      -webkit-line-clamp: 3;
      line-clamp: 3;
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
          <li><a href="{{ route('welcome') }}" class="active">Jadwal Peminjaman</a></li>
          <li><a href="{{ route('public.list') }}">List Peminjaman</a></li>
          {{-- <li><a href="#contact">Contact</a></li> --}}
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

    <!-- Hero Section dengan Kalender -->
    <section id="hero" class="hero section light-background">
      <div class="container">
        <div class="row gy-4 justify-content-center">
          <div class="col-lg-10">
            <h1 class="text-center mb-5">Jadwal Peminjaman Ruangan</h1>

            <!-- Informasi Status -->
            <div class="text-center mb-5">
              <h5>Informasi Status</h5>
              <span class="badge bg-success text-white p-3 me-4 fs-6">Disetujui</span>
              <span class="badge bg-warning text-dark p-3 me-4 fs-6">Pending</span>
              <span class="badge bg-danger text-white p-3 me-4 fs-6">Ditolak</span>
              <span class="badge bg-secondary text-white p-3 fs-6">Selesai</span>
            </div>

            <!-- Kalender FullCalendar -->
            <div class="card shadow-lg border-0">
              <div class="card-body p-4">
                <div id="calendar"></div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </section>

        <!-- Modal Detail Booking -->
    <div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-labelledby="bookingDetailModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-success text-white" id="modalHeader">
            <h5 class="modal-title text-white" id="bookingDetailModalLabel">
              <i class="fas fa-calendar-check mr-2"></i> Detail Peminjaman Ruangan
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <p><strong>Tanggal:</strong> <span id="modalTanggal"></span></p>
                <p><strong>Ruangan:</strong> <span id="modalRuangan" class="fw-bold text-primary"></span></p>
                <p><strong>Pengaju:</strong> <span id="modalPengaju"></span></p>
              </div>
              <div class="col-md-6">
                <p><strong>Waktu:</strong> <span id="modalWaktu" class="fw-bold"></span></p>
                <p><strong>Lokasi:</strong> <span id="modalLokasi"></span></p>
                <p><strong>Status:</strong> <span id="modalStatusBadge" class="badge bg-success">Disetujui</span> <span id="modalStatusText" class="ms-2 fw-bold"></span></p>
              </div>
            </div>
            <hr>
            <p><strong>Keperluan:</strong></p>
            <p id="modalKeperluan" class="bg-light p-3 rounded"></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
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

  <!-- FullCalendar JS (dengan integrity) -->
  <script src="{{ asset('dist/index.global.js') }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset('enno/assets/js/main.js') }}"></script>

  <!-- Script FullCalendar -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'id',
      firstDay: 1, // Senin pertama
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      height: 'auto',
      dayMaxEvents: 2, // +X more kalau lebih dari 4
      events: @json($events),

      // Batas jam operasional (07:00 - 20:00)
      // slotMinTime: '07:00:00',
      // slotMaxTime: '20:00:00',

      // TANDAI HARI SABTU & MINGGU DENGAN WARNA MERAH
      dayCellDidMount: function(arg) {
        let dayOfWeek = arg.date.getDay(); // 0 = Minggu, 6 = Sabtu

        if (dayOfWeek === 0 || dayOfWeek === 6) {
          arg.el.style.backgroundColor = '#ffcccc'; // merah muda
          arg.el.style.color = '#800000';          // teks merah tua
          arg.el.style.border = '1px solid #ff9999';
        }
      },

      // Custom tampilan event (mirip gambar kamu: title, jam, keperluan)
      eventContent: function (arg) {
        let timeText = arg.event.start.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) +
                      ' - ' + arg.event.end.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        let keperluan = arg.event.extendedProps.keperluan || '';

        return {
          html: `
            <div class="fc-event-custom">
                <div class="fc-event-title-custom">${arg.event.title}</div>
                <div class="fc-event-time-custom">${timeText}</div>
                <div class="fc-event-keperluan-custom">${keperluan}</div>
            </div>
          `
        };
      },

      // Styling warna sesuai status + tulisan kontras
      eventDidMount: function (info) {
        let status = (info.event.extendedProps.status || '').toLowerCase().trim();

        let bgColor = '#6c757d';
        let textColor = '#ffffff';

        if (status === 'approved') {
          bgColor = '#28a745'; // hijau
          textColor = '#ffffff';
        } else if (status === 'pending') {
          bgColor = '#ffc107'; // kuning
          textColor = '#212529'; // hitam agar terbaca
        } else if (status === 'rejected') {
          bgColor = '#dc3545'; // merah
          textColor = '#ffffff';
        } else if (status === 'completed') {
          bgColor = '#6c757d'; // abu-abu
          textColor = '#ffffff';
        }

        // Terapkan warna
        info.el.style.backgroundColor = bgColor;
        info.el.style.borderColor = bgColor;
        info.el.style.color = textColor;
        info.el.style.borderRadius = '8px';
        info.el.style.padding = '6px';
        info.el.style.fontSize = '0.85em';
        info.el.style.boxShadow = '0 2px 6px rgba(0,0,0,0.15)';

        // Tooltip hover
        info.el.title = 
          info.event.title + '\n' +
          info.event.start.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) + ' - ' +
          info.event.end.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) + '\n' +
          'Keperluan: ' + (info.event.extendedProps.keperluan || '');
      },

      // Klik event → BUKA MODAL DETAIL (tetap ada!)
      eventClick: function (info) {
        let kodeRuangan = info.event.title; // langsung dari title (sudah kode)
        let namaRuangan = info.event.extendedProps.nama_ruangan || '-'; // opsional: nama lengkap
        let pengaju = info.event.extendedProps.pengaju || 'Tidak diketahui';
        let waktuMulai = info.event.start.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        let waktuSelesai = info.event.end.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        let keperluan = info.event.extendedProps.keperluan || '-';
        let lokasi = info.event.extendedProps.lokasi || '-';
        let statusRaw = (info.event.extendedProps.status || '').toLowerCase().trim();
        let tanggal = info.event.start.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

        let statusText = 'Tidak Diketahui';
        let headerClass = 'bg-secondary text-white';
        let badgeClass = 'bg-secondary';

        if (statusRaw === 'approved') {
          statusText = 'Disetujui';
          headerClass = 'bg-success text-white';
          badgeClass = 'bg-success';
        } else if (statusRaw === 'pending') {
          statusText = 'Pending';
          headerClass = 'bg-warning text-dark';
          badgeClass = 'bg-warning text-dark';
        } else if (statusRaw === 'rejected') {
          statusText = 'Ditolak';
          headerClass = 'bg-danger text-white';
          badgeClass = 'bg-danger';
        } else if (statusRaw === 'completed') {
          statusText = 'Selesai';
          headerClass = 'bg-secondary text-white';
          badgeClass = 'bg-secondary';
        }

        // Isi modal
        document.getElementById('modalTanggal').textContent = tanggal;
        document.getElementById('modalRuangan').innerHTML = `
          <strong class="text-primary">${kodeRuangan}</strong><br>
          <small class="text-muted">(${namaRuangan})</small>
        `;
        document.getElementById('modalPengaju').textContent = pengaju;
        document.getElementById('modalWaktu').textContent = waktuMulai + ' - ' + waktuSelesai;
        document.getElementById('modalKeperluan').textContent = keperluan;
        document.getElementById('modalLokasi').textContent = lokasi;
        document.getElementById('modalHeader').className = 'modal-header ' + headerClass;
        document.getElementById('modalStatusBadge').className = 'badge ' + badgeClass;
        document.getElementById('modalStatusBadge').textContent = statusText;

        var myModal = new bootstrap.Modal(document.getElementById('bookingDetailModal'));
        myModal.show();
      }
    });

    calendar.render();
  });
</script>
</body>

</html>