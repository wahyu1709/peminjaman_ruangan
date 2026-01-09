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
                        @if(auth()->check() && (auth()->user()->role == 'admin' || auth()->user()->jenis_pengguna == 'staff'))
                            @php
                                $pendingOver1Hour = \App\Models\Booking::where('status', 'pending')
                                    ->where('created_at', '<', \Carbon\Carbon::now()->subHour())
                                    ->count();
                            @endphp

                            <li class="nav-item dropdown no-arrow mx-1">
                                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown">
                                    <i class="fas fa-bell fa-fw"></i>
                                    @if($pendingOver1Hour > 0)
                                        <span class="badge badge-danger badge-counter">{{ $pendingOver1Hour }}</span>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                                    <h6 class="dropdown-header bg-danger text-white">
                                        Peringatan Booking
                                    </h6>
                                    @if($pendingOver1Hour > 0)
                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('booking') }}">
                                            <div class="mr-3">
                                                <div class="icon-circle bg-warning">
                                                    <i class="fas fa-exclamation-triangle text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="font-weight-bold">{{ $pendingOver1Hour }} booking menunggu persetujuan lebih dari 1 jam</span>
                                            </div>
                                        </a>
                                    @else
                                        <a class="dropdown-item text-center text-gray-500 small">Tidak ada peringatan</a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-center small text-gray-500" href="{{ route('booking') }}">Lihat Semua Booking</a>
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
                        <span>Copyright &copy; FIK 2026</span>
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

@include('layouts/footer')