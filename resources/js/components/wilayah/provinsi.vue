<template>
    <div class="p-4">
        <div class="card">
            <div class="card-header">
                <div>Provinsi</div>
            </div>
            <div class="card-body">
                <!-- Pencarian otomatis -->
                <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label">Cari:</label>
                    <div class="col-sm-10">
                        <input
                            v-model="searchAuto"
                            type="text"
                            class="form-control"
                            placeholder="Ketik nama provinsi"
                            @input="applyAutoFilter"
                        />
                    </div>
                </div>

                <!-- Pencarian manual -->
                <!-- <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label">Cari Manual:</label>
                    <div class="col-sm-7">
                        <input
                            v-model="searchManual"
                            type="text"
                            class="form-control"
                            placeholder="Masukkan nama provinsi"
                        />
                    </div>
                    <div class="col-sm-3">
                        <button
                            class="btn btn-primary me-2"
                            @click="applyManualFilter"
                        >
                            <i class="fas fa-search me-1"></i>
                            Cari
                        </button>
                        <button class="btn btn-danger" @click="resetFilter">
                            <i class="fas fa-xmark"></i>
                            Reset
                        </button>
                    </div>
                </div> -->

                <div class="d-flex justify-start-end mb-3">
                    <button
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#modalTambahProvinsi"
                    >
                        <i class="fas fa-plus me-1"></i> Tambah Provinsi
                    </button>
                </div>

                <a-table
                    id="tbl_provinsi"
                    :columns="columns"
                    :data-source="filteredData"
                    :rowKey="'kd_provinsi'"
                    bordered
                    :pagination="{
                        pageSizeOptions: [
                            '10',
                            '20',
                            '25',
                            '50',
                            '100',
                            '200',
                            '250',
                            '500',
                            '1000',
                        ],
                        showSizeChanger: true,
                        showTotal: (total) => `Total ${total} data`,
                    }"
                    :scroll="{ y: 600 }"
                    :sticky="true"
                />
            </div>
        </div>

        <div
            class="modal fade"
            id="modalTambahProvinsi"
            tabindex="-1"
            aria-labelledby="modalTambahProvinsiLabel"
            aria-hidden="true"
            data-bs-backdrop="static"
            data-bs-keyboard="false"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahProvinsiLabel">
                            Tambah Provinsi
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
                            <label class="form-label">Nama Provinsi</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="formTambah.nama_provinsi"
                                placeholder="Masukkan nama provinsi"
                                @input="
                                    formTambah.nama_provinsi =
                                        formTambah.nama_provinsi.toUpperCase()
                                "
                            />
                        </div>
                        <!-- <div class="mb-3">
                            <label class="form-label">Status Tampil</label>
                            <select
                                class="form-select"
                                v-model="formTambah.status_tampil"
                            >
                                <option value="">-- Pilih Status --</option>
                                <option value="YA">YA</option>
                                <option value="TIDAK">TIDAK</option>
                            </select>
                        </div> -->
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
                            @click="btnSimpanProvinsi"
                        >
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="modal fade"
            id="modalEditProvinsi"
            tabindex="-1"
            aria-labelledby="modalEditProvinsiLabel"
            aria-hidden="true"
            data-bs-backdrop="static"
            data-bs-keyboard="false"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditProvinsiLabel">
                            Edit Provinsi
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
                            <input
                                type="hidden"
                                id="kd_provinsi"
                                class="form-control"
                                v-model="formEdit.kd_provinsi"
                            />

                            <label for="nama_provinsi" class="form-label"
                                ><strong>Nama:</strong></label
                            >
                            <input
                                type="text"
                                id="nama_provinsi"
                                class="form-control"
                                v-model="formEdit.nama_provinsi"
                                placeholder="Masukkan Nama Provinsi"
                                autocomplete="off"
                                @input="
                                    formEdit.nama_provinsi =
                                        formEdit.nama_provinsi.toUpperCase()
                                "
                            />

                            <div class="mb-3">
                                <label for="status_tampil" class="form-label"
                                    >Status User</label
                                >
                                <select
                                    id="status_tampil"
                                    v-model="formEdit.status_tampil"
                                    class="form-control"
                                >
                                    <option value="ACTIVE">ACTIVE</option>
                                    <option value="NON ACTIVE">
                                        NON ACTIVE
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Tutup
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="btnSimpanEditProvinsi"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { h } from "vue";
import { Modal } from "bootstrap";
import axios from "axios";

export default {
    data() {
        return {
            rawColumns: [
                {
                    title: "No",
                    dataIndex: "_no",
                    key: "_no",
                    width: 60,
                    customRender: ({ text }) => h("span", text),
                },
                {
                    title: "Provinsi",
                    dataIndex: "nama_provinsi",
                    key: "nama_provinsi",
                },
                {
                    title: "Status Tampil",
                    dataIndex: "status_tampil",
                    key: "status_tampil",
                },
                {
                    title: "Aksi",
                    key: "aksi",
                    width: 120,
                    customRender: ({ record }) =>
                        h(
                            "button",
                            {
                                class: "btn btn-sm btn-primary",
                                onClick: () => this.openEditModal(record),
                            },
                            "Edit"
                        ),
                },
            ],
            dataProvinsi: [],
            columns: [],
            filteredData: [],
            searchAuto: "",
            searchManual: "",
            formTambah: {
                nama_provinsi: "",
            },
            formEdit: {
                nama_provinsi: "",
                status_tampil: "",
                kd_provinsi: "",
            },
            modalInstance: null,
            loggedInUser: null,
        };
    },
    mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;
        this.columns = this.rawColumns;
        this.provinsi();
        this.checkSessionLogin();

        $("#modalTambahProvinsi").on("hidden.bs.modal", () => {
            this.resetFormTambah();
        });

        this.$nextTick(() => {
            const modalEl = $("#modalEditProvinsi")[0];
            this.modalInstance = new Modal(modalEl);

            defaultSelect2("#status_tampil", null, "#modalEditProvinsi");
        });
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
        async provinsi() {
            try {
                const data = await getAllDataProvinsi();
                this.dataProvinsi = (data || []).map((it, ix) => ({
                    ...it,
                    _no: ix + 1,
                }));
                this.filteredData = this.dataProvinsi;
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan dataLevels: ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                    buttonsStyling: false,
                });
            }
        },
        applyAutoFilter() {
            const query = this.searchAuto.toLowerCase();
            this.filteredData = this.dataProvinsi.filter((prov) =>
                prov.nama_provinsi.toLowerCase().includes(query)
            );
        },
        applyManualFilter() {
            const query = this.searchManual.toLowerCase();
            this.filteredData = this.dataProvinsi.filter((prov) =>
                prov.nama_provinsi.toLowerCase().includes(query)
            );
        },
        resetFilter() {
            this.searchManual = "";
            this.searchAuto = "";
            this.filteredData = this.dataProvinsi;
        },
        resetFormTambah() {
            this.formTambah.nama_provinsi = "";
        },
        openEditModal(record) {
            this.formEdit = {
                nama_provinsi: record.nama_provinsi,
                status_tampil: record.status_tampil,
                kd_provinsi: record.kd_provinsi,
            };

            this.$nextTick(() => {
                $("#status_tampil")
                    .val(this.formEdit.status_tampil)
                    .trigger("change");
            });
            this.modalInstance.show();
        },
        btnSimpanProvinsi() {
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
                    this.simpanProvinsi();
                }
            });
        },
        async simpanProvinsi() {
            let dataToSave = {
                ...this.formTambah,
                user_input: this.loggedInUser.kd_asli_user,
                user_login: this.loggedInUser.nama_user,
            };

            let requireValue = [];

            requireValue.push({
                value: dataToSave.nama_provinsi,
                message: "Nama Provinsi Tidak Boleh Kosong",
            });

            if (!validasiBanyakInputan(requireValue)) return;

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
                    "/wilayah/simpan-provinsi",
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
        btnSimpanEditProvinsi() {
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
                    this.ubahProvinsi();
                }
            });
        },
        async ubahProvinsi() {
            let dataToSave = {
                ...this.formEdit,
            };

            let requireValue = [];

            requireValue.push({
                value: dataToSave.nama_provinsi,
                message: "Nama Provinsi Tidak Boleh Kosong",
            });

            if (!validasiBanyakInputan(requireValue)) return;

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
                    "/wilayah/ubah-provinsi",
                    dataToSave
                );
                const result = response.data;

                Swal.close();
                if (result.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: result.message || "Data berhasil diubah!",
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

<style>
#tbl_provinsi .ant-table-tbody > tr:hover > td {
    background-color: #fbff00de !important;
}
</style>
