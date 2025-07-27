<!DOCTYPE html>
<html lang="en">
@include('layout.header')

<body class="sb-nav-fixed">
    <div id="app">
        @include('layout.navbar')
        <div id="layoutSidenav">
            @include('layout.sidebar')
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        @yield('content')
                    </div>
                </main>
                @include('layout.footer')
            </div>
        </div>
    </div>
    <!-- Panggil app.js hanya sekali di sini -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
    document.getElementById("logout").addEventListener("click", function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Yakin ingin logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Logout!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('logout') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 'success') {
                            window.location.href = result.redirect;
                        } else {
                            Swal.fire('Gagal', result.message || 'Logout gagal', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', `Terjadi kesalahan: ${error.message}`, 'error');
                    });
            }
        });
    });
    </script>
</body>

</html>