<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Pinjam | Login</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('enno/assets/img/ui-icon.png') }}" rel="icon">
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
    /* Input Group styling */
    .input-group {
        position: relative;
    }

    .btn-outline-secondary{
        border-color: #d1d3e2;
    }

    /* Button toggle eye */
    #togglePassword {
        border-left: none;
        padding: 0.375rem 0.75rem;
        background-color: #f8f9fa;
        border-radius: 0 10rem 10rem 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    #togglePassword:hover {
        background-color: #e9ecef;
        color: #495057;
    }

    #togglePassword i {
        font-size: 1.1rem;
        color: #6c757d;
    }

    /* Input focus state */
    .form-control-user:focus {
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        border-color: #4e73df;
    }
    </style>

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-6 col-lg-7 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Selamat Datang</h1>
                                    </div>

                                    <form class="user" method="POST" action="{{ route('loginProses') }}">
                                        @csrf

                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user @error('email') is-invalid @enderror"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Masukkan Email Address..." name="email" value="{{ old('email') }}">
                                            @error('email')
                                                <small class="text-danger">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="password" 
                                                    id="password" 
                                                    class="form-control form-control-user @error('password') is-invalid @enderror"
                                                    placeholder="Masukkan Password..." 
                                                    name="password" required>

                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                    </form>

                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="{{ route('passwordRequest') }}">Lupa Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="{{ route('register') }}">Buat Akun!</a>
                                    </div>
                                    <div class="text-center">
                                        <small>
                                            Kembali ke Beranda? <a href="{{ route('welcome') }}">Klik Disini</a>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>

    <script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function () {
                // Toggle type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);

                // Toggle eye icon
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        });
    </script>

    @session('success')
        <script>
            Swal.fire({
                title: "Sukses",
                text: "{{ session('success') }}",
                icon: "success"
            });
        </script>
    @endsession

    @session('error')
        <script>
            Swal.fire({
                title: "Gagal",
                text: "{{ session('error') }}",
                icon: "error"
            });
        </script>
    @endsession


</body>

</html>