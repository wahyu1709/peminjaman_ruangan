<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Pinjam | Beranda</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <link href="{{ asset('enno/assets/img/ui-icon.png') }}" rel="icon">

  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">

  <link href="{{ asset('enno/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('enno/assets/css/main.css') }}" rel="stylesheet">

  <style>
    .fc-popover { z-index: 1050 !important; }
    .fc-popover-header { background-color: #343a40 !important; color: white !important; }
    .fc-popover-body { max-height: 400px; overflow-y: auto; }

    body { padding-bottom: 100px; }

    .fc-more-popover .fc-event { margin: 4px 0; padding: 6px 8px; border-radius: 6px; color: white; font-size: 0.9em; }

    .fc-event-custom { display: block; line-height: 1.3; width: 100%; }
    .fc-event-title-custom { font-weight: 600; display: block; margin-bottom: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .fc-event-time-custom { font-size: 0.85em; opacity: 0.95; display: block; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .fc-event-keperluan-custom { font-size: 0.85em; opacity: 0.9; display: block; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-clamp: 2; }

    @supports not (-webkit-line-clamp: 1) {
      .fc-event-custom .fc-event-title,
      .fc-event-custom .fc-event-keperluan { white-space: nowrap; }
    }

    .fc-timegrid-event { border-radius: 6px !important; padding: 6px 8px !important; font-size: 0.9em !important; box-shadow: 0 2px 4px rgba(0,0,0,0.15) !important; overflow: hidden !important; }
    .fc-timegrid-event-harness { margin: 0 !important; }
    .fc-timegrid .fc-event-custom { padding: 2px 0; }
    .fc-timegrid .fc-event-title-custom { font-size: 0.95em; }
    .fc-timegrid .fc-event-time-custom { font-size: 0.8em; }
    .fc-timegrid .fc-event-keperluan-custom { font-size: 0.8em; -webkit-line-clamp: 3; line-clamp: 3; }

    /* Tabel barang di modal */
    .inventory-table th { background-color: #f8f9fa; font-size: 0.85rem; }
    .inventory-table td { font-size: 0.9rem; vertical-align: middle; }
    .inventory-badge { font-size: 0.75rem; }

    /* Section ruangan vs barang di modal */
    .modal-section-label {
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: #6c757d;
      margin-bottom: 6px;
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
          <li><a href="{{ route('public.ruangan') }}">Daftar Ruangan</a></li>
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
    <section id="hero" class="hero section light-background">
      <div class="container">
        <div class="row gy-4 justify-content-center">
          <div class="col-lg-10">
            <h1 class="text-center mb-5">Jadwal Peminjaman Ruangan</h1>

            <div class="text-center mb-5">
              <h5>Informasi Status</h5>
              <span class="badge bg-success text-white p-3 me-4 fs-6">Disetujui</span>
              <span class="badge bg-warning text-dark p-3 me-4 fs-6">Pending</span>
              <span class="badge bg-danger text-white p-3 me-4 fs-6">Ditolak</span>
              <span class="badge bg-secondary text-white p-3 fs-6">Selesai</span>
            </div>

            <div class="card shadow-lg border-0">
              <div class="card-body p-4">
                <div id="calendar"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== MODAL DETAIL BOOKING ===== -->
    <div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-labelledby="bookingDetailModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">

          <div class="modal-header" id="modalHeader">
            <h5 class="modal-title text-white" id="bookingDetailModalLabel">
              <i id="modalHeaderIcon" class="me-2"></i>
              <span id="modalHeaderTitle">Detail Peminjaman</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">

            <!-- Baris 1: Info umum -->
            <div class="row mb-3">
              <div class="col-md-6">
                <p class="mb-2"><strong>Tanggal:</strong> <span id="modalTanggal"></span></p>
                <p class="mb-2"><strong>Pengaju:</strong> <span id="modalPengaju"></span></p>
                <p class="mb-2"><strong>Unit/Peran:</strong> <span id="modalRole"></span></p>
              </div>
              <div class="col-md-6">
                <p class="mb-2"><strong>Waktu:</strong> <span id="modalWaktu" class="fw-bold"></span></p>
                <p class="mb-2"><strong>Status:</strong>
                  <span id="modalStatusBadge" class="badge"></span>
                </p>
              </div>
            </div>

            <hr class="my-3">

            <!-- Bagian RUANGAN (hanya tampil jika bukan inventory only) -->
            <div id="sectionRuangan">
              <div class="modal-section-label"><i class="bi bi-door-open me-1"></i>Ruangan</div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <p class="mb-1">
                    <span id="modalRuangan" class="fw-bold text-primary fs-5"></span>
                  </p>
                  <p class="text-muted mb-2" id="modalNamaRuangan"></p>
                </div>
                <div class="col-md-6">
                  <p class="mb-2"><strong>Lokasi:</strong> <span id="modalLokasi"></span></p>
                </div>
              </div>
            </div>

            <!-- Bagian BARANG (hanya tampil jika ada inventaris) -->
            <div id="sectionBarang" style="display:none;">
              <div class="modal-section-label"><i class="bi bi-boxes me-1"></i>Barang yang Dipinjam</div>
              <div class="table-responsive mb-3">
                <table class="table table-sm table-bordered inventory-table mb-0">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama Barang</th>
                      <th>Kategori</th>
                      <th class="text-center">Jumlah</th>
                    </tr>
                  </thead>
                  <tbody id="modalInventoryRows">
                    <!-- diisi JS -->
                  </tbody>
                </table>
              </div>
            </div>

            <hr class="my-3">

            <!-- Keperluan -->
            <div>
              <strong>Keperluan:</strong>
              <p id="modalKeperluan" class="bg-light p-3 rounded mt-2 mb-0"></p>
            </div>

          </div><!-- /.modal-body -->

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
    <!-- ===== /MODAL ===== -->

  </main>

  <footer id="footer" class="footer">
    <div class="container copyright text-center mt-4">
      <p>© {{ date('Y') }} Fakultas Ilmu Keperawatan Universitas Indonesia</p>
    </div>
  </footer>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <div id="preloader"></div>

  <script src="{{ asset('enno/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('enno/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('dist/index.global.js') }}"></script>
  <script src="{{ asset('enno/assets/js/main.js') }}"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'id',
      firstDay: 1,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      height: 'auto',
      dayMaxEvents: 2,
      events: @json($events),

      dayCellDidMount: function(arg) {
        let day = arg.date.getDay();
        if (day === 0 || day === 6) {
          arg.el.style.backgroundColor = '#ffcccc';
          arg.el.style.color = '#800000';
          arg.el.style.border = '1px solid #ff9999';
        }
      },

      eventContent: function (arg) {
        let timeText =
          arg.event.start.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) +
          ' - ' +
          arg.event.end.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
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

      eventDidMount: function (info) {
        let status = (info.event.extendedProps.status || '').toLowerCase().trim();
        let bgColor = '#6c757d', textColor = '#ffffff';

        if (status === 'approved')       { bgColor = '#28a745'; textColor = '#ffffff'; }
        else if (status === 'pending')   { bgColor = '#ffc107'; textColor = '#212529'; }
        else if (status === 'rejected')  { bgColor = '#dc3545'; textColor = '#ffffff'; }
        else if (status === 'completed') { bgColor = '#6c757d'; textColor = '#ffffff'; }

        info.el.style.backgroundColor = bgColor;
        info.el.style.borderColor     = bgColor;
        info.el.style.color           = textColor;
        info.el.style.borderRadius    = '8px';
        info.el.style.padding         = '6px';
        info.el.style.fontSize        = '0.85em';
        info.el.style.boxShadow       = '0 2px 6px rgba(0,0,0,0.15)';

        info.el.title =
          info.event.title + '\n' +
          info.event.start.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) + ' - ' +
          info.event.end.toLocaleTimeString('id-ID',   {hour:'2-digit', minute:'2-digit'}) + '\n' +
          'Keperluan: ' + (info.event.extendedProps.keperluan || '');
      },

      // ─── Event Click → Modal ──────────────────────────────────────────────
      eventClick: function (info) {
        const props          = info.event.extendedProps;
        const isInvOnly      = props.is_inventory_only;
        const statusRaw      = (props.status || '').toLowerCase().trim();
        const inventories    = props.inventories || [];

        // Waktu
        const waktuMulai   = info.event.start.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        const waktuSelesai = info.event.end.toLocaleTimeString('id-ID',   { hour: '2-digit', minute: '2-digit' });
        const tanggal      = info.event.start.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

        // Status → warna & label
        let statusText  = 'Tidak Diketahui';
        let headerClass = 'bg-secondary';
        let badgeClass  = 'bg-secondary';

        if (statusRaw === 'approved')       { statusText = 'Disetujui'; headerClass = 'bg-success'; badgeClass = 'bg-success'; }
        else if (statusRaw === 'pending')   { statusText = 'Pending';   headerClass = 'bg-warning'; badgeClass = 'bg-warning text-dark'; }
        else if (statusRaw === 'rejected')  { statusText = 'Ditolak';   headerClass = 'bg-danger';  badgeClass = 'bg-danger'; }
        else if (statusRaw === 'completed') { statusText = 'Selesai';   headerClass = 'bg-secondary'; badgeClass = 'bg-secondary'; }

        // Header modal
        const headerEl = document.getElementById('modalHeader');
        headerEl.className = 'modal-header ' + headerClass;

        if (isInvOnly) {
          document.getElementById('modalHeaderIcon').className  = 'bi bi-boxes me-2';
          document.getElementById('modalHeaderTitle').textContent = 'Detail Peminjaman Barang';
        } else {
          document.getElementById('modalHeaderIcon').className  = 'bi bi-door-open me-2';
          document.getElementById('modalHeaderTitle').textContent = 'Detail Peminjaman Ruangan';
        }

        // Field umum
        document.getElementById('modalTanggal').textContent = tanggal;
        document.getElementById('modalPengaju').textContent = props.pengaju || 'Anonim';
        document.getElementById('modalWaktu').textContent   = waktuMulai + ' - ' + waktuSelesai;
        document.getElementById('modalRole').textContent    = props.role_unit || '-';
        document.getElementById('modalKeperluan').textContent = props.keperluan || '-';

        const badgeEl = document.getElementById('modalStatusBadge');
        badgeEl.className   = 'badge ' + badgeClass;
        badgeEl.textContent = statusText;

        // ─── Section Ruangan ──────────────────────────────────────────────
        const sectionRuangan = document.getElementById('sectionRuangan');

        if (!isInvOnly && props.kode_ruangan) {
          sectionRuangan.style.display = 'block';
          document.getElementById('modalRuangan').textContent    = props.kode_ruangan;
          document.getElementById('modalNamaRuangan').textContent = props.nama_ruangan || '';
          document.getElementById('modalLokasi').textContent      = props.lokasi || '-';
        } else {
          sectionRuangan.style.display = 'none';
        }

        // ─── Section Barang ───────────────────────────────────────────────
        const sectionBarang = document.getElementById('sectionBarang');
        const tbody         = document.getElementById('modalInventoryRows');

        if (inventories.length > 0) {
          sectionBarang.style.display = 'block';
          tbody.innerHTML = inventories.map((item, i) => `
            <tr>
              <td class="text-center text-muted">${i + 1}</td>
              <td><strong>${item.name}</strong></td>
              <td><span class="badge bg-light text-dark inventory-badge">${item.category || '-'}</span></td>
              <td class="text-center"><span class="badge bg-primary">${item.quantity} unit</span></td>
            </tr>
          `).join('');
        } else {
          sectionBarang.style.display = 'none';
          tbody.innerHTML = '';
        }

        // Tampilkan modal
        new bootstrap.Modal(document.getElementById('bookingDetailModal')).show();
      }
    });

    calendar.render();
  });
  </script>

</body>
</html>