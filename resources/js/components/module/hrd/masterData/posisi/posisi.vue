<template>
    <div class="p-4">
        <div class="card mb-3">
            <div class="card-header">
                <div>MASTER POSISI</div>
            </div>

            <div class="card-body">
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
                                v-for="div in filterDataDivisi"
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
                    <label class="col-sm-2 col-form-label">Departement:</label>
                    <div class="col-sm-4">
                        <select
                            id="filter_departement"
                            class="form-select"
                            v-model="selectedDepartement"
                            :disabled="!selectedDivisi"
                        >
                            <option value="">Semua Divisi</option>
                            <option
                                v-for="div in filteredDepartement"
                                :key="div.kd_departement"
                                :value="div.nama_departement"
                                :title="div.nama_departement"
                            >
                                {{ div.nama_departement }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="mb-4 row align-items-center">
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
                    <button
                        v-if="
                            dataUser?.level_user === 'SUPER ADMIN' ||
                            hakAkses?.bisa_export === 'YA'
                        "
                        class="btn btn-success"
                        @click="exportExcel"
                    >
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <DataTable
                            id="tabelPoisisi"
                            :value="filterDataPosisi"
                            stripedRows
                            :paginator="true"
                            scrollable
                            scrollHeight="450px"
                            showGridlines
                            removableSort
                            :rows="5"
                            :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                            tableStyle="min-width: 60rem"
                            v-model:filters="filters"
                            @filter="onFilter"
                            :globalFilterFields="[
                                'nama_divisi',
                                'nama_departement',
                                'nama_position',
                            ]"
                        >
                            <template #header>
                                <div class="flex justify-end">
                                    <IconField>
                                        <InputIcon class="text-sm">
                                            <i class="fa fa-search text-base" />
                                        </InputIcon>
                                        <InputText
                                            class="h-9 text-sm"
                                            v-model="filters['global'].value"
                                            placeholder="Cari"
                                        />
                                    </IconField>
                                </div>
                            </template>
                            <Column header="No">
                                <template #body="row">
                                    {{ row.index + 1 }}
                                </template>
                            </Column>
                            <Column
                                field="nama_divisi"
                                header="Divisi"
                                sortable
                                bodyClass="text-capitalize"
                            />
                            <Column
                                field="nama_departement"
                                header="Departement"
                                sortable
                                bodyClass="text-capitalize"
                            />
                            <Column
                                field="nama_position"
                                header="Posisi"
                                sortable
                            />
                            <Column header="Aksi">
                                <template #body="row">
                                    <button
                                        class="btn btn-primary btn-sm me-2"
                                        @click="openEdit(row.data)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        class="btn btn-danger btn-sm"
                                        @click="hapusData(row.data)"
                                    >
                                        Hapus
                                    </button>
                                </template>
                            </Column>

                            <template #footer>
                                Total Data
                                {{
                                    filteredData?.length ??
                                    dataPosisition.length
                                }}
                            </template>
                        </DataTable>
                    </div>
                </div>

                <modalTambahPosisi ref="modalTambahPosisi" />
                <modalEditPosisi ref="modalEditPosisi" />
            </div>
        </div>
    </div>

    <loadingData :visible="loadingMenu" message="Loading" />
</template>

<script>
import { FilterMatchMode } from "@primevue/core/api";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import loadingData from "../../../../loading/loadingData.vue";
import modalTambahPosisi from "./modal/modalTambahPosisi.vue";
import modalEditPosisi from "./modal/modalEditPosisi.vue";

export default {
    components: {
        loadingData,
        modalTambahPosisi,
        modalEditPosisi,
        IconField,
        InputIcon,
        InputText,
    },
    data() {
        return {
            dataUser: window.userData,
            hakAkses: null,
            dataPosisition: [],
            filterDataPosisi: [],
            filterDataDivisi: [],
            filterDataDepertement: [],
            selectedDivisi: "",
            selectedDepartement: "",
            filteredData: null,
            filters: {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            },
            loadingMenu: true,
        };
    },
    async mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

        await this.divisi();
        await this.departement();
        await this.posisition();

        if (this.dataUser.level_user !== "SUPER ADMIN") {
            await this.cekStatusAkses();
        }

        this.$nextTick(() => {
            defaultSelect2("#filter_divisi", "-- PILIH DIVISI --");

            defaultSelect2("#filter_departement", "-- PILIH DEPARTEMENT --");

            $("#filter_divisi").on("change", (e) => {
                this.selectedDivisi = $(e.target).val();
            });

            $("#filter_departement").on("change", (e) => {
                this.selectedDepartement = $(e.target).val();
            });
        });

        this.loadingMenu = false;
    },
    computed: {
        filteredDepartement() {
            if (!this.selectedDivisi) return [];

            return this.filterDataDepertement.filter(
                (d) => d.divisi.nama_divisi === this.selectedDivisi
            );
        },
    },
    methods: {
        openModal() {
            this.$refs.modalTambahPosisi.openModal();
        },
        openEdit(item) {
            this.$refs.modalEditPosisi.openEdit(item);
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
        async posisition() {
            try {
                const data = await getAllPosisition();
                this.dataPosisition = data.map((it) => {
                    return {
                        ...it,
                        nama_divisi: it?.departement?.divisi?.nama_divisi ?? "",
                        nama_departement:
                            it?.departement?.nama_departement ?? "",
                    };
                });

                this.filterDataPosisi = data.map((it) => {
                    return {
                        ...it,
                        nama_divisi: it?.departement?.divisi?.nama_divisi ?? "",
                        nama_departement:
                            it?.departement?.nama_departement ?? "",
                    };
                });
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan this.divisi(): ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        async departement() {
            try {
                const data = await getAllDepartement();
                this.filterDataDepertement = data || [];
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
                this.filterDataDivisi = data || [];
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
        onFilter(e) {
            this.filteredData = e.filteredValue;
        },
        handleCari() {
            let dataFiltered = this.dataPosisition;

            if (this.selectedDivisi && !this.selectedDepartement) {
                dataFiltered = dataFiltered.filter((it) => {
                    return it.nama_divisi === this.selectedDivisi;
                });
            } else if (this.selectedDivisi && this.selectedDepartement) {
                dataFiltered = dataFiltered.filter((it) => {
                    return (
                        it.nama_divisi === this.selectedDivisi &&
                        it.nama_departement === this.selectedDepartement
                    );
                });
            }

            this.filterDataPosisi = dataFiltered;
        },
        resetFilter() {
            this.selectedDivisi = "";
            this.selectedDepartement = "";
            $("#filter_divisi").val("").trigger("change");
            $("#filter_departement").val("").trigger("change");

            this.filterDataPosisi = this.dataPosisition;
        },
    },
};
</script>

<style>
.p-datatable .p-datatable-tbody > tr:hover {
    background-color: yellow !important;
}
</style>
