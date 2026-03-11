<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Pinjam | Registrasi (Civitas FIK UI)</title>

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
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h2 class="mb-1">Registrasi Civitas FIK UI</h2>
                        <p class="mb-0 text-white-75">Mahasiswa, Staff, atau Dosen Fakultas Ilmu Keperawatan UI</p>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('register.store') }}" method="POST">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email Institusi <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="contoh@ui.ac.id" value="{{ old('email') }}" required>
                                <small class="form-text text-muted">Harus berakhiran <code>@ui.ac.id</code></small>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="nim_nip" class="form-label">NIM / NIP <span class="text-danger">*</span></label>
                                <input type="text" id="nim_nip" name="nim_nip" class="form-control" value="{{ old('nim_nip') }}" required>
                                <small class="form-text text-muted">NIM untuk mahasiswa, NIP untuk staff/dosen</small>
                                @error('nim_nip')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Jenis Pengguna <span class="text-danger">*</span></label>
                                <select name="jenis_pengguna" class="form-control" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="mahasiswa" {{ old('jenis_pengguna') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa FIK UI</option>
                                    <option value="staff" {{ old('jenis_pengguna') == 'staff' ? 'selected' : '' }}>Staff FIK UI</option>
                                    <option value="dosen" {{ old('jenis_pengguna') == 'dosen' ? 'selected' : '' }}>Dosen FIK UI</option>
                                </select>
                                @error('jenis_pengguna')
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

                            <button type="submit" class="btn btn-primary w-100 py-3">Daftar sebagai Civitas FIK UI</button>
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