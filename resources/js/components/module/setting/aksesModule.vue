<template>
    <div class="p-4">
        <div class="card mb-3">
            <div class="card-header">
                <div>Hak Akses</div>
            </div>
            <div class="card-body">
                <table
                    id="tabelAkesModuleUser"
                    class="display nowrap"
                    style="width: 100%"
                >
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>User</th>
                            <th>Hak Akses</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div
                class="modal fade"
                id="modalTambahHaAksesUser"
                tabindex="-1"
                aria-labelledby="modalTambahHaAksesUserLabel"
                aria-hidden="true"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
            >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                Tambah Atau Ubah Hak Akses
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Tutup"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama user</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="inputData.nama_user"
                                    disabled
                                    @input="inputData.nama_user"
                                />
                            </div>
                            <div class="row mb-3">
                                <label for="select_module" class="form-label"
                                    >Module</label
                                >
                                <div class="d-flex">
                                    <select
                                        class="form-control me-2"
                                        name="select_module"
                                        id="select_module"
                                        v-model="selectedModule"
                                    >
                                        <option value="">
                                            -- PILIH AKSES --
                                        </option>
                                        <option
                                            v-for="item in dataModule"
                                            :key="item.kd_module"
                                            :value="item.kd_module"
                                        >
                                            {{ item.nama_module }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <button
                                type="button"
                                class="btn btn-primary"
                                @click="tambahAkses"
                                :disabled="!selectedModule"
                            >
                                Tambah
                            </button>

                            <div v-if="inputData.akses.length > 0" class="mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%">#</th>
                                            <th>Module</th>
                                            <th style="width: 20%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                item, index
                                            ) in inputData.akses"
                                            :key="index"
                                        >
                                            <td>{{ index + 1 }}</td>
                                            <td>
                                                {{
                                                    dataModule.find(
                                                        (m) =>
                                                            m.kd_module ===
                                                            item.kd_module
                                                    )?.tampil_module
                                                }}
                                            </td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-danger"
                                                    @click="hapusAkses(index)"
                                                >
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
                                @click="btnSimpanAksesModule"
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
import LoadingData from "../../loading/loadingData.vue";
import { Modal } from "bootstrap";

export default {
    components: { LoadingData },
    data() {
        return {
            dataUser: [],
            dataModule: [],
            dataTableInstance: null,
            loading: true,
            modalInstance: null,
            inputData: {
                nama_user: "",
                kd_user: "",
                akses: [],
            },
            selectedModule: "",
        };
    },
    async mounted() {
        await this.user();
        await this.module();

        this.$nextTick(() => {
            const modalEl = $("#modalTambahHaAksesUser")[0];
            this.modalInstance = new Modal(modalEl);

            $("#tabelAkesModuleUser").on("click", ".btn-edit", (e) => {
                const rowData = this.dataTableInstance
                    .row($(e.target).closest("tr"))
                    .data();
                this.openEditModal(rowData);
            });

            defaultSelect2(
                "#select_module",
                "-- PILIH --",
                "#modalTambahHaAksesUser"
            );

            $("#select_module").on("change", (e) => {
                this.selectedModule = $(e.target).val();
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
                    text: `Terjadi kesalahan module: ${
                        error.statusText || error
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        async user() {
            try {
                const data = await getDataUserRegister();
                this.dataUser = data || [];
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan user: ${err.statusText || err}`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        refreshTable() {
            if (this.dataTableInstance) {
                this.dataTableInstance.clear().destroy();
                this.dataTableInstance = null;
            }

            this.dataTableInstance = $("#tabelAkesModuleUser").DataTable({
                data: this.dataUser,
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
                    { data: "nama_user" },
                    { data: "nama_user" },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return `
                                <button class="btn btn-sm btn-warning btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </button>`;
                        },
                    },
                ],
                initComplete: function () {
                    $("#tabelAkesModuleUser tbody").on(
                        "mouseenter",
                        "tr",
                        function () {
                            $(this).css("background-color", "Yellow");
                        }
                    );
                    $("#tabelAkesModuleUser tbody").on(
                        "mouseleave",
                        "tr",
                        function () {
                            $(this).css("background-color", "");
                        }
                    );
                },
            });
        },
        openEditModal(data) {
            this.inputData.kd_user = data.kd_user;
            this.inputData.nama_user = data.nama_user;
            this.inputData.kd_user = data.kd_asli_user;
            this.inputData.akses = data.akses || [];

            this.modalInstance.show();
        },
        tambahAkses() {
            const module = this.inputData.akses.find(
                (a) => a.kd_module === this.selectedModule
            );

            if (module) {
                const moduleName = this.dataModule.find(
                    (m) => m.kd_module === this.selectedModule
                );

                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Module ${moduleName.nama_module} sudah ada di daftar akses!`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
                return;
            }

            if (
                !this.inputData.akses.find(
                    (a) => a.kd_module === this.selectedModule
                )
            ) {
                this.inputData.akses.push({
                    kd_module: this.selectedModule,
                    // kd_user: this.inputData.kd_asli_user || "",
                });
            }
            this.selectedModule = "";
        },
        hapusAkses(index) {
            this.inputData.akses.splice(index, 1);
        },
        btnSimpanAksesModule() {
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
                    this.simpanAksesModule();
                }
            });
        },
        async simpanAksesModule() {
            let dataToSave = {
                ...this.inputData,
                user_input: window.encryptedUserId,
            };

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
                    "/akses-module-user",
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
                        this.modalInstance.hide();
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
