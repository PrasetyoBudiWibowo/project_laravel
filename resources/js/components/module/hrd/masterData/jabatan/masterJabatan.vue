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
                        :value="datajabatan"
                        stripedRows
                        :paginator="true"
                        :rows="10"
                        showGridlines
                        tableStyle="min-width: 60rem"
                    >
                        <Column header="No">
                            <template #body="row">
                                {{ row.index + 1 }}
                            </template>
                        </Column>
                        <Column field="nama_jabatan" header="Kode Jabatan" />
                        <Column
                            field="nama_jabatan"
                            header="Nama Jabatan"
                            bodyClass="text-capitalize"
                        />
                        <Column header="Aksi">
                            <template #body="row">
                                <button class="btn btn-primary btn-sm me-2">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm">
                                    Hapus
                                </button>
                            </template>
                        </Column>
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
            datajabatan: [],
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

        await this.jabatan();
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
        async jabatan() {
            try {
                const data = await getAllJabatan();
                this.datajabatan = data || [];

                console.log("dknkad", this.datajabatan);
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan getAllJabatan(): ${
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
