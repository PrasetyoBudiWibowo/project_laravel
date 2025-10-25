<template>
    <div class="p-4">
        <div class="card mb-3">
            <div class="card-header">
                <div>MASTER DIVISI</div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-start-end mb-3">
                    <button
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#modalTambahDivisi"
                    >
                        <i class="fas fa-plus me-1"></i> Tambah
                    </button>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table
                                id="tblDivisi"
                                class="table table-bordered"
                                style="width: 100%"
                            >
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Divisi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div
                    class="modal fade"
                    id="modalTambahDivisi"
                    tabindex="-1"
                    aria-labelledby="modalTambahDivisiLabel"
                    aria-hidden="true"
                    data-bs-backdrop="static"
                    data-bs-keyboard="false"
                >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5
                                    class="modal-title"
                                    id="modalTambahDivisiLabel"
                                >
                                    Tambah Divisi
                                </h5>
                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                ></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nama_divisi" class="form-label"
                                        >Nama Divisi</label
                                    >
                                    <input
                                        type="text"
                                        autocomplete="off"
                                        class="form-control"
                                        placeholder="Masukkan Nama Divisi"
                                        v-model="inputData.nama_divisi"
                                        @input="
                                            inputData.nama_divisi =
                                                inputData.nama_divisi.toUpperCase()
                                        "
                                    />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    data-bs-dismiss="modal"
                                    id="tutup"
                                >
                                    <i class="fa-solid fa-xmark"></i> Tutup
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    id="btnSimpanDivisi"
                                    @click="btnSimpanDivisi"
                                >
                                    <i class="fa-solid fa-paper-plane"></i>
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <loadingData :visible="loadingMenu" message="Loading" />
</template>

<script>
import * as yup from "yup";
import loadingData from "../../../loading/loadingData.vue";

export default {
    components: { loadingData },
    data() {
        return {
            inputData: {
                nama_divisi: "",
            },

            loadingMenu: true,

            dataUser: window.userData,
        };
    },
    async mounted() {
        btnSimpanDivisi;
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

        if (this.dataUser.level_user !== "SUPER ADMIN") {
            await this.cekStatusAkses();
        }

        this.$nextTick(() => {
            $("#modalTambahDivisi").on("hidden.bs.modal", () => {
                this.inputData.nama_divisi = "";
            });
        });

        this.loadingMenu = false;
    },
    methods: {
        async cekStatusAkses() {
            try {
                const data = {
                    user: window.encryptedUserId,
                    url: window.location.pathname,
                };

                const allowed = await validasiUserHalaman(data);

                if (!allowed) {
                    return;
                }
            } catch (error) {
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
                    buttonsStyling: false,
                });
            }
        },
        btnSimpanDivisi() {
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda Yakin Ingin Menyimpan Data ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger",
                },
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.simpanDivisi();
                }
            });
        },
        async simpanDivisi() {
            let dataToSave = {
                ...this.inputData,
                user_input: window.encryptedUserId,
            };

            let requireValue = [];

            requireValue.push({
                value: dataToSave.nama_divisi,
                message: "Divisi Tidak Boleh Kosong",
            });

            const schema = yup.object({
                nama_divisi: yup
                    .string()
                    .required("Nama Divisi wajib diisi")
                    .matches(
                        /^[A-Za-z\s]+$/,
                        "Nama Divisi Hanya Boleh huruf yang diperbolehkan"
                    ),
            });

            try {
                await schema.validate(dataToSave, { abortEarly: false });

                Swal.fire({
                    title: "Sedang Proses Simpan Data",
                    text: "Mohon tunggu.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                const response = await axios.post(
                    "/hrd/simpan-divisi",
                    dataToSave
                );
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
                    buttonsStyling: false,
                });
            }
        },
    },
};
</script>
