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

                <table
                    id="tabelModule"
                    class="display nowrap"
                    style="width: 100%"
                >
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Module</th>
                        </tr>
                    </thead>
                </table>
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
                                    placeholder="Masukkan nama module"
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
                                    placeholder="Masukan tampil module"
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">url</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="inputData.url_module"
                                    placeholder="Masukan url"
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
                                @click="btnSimpanModule"
                            >
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <LoadingData :visible="loading" message="Loading" />
    </div>
</template>
<script>
import * as yup from "yup";
import { validate } from "vee-validate";
import LoadingData from "../../loading/loadingData.vue";

export default {
    components: { LoadingData },
    data() {
        return {
            dataModule: [],
            dataTableInstance: null,
            inputData: {
                nama_module: "",
                tampil_module: "",
                url_module: "",
            },
            loading: true,
        };
    },
    async mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

        await this.module();

        this.$nextTick(() => {
            $("#modalTambahModule").on("hide.bs.modal", () => {
                this.resetFormTambah();
            });

            this.refreshTable();
        });

        this.loading = false;
    },
    methods: {
        async module() {
            try {
                const data = await getAllModule();
                this.dataModule = data || [];
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan module: ${err.statusText || err}`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        resetFormTambah() {
            this.inputData.nama_module = "";
            this.inputData.tampil_module = "";
            this.inputData.url_module = "";
        },
        btnSimpanModule() {
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
                message: "Module Tidak Boleh Kosong",
            });

            const schema = yup.object({
                nama_module: yup
                    .string()
                    .required("Nama module wajib diisi")
                    .matches(
                        /^[a-zA-Z0-9\s]+$/,
                        "Nama module Hanya Boleh huruf & angka yang diperbolehkan"
                    ),
                url_module: yup
                    .string()
                    .required("URL module wajib diisi")
                    .matches(
                        /^[a-zA-Z0-9_-]+$/,
                        "URL module hanya boleh huruf, angka, strip (-), atau underscore (_)"
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
        refreshTable() {
            if (this.dataTableInstance) {
                this.dataTableInstance.clear().destroy();
                this.dataTableInstance = null;
            }

            this.dataTableInstance = $("#tabelModule").DataTable({
                data: this.dataModule,
                scrollCollapse: true,
                scrollY: 300,
                fixedHeader: true,
                columns: [
                    {
                        data: null,
                        width: "5%",
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        },
                    },
                    { data: "nama_module" },
                ],
                initComplete: function () {
                    $("#tabelModule tbody").on("mouseenter", "tr", function () {
                        $(this).css("background-color", "Yellow");
                    });
                    $("#tabelModule tbody").on("mouseleave", "tr", function () {
                        $(this).css("background-color", "");
                    });
                },
            });
        },
    },
};
</script>
