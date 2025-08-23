<template>
    <div class="p-4">
        <div class="card mb-3">
            <div class="card-header">
                <div>Semua Module</div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-start-end mb-3">
                    <button
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#modalTambahModule"
                    >
                        <i class="fas fa-plus me-1"></i> Tambah Module
                    </button>
                </div>
            </div>

            <div
                class="modal fade"
                id="modalTambahModule"
                tabindex="-1"
                aria-labelledby="modalTambahModuleLabel"
                aria-hidden="true"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
            >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTambahModuleLabel">
                                Tambah Module
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
                                <label class="form-label">Nama Module</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="inputData.nama_module"
                                    placeholder="Masukkan kecamatan"
                                    @input="
                                        inputData.nama_module =
                                            inputData.nama_module.toUpperCase()
                                    "
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tampil Module</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="inputData.tampil_module"
                                    placeholder="Masukkan kecamatan"
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">url</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="inputData.url_module"
                                    placeholder="Masukkan kecamatan"
                                />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                Batal
                            </button>
                            <button
                                type="button"
                                class="btn btn-primary"
                                @click="btnSimpanKotaModule"
                            >
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import * as yup from "yup";
import { validate } from "vee-validate";

export default {
    data() {
        return {
            inputData: {
                nama_module: "",
                tampil_module: "",
                url_module: "",
            },
        };
    },
    async mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

        this.$nextTick(() => {
            $("#modalTambahKecamatan").on("hide.bs.modal", () => {
                this.resetFormTambah();
            });
        });
    },
    methods: {
        resetFormTambah() {
            this.inputData.nama_module = "";
            this.inputData.tampil_module = "";
            this.inputData.url_module = "";
        },
        btnSimpanKotaModule() {
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
                    this.simpanModule();
                }
            });
        },
        async simpanModule() {
            let dataToSave = {
                ...this.inputData,
                user_input: window.encryptedUserId,
            };

            let requireValue = [];

            requireValue.push({
                value: dataToSave.nama_module,
                message: "Kecamatan Tidak Boleh Kosong",
            });

            const schema = yup.object({
                nama_module: yup
                    .string()
                    .required("Nama module wajib diisi")
                    .matches(
                        /^[a-zA-Z0-9\s]+$/,
                        "Nama module Hanya Boleh huruf & angka yang diperbolehkan"
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

                const response = await axios.post("/module", dataToSave);
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
