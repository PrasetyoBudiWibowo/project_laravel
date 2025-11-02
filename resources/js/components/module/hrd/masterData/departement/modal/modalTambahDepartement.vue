<template>
    <div
        class="modal fade"
        id="modalTambahDepartement"
        ref="modal"
        tabindex="-1"
        aria-labelledby="modalTambahDepartementLabel"
        aria-hidden="true"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahDepartementLabel">
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
                        <label for="nama_departement" class="form-label"
                            >Nama Departement</label
                        >
                        <input
                            type="text"
                            autocomplete="off"
                            class="form-control"
                            placeholder="Masukkan Nama Departement"
                            v-model="inputData.nama_departement"
                            @input="
                                inputData.nama_departement =
                                    inputData.nama_departement.toUpperCase()
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
                        @click="btnSimpanDepartement"
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
    // props: {
    //     dataDivisi: Array,
    // },
    data() {
        return {
            selectedDivisi: "",
            bsModal: null,
            dataDivisi: [],
            inputData: {
                kd_divisi: "",
                nama_departement: "",
            },
        };
    },
    async mounted() {
        this.bsModal = new Modal(this.$refs.modal);

        this.$nextTick(() => {
            $("#modalTambahDepartement").on("hidden.bs.modal", () => {
                this.inputData.nama_departement = "";
                $("#input_kd_divisi").val("").trigger("change");
            });
        });

        await this.divisi();
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
        btnSimpanDepartement() {
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
                    this.simpanDepartement();
                }
            });
        },
        async simpanDepartement() {
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
                value: dataToSave.nama_departement,
                message: "Departement Tidak Boleh Kosong",
            });

            const schema = yup.object({
                nama_departement: yup
                    .string()
                    .required("Nama Departement wajib diisi")
                    .matches(
                        /^[A-Za-z\s]+$/,
                        "Nama Departement Hanya Boleh huruf yang diperbolehkan"
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
                    "/hrd/simpan-departement",
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
