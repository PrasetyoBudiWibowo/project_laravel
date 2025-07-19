<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Laravel App</title>
    <!-- Bootstrap 5 CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" /> -->
    <link href="{{ asset('css/bootstrap/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('/css/sweetalert2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('/css/select2.min.css') }}" rel="stylesheet" />

    <!-- Bootstrap 5 JS Bundle -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.all.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/helper.js') }}"></script>
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
        }

        .bg-image {
            background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: -1;
            filter: brightness(0.7);
        }

        .login-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3);
            padding: 2rem;
        }
    </style>
</head>

<body>
    <div class="bg-image"></div>

    <form class="d-flex justify-content-center align-items-center"
        style="height: 100vh;">
        @csrf
        <div class="login-card col-11 col-md-5 col-lg-4 border p-4 shadow rounded bg-white">
            <h4 class="mb-4 text-center">Login</h4>

            <div class="mb-3">
                <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
                <label for="nama_user" class="form-label">Username</label>
                <input type="text" class="form-control" id="nama_user" name="nama_user" placeholder="Masukkan Username"
                    autocomplete="off" autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Masukkan Password" autocomplete="off">
            </div>

            <div class="d-grid">
                <button id="userLogin" type="button" class="btn btn-primary">Log In</button>
            </div>

            <p class="text-center mt-3 text-muted">
                &copy; {{ date('Y') }} Your Company
            </p>
        </div>
    </form>

    <script>
        const login = async () => {
            const csrfToken = $('#csrf_token').val();
            let namaUser = $('#nama_user').val().trim();
            let password = $('#password').val();
            let requireValue = [];
            requireValue.push({
                value: namaUser,
                message: 'user name tidak boleh kosong'
            });
            requireValue.push({
                value: password,
                message: 'password tidak boleh kosong'
            });
            if (!validasiBanyakInputan(requireValue)) return;

            let dataToSave = {
                csrf_token: csrfToken,
                nama_user: namaUser,
                password: password
            }

            try {
                Swal.fire({
                    title: 'Sedang Login',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                })

                const response = await fetch("{{ route('login') }}", {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify(dataToSave)
                })

                const result = await response.json();
                Swal.close();
                if (result.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: result.message || 'Login berhasil!',
                    }).then(() => {
                        window.location.href = result.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: result.message,
                    });
                }
            } catch (error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: `Terjadi kesalahan ${error.message}.`,
                });
            }
        }

        $(document).ready(function() {
            $('#nama_user').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });
            $('#userLogin').on('click', () => {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Pastikan Username dan Password Sudah Benar',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        login()
                    }
                });
            });
        });
    </script>
</body>

</html>