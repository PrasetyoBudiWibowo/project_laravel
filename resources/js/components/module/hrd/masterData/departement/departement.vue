<template>
    <div class="p-4">
        <div class="card mb-3">
            <div class="card-header">
                <div>MASTER DEPARTEMENT</div>
            </div>

            <div class="card-body">
                <div class="d-flex justify-content mb-3 gap-2">
                    <button
                        v-if="
                            dataUser?.level_user === 'SUPER ADMIN' ||
                            hakAkses?.bisa_insert === 'YA'
                        "
                        class="btn btn-primary"
                        @click="openModal"
                    >
                        <i class="fas fa-plus me-1"></i> Tambah
                    </button>
                    <button class="btn btn-success" @click="exportExcel">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                </div>

                <div class="mb-2 row align-items-center">
                    <label class="col-sm-2 col-form-label"
                        >Cari Provinsi:</label
                    >
                    <div class="col-sm-4">
                        <select
                            id="filter_divisi"
                            class="form-select"
                            v-model="selectedDivisi"
                        >
                            <option value="">Semua Divisi</option>
                            <option
                                v-for="div in dataDivisi"
                                :key="div.kd_divisi"
                                :value="div.nama_divisi"
                                :title="div.nama_divisi"
                            >
                                {{ div.nama_divisi }}
                            </option>
                        </select>
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
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table
                                id="tableDepartement"
                                class="display nowrap table-bordered"
                                style="width: 100%"
                            >
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Divisi</th>
                                        <th>Department</th>
                                        <th
                                            v-show="
                                                dataUser?.level_user ===
                                                    'SUPER ADMIN' ||
                                                hakAkses?.bisa_edit === 'YA'
                                            "
                                        >
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            item, index
                                        ) in filteredDataDepartement"
                                        :key="item.kd_departement"
                                    >
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.divisi.nama_divisi }}</td>
                                        <td>{{ item.nama_departement }}</td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-warning me-2"
                                                @click="openEdit(item)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                class="btn btn-sm btn-danger"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- <modalTambahDepartement
                    ref="modalTambahDepartement"
                    :dataDivisi="dataDivisi"
                /> -->

                <modalTambahDepartement ref="modalTambahDepartement" />
                <modalEditDepartement ref="modalEditDepartement" />
            </div>
        </div>
    </div>

    <loadingData :visible="loadingMenu" message="Loading" />
</template>

<script>
import modalTambahDepartement from "./modal/modalTambahDepartement.vue";
import modalEditDepartement from "./modal/modalEditDepartement.vue";
import loadingData from "../../../../loading/loadingData.vue";

export default {
    components: { modalTambahDepartement, loadingData, modalEditDepartement },
    data() {
        return {
            dataUser: window.userData,
            hakAkses: null,
            dataTableInstance: null,
            dataDivisi: [],
            filteredDataDepartement: [],
            selectedDivisi: "",
            dataDepartement: [],

            loadingMenu: true,
        };
    },
    async mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

        if (this.dataUser.level_user !== "SUPER ADMIN") {
            await this.cekStatusAkses();
        }

        await this.divisi();
        await this.departement();

        this.filteredDataDepartement = this.dataDepartement;
        this.refreshTable();

        this.$nextTick(() => {
            // this.refreshTable();

            defaultSelect2("#filter_divisi", "-- PILIH DIVISI --", null);

            $("#filter_divisi").on("change", (e) => {
                this.selectedProvinsi = e.target.value;
            });
        });

        this.loadingMenu = false;
    },
    methods: {
        openModal() {
            this.$refs.modalTambahDepartement.openModal();
        },
        openEdit(item) {
            this.$refs.modalEditDepartement.openModal(item);
        },
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

                this.hakAkses = allowed[0];
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
        async departement() {
            try {
                const data = await getAllDepartement();
                this.dataDepartement = data || [];
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan this.departement(): ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        async divisi() {
            try {
                const data = await getAllDivisi();
                this.dataDivisi = data || [];
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan this.departement(): ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        handleCari() {
            this.filterDepartement();
        },
        resetFilter() {
            this.selectedDivisi = "";
            this.filterDepartement();
        },
        filterDepartement() {
            let filtered = this.dataDepartement;

            if (this.selectedDivisi) {
                filtered = filtered.filter(
                    (it) => it.divisi.nama_divisi === this.selectedDivisi
                );
            }
        },
        refreshTable() {
            if (this.dataTableInstance) {
                this.dataTableInstance.clear().destroy();
                this.dataTableInstance = null;
            }

            this.$nextTick(() => {
                this.dataTableInstance = $("#tableDepartement").DataTable({
                    // scrollCollapse: true,
                    // scrollY: 300,
                    // fixedHeader: true,
                    initComplete: function () {
                        $("#tableDepartement tbody").on(
                            "mouseenter",
                            "tr",
                            function () {
                                $(this).css("background-color", "Yellow");
                            }
                        );
                        $("#tableDepartement tbody").on(
                            "mouseleave",
                            "tr",
                            function () {
                                $(this).css("background-color", "");
                            }
                        );
                    },
                    columnDefs: [
                        { targets: 0, orderable: false }, // nomor urut
                    ],
                });
            });
        },
        exportExcel() {
            axios({
                url: `/hrd/export-excel-departement`,
                method: "GET",
                responseType: "blob",
            }).then((response) => {
                const fileURL = window.URL.createObjectURL(
                    new Blob([response.data])
                );
                const fileLink = document.createElement("a");
                fileLink.href = fileURL;
                fileLink.setAttribute("download", "departement.xlsx");
                document.body.appendChild(fileLink);
                fileLink.click();
            });
        },
    },
};
</script>
