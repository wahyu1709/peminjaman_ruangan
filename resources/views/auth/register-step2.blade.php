<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Pinjam | Registrasi (Pihak Eksternal)</title>

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
                    <div class="card-header bg-secondary text-white text-center py-4">
                        <h2 class="mb-1">Registrasi Pihak Eksternal</h2>
                        <p class="mb-0 text-white-75">Untuk instansi, perusahaan, atau individu di luar FIK UI</p>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('register.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="jenis_pengguna" value="umum">

                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="instansi" class="form-label">Instansi / Organisasi <span class="text-danger">*</span></label>
                                <input type="text" id="instansi" name="instansi" class="form-control" value="{{ old('instansi') }}" required>
                                @error('instansi')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="phone" class="form-label">No. HP <span class="text-danger">*</span></label>
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="+6281234567890" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" id="password" name="password" class="form-control" minlength="6" required>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                                @error('password_confirmation')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-secondary w-100 py-3">Daftar sebagai Pihak Eksternal</button>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3 bg-light">
                        <p class="mb-0 text-muted">
                            Kembali ke <a href="{{ route('register') }}" class="text-primary">pilih jenis pengguna</a>
                        </p>
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