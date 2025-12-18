<template>
    <div class="p-4">
        <div class="card mb-3">
            <div class="card-header">
                <div>EDIT KARYAWAN</div>
            </div>

            <div class="card-body">
                <!-- =============================
                    FOTO KARYAWAN
                ================================= -->
                <div class="card mb-3">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                        @click="show.showFoto = !show.showFoto"
                        style="cursor: pointer"
                    >
                        <span>Foto Karyawan</span>

                        <i
                            class="fas fa-chevron-down"
                            :class="{ 'rotate-icon': show.showFoto }"
                        ></i>
                    </div>

                    <transition name="slide">
                        <div v-if="show.showFoto" class="p-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <img
                                    id="img_kry_preview"
                                    :src="
                                        dataKaryawan?.foto_karyawan
                                            ? `/assets/img/karyawan/${dataKaryawan.foto_karyawan}.${dataKaryawan.format_gambar}`
                                            : '/assets/img/default/Default-Profile.png'
                                    "
                                    alt="Profile Image"
                                    class="img-thumbnail"
                                    width="200"
                                />
                                <button
                                    class="btn btn-link p-0"
                                    @click="openFotoModal"
                                >
                                    <i class="fas fa-edit fs-4"></i>
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>

                <!-- =============================
                    DATA PRIBADI
                ================================= -->
                <div class="card mb-3">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                        @click="show.showPribadi = !show.showPribadi"
                        style="cursor: pointer"
                    >
                        <span>Data Pribadi</span>

                        <i
                            class="fas fa-chevron-down"
                            :class="{ 'rotate-icon': show.showPribadi }"
                        ></i>
                    </div>

                    <transition name="slide">
                        <div v-if="show.showPribadi" class="p-3 border-bottom">
                            <div class="mb-2 row">
                                <div class="col-6">
                                    <label class="form-label"
                                        >Nama Karyawan</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="dataKaryawan.nama_karyawan"
                                        disabled
                                    />
                                </div>
                                <div class="col-6">
                                    <label class="form-label"
                                        >Nama Panggilan</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="
                                            dataKaryawan.nama_panggilan_karyawan
                                        "
                                        disabled
                                    />
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-secondary" @click="goBack">
                        <i class="fas fa-arrow-left me-1"></i>
                        Kembali
                    </button>
                </div>
            </div>
        </div>

        <modalEditFotoKaryawan ref="modalEditFotoKaryawan" />
    </div>
</template>

<script>
import modalEditFotoKaryawan from "./modal/modalEditFotoKaryawan.vue";

export default {
    components: {
        modalEditFotoKaryawan,
    },
    data() {
        return {
            dataKaryawan: null,
            show: {
                showFoto: false,
                showPribadi: false,
            },
        };
    },
    props: {
        encrypted: {
            type: String,
            required: true,
        },
    },
    async mounted() {
        await this.getKaryawan();
    },
    methods: {
        openFotoModal() {
            this.$refs.modalEditFotoKaryawan.openFotoModal(
                this.encrypted,
                this.dataKaryawan
            );
        },
        goBack() {
            window.location.href = "/hrd/master-karyawan";
        },
        async getKaryawan() {
            try {
                const data = await karyawanByKode(this.encrypted);

                this.dataKaryawan = data ?? null;
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan saat mengambil data: ${
                        err?.response?.data?.message || err.message || err
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
.rotate-icon {
    transform: rotate(180deg);
    transition: 0.3s ease;
}

.slide-enter-active,
.slide-leave-active {
    transition: all 0.2s ease;
}
.slide-enter-from,
.slide-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}
</style>
