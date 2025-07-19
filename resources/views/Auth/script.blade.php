<script>
    const login = async () => {
        const csrfToken = $('#csrf_token').val();
        let namaUser = $('#nama_user').val().trim();
        let password = $('#password').val();
        let requireValue = [
            { value: namaUser, message: 'user name tidak boleh kosong' },
            { value: password, message: 'password tidak boleh kosong' }
        ];
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
            });

            const response = await fetch("{{ route('login') }}", {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify(dataToSave)
            });

            const result = await response.json();
            Swal.close();
            if (result.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: result.message || 'Login berhasil!',
                })
                window.location.href = result.redirect;
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

    $(document).ready(function () {
        $('#nama_user').on('input', function () {
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
