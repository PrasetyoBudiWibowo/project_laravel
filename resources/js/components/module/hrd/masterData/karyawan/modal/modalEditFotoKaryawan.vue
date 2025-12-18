<template>
    <div
        class="modal fade"
        id="modalEditFoto"
        tabindex="-1"
        aria-labelledby="modalEditFotoLabel"
        aria-hidden="true"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Foto Karyawan</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>
                </div>

                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img
                            :src="previewFoto || currentFotoUrl"
                            alt="Preview"
                            class="img-thumbnail"
                            width="200"
                        />
                    </div>
                    <input
                        id="input_foto_karyawan"
                        type="file"
                        class="form-control"
                        accept="image/*"
                        @change="handleFotoChange"
                    />

                    <div class="mb-3 mt-2">
                        <label class="form-label fw-semibold">
                            Catatan (opsional)
                        </label>
                        <textarea
                            class="form-control"
                            rows="3"
                            placeholder="ALASAN DI UBAH..."
                            :value="keterangan_input"
                            @input="
                                keterangan_input =
                                    $event.target.value.toUpperCase()
                            "
                        ></textarea>
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
                        class="btn btn-primary"
                        @click="btnSimpanFotoKaryawan"
                        :disabled="!fotoFile"
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

export default {
    data() {
        return {
            show: {
                showFoto: false,
                showPribadi: false,
            },
            modalFoto: null,
            fotoFile: null,
            previewFoto: null,

            datakaryawan: {
                foto_karyawan: null,
                format_gambar: null,
            },

            keterangan_input: "",

            formatGambar: ["image/jpeg", "image/png", "image/jpg"],
            maxSize: 50 * 1024 * 1024,
        };
    },
    watch: {
        keterangan_input(val) {
            if (val) {
                this.keterangan_input = val.toUpperCase();
            }
        },
    },
    computed: {
        currentFotoUrl() {
            return this.dataKaryawan?.foto_karyawan
                ? `/assets/img/karyawan/${this.dataKaryawan.foto_karyawan}.${this.dataKaryawan.format_gambar}`
                : "/assets/img/default/Default-Profile.png";
        },
    },
    mounted() {
        this.modalFoto = new Modal(document.getElementById("modalEditFoto"));

        this.$nextTick(() => {
            $("#modalEditFoto").on("hidden.bs.modal", () => {
                this.resetFormInput();
            });
        });
    },
    methods: {
        openFotoModal(encrypted, data) {
            if (data?.foto_karyawan !== null && data?.format_gambar !== null) {
                this.previewFoto = `/assets/img/karyawan/${data?.foto_karyawan}.${data?.format_gambar}`;
            } else {
                this.previewFoto = null;
            }

            this.fotoFile = null;
            this.datakaryawan = {
                foto_karyawan: data?.foto_karyawan ?? null,
                format_gambar: data?.format_gambar ?? null,
            };

            this.encrypted = encrypted;
            this.modalFoto.show();
        },
        closeModal() {
            this.modalFoto.hide();
        },
        handleFotoChange(e) {
            const file = e.target.files[0];
            if (!file) return;

            if (!this.formatGambar.includes(file.type)) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: "Format file harus JPG, JPEG, atau PNG",
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
                this.resetFormInput();
                return;
            }

            if (file.size > this.maxSize) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: "Ukuran gambar maksimal 50 MB",
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
                this.resetFormInput();
                return;
            }

            if (this.previewFoto) {
                URL.revokeObjectURL(this.previewFoto);
                this.previewFoto = null;
            }

            this.fotoFile = file;
            this.previewFoto = URL.createObjectURL(file);
        },
        resetFormInput() {
            if (this.previewFoto) {
                URL.revokeObjectURL(this.previewFoto);
            }

            this.fotoFile = null;
            this.previewFoto = null;
            this.keterangan_input = "";

            const input = document.getElementById("input_foto_karyawan");
            if (input) input.value = "";
        },
        btnSimpanFotoKaryawan() {
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
                    this.simpanFotoKaryawan();
                }
            });
        },
        async simpanFotoKaryawan() {
            const formData = new FormData();
            formData.append("type", "FOTO");
            formData.append("foto_karyawan", this.fotoFile);
            formData.append("kd_karyawan", this.encrypted);
            formData.append("user_ubah", window.encryptedUserId);
            formData.append("keterangan_input", this.keterangan_input);

            // for (const [key, value] of formData.entries()) {
            //     console.log(key, value);
            // }

            try {
                Swal.fire({
                    title: "Sedang Proses Simpan Data",
                    text: "Mohon tunggu.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                const response = await axios.post(
                    "/hrd/ubah-karyawan",
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                const result = response.data;
                if (result.status === "success") {
                    Swal.close();
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: result.message || "Data berhasil Disimpan!",
                        confirmButtonText: "Tutup",
                        customClass: {
                            confirmButton: "btn btn-success",
                        },
                        buttonsStyling: false,
                    }).then(() => {
                        window.location.href = result.redirect;
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
                        buttonsStyling: false,
                    });
                }
            } catch (error) {
                Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan simpanFotoKaryawan : ${
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
