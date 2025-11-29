<template>
    <div
        class="modal fade"
        id="modalTambahKaryawan"
        ref="modal"
        tabindex="-1"
        aria-labelledby="modalTambahKaryawanLabel"
        aria-hidden="true"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahKaryawanLabel">
                        Tambah karyawan
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        @click="closeModal"
                    ></button>
                </div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="nama_karyawan" class="form-label"
                                >Nama Karyawan</label
                            >
                            <input
                                type="text"
                                autocomplete="off"
                                class="form-control"
                                placeholder="Masukkan Nama Karyawan"
                                v-model="inputData.nama_karyawan"
                                @input="
                                    inputData.nama_karyawan =
                                        inputData.nama_karyawan.toUpperCase()
                                "
                            />
                        </div>

                        <div class="col-6">
                            <label
                                for="nama_panggilan_karyawan"
                                class="form-label"
                                >Nama Panggilan</label
                            >
                            <input
                                type="text"
                                autocomplete="off"
                                class="form-control"
                                placeholder="Masukkan Panggilan (optional)"
                                v-model="inputData.nama_panggilan_karyawan"
                                @input="
                                    inputData.nama_panggilan_karyawan =
                                        inputData.nama_panggilan_karyawan.toUpperCase()
                                "
                            />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="input_kd_divisi" class="form-label"
                                >Divisi</label
                            >
                            <select
                                id="input_kd_divisi"
                                class="form-select"
                                v-model="inputData.kd_divisi"
                            >
                                <option value="">-- Pilih Divisi --</option>
                                <option
                                    v-for="divisi in dataDivisi"
                                    :key="divisi.kd_divisi"
                                    :value="divisi.kd_divisi"
                                >
                                    {{ divisi.nama_divisi }}
                                </option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label for="input_kd_departement" class="form-label"
                                >Departement</label
                            >
                            <select
                                id="input_kd_departement"
                                class="form-select"
                                v-model="inputData.kd_departement"
                            >
                                <option value="">
                                    -- Pilih Departement --
                                </option>
                                <option
                                    v-for="departement in dataDepartement"
                                    :key="departement.kd_departement"
                                    :value="departement.kd_departement"
                                >
                                    {{ departement.nama_departement }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="input_kd_posisition" class="form-label"
                                >Posisi</label
                            >
                            <select
                                id="input_kd_posisition"
                                class="form-select"
                                v-model="inputData.kd_position"
                            >
                                <option value="">-- Pilih Position --</option>
                                <option
                                    v-for="posisition in dataPosisition"
                                    :key="posisition.kd_position"
                                    :value="posisition.kd_position"
                                >
                                    {{ posisition.nama_position }}
                                </option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label for="input_kd_jabatan" class="form-label"
                                >Posisi</label
                            >
                            <select
                                id="input_kd_jabatan"
                                class="form-select"
                                v-model="inputData.kd_jabatan"
                            >
                                <option value="">-- Pilih Position --</option>
                                <option
                                    v-for="jabatan in datajabatan"
                                    :key="jabatan.kd_jabatan"
                                    :value="jabatan.kd_jabatan"
                                >
                                    {{ jabatan.nama_jabatan }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="closeModal"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="btnSimpanPosisi"
                    >
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Modal } from "bootstrap";
import * as yup from "yup";

export default {
    data() {
        return {
            selectedDivisi: "",
            selectedDepartement: "",
            dataDivisi: [],
            dataDepartement: [],
            dataPosisition: [],
            datajabatan: [],
            inputData: {
                nama_karyawan: "",
                nama_panggilan_karyawan: "",
                kd_divisi: "",
                kd_departement: "",
                kd_position: "",
                kd_jabatan: "",
            },
        };
    },
    async mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

        this.bsModal = new Modal(this.$refs.modal);

        await this.divisi();
        await this.departement();
        await this.posisition();
        await this.jabatan();
    },
    methods: {
        openModal() {
            this.bsModal.show();
        },
        closeModal() {
            this.bsModal.hide();
        },
        async divisi() {
            try {
                const data = await getAllDivisi();
                this.dataDivisi = data || [];
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan saat memuat divisi: ${
                        error.response?.data?.message || error.message
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
                this.dataDepartement = data || [];
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan saat memuat departement: ${
                        error.response?.data?.message || error.message
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
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
