<template>
    <div class="p-4">
        <div class="card mb-3">
            <div class="card-header">
                <div>MASTER POSISI</div>
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
                            :value="dataPosisition"
                            stripedRows
                            :paginator="true"
                            scrollable
                            scrollHeight="450px"
                            showGridlines
                            removableSort
                            :rows="5"
                            :rowsPerPageOptions="[5, 10, 20, 50, 100]"
                            tableStyle="min-width: 60rem"
                        >
                            <Column header="No">
                                <template #body="row">
                                    {{ row.index + 1 }}
                                </template>
                            </Column>

                            <Column
                                field="departement.divisi.nama_divisi"
                                header="Divisi"
                                sortable
                                bodyClass="text-capitalize"
                            />

                            <Column
                                field="departement.nama_departement"
                                header="Departement"
                                sortable
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
                                Total Data {{ dataPosisition?.length || 0 }}
                            </template>
                        </DataTable>
                    </div>
                </div>

                <modalTambahPosisi ref="modalTambahPosisi" />
                <modalEditPosisi ref="modalEditPosisi" />
            </div>
        </div>
    </div>
</template>

<script>
import loadingData from "../../../../loading/loadingData.vue";
import modalTambahPosisi from "./modal/modalTambahPosisi.vue";
import modalEditPosisi from "./modal/modalEditPosisi.vue";

export default {
    components: { loadingData, modalTambahPosisi, modalEditPosisi },
    data() {
        return {
            dataUser: window.userData,
            hakAkses: null,
            dataPosisition: [],
        };
    },
    async mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

        await this.posisition();

        if (this.dataUser.level_user !== "SUPER ADMIN") {
            await this.cekStatusAkses();
        }
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
                this.dataPosisition = data || [];
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
    },
};
</script>

<style>
.p-datatable .p-datatable-tbody > tr:hover {
    background-color: yellow !important;
}
</style>
