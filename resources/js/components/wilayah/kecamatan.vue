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
                        </tr>
                    </thead>
                </table>
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
        await this.kecamatan();
        await this.provinsi();
        await this.kotaKabupaten();

        this.filteredDataKecamatan = this.dataKecamatan;

        this.$nextTick(() => {
            this.refreshTable();

            defaultSelect2("#filter_provinsi", "-- PILIH PROVINSI --", null);
            defaultSelect2(
                "#filter_kota_kabupaten",
                "-- PILIH KOTA/KABUPATEN --",
                null
            );

            $("#filter_provinsi").on("change", (e) => {
                this.selectedProvinsi = e.target.value;
                this.selectedkotaKabupaten = "";
            });
            $("#filter_kota_kabupaten").on("change", (e) => {
                this.selectedkotaKabupaten = e.target.value;
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
