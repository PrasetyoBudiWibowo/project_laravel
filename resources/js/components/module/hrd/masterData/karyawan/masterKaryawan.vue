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
                        <DataTable
                            id="tabelKaryawan"
                            :value="datakaryawan"
                            stripedRows
                            showGridlines
                            tableStyle="min-width: 60rem"
                        >
                            <Column header="No">
                                <template #body="slotProps">
                                    {{ slotProps.index + 1 }}
                                </template>
                            </Column>

                            <Column header="Foto">
                                <template #body="slotProps">
                                    <img
                                        :src="getFoto(slotProps.data)"
                                        alt="Foto"
                                        style="
                                            width: 90px;
                                            height: 90px;
                                            border-radius: 50%;
                                            object-fit: cover;
                                        "
                                    />
                                </template>
                            </Column>

                            <Column header="Nama Karyawan">
                                <template #body="slotProps">
                                    <div>
                                        <strong>Nama Lengkap :</strong><br />
                                        {{ slotProps.data.nama_karyawan }}<br />

                                        <strong>Nama Panggilan :</strong><br />
                                        {{
                                            slotProps.data
                                                .nama_panggilan_karyawan
                                        }}
                                    </div>
                                </template>
                            </Column>
                            <Column header="Penempatan">
                                <template #body="slotProps">
                                    <div>
                                        <strong>Nama Divisi :</strong><br />
                                        {{ slotProps.data.Divisi.nama_divisi
                                        }}<br />

                                        <strong>Nama Departement :</strong
                                        ><br />
                                        {{
                                            slotProps.data.Departement
                                                .nama_departement
                                        }}<br />
                                        <strong>Posisi :</strong><br />
                                        {{ slotProps.data.Posisi.nama_position
                                        }}<br />
                                        <strong>Jabatan :</strong><br />
                                        {{
                                            slotProps.data.JabatanKaryawan
                                                .nama_jabatan
                                        }}
                                    </div>
                                </template>
                            </Column>
                            <Column header="Aksi">
                                <template #body="slotProps">
                                    <div class="d-grid aksi-grid">
                                        <button class="btn btn-primary btn-sm">
                                            <i class="fas fa-user"></i>
                                        </button>
                                        <button class="btn btn-success btn-sm">
                                            <i class="fas fa-file-alt"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm">
                                            <i class="fas fa-building"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </template>
                            </Column>
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

        await this.karyawan();

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
        async karyawan() {
            try {
                const data = await getAllDataKaryawan();
                this.datakaryawan = data || [];

                console.log("dals", data);
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan this.karyawan(): ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        getFoto(row) {
            if (row.foto_karyawan) {
                return `/assets/img/user/${row.img_user}.${row.format_img_user}`;
            } else {
                return `/assets/img/default/Default-Profile.png`;
            }
        },
    },
};
</script>

<style>
.aksi-grid {
    grid-template-columns: repeat(2, 1fr); /* 2 kolom */
    gap: 6px; /* Jarak antar tombol */
}
</style>
