<!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('welcome') }}">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Peminjaman Ruangan</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ $menuDashboard ?? '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            @if (auth()->user()->role == 'admin')
                <!-- Heading -->
                <div class="sidebar-heading">
                    Menu Admin
                </div>

                <!-- Nav Item - Tables -->
                <li class="nav-item {{ $menuAdminBooking ?? '' }}">
                    <a class="nav-link" href="{{ route('booking') }}">
                        <i class="fas fa-calendar"></i>
                        <span>Pinjam Ruangan</span></a>
                </li>

                <!-- Nav Item - Tables -->
                <li class="nav-item {{ $menuAdminHistory ?? '' }}">
                    <a class="nav-link" href="{{ route('booking.history') }}">
                        <i class="fas fa-clock"></i>
                        <span>Riwayat Pinjam Ruangan</span></a>
                </li>

                <!-- Nav Item - Tables -->
                <li class="nav-item {{ $menuAdminRoom ?? '' }}">
                    <a class="nav-link" href="{{ route('room') }}">
                        <i class="fas fa-door-open"></i>
                        <span>Manajemen Ruangan</span></a>
                </li>

                <!-- Nav Item - Charts -->
                <li class="nav-item {{ $menuAdminUser ?? '' }}">
                    <a class="nav-link" href="{{ route('user') }}">
                        <i class="fas fa-user"></i>
                        <span>Manajemen Mahasiswa</span></a>
                </li>

                <!-- Nav Item - Tables -->
                <li class="nav-item {{ $menuAdminAdmin ?? '' }}">
                    <a class="nav-link" href="{{ route('admin') }}">
                        <i class="fas fa-user-tie"></i>
                        <span>Manajemen Admin</span></a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">
            @else
                <!-- Heading -->
                <div class="sidebar-heading">
                    Menu User
                </div>

                <!-- Nav Item - Tables -->
                <li class="nav-item {{ $menuUserBooking ?? '' }}">
                    <a class="nav-link" href="{{ route('booking') }}">
                        <i class="fas fa-calendar"></i>
                        <span>Pinjam Ruangan</span></a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">

                <!-- Sidebar Toggler (Sidebar) -->
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>
            @endif

        </ul>
        <!-- End of Sidebar -->