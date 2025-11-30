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
                                :disabled="!inputData.kd_divisi"
                            >
                                <option value="">
                                    -- Pilih Departement --
                                </option>
                                <option
                                    v-for="departement in filteredDepartement"
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
                                :disabled="!inputData.kd_departement"
                            >
                                <option value="">-- Pilih Position --</option>
                                <option
                                    v-for="posisition in filteredPosisition"
                                    :key="posisition.kd_position"
                                    :value="posisition.kd_position"
                                >
                                    {{ posisition.nama_position }}
                                </option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label for="input_kd_jabatan" class="form-label"
                                >Jabatan</label
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
                        @click="btnSimpanKaryawan"
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

        this.$nextTick(() => {
            defaultSelect2(
                "#input_kd_divisi",
                "-- PILIH DIVISI --",
                "#modalTambahKaryawan"
            );

            defaultSelect2(
                "#input_kd_departement",
                "-- PILIH DEPARTEMENT --",
                "#modalTambahKaryawan"
            );

            defaultSelect2(
                "#input_kd_posisition",
                "-- PILIH POSISI --",
                "#modalTambahKaryawan"
            );

            defaultSelect2(
                "#input_kd_jabatan",
                "-- PILIH JABATAN --",
                "#modalTambahKaryawan"
            );

            $("#input_kd_divisi").on("change", (e) => {
                this.inputData.kd_divisi = e.target.value;
                this.selectedDivisi =
                    e.target.options[e.target.selectedIndex].text;
            });

            $("#input_kd_departement").on("change", (e) => {
                this.inputData.kd_departement = e.target.value;
                this.selectedDepartement =
                    e.target.options[e.target.selectedIndex].text;
            });

            $("#input_kd_posisition").on("change", (e) => {
                this.inputData.kd_position = e.target.value;
            });

            $("#input_kd_jabatan").on("change", (e) => {
                this.inputData.kd_jabatan = e.target.value;
            });
        });

        await this.divisi();
        await this.departement();
        await this.posisition();
        await this.jabatan();
    },
    watch: {
        "inputData.kd_divisi"() {
            this.inputData.kd_departement = "";
            this.inputData.kd_position = "";
        },
        "inputData.kd_departement"() {
            this.inputData.kd_position = "";
        },
    },
    computed: {
        filteredDepartement() {
            if (!this.inputData.kd_divisi) return [];

            return this.dataDepartement.filter(
                (d) => d.divisi.nama_divisi === this.selectedDivisi
            );
        },
        filteredPosisition() {
            if (!this.inputData.kd_departement) return [];

            return this.dataPosisition.filter(
                (it) =>
                    it.departement.nama_departement === this.selectedDepartement
            );
        },
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
        btnSimpanKaryawan() {
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda Yakin Ingin Menyimpan Data ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger",
                },
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.simpanKaryawan();
                }
            });
        },
        async simpanKaryawan() {
            let dataToSave = {
                ...this.inputData,
                user_input: window.encryptedUserId,
            };

            let requireValue = [];

            requireValue.push({
                value: dataToSave.nama_karyawan,
                message: "Nama karyawan Tidak Boleh Kosong",
            });

            requireValue.push({
                value: dataToSave.kd_divisi,
                message: "Divisi Tidak Boleh Kosong",
            });

            requireValue.push({
                value: dataToSave.kd_departement,
                message: "Departement Tidak Boleh Kosong",
            });

            requireValue.push({
                value: dataToSave.kd_position,
                message: "Posisi Tidak Boleh Kosong",
            });

            if (!validasiBanyakInputan(requireValue)) return;

            const schema = yup.object({
                nama_karyawan: yup
                    .string()
                    .required("Nama Karyawan wajib diisi")
                    .matches(
                        /^[A-Za-z\s&]+$/,
                        "Nama Karyawan Hanya Boleh huruf yang diperbolehkan"
                    ),
            });

            try {
                await schema.validate(dataToSave, { abortEarly: false });

                Swal.fire({
                    title: "Sedang Proses Simpan Data",
                    text: "Mohon tunggu.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                const response = await axios.post(
                    "/hrd/simpan-karyawan",
                    dataToSave
                );
                const result = response.data;

                Swal.close();

                if (result.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: result.message || "Data berhasil Disimpan!",
                        customClass: {
                            confirmButton: "btn btn-success",
                        },
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: result.message,
                        confirmButtonText: "Tutup",
                        customClass: {
                            confirmButton: "btn btn-danger",
                        },
                    });
                }
            } catch (error) {
                Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan simpanDepartement : ${
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
