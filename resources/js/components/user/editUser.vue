<template>
    <div>
        <h2>Ubah Data User {{ form.nama_user }}</h2>
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Foto:</label>

                <div class="d-flex align-items-end gap-2 mb-2" v-if="preview">
                    <img
                        :src="preview"
                        alt="Preview"
                        class="img-thumbnail"
                        style="max-height: 150px"
                    />
                    <button
                        v-if="
                            form.foto || (form.img_user && form.img_user !== '')
                        "
                        type="button"
                        class="btn btn-outline-danger btn-sm h-25 mt-2"
                        @click="batalUpload"
                    >
                        Reset Gambar
                    </button>
                </div>

                <input
                    type="file"
                    class="form-control"
                    @change="handleFileUpload"
                    ref="fileInput"
                />
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Nama:</label>
                <input
                    type="text"
                    class="form-control"
                    v-model="form.nama_user"
                />
            </div>
            <div class="col-md-4">
                <label>Password:</label>
                <input
                    type="text"
                    class="form-control"
                    v-model="form.password"
                />
            </div>
        </div>

        <button class="btn btn-primary" @click="btnSimpanEditUser">
            Simpan
        </button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                nama_user: "",
                password: "",
                img_user: null,
                foto: null,
                loggedInUser: null,
            },
            preview: null,
        };
    },
    props: {
        encryptedId: String,
    },
    mounted() {
        this.getUser();
        this.checkSessionLogin();
    },
    methods: {
        async checkSessionLogin() {
            try {
                const response = await checkSession();

                this.loggedInUser = response.data.user;
            } catch (error) {
                console.error("Belum login:", error);
                Swal.fire({
                    icon: "warning",
                    title: "Session Habis",
                    text: "Silakan login terlebih dahulu.",
                }).then(() => {
                    window.location.href = "/login";
                });
            }
        },
        async getUser() {
            try {
                const data = await userByCode(this.encryptedId);
                this.form.nama_user = data.nama_user;
                this.form.img_user = data.img_user;
                this.form.password = data.password_tampil;

                if (!data.img_user || data.img_user === "") {
                    this.preview = "/assets/img/default/Default-Profile.png";
                }
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan: ${
                        error.response?.data?.message || error.message
                    }`,
                });
            }
        },
        handleFileUpload(event) {
            const file = event.target.files[0];

            if (!file) return;

            const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];

            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: "error",
                    title: "File Tidak Valid",
                    text: "Hanya file gambar (JPG, JPEG, PNG) yang diizinkan.",
                });

                this.form.foto = null;
                this.preview = this.form.img_user
                    ? `/assets/img/user/${this.form.img_user}`
                    : "/assets/img/default/Default-Profile.png";

                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = "";
                }

                return;
            }

            this.form.foto = file;
            this.preview = URL.createObjectURL(file);
        },
        batalUpload() {
            this.form.foto = null;
            this.preview = "/assets/img/default/Default-Profile.png";
            this.$refs.fileInput.value = "";

            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = "";
            }
        },
        btnSimpanEditUser() {
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda Yakin Ingin Menyimpan Data ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Batal",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.editUser();
                }
            });
        },
        async editUser() {
            const formData = new FormData();
            formData.append("nama_user", this.form.nama_user);
            formData.append("password", this.form.password);
            formData.append("kd_asli_user", this.loggedInUser.kd_asli_user);

            if (this.form.foto) {
                formData.append("foto", this.form.foto);
            }

            // for (let pair of formData.entries()) {
            //     console.log(pair[0] + ":", pair[1]);
            // }

            try {
                Swal.fire({
                    title: "Sedang Proses Simpan Data",
                    text: "Mohon tunggu.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                const response = await axios.post(
                    "/valisdasi-ubah-user",
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                const result = response.data;
                if (result.status === "success") {
                    Swal.close();
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: result.message || "Data berhasil Disimpan!",
                    }).then(() => {
                        window.location.href = result.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: result.message,
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
                });
            }
        },
    },
};
</script>
