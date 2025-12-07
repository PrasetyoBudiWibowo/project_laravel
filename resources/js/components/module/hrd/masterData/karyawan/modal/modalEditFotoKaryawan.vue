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
                        @click="submitUpdateFoto"
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
import { Modal } from "bootstrap"; // pastikan bootstrap.bundle sudah dipakai

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
        };
    },
    computed: {
        currentFotoUrl() {
            return this.dataKaryawan?.foto_karyawan
                ? `/img/karyawan/${this.dataKaryawan.foto_karyawan}.${this.dataKaryawan.format_gambar}`
                : "/img/default/Default-Profile.png";
        },
    },
    mounted() {
        this.modalFoto = new Modal(document.getElementById("modalEditFoto"));

        this.$nextTick(() => {
            $("#modalEditFoto").on("hidden.bs.modal", () => {
                this.fotoFile = null;
                this.previewFoto = null;
                $("#input_foto_karyawan").val("");
            });
        });
    },
    methods: {
        openFotoModal() {
            this.previewFoto = null;
            this.fotoFile = null;
            this.modalFoto.show();
        },
        closeModal() {
            this.modalFoto.hide();
        },
        handleFotoChange(e) {
            const file = e.target.files[0];
            if (!file) return;

            this.fotoFile = file;

            // buat preview
            this.previewFoto = URL.createObjectURL(file);
        },
    },
};
</script>
