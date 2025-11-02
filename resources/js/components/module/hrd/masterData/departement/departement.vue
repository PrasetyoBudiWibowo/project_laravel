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
                                        <th>Depatement</th>
                                        <th>Aksi</th>
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
            </div>
        </div>
    </div>
</template>

<script>
import modalTambahDepartement from "./modal/modalTambahDepartement.vue";

export default {
    components: { modalTambahDepartement },
    data() {
        return {
            dataUser: window.userData,
            hakAkses: null,
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
    },
    methods: {
        openModal() {
            this.$refs.modalTambahDepartement.openModal();
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
                console.log("dhnas", (this.hakAkses = allowed[0]));
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
