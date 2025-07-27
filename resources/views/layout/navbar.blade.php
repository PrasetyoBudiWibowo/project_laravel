<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="#">Start Bootstrap</a>
    <button class="btn btn-link btn-sm me-4" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <ul class="navbar-nav ms-auto me-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" data-bs-toggle="dropdown"><i
                    class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" id="logout" href="#">Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>
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