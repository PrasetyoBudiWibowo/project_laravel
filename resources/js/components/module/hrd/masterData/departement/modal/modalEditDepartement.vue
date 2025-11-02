<template>
    <div
        class="modal fade"
        id="modalEditDepartement"
        ref="modal"
        tabindex="-1"
        aria-labelledby="modalEditDepartementLabel"
        aria-hidden="true"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditDepartementLabel">
                        Ubah Departement
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        @click="closeModal"
                    ></button>
                </div>
                <div class="modal-body">
                    <input
                        type="hidden"
                        id="kd_departement"
                        class="form-control"
                        v-model="editData.kd_departement"
                    />
                    <div class="mb-3">
                        <label for="edit_kd_divisi" class="form-label"
                            >Divisi</label
                        >
                        <select
                            id="edit_kd_divisi"
                            class="form-select"
                            v-model="editData.kd_divisi"
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
                            v-model="editData.nama_departement"
                            @input="
                                editData.nama_departement =
                                    editData.nama_departement.toUpperCase()
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
                        @click="btnUbahDepartement"
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
            dataDivisi: [],
            modalInstance: null,
            editData: {
                kd_departement: "",
                kd_divisi: "",
                nama_departement: "",
            },
        };
    },
    async mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

        this.modalInstance = new Modal(this.$refs.modal);
        await this.divisi();

        this.$nextTick(() => {
            defaultSelect2(
                "#edit_kd_divisi",
                "-- PILIH DIVISI --",
                "#modalEditDepartement"
            );
        });
    },
    methods: {
        openModal(data) {
            let selectedDivisi = this.dataDivisi.filter(
                (it) => it.nama_divisi === data.divisi.nama_divisi
            );

            if (selectedDivisi) {
                this.$nextTick(() => {
                    $("#edit_kd_divisi")
                        .val(selectedDivisi[0].kd_divisi)
                        .trigger("change");
                });

                this.editData = {
                    kd_departement: data.kd_departement,
                    nama_departement: data.nama_departement,
                    kd_divisi: selectedDivisi[0].kd_divisi,
                };
            }

            this.modalInstance.show();
        },
        closeModal() {
            this.modalInstance.hide();
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
        btnUbahDepartement() {
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda Yakin Ingin Mengubah Data ini?",
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
                    this.ubahDepartement();
                }
            });
        },
        async ubahDepartement() {
            let dataToSave = {
                ...this.editData,
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
                    "/hrd/ubah-departement",
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
            } catch (err) {
                Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan simpanDepartement : ${
                        err.response?.data?.message || err.message
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
