<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Pinjam | Registrasi</title>

    <!-- Custom fonts -->
    <link href="{{ asset('enno/assets/img/ui-icon.png') }}" rel="icon">
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles -->
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body text-center p-5">
                        <h2 class="mb-2">Daftar Akun</h2>
                        <p class="text-muted mb-4">Pilih jenis pengguna untuk melanjutkan</p>

                        <!-- Civitas FIK UI -->
                        <a href="{{ route('register.step1') }}" 
                           class="btn btn-outline-primary w-100 mb-3 py-3 text-start d-flex align-items-start">
                            <i class="fas fa-user-graduate me-3 fs-4"></i>
                            <div>
                                <strong>Civitas Akademika FIK UI</strong><br>
                                <small>Mahasiswa, Staff, atau Dosen FIK UI</small>
                            </div>
                        </a>

                        <!-- Pihak Eksternal -->
                        <a href="{{ route('register.step2') }}" 
                           class="btn btn-outline-secondary w-100 py-3 text-start d-flex align-items-start">
                            <i class="fas fa-users me-3 fs-4"></i>
                            <div>
                                <strong>Pihak Eksternal</strong><br>
                                <small>Non-FIK UI (Instansi, Perusahaan, dll)</small>
                            </div>
                        </a>
                    </div>
                    <div class="text-center">
                        <small>
                            Sudah punya akun? <a href="{{ route('login') }}">Klik Disini</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>

    @session('success')
        <script>
            Swal.fire({ title: "Sukses", text: "{{ session('success') }}", icon: "success" });
        </script>
    @endsession

    @session('error')
        <script>
            Swal.fire({ title: "Gagal", text: "{{ session('error') }}", icon: "error" });
        </script>
    @endsession

</body>

</html>