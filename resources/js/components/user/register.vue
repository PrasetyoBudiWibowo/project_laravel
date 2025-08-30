<template>
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center font-weight-light my-4">
                        Register User
                    </h3>
                </div>
                <div class="card-body">
                    <form @submit.prevent="confirmRegister">
                        <!-- Switch Pilih Karyawan -->
                        <div class="form-check form-switch mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="switchPilihKaryawan"
                                v-model="showInputKaryawan"
                            />
                            <label
                                class="form-check-label"
                                for="switchPilihKaryawan"
                            >
                                {{
                                    showInputKaryawan
                                        ? "Gunakan Data Karyawan"
                                        : "Jangan Gunakan Data Karyawan"
                                }}
                            </label>
                        </div>

                        <!-- Input Tambahan -->
                        <div class="mb-3" v-if="showInputKaryawan">
                            <label for="kd_karyawan" class="form-label"
                                >Pilih Karyawan</label
                            >
                            <select
                                name="kd_karyawan"
                                id="kd_karyawan"
                                class="form-control"
                                v-model="kdKaryawan"
                            >
                                <option disabled value="">
                                    -- Pilih Karyawan --
                                </option>
                                <option
                                    v-for="kry in karyawan"
                                    :key="kry.kd_karyawan"
                                    :value="kry.kd_karyawan"
                                >
                                    {{ kry.nama_karyawan }}
                                </option>
                            </select>
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="nama_user" class="form-label"
                                >Username</label
                            >
                            <input
                                type="text"
                                id="nama_user"
                                v-model="username"
                                @input="username = username.toUpperCase()"
                                class="form-control"
                                placeholder="Masukkan Username"
                                autocomplete="off"
                                autofocus
                            />
                        </div>

                        <!-- Level User -->
                        <div class="mb-3">
                            <label for="id_usr_level" class="form-label"
                                >Level User</label
                            >
                            <select
                                id="id_usr_level"
                                v-model="selectedLevel"
                                class="form-control"
                                name="id_usr_level"
                            >
                                <option disabled value="">
                                    -- Pilih Level --
                                </option>
                                <option
                                    v-for="level in levels"
                                    :key="level.id"
                                    :value="level.id"
                                >
                                    {{ level.level_user }}
                                </option>
                            </select>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label"
                                >Password</label
                            >
                            <input
                                type="text"
                                id="password"
                                v-model="password"
                                class="form-control"
                                placeholder="Masukkan Password"
                                autocomplete="off"
                            />
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Buat Akun
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
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
                if ($("#kd_karyawan").hasClass("select2-hidden-accessible")) {
                    $("#kd_karyawan").select2("destroy");
                }
                this.kdKaryawan = "";
            }
        },
    },
    methods: {
        togglePilihkaryawan() {
            this.showInputKaryawan = !this.showInputKaryawan;
            if (!this.showInputKaryawan) {
                this.kdKaryawan = "";
            }
        },
        async checkSessionLogin() {
            try {
                const response = await checkSession();
                // console.log('User session:', response.data.user);

                this.loggedInUser = response.data.user;
            } catch (error) {
                Swal.fire({
                    icon: "warning",
                    title: "Session Habis",
                    text: "Silakan login terlebih dahulu.",
                }).then(() => {
                    window.location.href = "/login";
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
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan fetchLevels: ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
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
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan fetchkaryawan: ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        confirmRegister() {
            Swal.fire({
                title: "Konfirmasi",
                text: "Pastikan Username dan Password Sudah Benar",
                icon: "question",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger",
                },
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

            requireValue.push({
                value: namaUser,
                message: "User Name Tidak Boleh Kosong",
            });
            requireValue.push({
                value: password,
                message: "Password Tidak Boleh Kosong",
            });
            requireValue.push({
                value: selectedLevel,
                message: "Level User harus dipilih",
            });

            if (nilaiSwitch === true) {
                requireValue.push({
                    value: kdKaryaawan,
                    message: "Karyawan Tidak Boleh Kosong",
                });
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
                    title: "Sedang Registrasi",
                    text: "Mohon tunggu.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                const response = await axios.post("/register", dataToSave);
                const result = response.data;

                Swal.close();

                if (result.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: result.message || "Data berhasil Disimpan!",
                        customClass: {
                            confirmButton: "btn btn-success",
                        },
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: result.message,
                        confirmButtonText: "Tutup",
                        customClass: {
                            confirmButton: "btn btn-danger",
                        },
                    });
                }
            } catch (error) {
                Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan: ${
                        error.response?.data?.message || error.message
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
    },
};
</script>
