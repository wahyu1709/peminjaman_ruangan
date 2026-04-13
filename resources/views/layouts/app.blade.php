@include('layouts/header')

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        @include('layouts/sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Alerts -->
                        @if(auth()->check() && auth()->user()->role == 'admin')
                        <li class="nav-item dropdown no-arrow mx-1" id="notifDropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="fas fa-bell fa-fw"></i>
                                <span class="badge badge-danger badge-counter d-none" id="notifBadge">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow" style="width:340px;max-height:400px;overflow-y:auto;">
                                <div class="d-flex align-items-center justify-content-between px-3 py-2"
                                    style="background:linear-gradient(135deg,#4361ee,#3a0ca3);">
                                    <span class="text-white font-weight-bold" style="font-size:.85rem;">
                                        <i class="fas fa-bell mr-1"></i> Notifikasi Booking
                                    </span>
                                    <button onclick="clearNotifications()" 
                                            style="background:rgba(255,255,255,.2);border:none;color:#fff;
                                                border-radius:6px;padding:2px 8px;font-size:.75rem;cursor:pointer;">
                                        Hapus Semua
                                    </button>
                                </div>
                                <div id="notifList">
                                    <div class="text-center py-4 text-muted" id="notifEmpty">
                                        <i class="fas fa-bell-slash d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>
                                        <small>Belum ada notifikasi</small>
                                    </div>
                                </div>
                                <div class="dropdown-divider m-0"></div>
                                <a class="dropdown-item text-center small text-primary py-2 font-weight-bold" 
                                href="{{ route('booking') }}">
                                    <i class="fas fa-list mr-1"></i> Lihat Semua Booking
                                </a>
                            </div>
                        </li>
                        @endif

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->name }}</span>
                                <img class="img-profile rounded-circle"
                                    src="{{ asset('sbadmin2/img/undraw_profile.svg') }}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <div class="badge badge-success d-flex justify-content-center">
                                        {{ auth()->user()->role }}
                                    </div>
                                </a>
                                <a class="dropdown-item" href="{{ route('settings') }}">
                                    <i class="fas fa-cog fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>© {{ date('Y') }} Fakultas Ilmu Keperawatan Universitas Indonesia</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <div class="whatsapp-float">
        <a href="https://wa.me/6281234567890?text=Halo%20Admin%20FIK%20UI" 
        target="_blank" 
        class="btn btn-success rounded-circle shadow-lg">
            <i class="fab fa-whatsapp fa-2x"></i>
        </a>
    </div>

    <style>
    .whatsapp-float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        z-index: 1000;
    }
    .whatsapp-float .btn {
        width: 100%;
        height: 100%;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .whatsapp-float:hover .btn {
        transform: scale(1.1);
        transition: transform 0.2s;
    }
    </style>

@include('layouts/footer')