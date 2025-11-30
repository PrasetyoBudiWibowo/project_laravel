<template>
    <div
        class="modal fade"
        id="modalTambahJabatan"
        ref="modal"
        tabindex="-1"
        aria-labelledby="modalTambahJabatanLabel"
        aria-hidden="true"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahJabatanLabel">
                        Tambah Jabatan
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        @click="closeModal"
                    ></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_jabatan" class="form-label"
                            >Nama Jabatan</label
                        >
                        <input
                            type="text"
                            autocomplete="off"
                            class="form-control"
                            placeholder="Masukkan Nama Posisi"
                            v-model="inputData.nama_jabatan"
                            @input="
                                inputData.nama_jabatan =
                                    inputData.nama_jabatan.toUpperCase()
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
                        @click="btnSimpanJabatan"
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
            inputData: {
                nama_jabatan: "",
            },
        };
    },
    async mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

        this.bsModal = new Modal(this.$refs.modal);
    },
    methods: {
        openModal() {
            this.bsModal.show();
        },
        closeModal() {
            this.bsModal.hide();
        },
        btnSimpanJabatan() {
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
                    this.simpanJabatan();
                }
            });
        },
        async simpanJabatan() {
            let dataToSave = {
                ...this.inputData,
                user_input: window.encryptedUserId,
            };

            let requireValue = [];

            requireValue.push({
                value: dataToSave.nama_jabatan,
                message: "Nama Jabatan Tidak Boleh Kosong",
            });

            if (!validasiBanyakInputan(requireValue)) return;

            const schema = yup.object({
                nama_jabatan: yup
                    .string()
                    .required("Nama jabatan wajib diisi")
                    .matches(
                        /^[A-Za-z\s]+$/,
                        "Nama jabatan Hanya Boleh huruf yang diperbolehkan"
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
                    "/hrd/simpan-jabatan",
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
                    text: `Terjadi kesalahan simpanDivisi: ${
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
