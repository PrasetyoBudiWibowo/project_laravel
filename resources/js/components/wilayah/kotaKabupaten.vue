<template>
    <div class="p-4">
        <div class="card">
            <div class="card-header">
                <div>Kota Kabupaten</div>
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
                        >Cari Nama Kota/Kabupaten:</label
                    >
                    <div class="col-sm-4">
                        <input
                            type="text"
                            class="form-control"
                            v-model="searchNamaKota"
                            placeholder="Masukkan nama Kota/Kabupaten"
                        />
                    </div>
                </div>

                <div class="mb-2 row align-items-center">
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

                        <button
                            class="btn btn-primary ms-2"
                            @click="exportToExcel"
                            style="white-space: nowrap"
                        >
                            <i class="fa fa-file-excel me-1"></i> Export Excel
                        </button>

                        <button
                            class="btn btn-secondary ms-2"
                            @click="printFilteredKotaKabupaten"
                        >
                            <i class="fa fa-print me-1"></i> Print
                        </button>

                        <button
                            class="btn btn-primary"
                            @click="printViaMpdf"
                            style="white-space: nowrap"
                        >
                            <i class="fa-solid fa-file-pdf me-1"></i>
                            Print PDF (mPDF)
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-start-end mb-3">
                    <button
                        id="btnOpenModalTambahKotaKabupaten"
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#modalTambahKotaKabupaten"
                    >
                        <i class="fas fa-plus me-1"></i> Tambah Kota Kabupaten
                    </button>
                </div>

                <a-table
                    id="tbl_kota_kabupaten"
                    :columns="columns"
                    :rowKey="'kd_kota_kabupaten'"
                    :dataSource="filteredKotaKabupaten"
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

            <div
                class="modal fade"
                id="modalTambahKotaKabupaten"
                tabindex="-1"
                aria-labelledby="modalTambahKotaKabupatenLabel"
                aria-hidden="true"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
            >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5
                                class="modal-title"
                                id="modalTambahKotaKabupatenLabel"
                            >
                                Tambah Kota Kabupaten
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
                                <label
                                    for="input_kd_provinsi"
                                    class="form-label"
                                    >Level User</label
                                >
                                <select
                                    id="input_kd_provinsi"
                                    class="form-select"
                                    v-model="inputData.kd_provinsi"
                                >
                                    <option value="">
                                        -- Semua Provinsi --
                                    </option>
                                    <option
                                        v-for="prov in dataProvinsi"
                                        :key="prov.kd_provinsi"
                                        :value="prov.kd_provinsi"
                                    >
                                        {{ prov.nama_provinsi }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"
                                    >Nama Kota Kabupten</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="inputData.nama_kota_kabupaten"
                                    placeholder="Masukkan Kota Kabupaten"
                                    @input="
                                        inputData.nama_kota_kabupaten =
                                            inputData.nama_kota_kabupaten.toUpperCase()
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
                                @click="btnSimpanKotaKabupaten"
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
import { h } from "vue";
import { Modal } from "bootstrap";
import axios from "axios";
import * as XLSX from "xlsx";
import { saveAs } from "file-saver";
import printJS from "print-js";

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
                    dataIndex: ["provinsi", "nama_provinsi"],
                    key: "nama_provinsi",
                },
                {
                    title: "Kota/Kabupaten",
                    dataIndex: "nama_kota_kabupaten",
                    key: "nama_kota_kabupaten",
                },
            ],
            dataProvinsi: [],
            dataKotaKabupaten: [],
            columns: [],
            loggedInUser: null,
            selectedProvinsi: "",
            filteredKotaKabupaten: [],
            searchNamaKota: "",
            inputData: {
                kd_provinsi: "",
                nama_kota_kabupaten: "",
            },
        };
    },
    mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;
        this.columns = this.rawColumns;
        this.provinsi();
        this.kotaKabupaten();
        this.checkSessionLogin();

        $("#modalTambahKotaKabupaten").on("hide.bs.modal", () => {
            this.resetFormTambah();
        });

        this.$nextTick(() => {
            defaultSelect2("#filter_provinsi", "-- PILIH PROVINSI --", null);
            defaultSelect2(
                "#input_kd_provinsi",
                "-- PILIH PROVINSI --",
                "#modalTambahKotaKabupaten"
            );

            $("#input_kd_provinsi").on("change", (e) => {
                this.inputData.kd_provinsi = e.target.value;
            });
        });

        this.filteredKotaKabupaten = this.dataKotaKabupaten;
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
                this.dataProvinsi = data || [];
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
        async kotaKabupaten() {
            try {
                const data = await getAllDataKotaKabupaten();
                const result = (data || []).map((it, ix) => ({
                    ...it,
                    _no: ix + 1,
                }));
                this.dataKotaKabupaten = result;
                this.filteredKotaKabupaten = result;
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
        handleCari() {
            this.selectedProvinsi = $("#filter_provinsi").val();
            this.filterKotaKabupaten();
        },
        filterKotaKabupaten() {
            let filtered = this.dataKotaKabupaten;

            if (this.selectedProvinsi) {
                filtered = filtered.filter(
                    (item) =>
                        item.provinsi.kd_provinsi === this.selectedProvinsi
                );
            }

            if (this.searchNamaKota && this.searchNamaKota.trim() !== "") {
                const keyword = this.searchNamaKota.toLowerCase().trim();
                filtered = filtered.filter((item) =>
                    item.nama_kota_kabupaten.toLowerCase().includes(keyword)
                );
            }

            this.filteredKotaKabupaten = filtered;
        },
        resetFilter() {
            this.selectedProvinsi = "";
            $("#filter_provinsi").val("").trigger("change");
            this.filteredKotaKabupaten = this.dataKotaKabupaten;
            this.searchNamaKota = "";
        },
        exportToExcel() {
            const dataExport = this.filteredKotaKabupaten.map(
                ({ _no, provinsi, nama_kota_kabupaten }) => ({
                    No: _no,
                    Provinsi: provinsi.nama_provinsi,
                    "Kota/Kabupaten": nama_kota_kabupaten,
                })
            );

            // Buat worksheet kosong
            const worksheet = XLSX.utils.aoa_to_sheet([]);

            // Tulis judul di sel A1 (bisa diubah sesuai kebutuhan)
            const title = "Data Kota Kabupaten";
            XLSX.utils.sheet_add_aoa(worksheet, [[title]], { origin: "A1" });

            // Merge sel A1 sampai C1
            worksheet["!merges"] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: 2 } }, // s=start (row 0 col 0), e=end (row 0 col 2)
            ];

            // Tulis baris kosong (misal di baris 2)
            // XLSX.utils.sheet_add_aoa(worksheet, [[""]], { origin: "A2" }); // opsional

            // Tulis data mulai dari baris ke-3
            XLSX.utils.sheet_add_json(worksheet, dataExport, {
                origin: "A3",
                skipHeader: false,
            });

            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(
                workbook,
                worksheet,
                "Data Kota Kabupaten"
            );

            const wbout = XLSX.write(workbook, {
                bookType: "xlsx",
                type: "array",
            });

            const blob = new Blob([wbout], {
                type: "application/octet-stream",
            });

            saveAs(blob, "data_kota_kabupaten.xlsx");
        },
        printFilteredKotaKabupaten() {
            // Buat HTML untuk print
            const headerHTML = `
                <div style="text-align: center; margin-bottom: 20px;">
                <h2>Data Kota Kabupaten</h2>
                <p>Dicetak pada: ${new Date().toLocaleString()}</p>
                <hr/>
                </div>`;

            // Buat tabel dari data yang sudah difilter
            const rowsHTML = this.filteredKotaKabupaten
                .map(
                    (item) => `
                    <tr>
                    <td style="border:1px solid #000; padding:4px;">${item._no}</td>
                    <td style="border:1px solid #000; padding:4px;">${item.provinsi.nama_provinsi}</td>
                    <td style="border:1px solid #000; padding:4px;">${item.nama_kota_kabupaten}</td>
                    </tr>`
                )
                .join("");

            const tableHTML = `
                <table style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">
                <thead>
                    <tr>
                    <th style="border:1px solid #000; padding:6px; background:#eee;">No</th>
                    <th style="border:1px solid #000; padding:6px; background:#eee;">Provinsi</th>
                    <th style="border:1px solid #000; padding:6px; background:#eee;">Kota/Kabupaten</th>
                    </tr>
                </thead>
                <tbody>
                    ${rowsHTML}
                </tbody>
                </table>`;

            // Footer sederhana (opsional)
            const footerHTML = `
            <hr/>
            <div style="text-align: center; margin-top: 20px; font-size: 12px;">
            <em>Halaman dicetak menggunakan print-js</em>
            </div>`;

            // Gabungkan semua HTML
            const printContent = headerHTML + tableHTML + footerHTML;

            printJS({
                printable: printContent,
                type: "raw-html",
                style: `
                    @media print {
                        @page {
                        margin: 20mm;
                        }
                    }
                `,
                scanStyles: false,
            });
        },
        printViaMpdf() {
            const params = new URLSearchParams({
                kd_provinsi: this.selectedProvinsi || "",
                nama_kota_kabupaten: this.namaKotaFilter || "",
            }).toString();

            const url = `/wilayah/print-kota-kabupaten?${params}`;

            window.open(url, "_blank");
        },
        resetFormTambah() {
            this.inputData.kd_provinsi = "";
            this.inputData.nama_kota_kabupaten = "";
            $("#input_kd_provinsi").val("").trigger("change");
        },
        btnSimpanKotaKabupaten() {
            const btnOpenModal = document.getElementById(
                "btnOpenModalTambahKotaKabupaten"
            );
            if (btnOpenModal) {
                btnOpenModal.focus();
            } else {
                document.body.focus();
            }

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
                    this.simpanKotaKabupaten();
                }
            });
        },
        async simpanKotaKabupaten() {
            let dataToSave = {
                ...this.inputData,
                user_input: this.loggedInUser.kd_asli_user,
                user_login: this.loggedInUser.nama_user,
            };

            let requireValue = [];

            requireValue.push({
                value: dataToSave.kd_provinsi,
                message: "Provinsi Tidak Boleh Kosong",
            });

            requireValue.push({
                value: dataToSave.nama_kota_kabupaten,
                message: "Nama kota/kabupaten Tidak Boleh Kosong",
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
                    "/wilayah/simpan-kabupaten-kota",
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
<style>
#tbl_kota_kabupaten .ant-table-tbody > tr:hover > td {
    background-color: #fbff00de !important;
}
</style>
