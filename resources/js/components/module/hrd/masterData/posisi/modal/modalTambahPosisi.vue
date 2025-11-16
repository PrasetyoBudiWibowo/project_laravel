<template>
    <div
        class="modal fade"
        id="modalTambahPosisi"
        ref="modal"
        tabindex="-1"
        aria-labelledby="modalTambahPosisiLabel"
        aria-hidden="true"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahPosisiLabel">
                        Tambah Departement
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        @click="closeModal"
                    ></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
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

                    <div class="mb-3">
                        <label for="input_kd_depatement" class="form-label"
                            >Divisi</label
                        >
                        <select
                            id="input_kd_depatement"
                            class="form-select"
                            v-model="inputData.kd_departement"
                            :disabled="!inputData.kd_divisi"
                        >
                            <option value="">-- Pilih Departement --</option>
                            <option
                                v-for="departement in filteredDepartement"
                                :key="departement.kd_departement"
                                :value="departement.kd_departement"
                            >
                                {{ departement.nama_departement }}
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="nama_position" class="form-label"
                            >Nama Posisi</label
                        >
                        <input
                            type="text"
                            autocomplete="off"
                            class="form-control"
                            placeholder="Masukkan Nama Posisi"
                            v-model="inputData.nama_position"
                            @input="
                                inputData.nama_position =
                                    inputData.nama_position.toUpperCase()
                            "
                        />
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
            inputData: {
                kd_divisi: "",
                kd_departement: "",
                nama_position: "",
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

        this.$nextTick(() => {
            defaultSelect2(
                "#input_kd_divisi",
                "-- PILIH DIVISI --",
                "#modalTambahPosisi"
            );

            defaultSelect2(
                "#input_kd_depatement",
                "-- PILIH DEPARTEMENT --",
                "#modalTambahPosisi"
            );

            $("#input_kd_divisi").on("change", (e) => {
                this.inputData.kd_divisi = e.target.value;
                this.selectedDivisi =
                    e.target.options[e.target.selectedIndex].text;
            });

            $("#input_kd_depatement").on("change", (e) => {
                this.inputData.kd_departement = e.target.value;
            });

            $("#modalTambahPosisi").on("hidden.bs.modal", () => {
                this.inputData.nama_position = "";
                $("#input_kd_divisi").val("").trigger("change");
                $("#input_kd_depatement").val("").trigger("change");
            });
        });
    },
    computed: {
        filteredDepartement() {
            if (!this.inputData.kd_divisi) return [];

            return this.dataDepartement.filter(
                (d) => d.divisi.nama_divisi === this.selectedDivisi
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
        btnSimpanPosisi() {
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
                    this.simpanPosisi();
                }
            });
        },
        async simpanPosisi() {
            let dataToSave = {
                ...this.inputData,
                user_input: window.encryptedUserId,
            };

            let requireValue = [];

            requireValue.push({
                value: dataToSave.kd_divisi,
                message: "Divisi Tidak Boleh Kosong",
            });

            requireValue.push({
                value: dataToSave.kd_departement,
                message: "Departement Tidak Boleh Kosong",
            });

            requireValue.push({
                value: dataToSave.nama_position,
                message: "Posisi Tidak Boleh Kosong",
            });

            const schema = yup.object({
                nama_position: yup
                    .string()
                    .required("Nama Posisi wajib diisi")
                    .matches(
                        /^[A-Za-z\s&]+$/,
                        "Nama Posisi Hanya Boleh huruf yang diperbolehkan"
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
                    "/hrd/simpan-posisi",
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
