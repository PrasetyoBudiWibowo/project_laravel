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
                    <label class="col-sm-2 col-form-label">Divisi:</label>
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
            dataTableInstance: null,
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

        window.openEditDepartement = (data) => {
            this.openEdit(data);
        };

        await this.divisi();
        await this.departement();

        this.$nextTick(() => {
            this.refreshTable();

            defaultSelect2("#filter_divisi", "-- PILIH DIVISI --", null);

            $("#filter_divisi").on("change", (e) => {
                this.selectedDivisi = e.target.value;
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
                this.filteredDataDepartement = data || [];
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
            this.refreshTable();
        },
        resetFilter() {
            this.selectedDivisi = "";
            $("#filter_divisi").val("").trigger("change");

            this.filteredDataDepartement = this.dataDepartement;

            if (this.dataTableInstance) {
                this.dataTableInstance.clear();
                this.dataTableInstance.rows.add(this.filteredDataDepartement);
                this.dataTableInstance.draw();
            } else {
                this.refreshTable();
            }
        },
        filterDepartement() {
            let filtered = this.dataDepartement;

            if (this.selectedDivisi) {
                filtered = filtered.filter(
                    (it) => it.divisi.nama_divisi === this.selectedDivisi
                );
            }

            this.filteredDataDepartement = filtered;

            if (this.dataTableInstance) {
                this.dataTableInstance.clear();
                this.dataTableInstance.rows.add(filtered);
                this.dataTableInstance.draw();
            } else {
                this.refreshTable();
            }
        },
        refreshTable() {
            if (this.dataTableInstance) {
                this.dataTableInstance.clear().destroy();
                this.dataTableInstance = null;
            }

            this.dataTableInstance = $("#tableDepartement").DataTable({
                data: this.filteredDataDepartement,
                columns: [
                    {
                        data: null,
                        width: "5%",
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        },
                    },
                    { data: "divisi.nama_divisi" },
                    { data: "nama_departement" },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return `
                                <button class="btn btn-sm btn-warning me-2 btn-edit" 
                                        onclick='openEditDepartement(${JSON.stringify(
                                            data
                                        )})'">
                                    Edit
                                </button>
                                <button
                                    class="btn btn-sm btn-danger"
                                >
                                    Hapus
                                </button>`;
                        },
                    },
                ],
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
            });
        },
        exportExcel() {
            let url = `/hrd/export-excel-departement`;

            if (this.selectedDivisi) {
                url += `?divisi=${encodeURIComponent(this.selectedDivisi)}`;
            }

            axios({
                url: url,
                method: "GET",
                responseType: "blob",
            }).then((response) => {
                const fileURL = window.URL.createObjectURL(
                    new Blob([response.data])
                );
                const fileLink = document.createElement("a");
                fileLink.href = fileURL;
                if (this.selectedDivisi) {
                    fileLink.setAttribute(
                        "download",
                        `DEPARTEMENT-${this.selectedDivisi}.xlsx`
                    );
                } else {
                    fileLink.setAttribute("download", "departement.xlsx");
                }
                document.body.appendChild(fileLink);
                fileLink.click();
            });
        },
    },
};
</script>
