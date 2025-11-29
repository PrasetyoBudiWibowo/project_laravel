<template>
    <div class="p-4">
        <div class="card mb-3">
            <div class="card-header">
                <div>MASTER JABATAN</div>
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
                    <!-- <button
                        v-if="
                            dataUser?.level_user === 'SUPER ADMIN' ||
                            hakAkses?.bisa_export === 'YA'
                        "
                        class="btn btn-success"
                        @click="exportExcel"
                    >
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button> -->
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-12">
                    <DataTable
                        id="tabelJabatan"
                        stripedRows
                        :paginator="true"
                        showGridlines
                    >
                        <Column header="No"></Column>
                        <Column header="Kode Jabatan"></Column>
                        <Column header="Nama Jabatan"></Column>
                        <Column header="Aksi"></Column>
                    </DataTable>
                </div>
            </div>

            <modalTambahJabatan ref="modalTambahJabatan" />
        </div>
    </div>
</template>

<script>
import loadingData from "../../../../loading/loadingData.vue";
import modalTambahJabatan from "./modal/modalTambahJabatan.vue";

export default {
    components: {
        modalTambahJabatan,
    },
    data() {
        return {
            dataUser: window.userData,
            hakAkses: null,
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
    },
    methods: {
        openModal() {
            this.$refs.modalTambahJabatan.openModal();
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
