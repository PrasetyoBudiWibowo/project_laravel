<template>
    <div
        class="modal fade"
        id="modalEditPosisi"
        ref="modal"
        tabindex="-1"
        aria-labelledby="modalEditPosisiLabel"
        aria-hidden="true"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Posisi</h5>
                    <button
                        type="button"
                        class="btn-close"
                        @click="closeModal"
                    ></button>
                </div>

                <div class="modal-body">
                    <input
                        type="hidden"
                        id="kd_position"
                        class="form-control"
                        v-model="editData.kd_position"
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
                        <label for="edit_kd_departement" class="form-label"
                            >Departement</label
                        >
                        <select
                            id="edit_kd_departement"
                            class="form-select"
                            v-model="editData.kd_departement"
                            :disabled="!editData.kd_divisi"
                        >
                            <option value="">-- Pilih Departement --</option>
                            <option
                                v-for="departement in filteredEditDepartement"
                                :key="departement.kd_departement"
                                :value="departement.kd_departement"
                            >
                                {{ departement.nama_departement }}
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Posisi</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="editData.nama_position"
                            @input="
                                editData.nama_position =
                                    editData.nama_position.toUpperCase()
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
                        @click="btnUbahPosisi"
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
            modalInstance: null,
            selectedDivisi: "",
            selectedDepartement: "",
            dataDivisi: [],
            dataDepartement: [],
            editData: {
                kd_position: "",
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

        this.modalInstance = new Modal(this.$refs.modal);

        await this.divisi();
        await this.departement();

        this.$nextTick(() => {
            defaultSelect2(
                "#edit_kd_divisi",
                "-- PILIH DIVISI --",
                "#modalEditPosisi"
            );

            defaultSelect2(
                "#edit_kd_departement",
                "-- PILIH DEPARTEMENT --",
                "#modalEditPosisi"
            );

            $("#edit_kd_divisi").on("change", (e) => {
                this.editData.kd_divisi = e.target.value;

                this.selectedDivisi =
                    e.target.options[e.target.selectedIndex].text;
            });

            $("#edit_kd_departement").on("change", (e) => {
                this.editData.kd_departement = e.target.value;
            });
        });
    },
    computed: {
        filteredEditDepartement() {
            if (!this.editData.kd_divisi) return [];

            const selectedDiv = this.dataDivisi.find(
                (d) => d.kd_divisi === this.editData.kd_divisi
            );

            if (!selectedDiv) return [];

            return this.dataDepartement.filter(
                (dep) => dep.divisi.nama_divisi === selectedDiv.nama_divisi
            );
        },
    },
    methods: {
        openEdit(data) {
            const divisi = this.dataDivisi.find(
                (it) => it.nama_divisi === data.departement.divisi.nama_divisi
            );

            const departement = this.dataDepartement.find(
                (it) =>
                    it.nama_departement === data.departement.nama_departement
            );

            this.editData = {
                kd_position: data.kd_position,
                kd_divisi: divisi?.kd_divisi ?? "",
                kd_departement: departement?.kd_departement ?? "",
                nama_position: data.nama_position,
            };

            this.modalInstance.show();

            this.$nextTick(() => {
                $("#edit_kd_divisi")
                    .val(this.editData.kd_divisi)
                    .trigger("change");

                $("#edit_kd_departement")
                    .val(this.editData.kd_departement)
                    .trigger("change");
            });
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
        btnUbahPosisi() {
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
                    this.ubahPosisi();
                }
            });
        },
        async ubahPosisi() {
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
                    "/hrd/ubah-posisi",
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
