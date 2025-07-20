import { createApp } from "vue";
import axios from "axios";

const register = createApp({
    data() {
        return {
            levels: [],
            karyawan: [],
            selectedLevel: "",
            username: "",
            password: "",
            showInputKaryawan: false,
            kdKaryawan: "",
            userLogin: "",
            loggedInUser: null,
        };
    },
    mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;
        this.checkSessionLogin();
        this.fetchLevels();
        this.fetchkaryawan();
    },
    watch: {
        showInputKaryawan(val) {
            if (val) {
                this.$nextTick(() => {
                    defaultSelect2("#kd_karyawan", "-- Pilih Karyawan --");
                    $("#kd_karyawan").on("change", (e) => {
                        this.kdKaryawan = e.target.value;
                    });
                });
            } else {
                if ($('#kd_karyawan').hasClass("select2-hidden-accessible")) {
                    $('#kd_karyawan').select2('destroy');
                }
                this.kdKaryawan = '';
            }
        }
    },
    methods: {
        togglePilihkaryawan() {
            this.showInputKaryawan = !this.showInputKaryawan;
            if (!this.showInputKaryawan) {
                this.kdKaryawan = '';
            }
        },
    async checkSessionLogin() {
            try {
                const response = await checkSession();
                // console.log('User session:', response.data.user);

                this.loggedInUser = response.data.user;

            } catch (error) {
                console.error('Belum login:', error);
                Swal.fire({
                    icon: 'warning',
                    title: 'Session Habis',
                    text: 'Silakan login terlebih dahulu.',
                }).then(() => {
                    window.location.href = '/login';
                });
            }
        },
        async fetchLevels() {
            try {
                const data = await getLevelUser();
                this.levels = data || [];

            this.$nextTick(() => {
                defaultSelect2("#id_usr_level", "-- Pilih Level --");
            });

            $("#id_usr_level").on("change", (e) => {
                this.selectedLevel = e.target.value;
            });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: `Terjadi kesalahan fetchLevels: ${err.statusText || err}`,
                });
            }
        },
        async fetchkaryawan() {
            try {
                const data = await getAllDataKaryawan();
                this.karyawan = data || [];

                this.$nextTick(() => {
                    defaultSelect2("#kd_karyawan", "-- Pilih Karyawan --");
                });

                $("#kd_karyawan").on("change", (e) => {
                    this.kdKaryawan = e.target.value;
                });
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: `Terjadi kesalahan fetchkaryawan: ${err.statusText || err}`,
                });
            }
        },
        confirmRegister() {
            Swal.fire({
                title: "Konfirmasi",
                text: "Pastikan Username dan Password Sudah Benar",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Batal",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.register();
                }
            });
        },
        async register() {
            let requireValue = [];

            let namaUser = this.username;
            let password = this.password;
            let selectedLevel = this.selectedLevel;
            let nilaiSwitch = this.showInputKaryawan;
            let kdKaryaawan = this.kdKaryawan;
            let user_input = this.loggedInUser.kd_asli_user;
            let user_login = this.loggedInUser.nama_user;

            requireValue.push({ value: namaUser, message: 'User Name Tidak Boleh Kosong' });
            requireValue.push({ value: password, message: 'Password Tidak Boleh Kosong' });
            requireValue.push({ value: selectedLevel, message: 'Level User harus dipilih' });

            if (nilaiSwitch === true) {
                requireValue.push({ value: kdKaryaawan, message: 'Karyawan Tidak Boleh Kosong' });
            }

            if (!validasiBanyakInputan(requireValue)) return;

            let dataToSave = {
                id_usr_level: selectedLevel,
                nama_user: namaUser,
                password: password,
                is_karyawan: nilaiSwitch,
                kd_karyawan: kdKaryaawan,
                user_input: user_input,
                user_login: user_login,
            };

            try {
                Swal.fire({
                    title: 'Sedang Registrasi',
                    text: 'Mohon tunggu.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await axios.post('/register', dataToSave);
                const result = response.data;

                Swal.close();

                if (result.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: result.message || 'Data berhasil Disimpan!',
                    }).then(() => {
                        window.location.reload();
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
                    text: `Terjadi kesalahan: ${error.response?.data?.message || error.message}`,
                });
            }
        }
    },
});

register.mount("#register-app");
