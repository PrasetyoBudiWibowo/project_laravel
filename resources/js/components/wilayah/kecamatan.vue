<template>
    <div class="p-4">
        <div class="card mb-3">
            <div class="card-header">
                <div>Filter Data</div>
            </div>
            <div class="card-body">
                <div class="mb-2 row align-items-center">
                    <label class="col-sm-2 col-form-label"
                        >Cari Provinsi:</label
                    >
                    <div class="col-sm-4">
                        <select
                            id="filter_provinsi"
                            class="form-select"
                            v-model="selectedProvinsi"
                        >
                            <option value="">-- Semua Provinsi --</option>
                            <option
                                v-for="prov in dataProvinsi"
                                :key="prov.kd_provinsi"
                                :value="prov.kd_provinsi"
                            >
                                {{ prov.nama_provinsi }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="mb-2 row align-items-center">
                    <label class="col-sm-2 col-form-label"
                        >Cari Kota / Kabupaten:</label
                    >
                    <div class="col-sm-4">
                        <select
                            id="filter_kota_kabupaten"
                            class="form-select"
                            v-model="selectedkotaKabupaten"
                            :disabled="!selectedProvinsi"
                        >
                            <option value="">-- Semua Provinsi --</option>
                            <option
                                v-for="prov in filteredKotaKabupaten"
                                :key="prov.kd_kota_kabupaten"
                                :value="prov.kd_kota_kabupaten"
                            >
                                {{ prov.nama_kota_kabupaten }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="mb-3 row align-items-center">
                    <div class="col-sm-6 d-flex gap-2">
                        <button
                            class="btn btn-success"
                            @click="handleCari"
                            style="white-space: nowrap"
                        >
                            <i class="fa-solid fa-magnifying-glass me-1"></i>
                            Cari
                        </button>
                        <button
                            class="btn btn-danger"
                            @click="resetFilter"
                            style="white-space: nowrap"
                        >
                            <i class="fa fa-xmark me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>Kecamatan</div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-start-end mb-3">
                    <button
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#modalTambahKecamatan"
                    >
                        <i class="fas fa-plus me-1"></i> Tambah Kecamatan
                    </button>
                </div>

                <table
                    id="tabelKecamatan"
                    class="display nowrap"
                    style="width: 100%"
                >
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Provinsi</th>
                            <th>Nama Kota/Kabupaten</th>
                            <th>Nama Kecamatan</th>
                            <th>Status tampil</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div
                class="modal fade"
                id="modalTambahKecamatan"
                tabindex="-1"
                aria-labelledby="modalTambahKecamatanLabel"
                aria-hidden="true"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
            >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5
                                class="modal-title"
                                id="modalTambahKecamatanLabel"
                            >
                                Tambah Kecamatan
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
                                <label for="select_pro_kota" class="form-label">
                                    Pilih Provinsi dan Kota
                                </label>
                                <select
                                    class="form-control"
                                    name="select_pro_kota"
                                    id="select_pro_kota"
                                    v-model="selectedValue"
                                    @change="handleChangeOptionAddKecamatan"
                                >
                                    <option value="">
                                        -- PILIH PROVINSI / KOTA --
                                    </option>
                                    <option
                                        v-for="item in optionsProvKota"
                                        :key="item.kd_kecamatan"
                                        :value="`${item.kota_kabupaten.kd_provinsi}|${item.kd_kota_kabupaten}`"
                                    >
                                        {{
                                            item.kota_kabupaten.provinsi
                                                .nama_provinsi
                                        }}
                                        -
                                        {{
                                            item.kota_kabupaten
                                                .nama_kota_kabupaten
                                        }}
                                    </option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label
                                        for="select_provinsi"
                                        class="form-label"
                                        >Provinsi</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="select_provinsi"
                                        id="select_provinsi"
                                        :value="selectedProvinsi"
                                        @change="
                                            handleChangeOptionEditKecamatan
                                        "
                                        disabled
                                    />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label
                                        for="select_kota_kabupaten"
                                        class="form-label"
                                        >Kota/Kabupaten</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="select_kota_kabupaten"
                                        id="select_kota_kabupaten"
                                        :value="selectedKota"
                                        disabled
                                    />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Kecamatan</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="inputData.nama_kecamatan"
                                    placeholder="Masukkan kecamatan"
                                    @input="
                                        inputData.nama_kecamatan =
                                            inputData.nama_kecamatan.toUpperCase()
                                    "
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
                                @click="btnSimpanKotaKecamatan"
                            >
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="modal fade"
                id="modalEditKecamatan"
                tabindex="-1"
                aria-hidden="true"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
            >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Kecamatan</h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Tutup"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label
                                    for="select_edit_pro_kota"
                                    class="form-label"
                                >
                                    Pilih Provinsi dan Kota
                                </label>
                                <select
                                    class="form-control"
                                    name="select_edit_pro_kota"
                                    id="select_edit_pro_kota"
                                    v-model="selectedEditData"
                                    @change="handleChangeOptionEditKecamatan"
                                >
                                    <option value="">
                                        -- PILIH PROVINSI / KOTA --
                                    </option>
                                    <option
                                        v-for="item in optionEditProvKota"
                                        :key="item.kd_kecamatan"
                                        :value="`${item.kota_kabupaten.kd_provinsi}|${item.kd_kota_kabupaten}`"
                                    >
                                        {{
                                            item.kota_kabupaten.provinsi
                                                .nama_provinsi
                                        }}
                                        -
                                        {{
                                            item.kota_kabupaten
                                                .nama_kota_kabupaten
                                        }}
                                    </option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label
                                        for="select_edit_provinsi"
                                        class="form-label"
                                        >Provinsi</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="select_edit_provinsi"
                                        id="select_edit_provinsi"
                                        :value="selectedEditProvinsi"
                                        disabled
                                    />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label
                                        for="select_edit_kota_kabupaten"
                                        class="form-label"
                                        >Kota/Kabupaten</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="select_edit_kota_kabupaten"
                                        id="select_edit_kota_kabupaten"
                                        :value="selectedEditKota"
                                        disabled
                                    />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Kecamatan</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="editData.nama_kecamatan"
                                    placeholder="Masukkan Kecamatan"
                                    @input="
                                        editData.nama_kecamatan =
                                            editData.nama_kecamatan.toUpperCase()
                                    "
                                />
                            </div>

                            <div class="mb-3">
                                <label for="status_tampil" class="form-label"
                                    >Status Tampil</label
                                >
                                <select
                                    id="status_tampil"
                                    v-model="editData.status_tampil"
                                    class="form-control"
                                >
                                    <option value="ACTIVE">ACTIVE</option>
                                    <option value="NON ACTIVE">
                                        NON ACTIVE
                                    </option>
                                </select>
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
                                @click="btnSimpanEditKecamatan"
                            >
                                Ubah Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        v-if="loading"
        class="fixed inset-0 flex flex-col items-center justify-center bg-white/60 backdrop-blur-sm z-50 animate-fade-in"
    >
        <div class="relative">
            <div
                class="h-16 w-16 border-4 border-blue-300 rounded-full animate-spin border-t-transparent"
            ></div>
            <div
                class="absolute inset-2 bg-blue-500 rounded-full animate-pulse opacity-80"
            ></div>
        </div>
        <p class="mt-4 text-blue-700 font-semibold animate-bounce">
            Memuat data...
        </p>
    </div>
</template>

<script>
import * as yup from "yup";
import { validate } from "vee-validate";
import { h } from "vue";
import { Modal } from "bootstrap";
import axios from "axios";

export default {
    data() {
        return {
            dataKecamatan: [],
            dataTableInstance: null,
            loading: true,
            filterdDataKecamatan: [],
            dataProvinsi: [],
            selectedProvinsi: "",
            dataKotaKabupaten: [],
            selectedkotaKabupaten: "",
            inputData: {
                nama_kecamatan: "",
                kd_provinsi: "",
                kd_kota_kabupaten: "",
                status_tampil: "",
            },

            optionsProvKota: [],
            selectedValue: "",
            selectedInputProvinsi: "",
            selectedInputKota: "",

            editData: {
                kd_kecamatan: "",
                nama_kecamatan: "",
                kd_provinsi: "",
                kd_kota_kabupaten: "",
            },
            optionEditProvKota: [],
            selectedEditData: "",
            selectedEditProvinsi: "",
            selectedEditKota: "",

            modalInstance: null,
        };
    },
    computed: {
        filteredKotaKabupaten() {
            if (!this.selectedProvinsi) return [];
            return this.dataKotaKabupaten.filter(
                (kota) => kota.kd_provinsi === this.selectedProvinsi
            );
        },
    },
    async mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

        await this.kecamatan();
        await this.provinsi();
        await this.kotaKabupaten();

        this.filteredDataKecamatan = this.dataKecamatan;

        this.$nextTick(() => {
            const modalEl = $("#modalEditKecamatan")[0];
            this.modalInstance = new Modal(modalEl);

            this.refreshTable();

            defaultSelect2("#filter_provinsi", "-- PILIH PROVINSI --", null);
            defaultSelect2(
                "#filter_kota_kabupaten",
                "-- PILIH KOTA/KABUPATEN --",
                null
            );

            defaultSelect2(
                "#select_pro_kota",
                "-- PILIH PROVINSI / KOTA --",
                "#modalTambahKecamatan"
            );

            $("#modalTambahKecamatan").on("hide.bs.modal", () => {
                this.resetFormTambah();
            });

            $("#filter_provinsi").on("change", (e) => {
                this.selectedProvinsi = e.target.value;
                this.selectedkotaKabupaten = "";
            });
            $("#filter_kota_kabupaten").on("change", (e) => {
                this.selectedkotaKabupaten = e.target.value;
            });

            $("#select_pro_kota").on("change", (e) => {
                this.selectedValue = $(e.target).val();
                this.handleChangeOptionAddKecamatan();
            });

            $("#tabelKecamatan").on("click", ".btn-edit", (e) => {
                const rowData = this.dataTableInstance
                    .row($(e.target).closest("tr"))
                    .data();
                this.openEditModal(rowData);
            });

            defaultSelect2(
                "#select_edit_pro_kota",
                "-- PILIH PROVINSI / KOTA --",
                "#modalEditKecamatan"
            );

            defaultSelect2(
                "#status_tampil",
                "-- PILIH  --",
                "#modalEditKecamatan"
            );

            $("#select_edit_pro_kota").on("change", (e) => {
                this.selectedEditData = $(e.target).val();
                this.handleChangeOptionEditKecamatan(this.selectedEditData);
            });

            $("#status_tampil").on("change", (e) => {
                this.editData.status_tampil = e.target.value;
            });
        });

        this.loading = false;
    },
    beforeUnmount() {
        if (this.dataTableKecamatan) {
            this.dataTableKecamatan.destroy();
        }
    },
    methods: {
        async kecamatan() {
            try {
                const data = await getAllDataKecamatan();
                this.dataKecamatan = data || [];
                this.filterdDataKecamatan = data || [];

                this.optionsProvKota = (data || [])
                    .filter(
                        (item) =>
                            item.kota_kabupaten && item.kota_kabupaten.provinsi
                    )
                    .reduce(
                        (acc, item) => {
                            const key = `${item.kota_kabupaten.kd_provinsi}|${item.kd_kota_kabupaten}`;
                            if (!acc.map.has(key)) {
                                acc.map.set(key, true);
                                acc.list.push(item);
                            }
                            return acc;
                        },
                        { map: new Map(), list: [] }
                    ).list;

                this.optionEditProvKota = (data || [])
                    .filter(
                        (item) =>
                            item.kota_kabupaten && item.kota_kabupaten.provinsi
                    )
                    .reduce(
                        (acc, item) => {
                            const key = `${item.kota_kabupaten.kd_provinsi}|${item.kd_kota_kabupaten}`;
                            if (!acc.map.has(key)) {
                                acc.map.set(key, true);
                                acc.list.push(item);
                            }
                            return acc;
                        },
                        { map: new Map(), list: [] }
                    ).list;
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan dataKecamatan: ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        async provinsi() {
            try {
                const data = await getAllDataProvinsi();
                this.dataProvinsi = data || [];
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan provinsi: ${
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
        async kotaKabupaten() {
            try {
                const data = await getAllDataKotaKabupaten();
                this.dataKotaKabupaten = data || [];
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan kotaKabupaten: ${
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
        refreshTable() {
            if (this.dataTableInstance) {
                this.dataTableInstance.clear().destroy();
                this.dataTableInstance = null;
            }

            this.dataTableInstance = $("#tabelKecamatan").DataTable({
                data: this.filteredDataKecamatan,
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
                    { data: "kota_kabupaten.provinsi.nama_provinsi" },
                    { data: "kota_kabupaten.nama_kota_kabupaten" },
                    { data: "nama_kecamatan" },
                    {
                        data: "status_tampil",
                        render: (data) => {
                            const badge =
                                data === "ACTIVE" ? "bg-success" : "bg-danger";
                            return `<span class="badge ${badge}">${data}</span>`;
                        },
                    },
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
                    $("#tabelKecamatan tbody").on(
                        "mouseenter",
                        "tr",
                        function () {
                            $(this).css("background-color", "Yellow");
                        }
                    );
                    $("#tabelKecamatan tbody").on(
                        "mouseleave",
                        "tr",
                        function () {
                            $(this).css("background-color", "");
                        }
                    );
                },
            });
        },
        handleCari() {
            let filtered = this.dataKecamatan;

            if (this.selectedProvinsi && !this.selectedkotaKabupaten) {
                filtered = filtered.filter(
                    (item) =>
                        item?.kota_kabupaten?.kd_provinsi ===
                        this.selectedProvinsi
                );
            } else if (this.selectedProvinsi && this.selectedkotaKabupaten) {
                filtered = filtered.filter(
                    (item) =>
                        item?.kota_kabupaten?.kd_provinsi ===
                            this.selectedProvinsi &&
                        item.kd_kota_kabupaten === this.selectedkotaKabupaten
                );
            }

            this.filteredDataKecamatan = filtered;

            if (this.dataTableInstance) {
                this.dataTableInstance.clear();
                this.dataTableInstance.rows.add(filtered);
                this.dataTableInstance.draw();
            } else {
                this.refreshTable();
            }
        },
        resetFilter() {
            this.selectedProvinsi = "";
            this.selectedkotaKabupaten = "";
            $("#filter_provinsi").val("").trigger("change");
            $("#filter_kota_kabupaten").val("").trigger("change");

            this.filteredDataKecamatan = this.dataKecamatan;

            if (this.dataTableInstance) {
                this.dataTableInstance.clear();
                this.dataTableInstance.rows.add(this.filteredDataKecamatan);
                this.dataTableInstance.draw();
            } else {
                this.refreshTable();
            }
        },
        handleChangeOptionAddKecamatan() {
            if (this.selectedValue) {
                const [kdProvinsi, kdKotaKabupaten] =
                    this.selectedValue.split("|");

                const selectedItem = this.optionsProvKota.find(
                    (item) =>
                        (item.kota_kabupaten.kd_provinsi + "").trim() ===
                            kdProvinsi.trim() &&
                        (item.kd_kota_kabupaten + "").trim() ===
                            kdKotaKabupaten.trim()
                );

                if (selectedItem) {
                    this.selectedInputProvinsi =
                        selectedItem.kota_kabupaten.provinsi.nama_provinsi;
                    this.selectedInputKota =
                        selectedItem.kota_kabupaten.nama_kota_kabupaten;

                    this.inputData.kd_provinsi =
                        selectedItem.kota_kabupaten.kd_provinsi;
                    this.inputData.kd_kota_kabupaten =
                        selectedItem.kd_kota_kabupaten;
                } else {
                    this.selectedInputProvinsi = "";
                    this.selectedInputKota = "";
                    this.inputData.kd_provinsi = "";
                    this.inputData.kd_kota_kabupaten = "";
                }
            } else {
                this.selectedInputProvinsi = "";
                this.selectedInputKota = "";
                this.inputData.kd_provinsi = "";
                this.inputData.kd_kota_kabupaten = "";
            }
        },
        resetFormTambah() {
            this.inputData.nama_kecamatan = "";
            this.inputData.kd_provinsi = "";
            this.inputData.kd_kota_kabupaten = "";
            $("#select_pro_kota").val("").trigger("change");
        },
        openEditModal(rowData) {
            this.$nextTick(() => {
                this.selectedEditData = `${rowData.kota_kabupaten.kd_provinsi}|${rowData.kd_kota_kabupaten}`;

                $("#select_edit_pro_kota")
                    .val(this.selectedEditData)
                    .trigger("change");
                $("#status_tampil")
                    .val(rowData.status_tampil)
                    .trigger("change");

                if (rowData) {
                    this.selectedEditProvinsi =
                        rowData.kota_kabupaten.provinsi.nama_provinsi;
                    this.selectedEditKota =
                        rowData.kota_kabupaten.nama_kota_kabupaten;
                    this.editData.nama_kecamatan = rowData.nama_kecamatan;
                    this.editData.kd_kecamatan = rowData.kd_kecamatan;
                    this.editData.kd_provinsi =
                        rowData.kota_kabupaten.kd_provinsi;
                    this.editData.kd_kota_kabupaten = rowData.kd_kota_kabupaten;
                } else {
                    this.selectedEditProvinsi = "";
                    this.selectedEditKota = "";
                    this.editData = {
                        kd_provinsi: "",
                        kd_kota_kabupaten: "",
                        kd_kecamatan: "",
                        status_tampil: "",
                        nama_kecamatan: "",
                    };
                }

                this.modalInstance.show();
            });
        },
        handleChangeOptionEditKecamatan(data) {
            if (!data) {
                this.selectedEditProvinsi = "";
                this.selectedEditKota = "";
                this.editData.kd_kecamatan = "";
                this.editData.kd_provinsi = "";
                this.editData.kd_kota_kabupaten = "";
                this.editData.status_tampil = "";
                return;
            }

            const [kd_provinsi, kd_kota] = data.split("|");

            const selectedItem = this.optionEditProvKota.find(
                (it) =>
                    it.kota_kabupaten.kd_provinsi === kd_provinsi &&
                    it.kd_kota_kabupaten === kd_kota
            );

            if (selectedItem) {
                this.selectedEditProvinsi =
                    selectedItem.kota_kabupaten.provinsi.nama_provinsi;
                this.selectedEditKota =
                    selectedItem.kota_kabupaten.nama_kota_kabupaten;

                this.editData.kd_provinsi = kd_provinsi;
                this.editData.kd_kota_kabupaten = kd_kota;
            }
        },
        btnSimpanKotaKecamatan() {
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
                    this.simpanKecamatan();
                }
            });
        },
        async simpanKecamatan() {
            let dataToSave = {
                ...this.inputData,
                user_input: window.encryptedUserId,
            };

            let requireValue = [];

            requireValue.push({
                value: dataToSave.nama_kecamatan,
                message: "Kecamatan Tidak Boleh Kosong",
            });

            const schema = yup.object({
                nama_kecamatan: yup
                    .string()
                    .required("Nama Kecamatan wajib diisi")
                    .matches(
                        /^[a-zA-Z0-9\s]+$/,
                        "Nama Kecamatan Hanya Boleh huruf & angka yang diperbolehkan"
                    ),
                kd_provinsi: yup.string().required("Provinsi harus dipilih"),
                kd_kota_kabupaten: yup
                    .string()
                    .required("Kota/Kabupaten harus dipilih"),
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
                    "/wilayah/simpan-kecamatan",
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
        btnSimpanEditKecamatan() {
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
                    this.ubahKecamatan();
                }
            });
        },
        async ubahKecamatan() {
            let dataToSave = {
                ...this.editData,
                user_input: window.encryptedUserId,
            };

            let requireValue = [];

            requireValue.push({
                value: dataToSave.nama_kecamatan,
                message: "Kecamatan Tidak Boleh Kosong",
            });

            if (!validasiBanyakInputan(requireValue)) return;

            const schema = yup.object({
                nama_kecamatan: yup
                    .string()
                    .required("Nama Kecamatan wajib diisi")
                    .matches(
                        /^[a-zA-Z0-9\s]+$/,
                        "Nama Kecamatan Hanya Boleh huruf & angka yang diperbolehkan"
                    ),
                kd_provinsi: yup.string().required("Provinsi harus dipilih"),
                kd_kota_kabupaten: yup
                    .string()
                    .required("Kota/Kabupaten harus dipilih"),
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
                    "/wilayah/ubah-kecamatan",
                    dataToSave
                );
                const result = response.data;

                Swal.close();

                if (result.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: result.message || "Data berhasil Diubah!",
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

<style>
@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-in-out;
}
</style>
