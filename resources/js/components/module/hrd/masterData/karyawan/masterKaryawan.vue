<template>
    <div class="p-4">
        <div class="card mb-3">
            <div class="card-header">
                <div>MASTER KARYAWAN</div>
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
                    >
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <DataTable id="tabelKaryawan" stripedRows showGridlines>
                            <Column header="No"></Column>
                            <Column header="Foto"></Column>
                            <Column header="Nama Karyawan"></Column>
                            <Column header="Penempatan"></Column>
                            <Column header="Aksi"></Column>
                        </DataTable>
                    </div>
                </div>
            </div>

            <modalTambahKaryawan ref="modalTambahKaryawan" />
        </div>
    </div>

    <loadingData :visible="loadingMenu" message="Loading" />
</template>

<script>
import { FilterMatchMode } from "@primevue/core/api";
import loadingData from "../../../../loading/loadingData.vue";
import modalTambahKaryawan from "./modal/modalTambahKaryawan.vue";

export default {
    components: { loadingData, modalTambahKaryawan },
    data() {
        return {
            datakaryawan: [],
            loadingMenu: true,
            hakAkses: null,

            dataUser: window.userData,
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

        this.loadingMenu = false;
    },
    methods: {
        openModal() {
            this.$refs.modalTambahKaryawan.openModal();
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
    },
};
</script>
